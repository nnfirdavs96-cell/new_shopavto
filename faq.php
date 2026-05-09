<?php
require_once __DIR__ . '/config/config.php';
$pageTitle = 'FAQ';
require_once __DIR__ . '/includes/header.php';

$faqs = [
  ['Как подобрать запчасть по номеру детали?', 'Введите номер детали (OEM/артикул) в строку поиска на главной странице или в разделе "Поиск". Система найдёт точное совпадение или аналоги.'],
  ['Как подобрать запчасть по VIN-номеру?', 'Свяжитесь с нами по телефону +7 (800) 555-35-35 или email info@avtozapchast.ru, укажите VIN и нужную деталь — наши специалисты подберут подходящий вариант.'],
  ['Какая гарантия на запчасти?', 'На оригинальные запчасти действует гарантия производителя. На аналоги — от 6 до 12 месяцев. Подробные условия гарантии уточняйте при оформлении заказа.'],
  ['Как быстро доставляется заказ?', 'По Москве — курьером за 24 часа. По России — 2–5 рабочих дней через СДЭК или Почту России. Точные сроки зависят от региона и наличия товара на складе.'],
  ['Можно ли вернуть или обменять товар?', 'Да, возврат и обмен возможны в течение 14 дней с момента получения при сохранении товарного вида, упаковки и всех документов. Запчасти, бывшие в использовании, возврату не подлежат.'],
  ['Как оплатить заказ?', 'Принимаем оплату банковской картой онлайн, через СБП, а также наличными курьеру при доставке по Москве. Безналичный расчёт для юридических лиц.'],
  ['Работаете ли вы с юридическими лицами?', 'Да, работаем с организациями и ИП. Предоставляем все необходимые документы: счёт, накладная, счёт-фактура. Свяжитесь с нами для обсуждения условий сотрудничества.'],
  ['Как отследить статус заказа?', 'После оформления заказа вы можете отслеживать его статус в личном кабинете в разделе "Мои заказы". При изменении статуса вы получите уведомление.'],
];
?>

<!--breadcrumbs area start-->
<div class="breadcrumbs_area">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="breadcrumb_content">
          <ul>
            <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
            <li>FAQ</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<!--breadcrumbs area end-->

<div class="faq_page_bg section_padding">
  <div class="container">

    <div class="faq_content_area" style="margin-bottom:40px;">
      <div class="row">
        <div class="col-12 text-center">
          <h4 style="font-size:1.5rem; font-weight:700; color:#222; margin-bottom:12px;">
            Часто задаваемые вопросы
          </h4>
          <p style="color:#777; font-size:0.9rem; max-width:600px; margin:0 auto;">
            Здесь собраны ответы на наиболее распространённые вопросы о работе магазина, доставке, оплате и гарантиях.
          </p>
        </div>
      </div>
    </div>

    <div class="accordion_area">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div id="faq-accordion" class="card__accordion">
            <?php foreach ($faqs as $i => $faq): ?>
            <div class="card card_dipult" style="margin-bottom:8px; border:1px solid #eee; border-radius:4px; overflow:hidden;">
              <div class="card-header card_accor" id="faq-heading-<?= $i ?>"
                   style="background:<?= $i===0?'#ff6600':'#f9f9f9' ?>; padding:0;">
                <button class="btn btn-link" data-bs-toggle="collapse"
                        data-bs-target="#faq-collapse-<?= $i ?>"
                        style="width:100%; text-align:left; padding:16px 20px; text-decoration:none;
                               color:<?= $i===0?'#fff':'#222' ?>; font-weight:600; font-size:0.9rem;
                               display:flex; justify-content:space-between; align-items:center; border:none; background:none;">
                  <span><?= $faq[0] ?></span>
                  <i class="fa <?= $i===0?'fa-minus':'fa-plus' ?>"></i>
                </button>
              </div>
              <div id="faq-collapse-<?= $i ?>" class="collapse <?= $i===0?'show':'' ?>"
                   data-bs-parent="#faq-accordion">
                <div class="card-body" style="padding:16px 20px; font-size:0.875rem; color:#555; line-height:1.8; border-top:1px solid #eee;">
                  <?= $faq[1] ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div style="text-align:center; margin-top:40px;">
            <p style="color:#777; margin-bottom:16px;">Не нашли ответ на свой вопрос?</p>
            <a href="<?= APP_URL ?>/contact.php" class="btn btn-wh-2 btn-hover-dark">Связаться с нами</a>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
