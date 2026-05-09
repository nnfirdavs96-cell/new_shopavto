<?php
require_once dirname(__DIR__) . '/config/config.php';

if (isLoggedIn()) {
    $role = $_SESSION['role'] ?? 'buyer';
    switch ($role) {
        case 'superadmin': redirect(APP_URL . '/superadmin/index.php');
        case 'admin':      redirect(APP_URL . '/admin/index.php');
        case 'manager':    redirect(APP_URL . '/manager/index.php');
        default:           redirect(APP_URL . '/buyer/index.php');
    }
}

$errors   = [];
$emailVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка безопасности. Обновите страницу и попробуйте снова.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $emailVal = $email;

        if (empty($email) || empty($password)) {
            $errors[] = 'Введите email и пароль.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare("SELECT id, username, email, password_hash, role, is_active FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'Неверный email или пароль.';
            } elseif (!$user['is_active']) {
                $errors[] = 'Ваш аккаунт деактивирован. Обратитесь к администратору.';
            } else {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role']    = $user['role'];
                unset($_SESSION['user_data']);
                flashMessage('success', 'Добро пожаловать, ' . $user['username'] . '!');
                $redirect = $_GET['redirect'] ?? '';
                if ($redirect && strpos($redirect, APP_URL) === 0) redirect($redirect);
                switch ($user['role']) {
                    case 'superadmin': redirect(APP_URL . '/superadmin/index.php');
                    case 'admin':      redirect(APP_URL . '/admin/index.php');
                    case 'manager':    redirect(APP_URL . '/manager/index.php');
                    default:           redirect(APP_URL . '/buyer/index.php');
                }
            }
        }
    }
}

$csrfToken = generateCsrfToken();
?>
<!doctype html>
<html class="no-js" lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Вход | АвтоЗапчасть</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon.ico">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/plugins.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/template.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body>

<!-- customer login area start -->
<div class="login_register_wrap section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-5 col-md-7">
        <div class="login_wrap" style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:40px;">
          <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= APP_URL ?>/index.php" class="logo_text_link">
              <span class="logo_text_main" style="font-size:1.6rem;">АВТО<span class="logo_accent">ЗАПЧАСТЬ</span></span>
            </a>
          </div>
          <div class="padding_eight_all">
            <div class="section_title text-center">
              <h3>Вход в аккаунт</h3>
            </div>
          </div>

          <?php if (!empty($errors)): ?>
          <ul class="az-error-list">
            <?php foreach ($errors as $err): ?><li><?= sanitize($err) ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>

          <form method="post" action="" class="account_form">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
            <p>
              <label>Email <span>*</span></label>
              <input type="email" name="email" value="<?= sanitize($emailVal) ?>" placeholder="admin@avtozapchast.ru" required autofocus>
            </p>
            <p>
              <label>Пароль <span>*</span></label>
              <input type="password" name="password" placeholder="••••••••" required>
            </p>
            <div class="login_submit">
              <button type="submit">Войти</button>
            </div>
          </form>

          <div style="text-align:center;margin-top:16px;font-size:0.875rem;">
            Нет аккаунта? <a href="<?= APP_URL ?>/auth/register.php" class="az-link">Зарегистрироваться</a>
          </div>

          <div class="az-demo-box" style="margin-top:20px;">
            <div class="az-demo-label">Демо-аккаунты (пароль: Password123!)</div>
            <div class="az-demo-row"><span>superadmin@avtozapchast.ru</span><span>[superadmin]</span></div>
            <div class="az-demo-row"><span>admin@avtozapchast.ru</span><span>[admin]</span></div>
            <div class="az-demo-row"><span>manager@avtozapchast.ru</span><span>[manager]</span></div>
            <div class="az-demo-row"><span>buyer@avtozapchast.ru</span><span>[buyer]</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- customer login area end -->

<script src="<?= APP_URL ?>/assets/js/plugins.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
