<template>
  <div class="connect-page-container">
    <FrontHeaderComponent />

    <div class="wrapper">
      <main class="main-content">
        <div class="content-wrapper">
          <div class="title">
            Подключить организацию
          </div>
          <div class="description">
            Наш стандарт: от момента регистрации до вашей первой продажи через нас - 40 минут. Все просто.
          </div>

          <!-- Блок 1: Регистрация -->
          <div class="info-block">
            <b>1. Проходите через мастер регистрации</b>
            <ul>
              <li>регистрируете номер телефона</li>
              <li>устанавливаете имя, аву, номер карты</li>
              <li>заводите данные организации</li>
              <li>добавляете расчетный счет, куда будете получать деньги за услуги.</li>
            </ul>
          </div>

          <!-- Кнопка подключения -->
          <a class="connect-button text-center" href="/login">
            Мастер регистрации организации
          </a>

          <!-- Блоки 2 и 3: Договор и Подключение -->
          <div class="info-blocks-container">
            <!-- Блок 2: Договор -->
            <div class="info-block">
              <p><b>2. Заключаем договор</b></p>
              <p>Свяжитесь с нашим отделом заботы.</p>
              <p>
                Вам пришлют договор.<br>
                От вас печать, подпись, и отправляете нам скан договора.
              </p>
              <div class="contact-info">
                <b>Отдел заботы:</b>
                <a href="tel:+74992132233" class="contact-link">+7 (499) 213-22-33</a>
                <div class="social-links">
                  <a href="https://t.me/+79153690251" target="_blank" class="social-link">
                    <img src="img/landing/tg.svg" alt="Telegram" class="social-icon" /> Telegram
                  </a>
                  <a href="https://wa.me/79153690251" target="_blank" class="social-link">
                    <img src="img/landing/ws.svg" alt="WhatsApp" class="social-icon" /> WhatsApp
                  </a>
                </div>
              </div>
            </div>

            <!-- Блок 3: Подключение -->
            <div class="info-block">
              <p><b>3. Подключение</b></p>
              <p>
                Отдел заботы получает договор, проверяет данные и выдает вам доступ к возможности проводить платежи.
              </p>
              <p><b>Все готово.</b> Пользуйтесь.</p>
            </div>
          </div>
        </div>
      </main>
    </div>

    <FrontFooterComponent/>
  </div>
</template>
<script setup>
import FrontHeaderComponent from '../Components/Front/FrontHeaderComponent.vue'
import FrontFooterComponent from '../Components/Front/FrontFooterComponent.vue'
</script>
<style lang="scss" scoped>
@import "@scss/Landing/_mixins.scss";
@import "@scss/Landing/_variables.scss";
@import "@scss/Landing/buttons.scss";

.connect-page-container {
  padding-top: 80px;

  .main-content {
    padding: 80px 32px 40px;
    @include container;
    @media (max-width: 768px) {
      padding: 32px 16px 40px;
    }
  }

  .content-wrapper {
    display: flex;
    flex-direction: column;
    gap: 40px;
  }

  .title {
    font-size: 40px;
    color: $greenLight;

    @media (max-width: 768px) {
      font-size: 30px;
    }
  }

  .description {
    font-size: 20px;
    color: $darkBlue;

    @media (max-width: 768px) {
      font-size: 16px;
    }
  }

  .info-block {
    display: flex;
    flex-direction: column;
    gap: 20px;
    background-color: $backgroundBlueLight;
    color: $darkBlue;
    padding: 40px;
    border-radius: 20px;
    font-size: 20px;

    ul {
      padding-left: 20px;
      margin: 10px 0;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    p {
      margin: 0;
    }

    @media (max-width: 768px) {
      font-size: 16px;
      padding: 15px;
    }
  }

  .info-blocks-container {
    display: flex;
    gap: 30px;

    @media (max-width: 768px) {
      flex-direction: column;
    }
  }

  .contact-info {
    margin-top: 10px;
  }

  .contact-link {
    color: $darkBlue;
    text-decoration: none;
  }

  .social-links {
    display: flex;
    gap: 10px;
    margin-top: 10px;
  }

  .social-link {
    color: $darkBlue;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .social-icon {
    width: 20px;
    height: 20px;
  }

}
</style>
