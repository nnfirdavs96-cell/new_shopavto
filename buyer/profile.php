<?php
require_once dirname(__DIR__) . '/config/config.php';
requireRole('buyer');

$user   = getCurrentUser();
$db     = getDB();
$csrf   = generateCsrfToken();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка безопасности.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        $newPass  = $_POST['new_password'] ?? '';
        $confPass = $_POST['confirm_password'] ?? '';

        if (mb_strlen($username) < 3) $errors[] = 'Имя пользователя слишком короткое.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email.';

        if (empty($errors)) {
            $chk = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $chk->execute([$email, $user['id']]);
            if ($chk->fetch()) $errors[] = 'Этот email уже занят.';
        }

        if (!empty($newPass)) {
            if (mb_strlen($newPass) < 8) $errors[] = 'Пароль должен быть не менее 8 символов.';
            if ($newPass !== $confPass)   $errors[] = 'Пароли не совпадают.';
        }

        if (empty($errors)) {
            if (!empty($newPass)) {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $db->prepare("UPDATE users SET username=?, email=?, phone=?, password_hash=?, updated_at=NOW() WHERE id=?")
                   ->execute([$username, $email, $phone ?: null, $hash, $user['id']]);
            } else {
                $db->prepare("UPDATE users SET username=?, email=?, phone=?, updated_at=NOW() WHERE id=?")
                   ->execute([$username, $email, $phone ?: null, $user['id']]);
            }
            unset($_SESSION['user_data']);
            flashMessage('success', 'Профиль успешно обновлён.');
            redirect(APP_URL . '/buyer/profile.php');
        }
    }
}

$userStmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$user['id']]);
$userData = $userStmt->fetch();

$pageTitle = 'Мой профиль';
require_once dirname(__DIR__) . '/includes/header.php';
?>

<div class="breadcrumb_section page-decoration">
  <div class="container">
    <div class="az-breadcrumb">
      <a href="<?= APP_URL ?>/index.php">Главная</a>
      <span>/</span>
      <a href="<?= APP_URL ?>/buyer/index.php">Кабинет</a>
      <span>/</span>
      Мой профиль
    </div>
  </div>
</div>

<section class="section" style="padding:40px 0 60px;">
  <div class="container">
    <div class="az-dash-layout">

      <!-- Sidebar -->
      <aside class="az-dash-sidebar">
        <div class="az-dash-nav-title">Мой кабинет</div>
        <nav class="az-dash-nav">
          <a href="<?= APP_URL ?>/buyer/index.php">Главная</a>
          <a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a>
          <a href="<?= APP_URL ?>/buyer/cart.php">Корзина</a>
          <a href="<?= APP_URL ?>/buyer/profile.php" class="active">Профиль</a>
          <?php if (hasRole(['manager','superadmin'])): ?>
            <a href="<?= APP_URL ?>/manager/index.php">Менеджер</a>
          <?php endif; ?>
          <?php if (hasRole(['admin','superadmin'])): ?>
            <a href="<?= APP_URL ?>/admin/index.php">Администратор</a>
          <?php endif; ?>
          <?php if (hasRole('superadmin')): ?>
            <a href="<?= APP_URL ?>/superadmin/index.php">Супер-Админ</a>
          <?php endif; ?>
          <a href="<?= APP_URL ?>/auth/logout.php" style="color:#e74c3c;">Выйти</a>
        </nav>
      </aside>

      <!-- Main content -->
      <div class="az-dash-main">
        <div class="az-dash-heading">МОЙ ПРОФИЛЬ</div>

        <?php if (!empty($errors)): ?>
          <ul class="az-error-list" style="margin-bottom:20px;">
            <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
          </ul>
        <?php endif; ?>

        <div style="max-width:600px;">
          <form method="post" action="">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrf) ?>">

            <!-- Basic info -->
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:24px;margin-bottom:24px;">
              <h3 style="font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid #eee;">Основные данные</h3>

              <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;padding:10px 14px;background:#f9f9f9;border-radius:2px;">
                <span class="az-role-badge az-role-<?= sanitize($userData['role']) ?>"><?= sanitize($userData['role']) ?></span>
                <span style="font-size:0.78rem;color:#999;">
                  Зарегистрирован <?= date('d.m.Y', strtotime($userData['created_at'])) ?>
                </span>
              </div>

              <div class="az-form-group">
                <label class="az-form-label" for="username">Имя пользователя</label>
                <input type="text" id="username" name="username" class="az-form-input"
                       value="<?= sanitize($userData['username']) ?>" required>
              </div>
              <div class="az-form-group">
                <label class="az-form-label" for="email">Email</label>
                <input type="email" id="email" name="email" class="az-form-input"
                       value="<?= sanitize($userData['email']) ?>" required>
              </div>
              <div class="az-form-group" style="margin-bottom:0;">
                <label class="az-form-label" for="phone">Телефон</label>
                <input type="tel" id="phone" name="phone" class="az-form-input"
                       value="<?= sanitize($userData['phone'] ?? '') ?>" placeholder="+7 (___) ___-__-__">
              </div>
            </div>

            <!-- Change password -->
            <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:24px;margin-bottom:24px;">
              <h3 style="font-size:1rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;margin-bottom:18px;padding-bottom:10px;border-bottom:1px solid #eee;">Изменить пароль</h3>
              <p style="font-size:0.82rem;color:#999;margin-bottom:16px;">Оставьте поля пустыми, если не хотите менять пароль.</p>
              <div class="az-form-group">
                <label class="az-form-label" for="new_password">Новый пароль</label>
                <input type="password" id="new_password" name="new_password" class="az-form-input"
                       placeholder="Мин. 8 символов">
              </div>
              <div class="az-form-group" style="margin-bottom:0;">
                <label class="az-form-label" for="confirm_password">Подтверждение пароля</label>
                <input type="password" id="confirm_password" name="confirm_password" class="az-form-input"
                       placeholder="••••••••">
              </div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;">
              <button type="submit" class="az-btn-primary" style="width:auto;">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
              <a href="<?= APP_URL ?>/buyer/index.php" class="az-link" style="font-size:0.875rem;">Отмена</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
