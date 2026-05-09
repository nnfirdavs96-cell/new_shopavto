<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$user = getCurrentUser();
$db   = getDB();

$viewId      = (int)($_GET['id'] ?? 0);
$orderDetail = null;
$orderItems  = [];

if ($viewId) {
    $stmt = $db->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$viewId, $user['id']]);
    $orderDetail = $stmt->fetch();
    if ($orderDetail) {
        $iStmt = $db->prepare(
            "SELECT oi.*, p.name AS part_name, p.part_number, b.name AS brand_name
             FROM order_items oi
             JOIN parts p ON p.id = oi.part_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE oi.order_id = ?"
        );
        $iStmt->execute([$viewId]);
        $orderItems = $iStmt->fetchAll();
    }
}

$ordersStmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$user['id']]);
$orders = $ordersStmt->fetchAll();

$pageTitle = 'Мои заказы';
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
            <li><a href="<?= APP_URL ?>/buyer/index.php">Кабинет</a></li>
            <li>Мои заказы</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<div class="shop_area" style="padding:40px 0;">
  <div class="container">
    <div class="az-dash-layout">

      <!-- Sidebar -->
      <div class="az-dash-sidebar">
        <div class="az-dash-nav-title">Покупатель</div>
        <div class="az-dash-nav">
          <a href="<?= APP_URL ?>/buyer/index.php">Мой кабинет</a>
          <a href="<?= APP_URL ?>/buyer/orders.php" class="active">Мои заказы</a>
          <a href="<?= APP_URL ?>/buyer/cart.php">Корзина</a>
          <a href="<?= APP_URL ?>/buyer/profile.php">Профиль</a>
        </div>
        <div style="margin-top:16px;"><a href="<?= APP_URL ?>/auth/logout.php" style="color:#e74c3c;font-size:0.85rem;">Выйти</a></div>
      </div>

      <!-- Main -->
      <div class="az-dash-main">
        <div class="az-dash-heading">МОИ ЗАКАЗЫ</div>

        <?php if ($orderDetail): ?>
        <!-- Order detail -->
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;margin-bottom:24px;">
          <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #eee;">
            <div>
              <strong>Заказ #<?= $orderDetail['id'] ?></strong>
              <span style="font-size:0.8rem;color:#888;margin-left:12px;"><?= date('d.m.Y H:i', strtotime($orderDetail['created_at'])) ?></span>
            </div>
            <span class="az-status az-status-<?= $orderDetail['status'] ?>"><?= getOrderStatusLabel($orderDetail['status']) ?></span>
          </div>
          <div style="padding:20px;">
            <div class="row" style="margin-bottom:16px;">
              <div class="col-md-6">
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#999;margin-bottom:6px;">Адрес доставки</div>
                <p style="font-size:0.875rem;color:#555;"><?= nl2br(sanitize($orderDetail['shipping_address'])) ?></p>
              </div>
              <?php if ($orderDetail['notes']): ?>
              <div class="col-md-6">
                <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#999;margin-bottom:6px;">Примечания</div>
                <p style="font-size:0.875rem;color:#555;"><?= nl2br(sanitize($orderDetail['notes'])) ?></p>
              </div>
              <?php endif; ?>
            </div>
            <div class="az-table-wrap">
              <table class="az-data-table">
                <thead>
                  <tr>
                    <th>Артикул</th>
                    <th>Наименование</th>
                    <th>Бренд</th>
                    <th style="text-align:center;">Кол-во</th>
                    <th style="text-align:right;">Цена</th>
                    <th style="text-align:right;">Сумма</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orderItems as $item): ?>
                  <tr>
                    <td style="font-family:monospace;color:#ff6600;"><?= sanitize($item['part_number']) ?></td>
                    <td><a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['part_id'] ?>" style="color:#333;font-size:0.875rem;"><?= sanitize($item['part_name']) ?></a></td>
                    <td style="color:#888;font-size:0.8rem;"><?= sanitize($item['brand_name']) ?></td>
                    <td style="text-align:center;"><?= $item['quantity'] ?></td>
                    <td style="text-align:right;"><?= formatPrice($item['unit_price']) ?></td>
                    <td style="text-align:right;font-weight:700;color:#ff6600;"><?= formatPrice($item['unit_price'] * $item['quantity']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div style="text-align:right;margin-top:16px;padding-top:16px;border-top:1px solid #eee;">
              <span style="font-size:0.8rem;color:#888;">ИТОГО: </span>
              <span style="font-size:1.4rem;font-weight:700;color:#ff6600;"><?= formatPrice($orderDetail['total_amount']) ?></span>
            </div>
          </div>
        </div>
        <a href="<?= APP_URL ?>/buyer/orders.php" style="font-size:0.85rem;color:#ff6600;">← Все заказы</a>
        <?php endif; ?>

        <!-- Orders list -->
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;margin-top:24px;">
          <div style="padding:16px 20px;border-bottom:1px solid #eee;"><strong>Все заказы</strong></div>
          <?php if (empty($orders)): ?>
          <div style="text-align:center;padding:40px;color:#999;">
            <p>У вас ещё нет заказов.</p>
            <a href="<?= APP_URL ?>/catalog/index.php" class="button" style="display:inline-block;margin-top:12px;padding:8px 20px;font-size:0.85rem;">Перейти в каталог</a>
          </div>
          <?php else: ?>
          <div class="az-table-wrap" style="border:none;border-radius:0;">
            <table class="az-data-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Дата</th>
                  <th>Адрес</th>
                  <th style="text-align:right;">Сумма</th>
                  <th>Статус</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                  <td style="font-family:monospace;color:#ff6600;">#<?= $order['id'] ?></td>
                  <td style="color:#888;font-size:0.8rem;"><?= date('d.m.Y', strtotime($order['created_at'])) ?></td>
                  <td style="font-size:0.8rem;color:#555;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= sanitize(truncate($order['shipping_address'], 40)) ?></td>
                  <td style="text-align:right;font-weight:700;"><?= formatPrice($order['total_amount']) ?></td>
                  <td><span class="az-status az-status-<?= $order['status'] ?>"><?= getOrderStatusLabel($order['status']) ?></span></td>
                  <td><a href="?id=<?= $order['id'] ?>" class="button" style="padding:5px 12px;font-size:0.75rem;">Детали</a></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
