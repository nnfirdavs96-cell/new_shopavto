<?php
$currentUser = getCurrentUser();
$flash       = getFlashMessage();
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' | ' : '' ?>АвтоЗапчасть — Панель</title>
  <link rel="shortcut icon" type="image/x-icon" href="<?= APP_URL ?>/assets/img/favicon.ico">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/plugins.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/template.css">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/custom.css">
</head>
<body>

<!-- Admin top bar -->
<div style="background:#222; padding:10px 0; border-bottom:3px solid #ff6600; position:sticky; top:0; z-index:1000;">
  <div class="container">
    <div style="display:flex; align-items:center; justify-content:space-between;">
      <a href="<?= APP_URL ?>/index.php" style="font-size:1.2rem; font-weight:900; color:#fff; text-decoration:none; letter-spacing:2px;">
        АВТО<span style="color:#ff6600;">ЗАПЧАСТЬ</span>
        <span style="font-size:0.6rem; color:#888; font-weight:400; margin-left:10px; text-transform:uppercase; letter-spacing:1px;">панель управления</span>
      </a>
      <div style="display:flex; align-items:center; gap:20px; font-size:0.85rem;">
        <a href="<?= APP_URL ?>/index.php" style="color:#aaa; text-decoration:none;">← Сайт</a>
        <?php if (hasRole(['manager','superadmin'])): ?>
          <a href="<?= APP_URL ?>/manager/index.php" style="color:#aaa; text-decoration:none;">Менеджер</a>
        <?php endif; ?>
        <?php if (hasRole(['admin','superadmin'])): ?>
          <a href="<?= APP_URL ?>/admin/index.php" style="color:#aaa; text-decoration:none;">Админ</a>
        <?php endif; ?>
        <?php if (hasRole('superadmin')): ?>
          <a href="<?= APP_URL ?>/superadmin/index.php" style="color:#aaa; text-decoration:none;">Супер-Админ</a>
        <?php endif; ?>
        <span style="color:#fff; font-weight:600;"><?= sanitize($currentUser['username'] ?? '') ?></span>
        <span class="az-role-badge az-role-<?= sanitize($currentUser['role']) ?>"><?= sanitize($currentUser['role']) ?></span>
        <a href="<?= APP_URL ?>/auth/logout.php" style="color:#ff6600; text-decoration:none; font-weight:600;">Выйти</a>
      </div>
    </div>
  </div>
</div>

<?php if ($flash): ?>
<div class="az-flash az-flash-<?= sanitize($flash['type']) ?>" id="az-flash">
  <div class="container">
    <div class="az-flash-inner">
      <span><?= sanitize($flash['message']) ?></span>
      <button onclick="document.getElementById('az-flash').remove()" class="az-flash-close">&times;</button>
    </div>
  </div>
</div>
<?php endif; ?>

<div style="padding: 32px 0 60px;">
<div class="container">
