<?php
require_once dirname(__DIR__) . '/config/config.php';

if (isLoggedIn()) redirect(APP_URL . '/buyer/index.php');

$errors = [];
$vals   = ['username'=>'','email'=>'','phone'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $username        = trim($_POST['username'] ?? '');
        $email           = trim($_POST['email'] ?? '');
        $phone           = trim($_POST['phone'] ?? '');
        $password        = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $vals = compact('username','email','phone');

        if (mb_strlen($username) < 3 || mb_strlen($username) > 80) $errors[] = 'Имя пользователя: от 3 до 80 символов.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email.';
        if (mb_strlen($password) < 8) $errors[] = 'Пароль: минимум 8 символов.';
        if ($password !== $passwordConfirm) $errors[] = 'Пароли не совпадают.';

        if (empty($errors)) {
            $db  = getDB();
            $chk = $db->prepare("SELECT id FROM users WHERE email = ?");
            $chk->execute([$email]);
            if ($chk->fetch()) {
                $errors[] = 'Этот email уже зарегистрирован.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins  = $db->prepare("INSERT INTO users (username, email, password_hash, role, phone) VALUES (?, ?, ?, 'buyer', ?)");
                $ins->execute([$username, $email, $hash, $phone ?: null]);
                flashMessage('success', 'Регистрация прошла успешно! Войдите в систему.');
                redirect(APP_URL . '/auth/login.php');
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
  <title>Регистрация | АвтоЗапчасть</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon.ico">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/plugins.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/template.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body>

<div class="login_register_wrap section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-6 col-md-8">
        <div style="background:#fff;border:1px solid #e0e0e0;border-radius:4px;padding:40px;">
          <div style="text-align:center;margin-bottom:24px;">
            <a href="<?= APP_URL ?>/index.php" class="logo_text_link">
              <span class="logo_text_main" style="font-size:1.6rem;">АВТО<span class="logo_accent">ЗАПЧАСТЬ</span></span>
            </a>
          </div>
          <div class="section_title text-center" style="margin-bottom:24px;">
            <h3>Создать аккаунт</h3>
            <p style="color:#777;font-size:0.875rem;">Регистрация доступна только для покупателей</p>
          </div>

          <?php if (!empty($errors)): ?>
          <ul class="az-error-list">
            <?php foreach ($errors as $err): ?><li><?= sanitize($err) ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>

          <form method="post" action="" class="account_form">
            <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
            <div class="row">
              <div class="col-md-6">
                <p>
                  <label>Имя пользователя <span>*</span></label>
                  <input type="text" name="username" value="<?= sanitize($vals['username']) ?>" placeholder="ivanov_auto" required>
                </p>
              </div>
              <div class="col-md-6">
                <p>
                  <label>Телефон</label>
                  <input type="tel" name="phone" value="<?= sanitize($vals['phone']) ?>" placeholder="+7 (___) ___-__-__">
                </p>
              </div>
            </div>
            <p>
              <label>Email <span>*</span></label>
              <input type="email" name="email" value="<?= sanitize($vals['email']) ?>" placeholder="your@email.ru" required>
            </p>
            <div class="row">
              <div class="col-md-6">
                <p>
                  <label>Пароль <span>*</span></label>
                  <input type="password" name="password" placeholder="Мин. 8 символов" required>
                </p>
              </div>
              <div class="col-md-6">
                <p>
                  <label>Повтор пароля <span>*</span></label>
                  <input type="password" name="password_confirm" placeholder="••••••••" required>
                </p>
              </div>
            </div>
            <div class="login_submit">
              <button type="submit">Зарегистрироваться</button>
            </div>
          </form>

          <div style="text-align:center;margin-top:16px;font-size:0.875rem;">
            Уже есть аккаунт? <a href="<?= APP_URL ?>/auth/login.php" class="az-link">Войти</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="<?= APP_URL ?>/assets/js/plugins.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
</body>
</html>
