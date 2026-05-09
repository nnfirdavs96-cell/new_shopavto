<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$user = getCurrentUser();
$db   = getDB();
$csrf = generateCsrfToken();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        flashMessage('danger', 'Ошибка безопасности.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $address = trim($_POST['address'] ?? '');
    $notes   = trim($_POST['notes'] ?? '');
    if (empty($address)) {
        flashMessage('danger', 'Укажите адрес доставки.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $cartStmt = $db->prepare(
        "SELECT c.*, p.price, p.stock, p.name AS part_name
         FROM cart c JOIN parts p ON p.id = c.part_id
         WHERE c.user_id = ? AND p.is_active = 1"
    );
    $cartStmt->execute([$user['id']]);
    $cartItems = $cartStmt->fetchAll();
    if (empty($cartItems)) {
        flashMessage('warning', 'Ваша корзина пуста.');
        redirect(APP_URL . '/buyer/cart.php');
    }
    $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));
    $db->beginTransaction();
    try {
        $ordStmt = $db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, notes) VALUES (?, ?, ?, ?)");
        $ordStmt->execute([$user['id'], $total, $address, $notes ?: null]);
        $orderId = (int)$db->lastInsertId();
        $itmStmt = $db->prepare("INSERT INTO order_items (order_id, part_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        foreach ($cartItems as $item) {
            $itmStmt->execute([$orderId, $item['part_id'], $item['quantity'], $item['price']]);
        }
        $db->prepare("DELETE FROM cart WHERE user_id = ?")->execute([$user['id']]);
        $db->commit();
        flashMessage('success', "Заказ #$orderId успешно оформлен! Мы свяжемся с вами для подтверждения.");
        redirect(APP_URL . '/buyer/orders.php?id=' . $orderId);
    } catch (Exception $e) {
        $db->rollBack();
        flashMessage('danger', 'Ошибка оформления заказа. Попробуйте снова.');
        redirect(APP_URL . '/buyer/cart.php');
    }
}

$cartStmt = $db->prepare(
    "SELECT c.id AS cart_id, c.part_id, c.quantity, p.name, p.part_number, p.price, p.stock, b.name AS brand_name
     FROM cart c
     JOIN parts p ON p.id = c.part_id
     LEFT JOIN brands b ON b.id = p.brand_id
     WHERE c.user_id = ? AND p.is_active = 1
     ORDER BY c.added_at DESC"
);
$cartStmt->execute([$user['id']]);
$cartItems = $cartStmt->fetchAll();
$cartTotal = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $cartItems));

$pageTitle = 'Корзина';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<!--breadcrumb area start-->
<div class="breadcrumb_area">
  <div class="container">
    <div class="breadcrumb_content">
      <h2>Корзина</h2>
      <ul>
        <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
        <li>Корзина</li>
      </ul>
    </div>
  </div>
</div>
<!--breadcrumb area end-->

<!--Cart area start-->
<div class="cart_area">
  <div class="container">

    <?php if (empty($cartItems)): ?>
    <div style="text-align:center;padding:80px 20px;">
      <p style="font-size:1.1rem;color:#777;margin-bottom:20px;">Ваша корзина пуста</p>
      <a href="<?= APP_URL ?>/catalog/index.php" class="button">Перейти в каталог</a>
    </div>
    <?php else: ?>

    <div class="row">
      <div class="col-lg-8">

        <!--Cart table-->
        <div class="cart_table">
          <table class="az-cart-table">
            <thead>
              <tr>
                <th>Товар</th>
                <th style="text-align:center;">Кол-во</th>
                <th style="text-align:right;">Цена</th>
                <th style="text-align:right;">Сумма</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cartItems as $item): ?>
              <tr data-cart-row="<?= (int)$item['part_id'] ?>">
                <td>
                  <p class="az-part-num"><?= sanitize($item['part_number']) ?></p>
                  <a href="<?= APP_URL ?>/catalog/part.php?id=<?= $item['part_id'] ?>" style="color:#333;font-size:0.875rem;">
                    <?= sanitize(truncate($item['name'], 50)) ?>
                  </a>
                  <div style="font-size:0.75rem;color:#999;"><?= sanitize($item['brand_name']) ?></div>
                </td>
                <td style="text-align:center;">
                  <div class="az-qty-wrap" style="justify-content:center;">
                    <button class="az-qty-btn" data-qty-minus>−</button>
                    <input type="number" class="az-qty-input" data-qty-input value="<?= (int)$item['quantity'] ?>" min="1" max="99" readonly>
                    <button class="az-qty-btn" data-qty-plus>+</button>
                  </div>
                </td>
                <td style="text-align:right;font-size:0.875rem;color:#555;"><?= formatPrice($item['price']) ?></td>
                <td style="text-align:right;font-weight:700;color:#ff6600;" data-row-subtotal>
                  <?= formatPrice($item['price'] * $item['quantity']) ?>
                </td>
                <td>
                  <a href="#" data-cart-remove="<?= (int)$item['part_id'] ?>" style="color:#e74c3c;font-size:1.2rem;text-decoration:none;">✕</a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <!--Cart table end-->

      </div>
      <div class="col-lg-4">

        <!--Cart total + checkout-->
        <div class="cart_total_box" style="background:#f9f9f9;border:1px solid #e0e0e0;padding:24px;border-radius:4px;">
          <h3 style="font-size:1.1rem;font-weight:700;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #ddd;">ИТОГО</h3>
          <?php foreach ($cartItems as $item): ?>
          <div style="display:flex;justify-content:space-between;font-size:0.8rem;color:#555;padding:4px 0;">
            <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding-right:8px;"><?= sanitize(truncate($item['name'],28)) ?></span>
            <span style="white-space:nowrap;"><?= $item['quantity'] ?> × <?= formatPrice($item['price']) ?></span>
          </div>
          <?php endforeach; ?>
          <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 0;border-top:2px solid #ff6600;margin-top:10px;">
            <strong style="text-transform:uppercase;font-size:0.85rem;color:#555;">Итого:</strong>
            <strong style="font-size:1.4rem;color:#ff6600;" id="cart-total"><?= formatPrice($cartTotal) ?></strong>
          </div>

          <!--Checkout form-->
          <form method="post" action="" style="margin-top:16px;">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrf) ?>">
            <input type="hidden" name="checkout" value="1">
            <div class="az-form-group">
              <label class="az-form-label">Адрес доставки *</label>
              <textarea name="address" class="az-form-input" rows="3"
                        placeholder="г. Москва, ул. Пример, д. 1, кв. 10" required
                        style="resize:vertical;"></textarea>
            </div>
            <div class="az-form-group">
              <label class="az-form-label">Примечания</label>
              <textarea name="notes" class="az-form-input" rows="2"
                        placeholder="Удобное время доставки..."
                        style="resize:vertical;"></textarea>
            </div>
            <button type="submit" class="button" style="width:100%;text-align:center;">ОФОРМИТЬ ЗАКАЗ</button>
          </form>

          <div style="text-align:center;margin-top:14px;">
            <a href="<?= APP_URL ?>/catalog/index.php" style="font-size:0.85rem;color:#777;">← Продолжить покупки</a>
          </div>
        </div>

      </div>
    </div>

    <?php endif; ?>
  </div>
</div>
<!--Cart area end-->

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
