/**
 * АвтоЗапчасть — Main JavaScript
 * Combines Mazlay template JS logic with backend cart/search API calls
 */

'use strict';

/* ── Live Search (header) ────────────────────────────────────── */
(function initLiveSearch() {
  var input    = document.getElementById('header-search-input');
  var dropdown = document.getElementById('live-search-dropdown');
  if (!input || !dropdown) return;

  var timer    = null;
  var lastQ    = '';

  function fmt(p) {
    return parseFloat(p).toLocaleString('ru-RU', { maximumFractionDigits: 0 }) + ' ₽';
  }

  function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  function render(results, q) {
    if (!results.length) {
      dropdown.innerHTML = '<div class="live_search_no_results">Ничего не найдено по запросу «' + esc(q) + '»</div>';
    } else {
      dropdown.innerHTML = results.map(function(r) {
        return '<a href="/catalog/part.php?id=' + r.id + '" class="live_search_item">' +
               '<span class="live_search_num">' + esc(r.part_number) + '</span>' +
               '<span class="live_search_name">' + esc(r.name) + '</span>' +
               '<span class="live_search_price">' + fmt(r.price) + '</span>' +
               '</a>';
      }).join('');
    }
    dropdown.style.display = 'block';
  }

  async function doSearch(q) {
    try {
      var res  = await fetch('/api/search.php?q=' + encodeURIComponent(q));
      var data = await res.json();
      if (Array.isArray(data)) render(data, q);
    } catch(e) {}
  }

  input.addEventListener('input', function() {
    var q = this.value.trim();
    clearTimeout(timer);
    if (q.length < 2) {
      dropdown.style.display = 'none';
      lastQ = '';
      return;
    }
    if (q === lastQ) return;
    lastQ = q;
    timer = setTimeout(function() { doSearch(q); }, 300);
  });

  document.addEventListener('click', function(e) {
    if (!input.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });

  input.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { dropdown.style.display = 'none'; }
  });
})();


/* ── Flash auto-dismiss ──────────────────────────────────────── */
(function() {
  var f = document.getElementById('az-flash');
  if (!f) return;
  setTimeout(function() {
    f.style.transition = 'opacity 0.5s';
    f.style.opacity    = '0';
    setTimeout(function() { f.remove(); }, 500);
  }, 4000);
})();


/* ── Cart AJAX API ───────────────────────────────────────────── */
var Cart = {
  async add(partId, qty) {
    return await this._post({ action:'add', part_id:partId, quantity: qty || 1 });
  },
  async remove(partId) {
    return await this._post({ action:'remove', part_id:partId });
  },
  async update(partId, qty) {
    return await this._post({ action:'update', part_id:partId, quantity:qty });
  },
  async _post(data) {
    try {
      var res = await fetch('/api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      return await res.json();
    } catch(e) { return { success:false, error:e.message }; }
  },
  updateBadge(count) {
    var badge = document.getElementById('cart-badge');
    if (!badge) return;
    badge.textContent = count || '0';
  }
};


/* ── Add to cart buttons ─────────────────────────────────────── */
document.addEventListener('click', async function(e) {
  var btn = e.target.closest('[data-add-cart]');
  if (!btn) return;
  e.preventDefault();
  var partId = btn.dataset.addCart;
  var qty    = parseInt(btn.dataset.qty || 1);
  var orig   = btn.textContent;
  btn.textContent = '...';

  var result = await Cart.add(partId, qty);
  if (result.success) {
    btn.textContent = '✓ Добавлено!';
    Cart.updateBadge(result.cart_count);
    setTimeout(function() { btn.textContent = orig; }, 2000);
  } else {
    if (result.error === 'Необходима авторизация') {
      window.location.href = '/auth/login.php';
    } else {
      btn.textContent = result.error || 'Ошибка';
      setTimeout(function() { btn.textContent = orig; }, 2500);
    }
  }
});


/* ── Cart quantity controls ──────────────────────────────────── */
document.addEventListener('click', async function(e) {
  var plus = e.target.closest('[data-qty-plus]');
  if (plus) {
    var row   = plus.closest('[data-cart-row]');
    var input = row && row.querySelector('[data-qty-input]');
    if (!input) return;
    var newQty = Math.min(99, parseInt(input.value) + 1);
    input.value = newQty;
    await updateCartRow(row, parseInt(row.dataset.cartRow), newQty);
    return;
  }

  var minus = e.target.closest('[data-qty-minus]');
  if (minus) {
    var row   = minus.closest('[data-cart-row]');
    var input = row && row.querySelector('[data-qty-input]');
    if (!input) return;
    var newQty = Math.max(1, parseInt(input.value) - 1);
    input.value = newQty;
    await updateCartRow(row, parseInt(row.dataset.cartRow), newQty);
    return;
  }

  var rmBtn = e.target.closest('[data-cart-remove]');
  if (rmBtn) {
    e.preventDefault();
    var partId = rmBtn.dataset.cartRemove;
    var row    = rmBtn.closest('tr, [data-cart-row]');
    var result = await Cart.remove(partId);
    if (result.success) {
      if (row) row.remove();
      Cart.updateBadge(result.cart_count);
      updateCartTotal(result.cart_total);
    }
  }
});

async function updateCartRow(row, partId, qty) {
  var result = await Cart.update(partId, qty);
  if (result.success) {
    Cart.updateBadge(result.cart_count);
    updateCartTotal(result.cart_total);
    var sub = row && row.querySelector('[data-row-subtotal]');
    if (sub && result.row_subtotal !== undefined) {
      sub.textContent = parseFloat(result.row_subtotal).toLocaleString('ru-RU', {maximumFractionDigits:0}) + ' ₽';
    }
  }
}

function updateCartTotal(total) {
  var el = document.getElementById('cart-total');
  if (el && total !== undefined) {
    el.textContent = parseFloat(total).toLocaleString('ru-RU', {maximumFractionDigits:0}) + ' ₽';
  }
}


/* ── Status update (admin orders) ────────────────────────────── */
document.addEventListener('change', async function(e) {
  var sel = e.target.closest('[data-status-update]');
  if (!sel) return;
  var fd = new FormData();
  fd.append('action',     'update_status');
  fd.append('order_id',   sel.dataset.statusUpdate);
  fd.append('status',     sel.value);
  fd.append('csrf_token', sel.dataset.csrf);
  try {
    var res  = await fetch('/admin/orders.php', { method:'POST', body:fd });
    var data = await res.json();
    if (!data.success) alert('Ошибка: ' + (data.error||''));
  } catch(e) {}
});


/* ── Confirm delete ──────────────────────────────────────────── */
document.addEventListener('click', function(e) {
  var btn = e.target.closest('[data-confirm]');
  if (!btn) return;
  if (!confirm(btn.dataset.confirm || 'Вы уверены?')) {
    e.preventDefault();
    e.stopImmediatePropagation();
  }
});


/* ── Filter auto-submit ──────────────────────────────────────── */
document.addEventListener('change', function(e) {
  var el = e.target.closest('[data-auto-submit]');
  if (el) el.closest('form') && el.closest('form').submit();
});
