<?php
// header_admin.php — dark admin header for admin/manager/superadmin panels
$cartCount   = 0;
$currentUser = getCurrentUser();
$flash       = getFlashMessage();
$csrfToken   = generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?>АвтоЗапчасть Admin</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=JetBrains+Mono:wght@400;500;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/admin.css">
</head>
<body>

<header class="navbar" id="main-header">
  <div class="navbar-inner">
    <a href="<?= APP_URL ?>/index.php" class="nav-logo">
      <div class="nav-logo-icon"></div>
      АВТО<span>ЗАПЧАСТЬ</span>
    </a>
    <div class="nav-actions" style="margin-left:auto;display:flex;align-items:center;gap:12px;">
      <a href="<?= APP_URL ?>/index.php" style="color:var(--text-muted);font-size:0.8rem;">← Сайт</a>
      <?php if (isLoggedIn()): ?>
        <span style="font-family:var(--font-mono);font-size:0.75rem;color:var(--text-secondary);">
          <?= sanitize($currentUser['username'] ?? '') ?>
          <span class="role-badge <?= $currentUser['role'] ?>"><?= $currentUser['role'] ?></span>
        </span>
        <a href="<?= APP_URL ?>/auth/logout.php" style="color:var(--danger);font-size:0.8rem;">Выйти</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<?php if ($flash): ?>
<div class="flash-wrap" id="flash-container">
  <div class="flash flash-<?= sanitize($flash['type']) ?>">
    <span><?= sanitize($flash['message']) ?></span>
    <span class="flash-dismiss" onclick="this.parentElement.parentElement.remove()">✕</span>
  </div>
</div>
<?php endif; ?>

<main id="main-content">
