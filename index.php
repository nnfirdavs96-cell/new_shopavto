<?php
require_once __DIR__ . '/config/config.php';
$db = getDB();

// Categories with optional parent
$allCats  = getCategories();
$mainCats = array_values(array_filter($allCats, fn($c) => $c['parent_id'] === null));

// Products for tabs
$bestSellers  = $db->query("SELECT p.*, b.name as brand_name FROM parts p LEFT JOIN brands b ON p.brand_id=b.id ORDER BY p.id DESC LIMIT 10")->fetchAll();
$featured     = $db->query("SELECT p.*, b.name as brand_name FROM parts p LEFT JOIN brands b ON p.brand_id=b.id ORDER BY p.price DESC LIMIT 10")->fetchAll();
$newArrivals  = $db->query("SELECT p.*, b.name as brand_name FROM parts p LEFT JOIN brands b ON p.brand_id=b.id ORDER BY p.created_at DESC LIMIT 10")->fetchAll();
$onSale       = $db->query("SELECT p.*, b.name as brand_name FROM parts p LEFT JOIN brands b ON p.brand_id=b.id ORDER BY RAND() LIMIT 8")->fetchAll();
$brands       = getBrands();
$pageTitle    = 'Главная';

// Category image mapping
$catImageMap = [
  'dvigatel' => 'category1.jpg',
  'tormoznaya-sistema' => 'category2.jpg',
  'podveska' => 'category3.jpg',
  'elektrika' => 'category4.jpg',
  'kuzov' => 'category5.jpg',
  'transmissiya' => 'category6.jpg',
];
function catImg($slug, $map) { return $map[$slug] ?? 'category7.jpg'; }

/**
 * Render a single product card matching the original Mazlay structure.
 */
function renderProductCard(array $p): void {
  $primary   = (($p['id'] - 1) % 13) + 1;
  $secondary = ($p['id'] % 13) + 1;
  $url       = APP_URL . '/catalog/part.php?id=' . (int)$p['id'];
  ?>
  <article class="single_product">
    <figure>
      <div class="product_thumb">
        <a class="primary_img" href="<?= $url ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $primary ?>.jpg" alt=""></a>
        <a class="secondary_img" href="<?= $url ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $secondary ?>.jpg" alt=""></a>
        <div class="action_links">
          <ul>
            <li class="quick_button"><a href="#" data-add-cart="<?= (int)$p['id'] ?>" title="В корзину"><span class="ion-android-cart"></span></a></li>
            <li class="wishlist"><a href="#" title="В избранное"><span class="ion-ios-heart-outline"></span></a></li>
            <li class="compare"><a href="<?= $url ?>" title="Подробнее"><span class="ion-eye"></span></a></li>
          </ul>
        </div>
      </div>
      <figcaption class="product_content">
        <h4 class="product_name"><a href="<?= $url ?>"><?= sanitize($p['name']) ?></a></h4>
        <div class="price_box">
          <span class="current_price"><?= formatPrice($p['price']) ?></span>
        </div>
      </figcaption>
    </figure>
  </article>
  <?php
}

require_once __DIR__ . '/includes/header.php';
?>

<!--top tags area start-->
<div class="top_tags_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="tags_content">
                    <ul>
                        <li><span>Популярное:</span></li>
                        <?php foreach (array_slice($mainCats, 0, 8) as $tag): ?>
                            <li><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($tag['slug']) ?>"><?= sanitize($tag['name']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<!--top tags area end-->

<!--slider area start-->
<section class="slider_section mb-80">
    <div class="slider_area slider_carousel owl-carousel">
        <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner1.jpg">
           <div class="container">
               <div class="row">
                   <div class="col-12">
                       <div class="slider_content">
                            <h1>Большая распродажа <span>автозапчастей</span></h1>
                            <p>Эксклюзивное предложение -30% всю неделю</p>
                            <a class="button" href="<?= APP_URL ?>/catalog/index.php">В каталог <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        </div>
                   </div>
               </div>
           </div>
        </div>
        <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner2.jpg">
            <div class="container">
               <div class="row">
                   <div class="col-12">
                       <div class="slider_content center">
                            <h1>Запчасти <span>для всех марок автомобилей</span></h1>
                            <p>Оригинальные детали и качественные аналоги</p>
                            <a class="button" href="<?= APP_URL ?>/catalog/index.php">В каталог <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        </div>
                   </div>
               </div>
           </div>
        </div>
        <div class="single_slider d-flex align-items-center" data-bgimg="<?= APP_URL ?>/assets/img/bg/banner3.jpg">
            <div class="container">
               <div class="row">
                   <div class="col-12">
                       <div class="slider_content">
                            <h1>Профессиональный <span>склад автозапчастей</span></h1>
                            <p>Доставка по всей стране в кратчайшие сроки</p>
                            <a class="button" href="<?= APP_URL ?>/catalog/index.php">В каталог <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>
                        </div>
                   </div>
               </div>
           </div>
        </div>
    </div>
</section>
<!--slider area end-->

<!--banner area start-->
<div class="banner_area mb-80">
    <div class="container">
       <div class="row">
           <div class="col-12">
               <div class="welcome_title">
                   <h3>ДОБРО ПОЖАЛОВАТЬ В АВТОЗАПЧАСТЬ</h3>
                   <h2>ПРОФЕССИОНАЛЬНЫЙ <span>СКЛАД АВТОЗАПЧАСТЕЙ</span></h2>
                   <p>Оригинальные детали. Качественные аналоги. Быстрая доставка.</p>
               </div>
           </div>
       </div>
        <div class="row">
            <div class="col-lg-4 col-md-4">
                <figure class="single_banner">
                    <div class="banner_thumb">
                        <a href="<?= APP_URL ?>/catalog/index.php"><img src="<?= APP_URL ?>/assets/img/bg/banner4.jpg" alt=""></a>
                    </div>
                </figure>
            </div>
            <div class="col-lg-4 col-md-4">
                <figure class="single_banner">
                    <div class="banner_thumb">
                        <a href="<?= APP_URL ?>/catalog/index.php"><img src="<?= APP_URL ?>/assets/img/bg/banner5.jpg" alt=""></a>
                    </div>
                </figure>
            </div>
            <div class="col-lg-4 col-md-4">
                <figure class="single_banner">
                    <div class="banner_thumb">
                        <a href="<?= APP_URL ?>/catalog/index.php"><img src="<?= APP_URL ?>/assets/img/bg/banner23.jpg" alt=""></a>
                    </div>
                </figure>
            </div>
        </div>
    </div>
</div>
<!--banner area end-->

<!--Categories product area start-->
<div class="categories_product_area mb-80">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="categories_product_inner categories_column7 owl-carousel">
                    <?php foreach ($mainCats as $cat): $catImage = catImg($cat['slug'], $catImageMap); ?>
                        <div class="single_categories_product">
                            <div class="categories_product_thumb">
                                <a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>"><img src="<?= APP_URL ?>/assets/img/s-product/<?= $catImage ?>" alt=""></a>
                            </div>
                            <div class="categories_product_content">
                                <h3><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a></h3>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!--Categories product area end-->

<!--home section bg area start-->
<div class="home_section_bg">
    <!--product area start-->
    <div class="product_area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                       <h2><span>Наши</span> Товары</h2>
                        <p>Широкий ассортимент автозапчастей для иномарок и отечественных автомобилей. Подберите нужную деталь с помощью удобного каталога.</p>
                    </div>
                    <div class="product_tab_btn">
                        <ul class="nav" role="tablist" id="nav-tab">
                            <li>
                                <a class="active" data-bs-toggle="tab" href="#Sellers" role="tab" aria-controls="Sellers" aria-selected="true">
                                    Хиты продаж
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="tab" href="#Featured" role="tab" aria-controls="Featured" aria-selected="false">
                                    Рекомендуемые
                                </a>
                            </li>
                            <li>
                                <a data-bs-toggle="tab" href="#Arrivals" role="tab" aria-controls="Arrivals" aria-selected="false">
                                   Новинки
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="Sellers" role="tabpanel">
                    <div class="row">
                        <div class="product_carousel product_column5 owl-carousel">
                            <?php foreach ($bestSellers as $p): ?>
                                <div class="col-lg-3">
                                    <div class="product_items">
                                        <?php renderProductCard($p); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="Featured" role="tabpanel">
                    <div class="row">
                        <div class="product_carousel product_column5 owl-carousel">
                            <?php foreach ($featured as $p): ?>
                                <div class="col-lg-3">
                                    <div class="product_items">
                                        <?php renderProductCard($p); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="Arrivals" role="tabpanel">
                    <div class="row">
                        <div class="product_carousel product_column5 owl-carousel">
                            <?php foreach ($newArrivals as $p): ?>
                                <div class="col-lg-3">
                                    <div class="product_items">
                                        <?php renderProductCard($p); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--product area end-->

    <!--banner area start-->
    <div class="banner_area mb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <figure class="single_banner">
                        <div class="banner_thumb">
                            <a href="<?= APP_URL ?>/catalog/"><img src="<?= APP_URL ?>/assets/img/bg/banner4.jpg" alt=""></a>
                        </div>
                    </figure>
                </div>
                <div class="col-lg-6 col-md-6">
                    <figure class="single_banner">
                        <div class="banner_thumb">
                            <a href="<?= APP_URL ?>/catalog/"><img src="<?= APP_URL ?>/assets/img/bg/banner5.jpg" alt=""></a>
                        </div>
                    </figure>
                </div>
            </div>
        </div>
    </div>
    <!--banner area end-->

    <!--product area start-->
    <div class="product_area">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title title_style2">
                       <div class="title_content">
                           <h2>Специальные предложения <span>скидки</span></h2>
                            <p>Лучшие цены на популярные автозапчасти этого месяца</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="product_container">
               <div class="row">
                    <div class="col-12">
                        <div class="product_style_right">
                            <div class="row">
                                <div class="product_carousel product_column3 owl-carousel">
                                    <?php foreach ($onSale as $p): ?>
                                        <div class="col-lg-3">
                                            <?php renderProductCard($p); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    </div>
    <!--product area end-->
</div>
<!--home section bg area end-->

<!--brand area start-->
<div class="brand_area">
    <div class="container">
        <div class="col-12">
            <div class="brand_container owl-carousel ">
                <?php
                $brandChunks = array_chunk($brands, 2);
                $brandIdx = 0;
                foreach ($brandChunks as $chunk):
                ?>
                    <div class="brand_list">
                        <?php foreach ($chunk as $br): $imgN = ($brandIdx % 8) + 1; $brandIdx++; ?>
                            <div class="single_brand">
                                <a href="<?= APP_URL ?>/catalog/index.php?brand=<?= sanitize($br['slug'] ?? (string)$br['id']) ?>"><img src="<?= APP_URL ?>/assets/img/brand/brand<?= $imgN ?>.jpg" alt="<?= sanitize($br['name']) ?>"></a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<!--brand area end-->

<!--newsletter area start-->
<div class="newsletter_area">
    <div class="container">
        <div class="newsletter_inner">
            <div class="row">
                <div class="col-lg-4 col-md-6">
                    <div class="newsletter_container">
                        <h3>Мы в соцсетях</h3>
                        <p>Следите за новинками и акциями нашего магазина в социальных сетях.</p>
                        <div class="footer_social">
                           <ul>
                               <li><a class="facebook" href="#"><i class="icon-facebook"></i></a></li>
                               <li><a class="twitter" href="#"><i class="icon-twitter2"></i></a></li>
                               <li><a class="rss" href="#"><i class="icon-rss"></i></a></li>
                               <li><a class="youtube" href="#"><i class="icon-youtube"></i></a></li>
                               <li><a class="google" href="#"><i class="icon-google"></i></a></li>
                               <li><a class="instagram2" href="#"><i class="icon-instagram2"></i></a></li>
                           </ul>
                       </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="newsletter_container">
                        <h3>Подписка на новости</h3>
                        <p>Подпишитесь и получайте свежие предложения и купоны на скидку каждую неделю.</p>
                        <div class="subscribe_form">
                            <form id="mc-form" class="mc-form footer-newsletter" >
                                <input id="mc-email" type="email" autocomplete="off" placeholder="Введите ваш e-mail..." />
                                <button id="mc-submit">Подписаться</button>
                            </form>
                            <div class="mailchimp-alerts text-centre">
                                <div class="mailchimp-submitting"></div>
                                <div class="mailchimp-success"></div>
                                <div class="mailchimp-error"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-7">
                    <div class="newsletter_container col_3">
                        <h3>СВЯЖИТЕСЬ С НАМИ</h3>
                        <p>Наши специалисты помогут подобрать запчасти для вашего автомобиля.</p>
                        <div class="app_img">
                           <ul>
                               <li><a href="tel:+78001234567"><strong style="color:#fff;font-size:20px;">+7 (800) 123-45-67</strong></a></li>
                           </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--newsletter area end-->

<?php require_once __DIR__ . '/includes/footer.php'; ?>
