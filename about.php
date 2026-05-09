<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'О нас';
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
            <li>О нас</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<div class="about_bg_area section_padding">
  <div class="container">

    <!-- About section -->
    <section class="about_section mb-60">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-6">
          <div class="about_thumb">
            <img src="<?= APP_URL ?>/assets/img/about/about1.jpg" alt="О компании АвтоЗапчасть">
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="about_content" style="padding-left:30px;">
            <h2 style="font-size:1.8rem; font-weight:700; color:#222; margin-bottom:16px; line-height:1.3;">
              Профессиональный подбор<br>и продажа <span style="color:#ff6600;">автозапчастей</span>
            </h2>
            <p style="color:#777; font-size:0.9rem; line-height:1.8; margin-bottom:16px;">
              АвтоЗапчасть — это интернет-магазин оригинальных и качественных автомобильных запчастей. Мы работаем с ведущими производителями и поставщиками, гарантируя подлинность каждого товара.
            </p>
            <p style="color:#777; font-size:0.9rem; line-height:1.8; margin-bottom:24px;">
              Более 10 000 наименований запчастей для всех марок и моделей автомобилей. Быстрая доставка по всей России. Профессиональная консультация по подбору деталей по VIN-номеру.
            </p>
            <div class="about_signature">
              <img src="<?= APP_URL ?>/assets/img/about/about-us-signature.png" alt="">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Why choose us -->
    <div class="choseus_area" style="background:#f5f5f5; padding:50px 40px; border-radius:4px; margin-bottom:50px;">
      <div class="row">
        <div class="col-12 text-center" style="margin-bottom:30px;">
          <h2 style="font-size:1.6rem; font-weight:700; color:#222;">Почему выбирают <span style="color:#ff6600;">нас</span></h2>
        </div>
        <div class="col-lg-4 col-md-4">
          <div class="single_chose" style="text-align:center; padding:20px;">
            <div class="chose_icone" style="margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/About_icon1.png" alt="">
            </div>
            <div class="chose_content">
              <h3 style="font-weight:700; margin-bottom:10px;">Оригинальные запчасти</h3>
              <p style="color:#777; font-size:0.875rem; line-height:1.7;">Только сертифицированные детали от проверенных производителей. Гарантия качества на каждый товар.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4">
          <div class="single_chose" style="text-align:center; padding:20px;">
            <div class="chose_icone" style="margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/About_icon2.png" alt="">
            </div>
            <div class="chose_content">
              <h3 style="font-weight:700; margin-bottom:10px;">Быстрая доставка</h3>
              <p style="color:#777; font-size:0.875rem; line-height:1.7;">Доставка по Москве за 24 часа, по России — 2–5 рабочих дней. Отслеживание заказа в реальном времени.</p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4">
          <div class="single_chose" style="text-align:center; padding:20px;">
            <div class="chose_icone" style="margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/About_icon3.png" alt="">
            </div>
            <div class="chose_content">
              <h3 style="font-weight:700; margin-bottom:10px;">Техподдержка 24/7</h3>
              <p style="color:#777; font-size:0.875rem; line-height:1.7;">Наши специалисты помогут с подбором запчастей по VIN-номеру и ответят на любые вопросы.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Gallery / Info blocks -->
    <div class="about_gallery_section mb-55">
      <div class="row">
        <div class="col-lg-4 col-md-4">
          <article class="single_gallery_section" style="margin-bottom:30px;">
            <div style="overflow:hidden; border-radius:4px; margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/about2.jpg" alt="" style="width:100%; height:220px; object-fit:cover;">
            </div>
            <h3 style="font-weight:700; margin-bottom:10px;">Чем мы занимаемся?</h3>
            <p style="color:#777; font-size:0.875rem; line-height:1.7;">Подбор и продажа автозапчастей для легковых и грузовых автомобилей всех марок. Работаем с физическими и юридическими лицами.</p>
          </article>
        </div>
        <div class="col-lg-4 col-md-4">
          <article class="single_gallery_section" style="margin-bottom:30px;">
            <div style="overflow:hidden; border-radius:4px; margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/about3.jpg" alt="" style="width:100%; height:220px; object-fit:cover;">
            </div>
            <h3 style="font-weight:700; margin-bottom:10px;">Наша миссия</h3>
            <p style="color:#777; font-size:0.875rem; line-height:1.7;">Сделать качественные автозапчасти доступными для каждого. Мы строим долгосрочные отношения с клиентами, основанные на доверии.</p>
          </article>
        </div>
        <div class="col-lg-4 col-md-4">
          <article class="single_gallery_section" style="margin-bottom:30px;">
            <div style="overflow:hidden; border-radius:4px; margin-bottom:16px;">
              <img src="<?= APP_URL ?>/assets/img/about/about4.jpg" alt="" style="width:100%; height:220px; object-fit:cover;">
            </div>
            <h3 style="font-weight:700; margin-bottom:10px;">История компании</h3>
            <p style="color:#777; font-size:0.875rem; line-height:1.7;">Основана в 2010 году. За 15 лет работы мы выполнили более 500 000 заказов и стали одним из крупнейших онлайн-магазинов запчастей в России.</p>
          </article>
        </div>
      </div>
    </div>

    <!-- FAQ + Testimonials -->
    <div class="faq-client-say-area">
      <div class="row">
        <div class="col-lg-6 col-md-6">
          <div class="faq-client_title" style="margin-bottom:24px;">
            <h2 style="font-size:1.4rem; font-weight:700; color:#222;">Часто задаваемые вопросы</h2>
          </div>
          <div class="faq-style-wrap" id="faq-about">
            <?php
            $faqs = [
              ['Как подобрать запчасть по VIN?', 'Укажите VIN-номер автомобиля в строке поиска или свяжитесь с нашими специалистами — мы поможем подобрать нужную деталь.'],
              ['Какая гарантия на запчасти?', 'На все оригинальные запчасти предоставляется гарантия производителя. На аналоги — от 6 до 12 месяцев в зависимости от производителя.'],
              ['Как осуществляется доставка?', 'Доставка по Москве — курьером за 24 часа. По России — Почтой России или СДЭК за 2–5 рабочих дней.'],
              ['Можно ли вернуть товар?', 'Да, возврат возможен в течение 14 дней при сохранении товарного вида и упаковки. Подробнее — в разделе "Гарантия".'],
            ];
            foreach ($faqs as $i => $faq): ?>
            <div class="panel panel-default" style="border:1px solid #eee; border-radius:4px; margin-bottom:8px; overflow:hidden;">
              <div class="panel-heading" style="background:#f9f9f9;">
                <h5 class="panel-title" style="margin:0;">
                  <a role="button" data-bs-toggle="collapse" href="#faq-about-<?= $i ?>"
                     style="display:block; padding:14px 16px; text-decoration:none; color:#222; font-size:0.9rem; font-weight:600;">
                    <?= $faq[0] ?>
                  </a>
                </h5>
              </div>
              <div id="faq-about-<?= $i ?>" class="collapse <?= $i===0?'show':'' ?>">
                <div class="panel-body" style="padding:14px 16px; font-size:0.875rem; color:#777; line-height:1.7;">
                  <?= $faq[1] ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="faq-client_title" style="margin-bottom:24px;">
            <h2 style="font-size:1.4rem; font-weight:700; color:#222;">Отзывы клиентов</h2>
          </div>
          <div class="testimonial-two owl-carousel">
            <?php
            $testimonials = [
              ['Алексей К.', 'Постоянный клиент', 'Заказываю запчасти уже 3 года. Всегда оригинал, быстрая доставка. Рекомендую!', 'testimonial1.jpg'],
              ['Мария С.', 'Автовладелец', 'Отличный сервис! Помогли подобрать деталь по VIN, доставили на следующий день.', 'testimonial2.jpg'],
              ['Дмитрий В.', 'Автомеханик', 'Работаю с АвтоЗапчасть по оптовым ценам. Качество и сроки всегда на высоте.', 'testimonial3.jpg'],
            ];
            foreach ($testimonials as $t): ?>
            <div class="testimonial-wrap-two text-center">
              <div class="quote-container" style="padding:30px; background:#f9f9f9; border:1px solid #eee; border-radius:4px; text-align:center;">
                <div style="width:70px; height:70px; border-radius:50%; overflow:hidden; margin:0 auto 16px;">
                  <img src="<?= APP_URL ?>/assets/img/about/<?= $t[3] ?>" alt="" style="width:100%; height:100%; object-fit:cover;">
                </div>
                <p style="font-style:italic; color:#555; font-size:0.9rem; line-height:1.7; margin-bottom:16px;">"<?= $t[2] ?>"</p>
                <h6 style="font-weight:700; margin-bottom:4px;"><?= $t[0] ?></h6>
                <p style="font-size:0.8rem; color:#999;"><?= $t[1] ?></p>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
