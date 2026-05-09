<?php
require_once __DIR__ . '/config/config.php';
http_response_code(404);
$pageTitle = '404 — Страница не найдена';
require_once __DIR__ . '/includes/header.php';
?>

<div class="error_page_bg section_padding">
  <div class="container">
    <div class="error_section">
      <div class="row">
        <div class="col-12">
          <div class="error_form" style="text-align:center; padding:60px 20px;">
            <h1 style="font-size:8rem; font-weight:900; color:#ff6600; line-height:1; margin-bottom:16px;">404</h1>
            <h2 style="font-size:1.8rem; font-weight:700; color:#222; margin-bottom:16px; text-transform:uppercase;">
              Страница не найдена
            </h2>
            <p style="color:#777; font-size:0.9rem; margin-bottom:32px; line-height:1.8;">
              Запрашиваемая страница не существует, была удалена<br>или временно недоступна.
            </p>
            <form action="<?= APP_URL ?>/search/index.php" method="get"
                  style="max-width:400px; margin:0 auto 32px; display:flex; gap:0;">
              <input type="text" name="q" placeholder="Поиск запчастей..."
                     style="flex:1; border:1px solid #ddd; border-right:none; padding:12px 16px; font-size:0.875rem; outline:none; border-radius:2px 0 0 2px;">
              <button type="submit"
                      style="background:#ff6600; color:#fff; border:none; padding:12px 20px; cursor:pointer; border-radius:0 2px 2px 0;">
                <i class="ion-ios-search-strong"></i>
              </button>
            </form>
            <a href="<?= APP_URL ?>/index.php" class="btn btn-wh-2 btn-hover-dark">← На главную</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
