<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Главная — Профессиональные автозапчасти';

$db = getDB();

$catStmt = $db->query("SELECT * FROM categories WHERE is_active = 1 AND parent_id IS NULL ORDER BY sort_order LIMIT 7");
$featCategories = $catStmt->fetchAll();

$brandStmt = $db->query("SELECT * FROM brands WHERE is_active = 1 ORDER BY name LIMIT 8");
$featBrands = $brandStmt->fetchAll();

$partsStmt = $db->query(
    "SELECT p.*, b.name AS brand_name, c.name AS category_name
     FROM parts p
     LEFT JOIN brands b ON b.id = p.brand_id
     LEFT JOIN categories c ON c.id = p.category_id
     WHERE p.is_active = 1
     ORDER BY p.created_at DESC
     LIMIT 10"
);
$featParts = $partsStmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!--top tags area start-->
<div class="top_tags_area">
  <div class="container">
    <div class="row"><div class="col-12">
      <div class="tags_content"><ul>
        <li><span>Популярные разделы:</span></li>
        <?php foreach ($featCategories as $cat): ?>
        <li><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul></div>
    </div></div>
  </div>
</div>
<!--top tags area end-->

<!--slider area start-->
<section class="slider_section mb-80">
  <div class="slider_area slider_carousel owl-carousel">
    <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner1.jpg">
      <div class="container"><div class="row"><div class="col-12">
        <div class="slider_content">
          <h1>Оригинальные <span>Автозапчасти</span></h1>
          <p>Более 50 000 позиций в наличии. Доставка по всей России.</p>
          <a class="button" href="<?= APP_URL ?>/catalog/index.php">В каталог <i class="fa fa-angle-double-right"></i></a>
        </div>
      </div></div></div>
    </div>
    <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner2.jpg">
      <div class="container"><div class="row"><div class="col-12">
        <div class="slider_content center">
          <h1>Быстрый <span>подбор по номеру</span></h1>
          <p>Поиск по оригинальному номеру детали за секунды</p>
          <a class="button" href="<?= APP_URL ?>/search/index.php">Поиск запчасти <i class="fa fa-angle-double-right"></i></a>
        </div>
      </div></div></div>
    </div>
    <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner3.jpg">
      <div class="container"><div class="row"><div class="col-12">
        <div class="slider_content">
          <h1>Гарантия <span>качества</span></h1>
          <p>Только проверенные бренды: Bosch, NGK, Brembo, SKF и другие</p>
          <a class="button" href="<?= APP_URL ?>/catalog/index.php">Смотреть все <i class="fa fa-angle-double-right"></i></a>
        </div>
      </div></div></div>
    </div>
  </div>
</section>
<!--slider area end-->

<!--banner area start-->
<div class="banner_area mb-80">
  <div class="container">
    <div class="row"><div class="col-12">
      <div class="welcome_title">
        <h3>ДОБРО ПОЖАЛОВАТЬ В АВТОЗАПЧАСТЬ</h3>
        <h2>ПРОФЕССИОНАЛЬНЫЙ <span>СКЛАД АВТОЗАПЧАСТЕЙ</span></h2>
        <p>Оригинальные и аналоговые запчасти с гарантией качества. 12 лет на рынке.</p>
      </div>
    </div></div>
    <div class="row">
      <div class="col-lg-4 col-md-4">
        <figure class="single_banner"><div class="banner_thumb">
          <a href="<?= APP_URL ?>/catalog/index.php?category=dvigatel"><img src="<?= APP_URL ?>/assets/img/bg/banner1.jpg" alt="Двигатель"></a>
        </div></figure>
      </div>
      <div class="col-lg-4 col-md-4">
        <figure class="single_banner"><div class="banner_thumb">
          <a href="<?= APP_URL ?>/catalog/index.php?category=tormoznaya-sistema"><img src="<?= APP_URL ?>/assets/img/bg/banner2.jpg" alt="Тормоза"></a>
        </div></figure>
      </div>
      <div class="col-lg-4 col-md-4">
        <figure class="single_banner"><div class="banner_thumb">
          <a href="<?= APP_URL ?>/catalog/index.php?category=podveska"><img src="<?= APP_URL ?>/assets/img/bg/banner3.jpg" alt="Подвеска"></a>
        </div></figure>
      </div>
    </div>
  </div>
</div>
<!--banner area end-->

<!--Categories product area start-->
<div class="categories_product_area mb-80">
  <div class="container"><div class="row"><div class="col-12">
    <div class="categories_product_inner categories_column7 owl-carousel">
      <?php
      $catImages = [
        'dvigatel'=>'category2.jpg','tormoznaya-sistema'=>'category1.jpg',
        'podveska'=>'category5.jpg','elektrika'=>'category6.jpg',
        'kuzov'=>'category3.jpg','transmissiya'=>'category4.jpg','filtry'=>'category7.jpg',
      ];
      foreach ($featCategories as $cat):
        $img = $catImages[$cat['slug']] ?? 'category1.jpg';
      ?>
      <div class="single_categories_product">
        <div class="categories_product_thumb">
          <a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>">
            <img src="<?= APP_URL ?>/assets/img/s-product/<?= $img ?>" alt="<?= sanitize($cat['name']) ?>">
          </a>
        </div>
        <div class="categories_product_content">
          <h4><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a></h4>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div></div></div>
</div>
<!--Categories product area end-->

<!--product area start-->
<div class="home_section_bg">
<div class="product_area">
  <div class="container">
    <div class="row"><div class="col-12">
      <div class="section_title">
        <h2><span>Новые</span> поступления</h2>
        <p>Оригинальные и аналоговые запчасти от ведущих мировых производителей.</p>
      </div>
    </div></div>
    <div class="row">
      <div class="product_carousel product_column5 owl-carousel">
        <?php
        $productImgs = ['product1.jpg','product2.jpg','product3.jpg','product4.jpg','product5.jpg','product6.jpg','product7.jpg','product8.jpg','product9.jpg','product10.jpg'];
        foreach ($featParts as $part):
          $stock   = getStockStatus((int)$part['stock']);
          $imgFile = $productImgs[((int)$part['id'] - 1) % count($productImgs)];
        ?>
        <div class="col-lg-3">
          <div class="product_items">
            <article class="single_product">
              <figure>
                <div class="product_thumb">
                  <a class="primary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$part['id'] ?>">
                    <img src="<?= APP_URL ?>/assets/img/product/<?= $imgFile ?>" alt="<?= sanitize($part['name']) ?>">
                  </a>
                  <div class="label_product">
                    <span class="label_new"><?= sanitize($part['brand_name']) ?></span>
                  </div>
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
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<!--banner area 2 start-->
<div class="banner_area mb-80">
  <div class="container"><div class="row">
    <div class="col-lg-6 col-md-6">
      <figure class="single_banner"><div class="banner_thumb">
        <a href="<?= APP_URL ?>/catalog/index.php?category=elektrika"><img src="<?= APP_URL ?>/assets/img/bg/banner4.jpg" alt="Электрика"></a>
      </div></figure>
    </div>
    <div class="col-lg-6 col-md-6">
      <figure class="single_banner"><div class="banner_thumb">
        <a href="<?= APP_URL ?>/catalog/index.php?category=kuzov"><img src="<?= APP_URL ?>/assets/img/bg/banner5.jpg" alt="Кузов"></a>
      </div></figure>
    </div>
  </div></div>
</div>
<!--banner area 2 end-->
</div>
<!--product area end-->

<!--brand area start-->
<div class="brand_area brand_padding">
  <div class="container"><div class="col-12">
    <div class="brand_container owl-carousel">
      <?php
      $brandImgs = ['brand1.jpg','brand2.jpg','brand3.jpg','brand4.jpg','brand5.jpg','brand6.jpg','brand7.jpg','brand8.jpg'];
      $chunks = array_chunk($featBrands, 2);
      foreach ($chunks as $i => $chunk):
      ?>
      <div class="brand_list">
        <?php foreach ($chunk as $j => $brand): $imgB = $brandImgs[($i * 2 + $j) % count($brandImgs)]; ?>
        <div class="single_brand">
          <a href="<?= APP_URL ?>/catalog/index.php?brand=<?= (int)$brand['id'] ?>">
            <img src="<?= APP_URL ?>/assets/img/brand/<?= $imgB ?>" alt="<?= sanitize($brand['name']) ?>">
          </a>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div></div>
</div>
<!--brand area end-->

<!--newsletter area start-->
<div class="newsletter_area newsletter_padding">
  <div class="container">
    <div class="newsletter_inner"><div class="row">
      <div class="col-lg-4 col-md-6">
        <div class="newsletter_container">
          <h3>Мы в соцсетях</h3>
          <p>Следите за нами и получайте актуальные предложения.</p>
          <div class="footer_social"><ul>
            <li><a class="facebook" href="#"><i class="icon-facebook"></i></a></li>
            <li><a class="twitter" href="#"><i class="icon-twitter2"></i></a></li>
            <li><a class="instagram2" href="#"><i class="icon-instagram2"></i></a></li>
          </ul></div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="newsletter_container">
          <h3>Наши преимущества</h3>
          <p>✓ 50 000+ позиций в наличии<br>✓ Гарантия качества<br>✓ Доставка по России 2-5 дней<br>✓ Техническая поддержка 12 лет</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-7">
        <div class="newsletter_container col_3">
          <h3>КОНТАКТЫ</h3>
          <p>г. Москва, ул. Автомобильная, д. 1<br>
             Тел: <strong>+7 (800) 555-35-35</strong><br>
             Email: info@avtozapchast.ru<br>
             Пн–Пт 9:00–20:00, Сб–Вс 10:00–18:00</p>
        </div>
      </div>
    </div></div>
  </div>
</div>
<!--newsletter area end-->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
