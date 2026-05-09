<?php
require_once dirname(__DIR__) . '/config/config.php';

$db = getDB();
$catSlug   = trim($_GET['category'] ?? '');
$brandSlug = trim($_GET['brand'] ?? '');
$minPrice  = (float)($_GET['min_price'] ?? 0);
$maxPrice  = (float)($_GET['max_price'] ?? 0);
$sort      = $_GET['sort'] ?? 'newest';
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 12;

// Build WHERE clause + bindings
$where = ['1=1'];
$bind  = [];
if ($catSlug)        { $where[] = 'c.slug = ?';   $bind[] = $catSlug; }
if ($brandSlug)      { $where[] = 'b.slug = ?';   $bind[] = $brandSlug; }
if ($minPrice > 0)   { $where[] = 'p.price >= ?'; $bind[] = $minPrice; }
if ($maxPrice > 0)   { $where[] = 'p.price <= ?'; $bind[] = $maxPrice; }
$whereSQL = implode(' AND ', $where);

// Sort mapping
$sortSQL = match($sort) {
    'name_asc'   => 'p.name ASC',
    'name_desc'  => 'p.name DESC',
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    default      => 'p.created_at DESC',
};

// Count
$cntSQL  = "SELECT COUNT(*) FROM parts p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE $whereSQL";
$cntStmt = $db->prepare($cntSQL);
$cntStmt->execute($bind);
$total      = (int)$cntStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $perPage;

// Fetch products
$prodSQL = "SELECT p.*, b.name AS brand_name, b.slug AS brand_slug, c.name AS cat_name
            FROM parts p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE $whereSQL
            ORDER BY $sortSQL
            LIMIT $perPage OFFSET $offset";
$prodStmt = $db->prepare($prodSQL);
$prodStmt->execute($bind);
$products = $prodStmt->fetchAll();

$allCats   = getCategories();
$allBrands = getBrands();
$pageTitle = 'Каталог';

// Helper to build pagination/sort URLs while preserving filters
function build_qs(array $extra = []): string {
    $qs = array_merge($_GET, $extra);
    return http_build_query(array_filter($qs, fn($v) => $v !== '' && $v !== null));
}

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
                            <li>Каталог</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--breadcrumbs area end-->

    <!--shop  area start-->
    <div class="shop_area shop_reverse">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-12">
                    <!--sidebar widget start-->
                    <aside class="sidebar_widget">
                        <div class="widget_list widget_categories">
                            <h3>Категории</h3>
                            <ul>
                                <li><a href="<?= APP_URL ?>/catalog/index.php"<?= !$catSlug ? ' class="active"' : '' ?>>Все категории</a></li>
                                <?php foreach ($allCats as $cat): if (!empty($cat['parent_id'])) continue; ?>
                                    <li>
                                        <a href="?category=<?= urlencode($cat['slug']) ?>"<?= $catSlug === $cat['slug'] ? ' class="active"' : '' ?>>
                                            <?= sanitize($cat['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="widget_list widget_filter">
                            <h3>Цена</h3>
                            <form method="get" action="">
                                <?php if ($catSlug): ?><input type="hidden" name="category" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
                                <?php if ($brandSlug): ?><input type="hidden" name="brand" value="<?= sanitize($brandSlug) ?>"><?php endif; ?>
                                <input type="hidden" name="sort" value="<?= sanitize($sort) ?>">
                                <div style="display:flex;gap:8px;margin-bottom:12px;">
                                    <input type="number" name="min_price" placeholder="от" value="<?= $minPrice > 0 ? $minPrice : '' ?>" min="0" style="width:50%;border:1px solid #ddd;padding:8px;">
                                    <input type="number" name="max_price" placeholder="до" value="<?= $maxPrice > 0 ? $maxPrice : '' ?>" min="0" style="width:50%;border:1px solid #ddd;padding:8px;">
                                </div>
                                <button type="submit">Применить фильтр</button>
                            </form>
                        </div>
                        <div class="widget_list widget_categories">
                            <h3>Бренды</h3>
                            <ul>
                                <li><a href="?<?= $catSlug ? 'category=' . urlencode($catSlug) : '' ?>"<?= !$brandSlug ? ' class="active"' : '' ?>>Все бренды</a></li>
                                <?php foreach ($allBrands as $b): ?>
                                    <li>
                                        <a href="?<?= $catSlug ? 'category=' . urlencode($catSlug) . '&' : '' ?>brand=<?= urlencode($b['slug']) ?>"<?= $brandSlug === $b['slug'] ? ' class="active"' : '' ?>>
                                            <?= sanitize($b['name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </aside>
                    <!--sidebar widget end-->
                </div>
                <div class="col-lg-9 col-md-12">

                    <!--shop toolbar start-->
                    <div class="shop_toolbar_wrapper">
                        <div class="shop_toolbar_btn">
                            <button data-role="grid_4" type="button" class="active btn-grid-4" data-bs-toggle="tooltip" title="4"></button>
                            <button data-role="grid_3" type="button" class="btn-grid-3" data-bs-toggle="tooltip" title="3"></button>
                            <button data-role="grid_list" type="button" class="btn-list" data-bs-toggle="tooltip" title="List"></button>
                        </div>
                        <div class="niceselect_option">
                            <form class="select_option" method="get" action="">
                                <?php if ($catSlug): ?><input type="hidden" name="category" value="<?= sanitize($catSlug) ?>"><?php endif; ?>
                                <?php if ($brandSlug): ?><input type="hidden" name="brand" value="<?= sanitize($brandSlug) ?>"><?php endif; ?>
                                <?php if ($minPrice > 0): ?><input type="hidden" name="min_price" value="<?= $minPrice ?>"><?php endif; ?>
                                <?php if ($maxPrice > 0): ?><input type="hidden" name="max_price" value="<?= $maxPrice ?>"><?php endif; ?>
                                <select name="sort" id="short" onchange="this.form.submit()">
                                    <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Сортировка: новинки</option>
                                    <option value="price_asc"  <?= $sort === 'price_asc'  ? 'selected' : '' ?>>Цена: по возрастанию</option>
                                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Цена: по убыванию</option>
                                    <option value="name_asc"   <?= $sort === 'name_asc'   ? 'selected' : '' ?>>Название: А-Я</option>
                                    <option value="name_desc"  <?= $sort === 'name_desc'  ? 'selected' : '' ?>>Название: Я-А</option>
                                </select>
                            </form>
                        </div>
                        <div class="page_amount">
                            <p>Показано <?= count($products) ?> из <?= $total ?> товаров</p>
                        </div>
                    </div>
                    <!--shop toolbar end-->
                    <div class="row shop_wrapper">
                        <?php if (empty($products)): ?>
                            <div class="col-12">
                                <p style="padding:40px;text-align:center;color:#777;">По вашему запросу ничего не найдено.</p>
                            </div>
                        <?php else: foreach ($products as $p):
                            $img1 = (((int)$p['id'] - 1) % 13) + 1;
                            $img2 = ((int)$p['id'] % 13) + 1;
                        ?>
                        <div class="col-lg-4 col-md-4 col-sm-6">
                            <article class="single_product">
                                <figure>
                                    <div class="product_thumb">
                                        <a class="primary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$p['id'] ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $img1 ?>.jpg" alt=""></a>
                                        <a class="secondary_img" href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$p['id'] ?>"><img src="<?= APP_URL ?>/assets/img/product/product<?= $img2 ?>.jpg" alt=""></a>
                                        <div class="action_links">
                                            <ul>
                                                <li class="quick_button"><a href="#" data-add-cart="<?= (int)$p['id'] ?>" title="В корзину"><span class="ion-android-cart"></span></a></li>
                                                <li class="wishlist"><a href="#" title="В избранное"><span class="ion-ios-heart-outline"></span></a></li>
                                                <li class="compare"><a href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$p['id'] ?>" title="Подробнее"><span class="ion-eye"></span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <figcaption class="product_content">
                                        <h4 class="product_name"><a href="<?= APP_URL ?>/catalog/part.php?id=<?= (int)$p['id'] ?>"><?= sanitize($p['name']) ?></a></h4>
                                        <div class="price_box">
                                            <span class="current_price"><?= formatPrice($p['price']) ?></span>
                                        </div>
                                    </figcaption>
                                </figure>
                            </article>
                        </div>
                        <?php endforeach; endif; ?>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="shop_toolbar t_bottom">
                        <div class="pagination">
                            <ul>
                                <?php if ($page > 1): ?>
                                    <li class="prev"><a href="?<?= build_qs(['page' => $page - 1]) ?>">prev</a></li>
                                <?php endif; ?>
                                <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
                                    <?php if ($p === $page): ?>
                                        <li class="current"><?= $p ?></li>
                                    <?php else: ?>
                                        <li><a href="?<?= build_qs(['page' => $p]) ?>"><?= $p ?></a></li>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <?php if ($page < $totalPages): ?>
                                    <li class="next"><a href="?<?= build_qs(['page' => $page + 1]) ?>">next</a></li>
                                    <li><a href="?<?= build_qs(['page' => $totalPages]) ?>">&gt;&gt;</a></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!--shop wrapper end-->
                </div>
            </div>
        </div>
    </div>
    <!--shop  area end-->

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
