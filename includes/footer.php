
<!--footer area start-->
<footer class="footer_widgets">

  <!--shipping area start-->
  <div class="shipping_area">
    <div class="container">
      <div class="shipping_inner">
        <div class="single_shipping">
          <div class="shipping_icone"><img src="<?= APP_URL ?>/assets/img/about/shipping1.png" alt=""></div>
          <div class="shipping_content"><h4>Быстрая доставка</h4><p>Москва — 24 часа</p></div>
        </div>
        <div class="single_shipping">
          <div class="shipping_icone"><img src="<?= APP_URL ?>/assets/img/about/shipping2.png" alt=""></div>
          <div class="shipping_content"><h4>Гарантия качества</h4><p>Оригинальные запчасти</p></div>
        </div>
        <div class="single_shipping">
          <div class="shipping_icone"><img src="<?= APP_URL ?>/assets/img/about/shipping3.png" alt=""></div>
          <div class="shipping_content"><h4>Точный подбор</h4><p>По номеру детали</p></div>
        </div>
        <div class="single_shipping">
          <div class="shipping_icone"><img src="<?= APP_URL ?>/assets/img/about/shipping4.png" alt=""></div>
          <div class="shipping_content"><h4>Техподдержка</h4><p>Пн–Пт 9:00–20:00</p></div>
        </div>
        <div class="single_shipping">
          <div class="shipping_icone"><img src="<?= APP_URL ?>/assets/img/about/shipping5.png" alt=""></div>
          <div class="shipping_content"><h4>Оплата онлайн</h4><p>Безопасные платежи</p></div>
        </div>
      </div>
    </div>
  </div>
  <!--shipping area end-->

  <div class="footer_top">
    <div class="container">
      <div class="row">
        <div class="col-lg-3">
          <div class="widgets_container">
            <h3>КОНТАКТЫ</h3>
            <div class="footer_contact">
              <div class="footer_contact_inner">
                <div class="contact_icone"><img src="<?= APP_URL ?>/assets/img/icon/icon-phone.png" alt=""></div>
                <div class="contact_text">
                  <p>Бесплатно 24/7:<br><strong><a href="tel:88005553535">+7 (800) 555-35-35</a></strong></p>
                </div>
              </div>
              <p>г. Москва, ул. Автомобильная, д. 1<br>info@avtozapchast.ru</p>
            </div>
          </div>
        </div>
        <div class="col-lg-9">
          <div class="footer_col_container">
            <div class="widgets_container widget_menu">
              <h3>Каталог</h3>
              <div class="footer_menu">
                <ul>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=dvigatel">Двигатель</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=tormoznaya-sistema">Тормозная система</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=podveska">Подвеска</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=elektrika">Электрика</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=kuzov">Кузов</a></li>
                  <li><a href="<?= APP_URL ?>/catalog/index.php?category=transmissiya">Трансмиссия</a></li>
                </ul>
              </div>
            </div>
            <div class="widgets_container widget_menu">
              <h3>Покупателям</h3>
              <div class="footer_menu">
                <ul>
                  <li><a href="<?= APP_URL ?>/buyer/index.php">Мой кабинет</a></li>
                  <li><a href="<?= APP_URL ?>/buyer/cart.php">Корзина</a></li>
                  <li><a href="<?= APP_URL ?>/buyer/orders.php">Мои заказы</a></li>
                  <li><a href="<?= APP_URL ?>/buyer/profile.php">Профиль</a></li>
                </ul>
              </div>
            </div>
            <div class="widgets_container widget_menu">
              <h3>Аккаунт</h3>
              <div class="footer_menu">
                <ul>
                  <li><a href="<?= APP_URL ?>/auth/login.php">Войти</a></li>
                  <li><a href="<?= APP_URL ?>/auth/register.php">Регистрация</a></li>
                  <li><a href="<?= APP_URL ?>/index.php">Главная</a></li>
                  <li><a href="<?= APP_URL ?>/search/index.php">Поиск</a></li>
                </ul>
              </div>
            </div>
            <div class="widgets_container widget_menu">
              <h3>О нас</h3>
              <div class="footer_menu">
                <ul>
                  <li><a href="<?= APP_URL ?>/about.php">О компании</a></li>
                  <li><a href="<?= APP_URL ?>/faq.php">Доставка и оплата</a></li>
                  <li><a href="<?= APP_URL ?>/faq.php">FAQ</a></li>
                  <li><a href="<?= APP_URL ?>/contact.php">Контакты</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="footer_bottom">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 col-md-6">
          <div class="copyright_area">
            <p>&copy; <?= date('Y') ?> АвтоЗапчасть. Все права защищены. PHP/MySQL · PDO · CSRF Protected</p>
          </div>
        </div>
        <div class="col-lg-6 col-md-6">
          <div class="footer_payment text-right">
            <img src="<?= APP_URL ?>/assets/img/icon/payment.png" alt="Способы оплаты">
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>
<!--footer area end-->

<script src="<?= APP_URL ?>/assets/js/plugins.js"></script>
<script src="<?= APP_URL ?>/assets/js/main.js"></script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
