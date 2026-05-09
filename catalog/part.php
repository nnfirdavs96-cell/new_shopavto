<?php
require_once dirname(__DIR__) . '/config/config.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    require_once dirname(__DIR__) . '/404.php';
    exit;
}

$db = getDB();
$stmt = $db->prepare("SELECT p.*, b.name as brand_name, b.slug as brand_slug, c.name as cat_name, c.slug as cat_slug
                       FROM parts p
                       LEFT JOIN brands b ON p.brand_id=b.id
                       LEFT JOIN categories c ON p.category_id=c.id
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) {
    http_response_code(404);
    require_once dirname(__DIR__) . '/404.php';
    exit;
}

// Related products from same category
$rel = $db->prepare("SELECT p.* FROM parts p WHERE p.category_id = ? AND p.id != ? ORDER BY RAND() LIMIT 4");
$rel->execute([$product['category_id'], $id]);
$related = $rel->fetchAll();

$pageTitle = $product['name'];
$stock = getStockStatus((int)$product['stock']);

// Image gallery: pick four thumbnails based on product id
$mainNum    = (($id - 1) % 13) + 1;
$thumbNums  = [];
for ($i = 0; $i < 4; $i++) {
    $thumbNums[] = (($id - 1 + $i) % 13) + 1;
}
$bigImg = function ($n) {
    return $n <= 5 ? "productbig{$n}.jpg" : "product{$n}.jpg";
};
$mainBig = $bigImg($mainNum);

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
                            <li><a href="<?= APP_URL ?>/catalog/index.php">Каталог</a></li>
                            <?php if (!empty($product['cat_slug'])): ?>
                                <li><a href="<?= APP_URL ?>/catalog/index.php?category=<?= urlencode($product['cat_slug']) ?>"><?= sanitize($product['cat_name']) ?></a></li>
                            <?php endif; ?>
                            <li><?= sanitize($product['name']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <div class="product_page_bg">
        <div class="container">
            <!--product details start-->
            <div class="product_details">
                <div class="row">
                    <div class="col-lg-5 col-md-6">
                        <div class="product-details-tab">
                            <div id="img-1" class="zoomWrapper single-zoom">
                                <a href="#">
                                    <img id="zoom1" src="<?= APP_URL ?>/assets/img/product/<?= $mainBig ?>" data-zoom-image="<?= APP_URL ?>/assets/img/product/<?= $mainBig ?>" alt="<?= sanitize($product['name']) ?>">
                                </a>
                            </div>
                            <div class="single-zoom-thumb">
                                <ul class="s-tab-zoom owl-carousel single-product-active" id="gallery_01">
                                    <?php foreach ($thumbNums as $i => $tn): $tImg = $bigImg($tn); ?>
                                        <li>
                                            <a href="#" class="elevatezoom-gallery<?= $i === 0 ? ' active' : '' ?>" data-update="" data-image="<?= APP_URL ?>/assets/img/product/<?= $tImg ?>" data-zoom-image="<?= APP_URL ?>/assets/img/product/<?= $tImg ?>">
                                                <img src="<?= APP_URL ?>/assets/img/product/<?= $tImg ?>" alt="thumb-<?= $i + 1 ?>"/>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 col-md-6">
                        <div class="product_d_right">
                            <form method="post" action="<?= APP_URL ?>/api/cart.php">
                                <input type="hidden" name="csrf_token" value="<?= sanitize(generateCsrfToken()) ?>">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="part_id" value="<?= $id ?>">

                                <h3><a href="#"><?= sanitize($product['name']) ?></a></h3>
                                <div class="product_rating">
                                    <ul>
                                        <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                        <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                        <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                        <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                        <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                        <li class="review"><a href="#">(пока нет отзывов)</a></li>
                                    </ul>
                                </div>
                                <div class="price_box">
                                    <span class="current_price"><?= formatPrice($product['price']) ?></span>
                                    <span class="az-badge az-badge-<?= sanitize($stock['class']) ?>" style="margin-left:14px;"><?= sanitize($stock['label']) ?></span>
                                </div>
                                <div class="product_desc">
                                    <p><?= nl2br(sanitize(mb_substr((string)($product['description'] ?? ''), 0, 320))) ?><?= mb_strlen((string)($product['description'] ?? '')) > 320 ? '…' : '' ?></p>
                                </div>

                                <?php if (!empty($product['part_number'])): ?>
                                <div class="product_meta" style="margin-bottom:14px;">
                                    <span>Артикул: <strong><?= sanitize($product['part_number']) ?></strong></span>
                                </div>
                                <?php endif; ?>

                                <div class="product_variant quantity">
                                    <label>Количество:</label>
                                    <input class="cart-plus-minus-box" name="quantity" min="1" max="<?= max(1, (int)$product['stock']) ?>" value="1" type="number" <?= (int)$product['stock'] <= 0 ? 'disabled' : '' ?>>
                                    <button class="button" type="submit" data-add-cart="<?= $id ?>" <?= (int)$product['stock'] <= 0 ? 'disabled' : '' ?>>В корзину</button>
                                </div>
                                <div class=" product_d_action">
                                    <ul>
                                        <li><a href="#" title="В избранное">+ В избранное</a></li>
                                        <li><a href="#" title="Сравнить">+ Сравнить</a></li>
                                    </ul>
                                </div>
                                <div class="product_meta">
                                    <?php if (!empty($product['cat_name'])): ?>
                                        <span>Категория: <a href="<?= APP_URL ?>/catalog/index.php?category=<?= urlencode($product['cat_slug']) ?>"><?= sanitize($product['cat_name']) ?></a></span>
                                    <?php endif; ?>
                                    <?php if (!empty($product['brand_name'])): ?>
                                        <span style="margin-left:14px;">Бренд: <a href="<?= APP_URL ?>/catalog/index.php?brand=<?= urlencode($product['brand_slug']) ?>"><?= sanitize($product['brand_name']) ?></a></span>
                                    <?php endif; ?>
                                </div>
                            </form>
                            <div class="priduct_social">
                                <ul>
                                    <li><a class="facebook" href="#" title="facebook"><i class="fa fa-facebook"></i> Поделиться</a></li>
                                    <li><a class="twitter" href="#" title="twitter"><i class="fa fa-twitter"></i> Твит</a></li>
                                    <li><a class="pinterest" href="#" title="pinterest"><i class="fa fa-pinterest"></i> Сохранить</a></li>
                                    <li><a class="google-plus" href="#" title="google +"><i class="fa fa-google-plus"></i> Поделиться</a></li>
                                    <li><a class="linkedin" href="#" title="linkedin"><i class="fa fa-linkedin"></i> LinkedIn</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--product details end-->

            <!--product info start-->
            <div class="product_d_info">
                <div class="row">
                    <div class="col-12">
                        <div class="product_d_inner">
                            <div class="product_info_button">
                                <ul class="nav" role="tablist" id="nav-tab">
                                    <li>
                                        <a class="active" data-bs-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="true">Описание</a>
                                    </li>
                                    <li>
                                        <a data-bs-toggle="tab" href="#sheet" role="tab" aria-controls="sheet" aria-selected="false">Характеристики</a>
                                    </li>
                                    <li>
                                        <a data-bs-toggle="tab" href="#reviews" role="tab" aria-controls="reviews" aria-selected="false">Отзывы (0)</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="info" role="tabpanel">
                                    <div class="product_info_content">
                                        <?php if (!empty($product['description'])): ?>
                                            <p><?= nl2br(sanitize($product['description'])) ?></p>
                                        <?php else: ?>
                                            <p>Подробное описание для товара "<?= sanitize($product['name']) ?>" будет добавлено в ближайшее время. Свяжитесь с нашими консультантами для получения дополнительной информации о совместимости и наличии.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="sheet" role="tabpanel">
                                    <div class="product_d_table">
                                        <table>
                                            <tbody>
                                                <?php if (!empty($product['part_number'])): ?>
                                                <tr>
                                                    <td class="first_child">Артикул</td>
                                                    <td><?= sanitize($product['part_number']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($product['brand_name'])): ?>
                                                <tr>
                                                    <td class="first_child">Бренд</td>
                                                    <td><?= sanitize($product['brand_name']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($product['cat_name'])): ?>
                                                <tr>
                                                    <td class="first_child">Категория</td>
                                                    <td><?= sanitize($product['cat_name']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($product['country'])): ?>
                                                <tr>
                                                    <td class="first_child">Страна производства</td>
                                                    <td><?= sanitize($product['country']) ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <?php if (!empty($product['weight'])): ?>
                                                <tr>
                                                    <td class="first_child">Вес</td>
                                                    <td><?= sanitize($product['weight']) ?> кг</td>
                                                </tr>
                                                <?php endif; ?>
                                                <tr>
                                                    <td class="first_child">Наличие на складе</td>
                                                    <td><?= (int)$product['stock'] ?> шт.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="reviews" role="tabpanel">
                                    <div class="reviews_wrapper">
                                        <h2>Отзывы о товаре "<?= sanitize($product['name']) ?>"</h2>
                                        <p>Пока нет отзывов. Будьте первым, кто оставит отзыв!</p>
                                        <div class="comment_title">
                                            <h2>Добавить отзыв</h2>
                                            <p>Ваш email не будет опубликован. Обязательные поля помечены.</p>
                                        </div>
                                        <div class="product_rating mb-10">
                                            <h3>Ваша оценка</h3>
                                            <ul>
                                                <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                            </ul>
                                        </div>
                                        <div class="product_review_form">
                                            <form action="#">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <label for="review_comment">Ваш отзыв</label>
                                                        <textarea name="comment" id="review_comment"></textarea>
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <label for="author">Имя</label>
                                                        <input id="author" type="text">
                                                    </div>
                                                    <div class="col-lg-6 col-md-6">
                                                        <label for="email">Email</label>
                                                        <input id="email" type="text">
                                                    </div>
                                                </div>
                                                <button type="submit">Отправить</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--product info end-->

            <?php if (!empty($related)): ?>
            <!--related products start-->
            <section class="product_area related_products releted_product">
                <div class="row">
                    <div class="col-12">
                        <div class="section_title title_style2">
                            <div class="title_content">
                                <h2><span>Похожие</span> товары</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="product_carousel product_details_column5 owl-carousel">
                        <?php foreach ($related as $r):
                            $rid = (int)$r['id'];
                            $rn1 = (($rid - 1) % 13) + 1;
                            $rn2 = ($rid % 13) + 1;
                        ?>
                        <div class="col-lg-3">
                            <article class="single_product">
                                <figure>
                                    <div class="product_thumb">
                                        <a class="primary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= $rid ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $rn1 ?>.jpg" alt=""></a>
                                        <a class="secondary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= $rid ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $rn2 ?>.jpg" alt=""></a>
                                        <div class="quick_button">
                                            <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $rid ?>" title="Подробнее"><i class="icon-eye"></i></a>
                                        </div>
                                    </div>
                                    <div class="product_content">
                                        <div class="product_content_inner">
                                            <p class="manufacture_product"><a href="#">Запчасти</a></p>
                                            <h4 class="product_name"><a href="<?= APP_URL ?>/catalog/part.php?id=<?= $rid ?>"><?= sanitize($r['name']) ?></a></h4>
                                            <div class="product_rating">
                                                <ul>
                                                    <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                    <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                    <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                    <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                    <li><a href="#"><i class="ion-android-star-outline"></i></a></li>
                                                </ul>
                                            </div>
                                            <div class="price_box">
                                                <span class="current_price"><?= formatPrice($r['price']) ?></span>
                                            </div>
                                        </div>
                                        <div class="action_links">
                                            <ul>
                                                <li class="add_to_cart"><a href="#" data-add-cart="<?= $rid ?>" title="В корзину">В корзину</a></li>
                                                <li class="wishlist"><a href="#" title="В избранное"><i class="icon-heart"></i></a></li>
                                                <li class="compare"><a href="<?= APP_URL ?>/catalog/part.php?id=<?= $rid ?>" title="Подробнее"><i class="icon-rotate-cw"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </figure>
                            </article>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
            <!--related products end-->
            <?php endif; ?>
        </div>
    </div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
