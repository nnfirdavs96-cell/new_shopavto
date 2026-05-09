<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'Контакты';
$sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Ошибка безопасности.';
    } else {
        $name    = sanitize(trim($_POST['name'] ?? ''));
        $email   = trim($_POST['email'] ?? '');
        $subject = sanitize(trim($_POST['subject'] ?? ''));
        $message = sanitize(trim($_POST['message'] ?? ''));
        if (empty($name))    $errors[] = 'Введите ваше имя.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Введите корректный email.';
        if (empty($message)) $errors[] = 'Введите сообщение.';
        if (empty($errors))  $sent = true;
    }
}

$csrfToken = generateCsrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<!--breadcrumbs area start-->
<div class="breadcrumbs_area">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb_content">
          <ul>
            <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
            <li>Контакты</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<div class="contact_page_bg section_padding">

  <!-- Map -->
  <div class="contact_map" style="margin-bottom:50px;">
    <div class="map-area">
      <iframe
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2244.8920604766857!2d37.61763!3d55.75396!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x46b54a50b315e573%3A0xa886bf5a3d9b2e68!2z0KLQstC10YDRgdC60LDRjyDRg9C7LiwgMiwg0JzQvtGB0LrQstCw!5e0!3m2!1sru!2sru!4v1715000000000!5m2!1sru!2sru"
        width="100%" height="380" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
  </div>

  <div class="container">
    <div class="contact_area">
      <div class="row">

        <!-- Contact info -->
        <div class="col-lg-6 col-md-12">
          <div class="contact_message content">
            <h3>Свяжитесь с нами</h3>
            <p style="color:#777; font-size:0.9rem; line-height:1.8; margin-bottom:24px;">
              Наши специалисты готовы помочь с подбором запчастей и ответить на любые вопросы. Работаем без выходных.
            </p>
            <ul style="list-style:none; padding:0;">
              <li style="display:flex; align-items:flex-start; gap:12px; margin-bottom:16px; color:#555; font-size:0.9rem;">
                <i class="fa fa-map-marker" style="color:#ff6600; margin-top:3px; min-width:16px;"></i>
                <span>г. Москва, ул. Автомобильная, д. 1, офис 201</span>
              </li>
              <li style="display:flex; align-items:center; gap:12px; margin-bottom:16px; font-size:0.9rem;">
                <i class="fa fa-phone" style="color:#ff6600; min-width:16px;"></i>
                <a href="tel:88005553535" style="color:#555; text-decoration:none;">+7 (800) 555-35-35</a>
              </li>
              <li style="display:flex; align-items:center; gap:12px; margin-bottom:16px; font-size:0.9rem;">
                <i class="fa fa-envelope-o" style="color:#ff6600; min-width:16px;"></i>
                <a href="mailto:info@avtozapchast.ru" style="color:#555; text-decoration:none;">info@avtozapchast.ru</a>
              </li>
              <li style="display:flex; align-items:center; gap:12px; font-size:0.9rem;">
                <i class="fa fa-clock-o" style="color:#ff6600; min-width:16px;"></i>
                <span style="color:#555;">Пн–Пт: 9:00–20:00, Сб–Вс: 10:00–18:00</span>
              </li>
            </ul>
          </div>
        </div>

        <!-- Contact form -->
        <div class="col-lg-6 col-md-12">
          <div class="contact_message form">
            <h3>Напишите нам</h3>

            <?php if ($sent): ?>
              <div style="background:#d4edda; border:1px solid #c3e6cb; border-radius:4px; padding:16px; color:#155724; font-size:0.9rem;">
                Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.
              </div>
            <?php else: ?>

              <?php if (!empty($errors)): ?>
                <ul class="az-error-list" style="margin-bottom:16px;">
                  <?php foreach ($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?>
                </ul>
              <?php endif; ?>

              <form method="post" action="">
                <input type="hidden" name="csrf_token" value="<?= sanitize($csrfToken) ?>">
                <p>
                  <label>Ваше имя <span>*</span></label>
                  <input name="name" type="text" placeholder="Имя *"
                         value="<?= sanitize($_POST['name'] ?? '') ?>">
                </p>
                <p>
                  <label>Email <span>*</span></label>
                  <input name="email" type="email" placeholder="Email *"
                         value="<?= sanitize($_POST['email'] ?? '') ?>">
                </p>
                <p>
                  <label>Тема</label>
                  <input name="subject" type="text" placeholder="Тема сообщения"
                         value="<?= sanitize($_POST['subject'] ?? '') ?>">
                </p>
                <div class="contact_textarea">
                  <label>Сообщение <span>*</span></label>
                  <textarea name="message" placeholder="Ваше сообщение *"
                            class="form-control2" rows="5"><?= sanitize($_POST['message'] ?? '') ?></textarea>
                </div>
                <button type="submit">Отправить</button>
              </form>

            <?php endif; ?>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
