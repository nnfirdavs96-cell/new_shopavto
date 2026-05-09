<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$db   = getDB();
$uid  = $_SESSION['user_id'];
$csrf = generateCsrfToken();

// Checkout POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        flashMessage('danger', 'Ошибка безопасности.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $address = trim($_POST['shipping_address'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    if (empty($address)) {
        flashMessage('danger', 'Укажите адрес доставки.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $items = $db->prepare("SELECT c.*, p.price, p.stock FROM cart c JOIN parts p ON c.part_id=p.id WHERE c.user_id=?");
    $items->execute([$uid]);
    $cartItems = $items->fetchAll();
    if (empty($cartItems)) {
        flashMessage('danger', 'Корзина пуста.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $total = array_sum(array_map(fn($r) => $r['price'] * $r['quantity'], $cartItems));
    try {
        $db->beginTransaction();
        $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, notes, status) VALUES (?,?,?,?,'pending')")
           ->execute([$uid, $total, $address, $notes]);
        $orderId = $db->lastInsertId();
        $ins = $db->prepare("INSERT INTO order_items (order_id, part_id, quantity, unit_price) VALUES (?,?,?,?)");
        foreach ($cartItems as $it) {
            $ins->execute([$orderId, $it['part_id'], $it['quantity'], $it['price']]);
            $db->prepare("UPDATE parts SET stock=stock-? WHERE id=?")->execute([$it['quantity'], $it['part_id']]);
        }
        $db->prepare("DELETE FROM cart WHERE user_id=?")->execute([$uid]);
        $db->commit();
        flashMessage('success', 'Заказ #' . $orderId . ' оформлен!');
        redirect(APP_URL . '/buyer/orders.php?id=' . $orderId);
    } catch (Exception $e) {
        $db->rollBack();
        flashMessage('danger', 'Ошибка оформления заказа.');
        redirect(APP_URL . '/buyer/cart.php');
    }
}

$stmt = $db->prepare("SELECT c.id, c.quantity, p.id as part_id, p.name, p.part_number, p.price, p.stock FROM cart c JOIN parts p ON c.part_id=p.id WHERE c.user_id=? ORDER BY c.id");
$stmt->execute([$uid]);
$cartItems = $stmt->fetchAll();
$total = array_sum(array_map(fn($r) => $r['price'] * $r['quantity'], $cartItems));

$pageTitle = 'Корзина';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!--breadcrumbs area start-->
<div class="breadcrumbs_area">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb_content">
          <ul>
            <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
            <li>Корзина</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<div class="cart_page_bg section_padding">
  <div class="container">
    <div class="shopping_cart_area">

      <?php if (empty($cartItems)): ?>
        <div class="az-no-results">
          <div class="az-no-results-icon">🛒</div>
          <h3>Корзина пуста</h3>
          <p style="margin:12px 0 24px;">Добавьте товары из каталога</p>
          <a href="<?= APP_URL ?>/catalog/index.php" class="btn btn-wh-2 btn-hover-dark">Перейти в каталог</a>
        </div>
      <?php else: ?>

        <div class="table_desc">
          <div class="cart_page">
            <table>
              <thead>
                <tr>
                  <th class="product_remove">Удалить</th>
                  <th class="product_thumb">Фото</th>
                  <th class="product_name">Товар</th>
                  <th class="product-price">Цена</th>
                  <th class="product_quantity">Количество</th>
                  <th class="product_total">Сумма</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cartItems as $item):
                  $imgNum = ($item['part_id'] - 1) % 13 + 1;
                  $imgFile = $imgNum <= 5 ? "productbig{$imgNum}.jpg" : "product{$imgNum}.jpg";
                ?>
                <tr data-cart-row="<?= $item['id'] ?>">
                  <td class="product_remove">
                    <button data-cart-remove="<?= $item['id'] ?>" style="background:none;border:none;font-size:1.1rem;cursor:pointer;color:#999;">&times;</button>
                  </td>
                  <td class="product_thumb">
                    <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['part_id'] ?>">
                      <img src="<?= APP_URL ?>/assets/img/product/<?= $imgFile ?>" alt="<?= sanitize($item['name']) ?>" style="width:70px;height:70px;object-fit:cover;">
                    </a>
                  </td>
                  <td class="product_name">
                    <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['part_id'] ?>">
                      <?= sanitize($item['name']) ?>
                    </a>
                    <div class="az-part-num"><?= sanitize($item['part_number']) ?></div>
                  </td>
                  <td class="product-price"><?= formatPrice($item['price']) ?></td>
                  <td class="product_quantity">
                    <div class="az-qty-wrap">
                      <button class="az-qty-btn" data-qty-minus="<?= $item['id'] ?>">−</button>
                      <input class="az-qty-input" type="number" min="1" max="<?= $item['stock'] ?>"
                             value="<?= $item['quantity'] ?>" data-qty-input="<?= $item['id'] ?>">
                      <button class="az-qty-btn" data-qty-plus="<?= $item['id'] ?>">+</button>
                    </div>
                  </td>
                  <td class="product_total" data-row-subtotal="<?= $item['id'] ?>">
                    <?= formatPrice($item['price'] * $item['quantity']) ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="coupon_area">
          <div class="row">

            <!-- Checkout form -->
            <div class="col-lg-6 col-md-6">
              <div class="coupon_code left">
                <h3>Оформить заказ</h3>
                <div class="coupon_inner">
                  <form method="post" action="">
                    <input type="hidden" name="csrf_token" value="<?= sanitize($csrf) ?>">
                    <input type="hidden" name="checkout" value="1">
                    <p style="margin-bottom:10px; font-size:0.875rem; color:#555;">Адрес доставки:</p>
                    <textarea name="shipping_address" rows="3" placeholder="Город, улица, дом, квартира"
                              style="width:100%; border:1px solid #ddd; padding:10px; border-radius:2px; font-size:0.875rem; resize:vertical; margin-bottom:10px;"
                              required></textarea>
                    <textarea name="notes" rows="2" placeholder="Комментарий к заказу (необязательно)"
                              style="width:100%; border:1px solid #ddd; padding:10px; border-radius:2px; font-size:0.875rem; resize:vertical; margin-bottom:10px;"></textarea>
                    <button type="submit" class="btn btn-wh-2 btn-hover-dark" style="width:100%;">Оформить заказ</button>
                  </form>
                </div>
              </div>
            </div>

            <!-- Cart totals -->
            <div class="col-lg-6 col-md-6">
              <div class="coupon_code right">
                <h3>Итого по корзине</h3>
                <div class="coupon_inner">
                  <?php foreach ($cartItems as $item): ?>
                  <div class="cart_subtotal">
                    <p style="font-size:0.85rem;"><?= sanitize(truncate($item['name'], 30)) ?> ×<?= $item['quantity'] ?></p>
                    <p class="cart_amount"><?= formatPrice($item['price'] * $item['quantity']) ?></p>
                  </div>
                  <?php endforeach; ?>
                  <div class="cart_subtotal" style="border-top:2px solid #eee; margin-top:8px; padding-top:10px;">
                    <p style="font-weight:700;">Доставка</p>
                    <p class="cart_amount" style="color:#555;">Бесплатно</p>
                  </div>
                  <div class="cart_subtotal" style="border-top:1px solid #eee;">
                    <p style="font-weight:700; font-size:1rem;">Итого</p>
                    <p class="cart_amount" id="cart-total"><?= formatPrice($total) ?></p>
                  </div>
                  <div class="checkout_btn">
                    <a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
