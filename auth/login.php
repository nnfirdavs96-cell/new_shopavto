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

<!--breadcrumbs area start-->
<div class="breadcrumbs_area">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb_content">
          <ul>
            <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
            <li>Мой аккаунт</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- customer login start -->
<div class="login_page_bg">
  <div class="container">
    <div class="customer_login">
      <div class="row">

        <!--login area start-->
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
                <input type="email" name="email" value="<?= sanitize($email) ?>" required>
              </p>
              <p>
                <label>Пароль <span>*</span></label>
                <input type="password" name="password" required>
              </p>
              <div class="login_submit">
                <label for="remember">
                  <input id="remember" type="checkbox">
                  Запомнить меня
                </label>
                <button type="submit">Войти</button>
              </div>
            </form>
          </div>

          <!-- Demo accounts -->
          <div style="margin-top:24px; padding:16px; background:#fff; border:1px solid #ebebeb; border-radius:5px; font-size:0.78rem;">
            <div style="text-transform:uppercase; letter-spacing:0.1em; color:#999; margin-bottom:10px; font-weight:600;">Тестовые аккаунты</div>
            <?php
            $demos = [
              ['buyer@test.com',                 'buyer'],
              ['manager@test.com',               'manager'],
              ['admin@avtozapchast.ru',          'admin'],
              ['superadmin@avtozapchast.ru',     'superadmin'],
            ];
            foreach ($demos as $d): ?>
              <div style="display:flex; justify-content:space-between; align-items:center; padding:5px 0; border-bottom:1px solid #f5f5f5; color:#555;">
                <span><?= $d[0] ?></span>
                <span class="az-role-badge az-role-<?= $d[1] ?>"><?= $d[1] ?></span>
              </div>
            <?php endforeach; ?>
            <div style="color:#777; margin-top:10px; font-size:0.75rem;">Пароль: <strong>Password123!</strong></div>
          </div>
        </div>

        <!--register area start-->
        <div class="col-lg-6 col-md-6">
          <div class="account_form register">
            <h2>Регистрация</h2>
            <p style="color:#777; font-size:0.875rem; margin-bottom:18px;">
              Создайте покупательский аккаунт для оформления заказов и доступа к личному кабинету.
            </p>
            <form action="<?= APP_URL ?>/auth/register.php" method="get">
              <p>
                <label>Email <span>*</span></label>
                <input type="email" name="email" placeholder="your@email.ru">
              </p>
              <p>
                <label>Имя пользователя <span>*</span></label>
                <input type="text" name="username" placeholder="ivanov_auto">
              </p>
              <div class="login_submit">
                <button type="submit">Регистрация</button>
              </div>
            </form>
            <p style="font-size:0.75rem; color:#999; margin-top:14px; padding:10px 14px; background:#f9f9f9; border-radius:4px;">
              // Роли менеджера и администратора назначаются супер-администратором.
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
