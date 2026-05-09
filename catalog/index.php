<?php
require_once dirname(__DIR__) . '/config/config.php';
$pageTitle = 'Каталог запчастей';
$db = getDB();

$catSlug  = trim($_GET['category'] ?? '');
$brandId  = (int)($_GET['brand'] ?? 0);
$priceMin = (float)($_GET['price_min'] ?? 0);
$priceMax = (float)($_GET['price_max'] ?? 0);
$sort     = in_array($_GET['sort'] ?? '', ['price_asc','price_desc','name_asc','newest']) ? $_GET['sort'] : 'newest';
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 12;

$currentCat = null;
if ($catSlug) {
    $catStmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
    $catStmt->execute([$catSlug]);
    $currentCat = $catStmt->fetch();
}

$where  = ['p.is_active = 1'];
$params = [];
if ($currentCat) {
    $subStmt = $db->prepare("SELECT id FROM categories WHERE parent_id = ? AND is_active = 1");
    $subStmt->execute([$currentCat['id']]);
    $subIds = array_column($subStmt->fetchAll(), 'id');
    $subIds[] = $currentCat['id'];
    $in = implode(',', array_fill(0, count($subIds), '?'));
    $where[] = "p.category_id IN ($in)";
    $params  = array_merge($params, $subIds);
}
if ($brandId) { $where[] = 'p.brand_id = ?'; $params[] = $brandId; }
if ($priceMin > 0) { $where[] = 'p.price >= ?'; $params[] = $priceMin; }
if ($priceMax > 0) { $where[] = 'p.price <= ?'; $params[] = $priceMax; }
$whereSQL = 'WHERE ' . implode(' AND ', $where);

$countStmt = $db->prepare("SELECT COUNT(*) FROM parts p $whereSQL");
$countStmt->execute($params);
$total      = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

$orderMap = ['price_asc'=>'p.price ASC','price_desc'=>'p.price DESC','name_asc'=>'p.name ASC','newest'=>'p.created_at DESC'];
$orderSQL = $orderMap[$sort] ?? 'p.created_at DESC';

$partsStmt = $db->prepare(
    "SELECT p.*, b.name AS brand_name, c.name AS category_name
     FROM parts p
     LEFT JOIN brands b ON b.id = p.brand_id
     LEFT JOIN categories c ON c.id = p.category_id
     $whereSQL ORDER BY $orderSQL LIMIT $perPage OFFSET $offset"
);
$partsStmt->execute($params);
$parts = $partsStmt->fetchAll();

$allCategories = getCategories();
$allBrands     = getBrands();

require_once dirname(__DIR__) . '/includes/header.php';

$productImgs = ['product1.jpg','product2.jpg','product3.jpg','product4.jpg','product5.jpg','product6.jpg','product7.jpg','product8.jpg','product9.jpg','product10.jpg','product11.jpg','product12.jpg'];
?>

<!--breadcrumb area start-->
<div class="breadcrumb_area">
  <div class="container">
    <div class="breadcrumb_content">
      <h2><?= $currentCat ? sanitize($currentCat['name']) : 'Каталог запчастей' ?></h2>
      <ul>
        <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
        <li><a href="<?= APP_URL ?>/catalog/index.php">Каталог</a></li>
        <?php if ($currentCat): ?>
          <li><?= sanitize($currentCat['name']) ?></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</div>
<!--breadcrumb area end-->

<!--shop area start-->
<div class="shop_area">
  <div class="container">
    <div class="row">

      <!--sidebar start-->
      <div class="col-lg-3 col-md-12">
        <aside class="sidebar_widget">

          <!-- Categories -->
          <div class="widget_list widget_categories">
            <h3>Категории</h3>
            <ul>
              <li><a href="<?= APP_URL ?>/catalog/index.php" class="az-filter-link <?= !$catSlug ? 'active' : '' ?>">Все категории</a></li>
              <?php foreach ($allCategories as $cat): if ($cat['parent_id'] !== null) continue; ?>
              <li>
                <a href="?category=<?= sanitize($cat['slug']) ?><?= $brandId ? '&brand='.$brandId : '' ?>"
                   class="az-filter-link <?= $catSlug === $cat['slug'] ? 'active' : '' ?>">
                  <?= sanitize($cat['name']) ?>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Brands -->
          <div class="widget_list widget_categories">
            <h3>Производители</h3>
            <ul>
              <li><a href="?<?= $catSlug ? 'category='.$catSlug : '' ?>" class="az-filter-link <?= !$brandId ? 'active' : '' ?>">Все бренды</a></li>
              <?php foreach ($allBrands as $b): ?>
              <li>
                <a href="?<?= $catSlug ? 'category='.$catSlug.'&' : '' ?>brand=<?= $b['id'] ?>"
                   class="az-filter-link <?= $brandId === (int)$b['id'] ? 'active' : '' ?>">
                  <?= sanitize($b['name']) ?> <small style="color:#999;"><?= sanitize($b['country'] ?? '') ?></small>
                </a>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>

          <!-- Price filter -->
          <div class="widget_list widget_filter">
            <h3>Цена (₽)</h3>
            <form method="get" action="">
              <?php if ($catSlug): ?><input type="hidden" name="category" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
              <?php if ($brandId): ?><input type="hidden" name="brand" value="<?= $brandId ?>"><?php endif; ?>
              <input type="hidden" name="sort" value="<?= sanitize($sort) ?>">
              <div style="display:flex;gap:8px;margin-bottom:10px;">
                <input type="number" name="price_min" placeholder="от" value="<?= $priceMin > 0 ? $priceMin : '' ?>" min="0" style="width:50%;border:1px solid #ddd;padding:6px;border-radius:2px;">
                <input type="number" name="price_max" placeholder="до" value="<?= $priceMax > 0 ? $priceMax : '' ?>" min="0" style="width:50%;border:1px solid #ddd;padding:6px;border-radius:2px;">
              </div>
              <button type="submit" style="background:#ff6600;color:#fff;border:none;padding:8px 18px;cursor:pointer;width:100%;border-radius:2px;font-size:0.85rem;">Применить</button>
              <a href="<?= APP_URL ?>/catalog/index.php" style="display:block;text-align:center;margin-top:6px;font-size:0.8rem;color:#888;">Сбросить</a>
            </form>
          </div>

        </aside>
      </div>
      <!--sidebar end-->

      <!--products area start-->
      <div class="col-lg-9 col-md-12">

        <!--toolbar-->
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:10px;">
          <p style="color:#777;font-size:0.85rem;">Найдено: <strong><?= $total ?></strong> товаров</p>
          <form method="get" action="">
            <?php if ($catSlug): ?><input type="hidden" name="category" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
            <?php if ($brandId): ?><input type="hidden" name="brand" value="<?= $brandId ?>"><?php endif; ?>
            <?php if ($priceMin): ?><input type="hidden" name="price_min" value="<?= $priceMin ?>"><?php endif; ?>
            <?php if ($priceMax): ?><input type="hidden" name="price_max" value="<?= $priceMax ?>"><?php endif; ?>
            <select name="sort" onchange="this.form.submit()" style="border:1px solid #ddd;padding:6px 12px;border-radius:2px;font-size:0.85rem;">
              <option value="newest"     <?= $sort==='newest'     ?'selected':'' ?>>Новинки</option>
              <option value="price_asc"  <?= $sort==='price_asc'  ?'selected':'' ?>>Цена ↑</option>
              <option value="price_desc" <?= $sort==='price_desc' ?'selected':'' ?>>Цена ↓</option>
              <option value="name_asc"   <?= $sort==='name_asc'   ?'selected':'' ?>>Название А-Я</option>
            </select>
          </form>
        </div>

        <?php if (empty($parts)): ?>
          <div class="az-no-results">
            <div class="az-no-results-icon">⚙</div>
            <p>По вашему запросу ничего не найдено.</p>
            <a href="<?= APP_URL ?>/catalog/index.php" style="display:inline-block;margin-top:14px;padding:8px 20px;background:#ff6600;color:#fff;border-radius:2px;text-decoration:none;font-size:0.85rem;">Сбросить фильтры</a>
          </div>
        <?php else: ?>

        <!--product list start-->
        <div class="product_list_content">
          <div class="row">
            <?php foreach ($parts as $part):
              $stock   = getStockStatus((int)$part['stock']);
              $imgFile = $productImgs[((int)$part['id'] - 1) % count($productImgs)];
            ?>
            <div class="col-lg-4 col-md-6 col-sm-6">
              <article class="single_product">
                <figure>
                  <div class="product_thumb">
                    <a class="primary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$part['id'] ?>">
                      <img src="<?= APP_URL ?>/assets/img/product/<?= $imgFile ?>" alt="<?= sanitize($part['name']) ?>">
                    </a>
                    <div class="label_product">
                      <span class="label_new"><?= sanitize($part['brand_name']) ?></span>
                    </div>
                    <?php if ($part['stock'] <= 0): ?>
                    <div class="label_product" style="top:30px;">
                      <span class="label_sale">Нет</span>
                    </div>
                    <?php endif; ?>
                  </div>
                  <div class="product_content">
                    <div class="product_content_inner">
                      <p class="manufacture_product az-part-num"><?= sanitize($part['part_number']) ?></p>
                      <h4 class="product_name">
                        <a href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$part['id'] ?>"><?= sanitize(truncate($part['name'], 50)) ?></a>
                      </h4>
                      <div class="price_box">
                        <span class="current_price"><?= formatPrice($part['price']) ?></span>
                      </div>
                    </div>
                    <div class="action_links"><ul>
                      <?php if (isLoggedIn()): ?>
                        <li class="add_to_cart"><a href="#" data-add-cart="<?= (int)$part['id'] ?>">В корзину</a></li>
                      <?php else: ?>
                        <li class="add_to_cart"><a href="<?= APP_URL ?>/auth/login.php">Войти</a></li>
                      <?php endif; ?>
                    </ul></div>
                  </div>
                </figure>
              </article>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <!--product list end-->

        <?php endif; ?>

        <!--pagination start-->
        <?php if ($totalPages > 1): ?>
        <div class="az-pagination">
          <?php
          $qp = $_GET;
          if ($page > 1): $qp['page'] = $page - 1; ?>
            <a href="?<?= http_build_query($qp) ?>" class="az-page-link">‹</a>
          <?php endif; ?>
          <?php for ($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): $qp['page'] = $p; ?>
            <a href="?<?= http_build_query($qp) ?>" class="az-page-link <?= $p==$page?'active':'' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <?php if ($page < $totalPages): $qp['page'] = $page + 1; ?>
            <a href="?<?= http_build_query($qp) ?>" class="az-page-link">›</a>
          <?php endif; ?>
        </div>
        <?php endif; ?>
        <!--pagination end-->

      </div>
      <!--products area end-->

    </div>
  </div>
</div>
<!--shop area end-->

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
