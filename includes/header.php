<?php
// header.php — Mazlay template + backend PHP logic
$cartCount   = isLoggedIn() ? getCartCount() : 0;
$currentUser = getCurrentUser();
$flash       = getFlashMessage();
$csrfToken   = generateCsrfToken();
$allCats     = getCategories();
?>
<!doctype html>
<html class="no-js" lang="ru">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?>АвтоЗапчасть</title>
  <meta name="description" content="АвтоЗапчасть — профессиональный подбор и продажа автозапчастей">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon.ico">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/plugins.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/template.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body>

<!--offcanvas menu area start-->
<div class="off_canvars_overlay"></div>
<div class="offcanvas_menu">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="canvas_open">
          <a href="javascript:void(0)"><i class="ion-navicon"></i></a>
        </div>
        <div class="offcanvas_menu_wrapper">
          <div class="canvas_close">
            <a href="javascript:void(0)"><i class="ion-android-close"></i></a>
          </div>
          <div class="call_support">
            <p><i class="icon-phone-call"></i> <span>Позвоните нам: <a href="tel:88005553535">+7 (800) 555-35-35</a></span></p>
          </div>
          <div class="header_top_links">
            <ul>
              <?php if (isLoggedIn()): ?>
                <li><a href="<?= APP_URL ?>/buyer/index.php">Мой кабинет</a></li>
                <li><a href="<?= APP_URL ?>/buyer/cart.php">Корзина (<?= $cartCount ?>)</a></li>
                <li><a href="<?= APP_URL ?>/auth/logout.php">Выйти</a></li>
              <?php else: ?>
                <li><a href="<?= APP_URL ?>/auth/register.php">Регистрация</a></li>
                <li><a href="<?= APP_URL ?>/auth/login.php">Войти</a></li>
                <li><a href="<?= APP_URL ?>/buyer/cart.php">Корзина</a></li>
              <?php endif; ?>
            </ul>
          </div>
          <div class="search_container">
            <form action="<?= APP_URL ?>/search/index.php" method="get">
              <div class="search_box">
                <input placeholder="Поиск запчастей..." type="text" name="q">
                <button type="submit">Найти</button>
              </div>
            </form>
          </div>
          <div id="menu" class="text-left">
            <ul class="offcanvas_main_menu">
              <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
              <li class="menu-item-has-children">
                <a href="<?= APP_URL ?>/catalog/index.php">Каталог</a>
                <ul class="sub-menu">
                  <?php foreach ($allCats as $cat): if ($cat['parent_id'] !== null) continue; ?>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a></li>
                  <?php endforeach; ?>
                </ul>
              </li>
              <?php if (isLoggedIn()): ?>
                <li><a href="<?= APP_URL ?>/buyer/index.php">Мой кабинет</a></li>
                <li><a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a></li>
                <?php if (hasRole(['manager','superadmin'])): ?>
                  <li><a href="<?= APP_URL ?>/manager/index.php">Менеджер</a></li>
                <?php endif; ?>
                <?php if (hasRole(['admin','superadmin'])): ?>
                  <li><a href="<?= APP_URL ?>/admin/index.php">Администратор</a></li>
                <?php endif; ?>
                <?php if (hasRole('superadmin')): ?>
                  <li><a href="<?= APP_URL ?>/superadmin/index.php">Супер-Админ</a></li>
                <?php endif; ?>
              <?php endif; ?>
              <li><a href="<?= APP_URL ?>/search/index.php">Поиск</a></li>
            </ul>
          </div>
          <div class="offcanvas_footer">
            <span><a href="mailto:info@avtozapchast.ru"><i class="fa fa-envelope-o"></i> info@avtozapchast.ru</a></span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!--offcanvas menu area end-->

<header>
  <div class="main_header">

    <!--header top start-->
    <div class="header_top">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 col-md-6">
            <div class="header_top_links">
              <ul>
                <li><a href="tel:88005553535"><i class="icon-phone-call"></i> +7 (800) 555-35-35</a></li>
                <li><a href="mailto:info@avtozapchast.ru">info@avtozapchast.ru</a></li>
              </ul>
            </div>
          </div>
          <div class="col-lg-6 col-md-6">
            <div class="header_top_links text-right">
              <ul>
                <?php if (isLoggedIn()): ?>
                  <li>
                    <a href="<?= APP_URL ?>/buyer/index.php">
                      <?= sanitize($currentUser['username'] ?? '') ?>
                      <span class="az-role-badge az-role-<?= $currentUser['role'] ?>">[<?= sanitize($currentUser['role']) ?>]</span>
                    </a>
                  </li>
                  <?php if (hasRole(['manager','superadmin'])): ?>
                    <li><a href="<?= APP_URL ?>/manager/index.php">Менеджер</a></li>
                  <?php endif; ?>
                  <?php if (hasRole(['admin','superadmin'])): ?>
                    <li><a href="<?= APP_URL ?>/admin/index.php">Администратор</a></li>
                  <?php endif; ?>
                  <li><a href="<?= APP_URL ?>/auth/logout.php">Выйти</a></li>
                <?php else: ?>
                  <li><a href="<?= APP_URL ?>/auth/register.php">Регистрация</a></li>
                  <li><a href="<?= APP_URL ?>/auth/login.php">Войти</a></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--header top end-->

    <!--header middle start-->
    <div class="header_middle">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-2 col-md-4 col-sm-4 col-4">
            <div class="logo">
              <a href="<?= APP_URL ?>/index.php" class="logo_text_link">
                <span class="logo_text_main">АВТО<span class="logo_accent">ЗАПЧАСТЬ</span></span>
              </a>
            </div>
          </div>
          <div class="col-lg-10 col-md-6 col-sm-6 col-6">
            <div class="header_right_box">
              <div class="search_container">
                <form action="<?= APP_URL ?>/search/index.php" method="get">
                  <div class="hover_category">
                    <select class="select_option" name="category" id="header_cat">
                      <option value="">Все категории</option>
                      <?php foreach ($allCats as $c): if ($c['parent_id'] !== null) continue; ?>
                        <option value="<?= sanitize($c['slug']) ?>"><?= sanitize($c['name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="search_box">
                    <input id="header-search-input" placeholder="Номер детали или название..." type="text" name="q"
                           value="<?= isset($_GET['q']) ? sanitize($_GET['q']) : '' ?>" autocomplete="off">
                    <button type="submit">Поиск</button>
                  </div>
                </form>
                <div id="live-search-dropdown" class="live_search_dropdown" style="display:none;"></div>
              </div>
              <div class="header_configure_area">
                <div class="mini_cart_wrapper">
                  <a href="<?= APP_URL ?>/buyer/cart.php">
                    <i class="icon-shopping-bag2"></i>
                    <span class="cart_count" id="cart-badge"><?= $cartCount ?></span>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--header middle end-->

    <!--header bottom start-->
    <div class="header_bottom sticky-header">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-3">
            <div class="categories_menu">
              <div class="categories_title">
                <h2 class="categori_toggle">ВСЕ КАТЕГОРИИ</h2>
              </div>
              <div class="categories_menu_toggle">
                <ul>
                  <?php foreach ($allCats as $cat): if ($cat['parent_id'] !== null) continue; ?>
                  <li>
                    <a href="<?= APP_URL ?>/catalog/index.php?category=<?= sanitize($cat['slug']) ?>">
                      <?= sanitize($cat['name']) ?>
                    </a>
                  </li>
                  <?php endforeach; ?>
                </ul>
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="main_menu menu_position text-left">
              <nav>
                <ul>
                  <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php">Каталог</a></li>
                  <li><a href="<?= APP_URL ?>/search/index.php">Поиск</a></li>
                  <?php if (isLoggedIn()): ?>
                    <li><a href="<?= APP_URL ?>/buyer/index.php">Кабинет</a></li>
                    <li><a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a></li>
                  <?php endif; ?>
                </ul>
              </nav>
            </div>
          </div>
          <div class="col-lg-2">
            <div class="call_support text-right">
              <p><i class="icon-phone-call"></i> <span><a href="tel:88005553535">+7 (800) 555-35-35</a></span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--header bottom end-->

  </div>
</header>
<!--header area end-->

<?php if ($flash): ?>
<div class="az-flash az-flash-<?= sanitize($flash['type']) ?>" id="az-flash">
  <div class="container">
    <div class="az-flash-inner">
      <span><?= sanitize($flash['message']) ?></span>
      <button onclick="document.getElementById('az-flash').remove()" class="az-flash-close">&times;</button>
    </div>
  </div>
</div>
<?php endif; ?>
