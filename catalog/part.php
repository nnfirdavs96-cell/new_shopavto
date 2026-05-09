<?php
require_once dirname(__DIR__) . '/config/config.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { flashMessage('danger','Товар не найден.'); redirect(APP_URL.'/catalog/index.php'); }

$db   = getDB();
$stmt = $db->prepare(
    "SELECT p.*, b.name AS brand_name, b.country AS brand_country, c.name AS category_name, c.slug AS category_slug
     FROM parts p
     LEFT JOIN brands b ON b.id = p.brand_id
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.id = ? AND p.is_active = 1"
);
$stmt->execute([$id]);
$part = $stmt->fetch();
if (!$part) { flashMessage('danger','Товар не найден или снят с продажи.'); redirect(APP_URL.'/catalog/index.php'); }

$relStmt = $db->prepare(
    "SELECT p.*, b.name AS brand_name FROM parts p LEFT JOIN brands b ON b.id = p.brand_id
     WHERE p.is_active = 1 AND p.id != ? AND (p.category_id = ? OR p.brand_id = ?) LIMIT 5"
);
$relStmt->execute([$id, $part['category_id'], $part['brand_id']]);
$related = $relStmt->fetchAll();

$stock     = getStockStatus((int)$part['stock']);
$pageTitle = sanitize($part['name']);

require_once dirname(__DIR__) . '/includes/header.php';

$productBigImgs = ['productbig1.jpg','productbig2.jpg','productbig3.jpg','productbig4.jpg','productbig5.jpg'];
$bigImg = $productBigImgs[((int)$part['id'] - 1) % count($productBigImgs)];
$productImgs = ['product1.jpg','product2.jpg','product3.jpg','product4.jpg','product5.jpg','product6.jpg'];
$smImg = $productImgs[((int)$part['id'] - 1) % count($productImgs)];
?>

<!--breadcrumb area start-->
<div class="breadcrumb_area">
  <div class="container">
    <div class="breadcrumb_content">
      <h2><?= sanitize(truncate($part['name'], 50)) ?></h2>
      <ul>
        <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
        <li><a href="<?= APP_URL ?>/catalog/index.php">Каталог</a></li>
        <li><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($part['category_slug']) ?>"><?= sanitize($part['category_name']) ?></a></li>
        <li><?= sanitize($part['part_number']) ?></li>
      </ul>
    </div>
  </div>
</div>
<!--breadcrumb area end-->

<!--product details area start-->
<div class="product_details_area">
  <div class="container">
    <div class="row">

      <!--Product image-->
      <div class="col-lg-5 col-md-5">
        <div class="product_details">
          <div class="product-details-large">
            <div class="single_product_details">
              <img src="<?= APP_URL ?>/assets/img/product/<?= $bigImg ?>" alt="<?= sanitize($part['name']) ?>">
            </div>
          </div>
          <div class="details_small_images">
            <img src="<?= APP_URL ?>/assets/img/product/<?= $smImg ?>" alt="thumb" style="height:70px;width:70px;object-fit:cover;cursor:pointer;border:1px solid #eee;margin:2px;">
          </div>
        </div>
      </div>

      <!--Product info-->
      <div class="col-lg-7 col-md-7">
        <div class="product_details_tab">
          <p class="manufacture_product">
            <a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($part['category_slug']) ?>"><?= sanitize($part['category_name']) ?></a>
          </p>
          <h2><?= sanitize($part['name']) ?></h2>

          <div class="product_details_para" style="margin:10px 0;">
            <span style="font-family:monospace;color:#ff6600;font-weight:600;font-size:0.9rem;">
              Арт.: <?= sanitize($part['part_number']) ?>
            </span>
            &nbsp;&nbsp;
            <span class="az-badge az-badge-<?= $stock['class'] ?>"><?= $stock['label'] ?></span>
          </div>

          <div class="price_box" style="margin:16px 0;">
            <span class="current_price" style="font-size:1.8rem;"><?= formatPrice($part['price']) ?></span>
            <?php if ($part['stock'] > 0): ?>
              <small style="color:#777;margin-left:10px;"><?= (int)$part['stock'] ?> шт. в наличии</small>
            <?php endif; ?>
          </div>

          <?php if (isLoggedIn() && $part['stock'] > 0): ?>
          <div style="display:flex;align-items:center;gap:10px;margin:20px 0;">
            <div class="az-qty-wrap">
              <button class="az-qty-btn" type="button" data-qty-minus><span>-</span></button>
              <input type="number" id="qty-input" class="az-qty-input" value="1" min="1" max="<?= min((int)$part['stock'],99) ?>" data-qty-input>
              <button class="az-qty-btn" type="button" data-qty-plus><span>+</span></button>
            </div>
            <button class="button" data-add-cart="<?= (int)$part['id'] ?>" id="add-cart-btn" style="margin:0;">
              В корзину
            </button>
          </div>
          <?php elseif (!isLoggedIn()): ?>
          <a href="<?= APP_URL ?>/auth/login.php" class="button" style="display:inline-block;margin:20px 0;">Войдите для заказа</a>
          <?php else: ?>
          <p style="color:#e74c3c;margin:20px 0;">Товар временно отсутствует на складе</p>
          <?php endif; ?>

          <!-- Specs table -->
          <div class="product_d_table">
            <table>
              <tbody>
                <tr><td>Номер детали</td><td style="color:#ff6600;font-family:monospace;"><?= sanitize($part['part_number']) ?></td></tr>
                <tr><td>Производитель</td><td><?= sanitize($part['brand_name']) ?></td></tr>
                <tr><td>Страна</td><td><?= sanitize($part['brand_country'] ?? '—') ?></td></tr>
                <tr><td>Категория</td><td><?= sanitize($part['category_name']) ?></td></tr>
                <?php if ($part['weight']): ?><tr><td>Вес</td><td><?= sanitize($part['weight']) ?> кг</td></tr><?php endif; ?>
                <?php if ($part['dimensions']): ?><tr><td>Размеры</td><td><?= sanitize($part['dimensions']) ?></td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div><!-- /.row -->

    <!-- Description -->
    <?php if ($part['description']): ?>
    <div class="row" style="margin-top:40px;">
      <div class="col-12">
        <div class="product_d_info">
          <ul class="nav product_info_button" role="tablist">
            <li><a class="active" data-bs-toggle="tab" href="#desc">Описание</a></li>
          </ul>
          <div class="tab-content product_info_tab">
            <div id="desc" class="tab-pane fade show active" role="tabpanel">
              <p><?= nl2br(sanitize($part['description'])) ?></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Related products -->
    <?php if (!empty($related)): ?>
    <div class="row" style="margin-top:48px;">
      <div class="col-12">
        <div class="section_title" style="margin-bottom:20px;">
          <h2><span>Похожие</span> товары</h2>
        </div>
      </div>
      <?php
      $productImgs = ['product1.jpg','product2.jpg','product3.jpg','product4.jpg','product5.jpg','product6.jpg'];
      foreach ($related as $rel):
        $relStock = getStockStatus((int)$rel['stock']);
        $rImg = $productImgs[((int)$rel['id'] - 1) % count($productImgs)];
      ?>
      <div class="col-lg-3 col-md-4 col-sm-6" style="margin-bottom:24px;">
        <article class="single_product">
          <figure>
            <div class="product_thumb">
              <a class="primary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$rel['id'] ?>">
                <img src="<?= APP_URL ?>/assets/img/product/<?= $rImg ?>" alt="<?= sanitize($rel['name']) ?>">
              </a>
              <div class="label_product"><span class="label_new"><?= sanitize($rel['brand_name']) ?></span></div>
            </div>
            <div class="product_content">
              <div class="product_content_inner">
                <p class="manufacture_product az-part-num"><?= sanitize($rel['part_number']) ?></p>
                <h4 class="product_name">
                  <a href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$rel['id'] ?>"><?= sanitize(truncate($rel['name'],45)) ?></a>
                </h4>
                <div class="price_box"><span class="current_price"><?= formatPrice($rel['price']) ?></span></div>
              </div>
              <div class="action_links"><ul>
                <?php if (isLoggedIn()): ?>
                  <li class="add_to_cart"><a href="#" data-add-cart="<?= (int)$rel['id'] ?>">В корзину</a></li>
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
    <?php endif; ?>

  </div>
</div>
<!--product details area end-->

<script>
document.getElementById('qty-input')?.addEventListener('change', function() {
  const btn = document.getElementById('add-cart-btn');
  if (btn) btn.dataset.qty = this.value;
});
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
