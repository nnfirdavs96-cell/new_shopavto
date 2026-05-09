<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$user = getCurrentUser();
$db   = getDB();

$ordersTotal = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$ordersTotal->execute([$user['id']]);
$totalOrders = (int)$ordersTotal->fetchColumn();

$ordersPending = $db->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ? AND status = 'pending'");
$ordersPending->execute([$user['id']]);
$pendingOrders = (int)$ordersPending->fetchColumn();

$totalSpent = $db->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE user_id = ? AND status != 'cancelled'");
$totalSpent->execute([$user['id']]);
$spent = (float)$totalSpent->fetchColumn();

$recentStmt = $db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$recentStmt->execute([$user['id']]);
$recentOrders = $recentStmt->fetchAll();

$pageTitle = 'Мой кабинет';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!--breadcrumb area start-->
<div class="breadcrumb_area">
  <div class="container">
    <div class="breadcrumb_content">
      <h2>Мой кабинет</h2>
      <ul>
        <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
        <li>Кабинет</li>
      </ul>
    </div>
  </div>
</div>
<!--breadcrumb area end-->

<div class="shop_area" style="padding:40px 0;">
  <div class="container">
    <div class="az-dash-layout">

      <!-- Sidebar nav -->
      <div class="az-dash-sidebar">
        <div class="az-dash-nav-title">Покупатель</div>
        <div class="az-dash-nav">
          <a href="<?= APP_URL ?>/buyer/index.php" class="active">Мой кабинет</a>
          <a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a>
          <a href="<?= APP_URL ?>/buyer/cart.php">Корзина</a>
          <a href="<?= APP_URL ?>/buyer/profile.php">Профиль</a>
        </div>
        <?php if (hasRole(['manager','superadmin'])): ?>
        <div class="az-dash-nav-title" style="margin-top:16px;">Менеджер</div>
        <div class="az-dash-nav">
          <a href="<?= APP_URL ?>/manager/index.php">Панель менеджера</a>
        </div>
        <?php endif; ?>
        <?php if (hasRole(['admin','superadmin'])): ?>
        <div class="az-dash-nav-title" style="margin-top:16px;">Администратор</div>
        <div class="az-dash-nav">
          <a href="<?= APP_URL ?>/admin/index.php">Панель администратора</a>
        </div>
        <?php endif; ?>
        <div style="margin-top:16px;">
          <a href="<?= APP_URL ?>/auth/logout.php" style="color:#e74c3c;font-size:0.85rem;">Выйти</a>
        </div>
      </div>

      <!-- Main content -->
      <div class="az-dash-main">
        <div class="az-dash-heading">МОЙ КАБИНЕТ</div>
        <p style="color:#777;margin-bottom:24px;">Добро пожаловать, <strong><?= sanitize($user['username']) ?></strong>!</p>

        <!-- Stats -->
        <div class="row" style="margin-bottom:28px;">
          <div class="col-md-4">
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:20px;border-bottom:2px solid #ff6600;">
              <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#999;margin-bottom:6px;">Всего заказов</div>
              <div style="font-size:2rem;font-weight:700;color:#222;"><?= $totalOrders ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:20px;border-bottom:2px solid #ff6600;">
              <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#999;margin-bottom:6px;">В ожидании</div>
              <div style="font-size:2rem;font-weight:700;color:#222;"><?= $pendingOrders ?></div>
            </div>
          </div>
          <div class="col-md-4">
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:20px;border-bottom:2px solid #ff6600;">
              <div style="font-size:0.7rem;text-transform:uppercase;letter-spacing:0.1em;color:#999;margin-bottom:6px;">Потрачено</div>
              <div style="font-size:1.4rem;font-weight:700;color:#ff6600;"><?= formatPrice($spent) ?></div>
            </div>
          </div>
        </div>

        <!-- Recent orders -->
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;margin-bottom:24px;">
          <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #eee;">
            <strong>Последние заказы</strong>
            <a href="<?= APP_URL ?>/buyer/orders.php" style="font-size:0.85rem;color:#ff6600;">Все заказы →</a>
          </div>
          <?php if (empty($recentOrders)): ?>
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
                  <th>Сумма</th>
                  <th>Статус</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td style="font-family:monospace;color:#ff6600;">#<?= $order['id'] ?></td>
                  <td style="color:#888;font-size:0.8rem;"><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                  <td style="font-weight:700;"><?= formatPrice($order['total_amount']) ?></td>
                  <td>
                    <span class="az-status az-status-<?= $order['status'] ?>"><?= getOrderStatusLabel($order['status']) ?></span>
                  </td>
                  <td>
                    <a href="<?= APP_URL ?>/buyer/orders.php?id=<?= $order['id'] ?>" class="button" style="padding:5px 12px;font-size:0.75rem;">Детали</a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>

        <!-- Quick links -->
        <div class="row">
          <div class="col-md-4"><a href="<?= APP_URL ?>/catalog/index.php" class="button" style="display:block;text-align:center;margin-bottom:8px;">Каталог запчастей</a></div>
          <div class="col-md-4"><a href="<?= APP_URL ?>/buyer/cart.php" class="button" style="display:block;text-align:center;margin-bottom:8px;">Моя корзина</a></div>
          <div class="col-md-4"><a href="<?= APP_URL ?>/buyer/profile.php" class="button" style="display:block;text-align:center;margin-bottom:8px;">Редактировать профиль</a></div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
