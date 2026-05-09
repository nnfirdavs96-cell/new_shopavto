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

$pageTitle = 'Регистрация';
$csrfToken = generateCsrfToken();
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
            <li>Регистрация</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<!-- customer login start -->
<div class="login_page_bg">
  <div class="container">
    <div class="customer_login">
      <div class="row">

        <!--register area start-->
        <div class="col-lg-6 col-md-6">
          <div class="account_form register">
            <h2>Регистрация</h2>

            <?php if (!empty($errors)): ?>
              <ul class="az-error-list" style="margin-bottom:16px;">
                <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
              </ul>
            <?php endif; ?>

            <form method="post" action="">
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
          </div>
        </div>
        <!--register area end-->

        <!--login link area start-->
        <div class="col-lg-6 col-md-6">
          <div class="account_form login">
            <h2>Уже есть аккаунт?</h2>
            <p style="color:#777; font-size:0.875rem; margin-bottom:18px;">
              Войдите в свой аккаунт, чтобы делать заказы и отслеживать их статус.
            </p>
            <div class="login_submit">
              <a href="<?= APP_URL ?>/auth/login.php" class="button">Войти</a>
            </div>
          </div>
        </div>
        <!--login link area end-->

      </div>
    </div>
  </div>
</div>
<!-- customer login end -->

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
