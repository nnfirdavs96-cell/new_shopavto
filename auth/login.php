<?php
require_once dirname(__DIR__) . '/config/config.php';

if (isLoggedIn()) {
    $user = getCurrentUser();
    switch ($user['role']) {
        case 'superadmin': redirect(APP_URL . '/superadmin/index.php'); break;
        case 'admin':      redirect(APP_URL . '/admin/index.php'); break;
        case 'manager':    redirect(APP_URL . '/manager/index.php'); break;
        default:           redirect(APP_URL . '/buyer/index.php');
    }
}

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка безопасности. Обновите страницу.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $errors[] = 'Введите email и пароль.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_data'] = $user;
                flashMessage('success', 'Добро пожаловать, ' . $user['username'] . '!');
                switch ($user['role']) {
                    case 'superadmin': redirect(APP_URL . '/superadmin/index.php'); break;
                    case 'admin':      redirect(APP_URL . '/admin/index.php'); break;
                    case 'manager':    redirect(APP_URL . '/manager/index.php'); break;
                    default:           redirect(APP_URL . '/buyer/index.php');
                }
            } else {
                $errors[] = 'Неверный email или пароль.';
            }
        }
    }
}

$pageTitle = 'Войти';
$csrfToken = generateCsrfToken();
require_once dirname(__DIR__) . '/includes/header.php';
?>

<section class="login_register_wrap section">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="customer_login">
          <div class="row">

            <!-- LOGIN -->
            <div class="col-lg-6 col-md-6">
              <div class="account_form login">
                <h2>Войти</h2>

                <?php if (!empty($errors)): ?>
                  <ul class="az-error-list" style="margin-bottom:16px;">
                    <?php foreach ($errors as $e): ?><li><?= sanitize($e) ?></li><?php endforeach; ?>
                  </ul>
                <?php endif; ?>

                <form method="post" action="">
                  <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                  <p>
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" value="<?= sanitize($email) ?>"
                           placeholder="your@email.ru" required>
                  </p>
                  <p>
                    <label>Пароль <span>*</span></label>
                    <input type="password" name="password" placeholder="••••••••" required>
                  </p>
                  <div class="login_submit">
                    <button type="submit">Войти</button>
                  </div>
                </form>

                <!-- Demo accounts -->
                <div style="margin-top:20px; padding:14px; background:#f9f9f9; border:1px solid #eee; border-radius:4px; font-size:0.78rem;">
                  <div style="text-transform:uppercase; letter-spacing:0.1em; color:#999; margin-bottom:8px;">Тестовые аккаунты</div>
                  <?php
                  $demos = [
                    ['buyer@test.com',      'buyer'],
                    ['manager@test.com',    'manager'],
                    ['admin@avtozapchast.ru','admin'],
                    ['superadmin@avtozapchast.ru','superadmin'],
                  ];
                  foreach ($demos as $d): ?>
                    <div style="display:flex; justify-content:space-between; padding:3px 0; border-bottom:1px solid #eee; color:#555;">
                      <span><?= $d[0] ?></span>
                      <span class="az-role-badge az-role-<?= $d[1] ?>"><?= $d[1] ?></span>
                    </div>
                  <?php endforeach; ?>
                  <div style="color:#999; margin-top:6px;">Пароль для всех: <strong>Password123!</strong></div>
                </div>
              </div>
            </div>

            <!-- REGISTER -->
            <div class="col-lg-6 col-md-6">
              <div class="account_form register">
                <h2>Регистрация</h2>
                <p style="color:#777; font-size:0.875rem; margin-bottom:20px;">Создайте покупательский аккаунт для оформления заказов.</p>
                <form method="get" action="<?= APP_URL ?>/auth/register.php">
                  <p>
                    <label>Имя пользователя <span>*</span></label>
                    <input type="text" name="username" placeholder="ivanov_auto">
                  </p>
                  <p>
                    <label>Email <span>*</span></label>
                    <input type="email" name="email" placeholder="your@email.ru">
                  </p>
                  <div class="login_submit">
                    <a href="<?= APP_URL ?>/auth/register.php" class="btn btn-primary" style="padding:12px 28px; background:#ff6600; color:#fff; text-decoration:none; font-weight:600; text-transform:uppercase; font-size:0.875rem;">Зарегистрироваться</a>
                  </div>
                </form>
                <p style="font-size:0.8rem; color:#999; margin-top:16px;">// Роли менеджера и администратора назначаются супер-администратором.</p>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
