<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$db  = getDB();
$uid = $_SESSION['user_id'];

// Toggle wishlist (add/remove via part_id in session)
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_part'])) {
    if (verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $pid = (int)$_POST['toggle_part'];
        $key = array_search($pid, $_SESSION['wishlist']);
        if ($key !== false) {
            unset($_SESSION['wishlist'][$key]);
            $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        } else {
            $_SESSION['wishlist'][] = $pid;
        }
    }
    redirect(APP_URL . '/buyer/wishlist.php');
}

$wishlistItems = [];
if (!empty($_SESSION['wishlist'])) {
    $ids  = implode(',', array_map('intval', $_SESSION['wishlist']));
    $wishlistItems = $db->query("SELECT p.*, b.name as brand_name FROM parts p LEFT JOIN brands b ON p.brand_id=b.id WHERE p.id IN ($ids)")->fetchAll();
}

$csrf      = generateCsrfToken();
$pageTitle = 'Список желаний';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="breadcrumb_section page-decoration">
  <div class="container">
    <div class="az-breadcrumb">
      <a href="<?= APP_URL ?>/index.php">Главная</a><span>/</span>
      <a href="<?= APP_URL ?>/buyer/index.php">Кабинет</a><span>/</span>
      Список желаний
    </div>
  </div>
</div>

<div class="wishlist_page_bg section_padding">
  <div class="container">
    <div class="wishlist_area">

      <?php if (empty($wishlistItems)): ?>
        <div class="az-no-results">
          <div class="az-no-results-icon">♡</div>
          <h3>Список желаний пуст</h3>
          <p style="margin:12px 0 24px; color:#999;">Добавляйте товары нажав на иконку сердца на странице товара</p>
          <a href="<?= APP_URL ?>/catalog/index.php" class="btn btn-wh-2 btn-hover-dark">Перейти в каталог</a>
        </div>
      <?php else: ?>

        <div class="wishlist_inner">
          <div class="table_desc wishlist">
            <div class="cart_page">
              <table>
                <thead>
                  <tr>
                    <th class="product_remove">Удалить</th>
                    <th class="product_thumb">Фото</th>
                    <th class="product_name">Товар</th>
                    <th class="product-price">Цена</th>
                    <th class="product_quantity">Наличие</th>
                    <th class="product_total">В корзину</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($wishlistItems as $item):
                    $imgNum  = ($item['id'] - 1) % 13 + 1;
                    $imgFile = $imgNum <= 5 ? "productbig{$imgNum}.jpg" : "product{$imgNum}.jpg";
                    $stock   = getStockStatus($item['stock']);
                  ?>
                  <tr>
                    <td class="product_remove">
                      <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= sanitize($csrf) ?>">
                        <input type="hidden" name="toggle_part" value="<?= $item['id'] ?>">
                        <button type="submit" style="background:none;border:none;font-size:1.1rem;cursor:pointer;color:#999;">&times;</button>
                      </form>
                    </td>
                    <td class="product_thumb">
                      <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['id'] ?>">
                        <img src="<?= APP_URL ?>/assets/img/product/<?= $imgFile ?>"
                             alt="<?= sanitize($item['name']) ?>" style="width:70px;height:70px;object-fit:cover;">
                      </a>
                    </td>
                    <td class="product_name">
                      <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['id'] ?>">
                        <?= sanitize($item['name']) ?>
                      </a>
                      <div class="az-part-num"><?= sanitize($item['part_number']) ?></div>
                    </td>
                    <td class="product-price"><?= formatPrice($item['price']) ?></td>
                    <td class="product_quantity">
                      <span class="az-badge az-badge-<?= $stock['class'] ?>"><?= $stock['label'] ?></span>
                    </td>
                    <td class="product_total">
                      <?php if ($item['stock'] > 0): ?>
                        <button class="btn btn-wh-2 btn-hover-dark"
                                data-add-cart="<?= $item['id'] ?>" style="font-size:0.75rem; padding:8px 14px;">
                          В корзину
                        </button>
                      <?php else: ?>
                        <span style="color:#999; font-size:0.8rem;">Нет в наличии</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="row" style="margin-top:30px;">
          <div class="col-12">
            <div class="wishlist_share" style="display:flex; align-items:center; gap:16px;">
              <h4 style="margin:0; font-size:0.9rem; color:#555;">Поделиться:</h4>
              <ul style="list-style:none; display:flex; gap:10px; margin:0; padding:0;">
                <li><a href="#" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;background:#3b5998;color:#fff;border-radius:2px;font-size:0.8rem;"><i class="fa fa-facebook"></i></a></li>
                <li><a href="#" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;background:#1da1f2;color:#fff;border-radius:2px;font-size:0.8rem;"><i class="fa fa-twitter"></i></a></li>
                <li><a href="#" style="display:flex;align-items:center;justify-content:center;width:32px;height:32px;background:#bd081c;color:#fff;border-radius:2px;font-size:0.8rem;"><i class="fa fa-pinterest"></i></a></li>
              </ul>
            </div>
          </div>
        </div>

      <?php endif; ?>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
