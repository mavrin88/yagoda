<template>
  <header class="header">
    <!-- Верхняя панель -->
    <div class="header-top">

      <div class="menu-logo-container">
        <!-- Кнопка меню -->
        <button class="header-menu-button" id="menuButton" @click="openDrawer" ref="menuButton">
          <img src="img/landing/header/menu.svg" alt="Меню" class="header-menu-icon" />
        </button>

        <!-- Логотип и текст -->
        <div class="header-logo">
          <a href="/" class="header-logo-link">
            <img src="img/landing/header/logo.svg" alt="Yagoda" class="header-logo-image" />
          </a>
          <div class="header-logo-text">
            оплата&nbsp;и&nbsp;чаевые в&nbsp;один&nbsp;клик
          </div>
        </div>
      </div>

      <div class="right-container">
        <!-- Кнопка подключения организации (видна на больших экранах) -->
        <div class="header-connect-button">
          <a href="/connect"><button class="connect-button">подключить организацию</button></a>
        </div>

        <!-- Кнопка входа/регистрации -->
        <div class="header-login">
          <a href="/login" class="header-login-link">
            Войти | Регистрация
          </a>
          <button class="header-login-button">
            <img src="img/landing/header/login.svg" alt="Войти" class="header-login-icon" />
          </button>
        </div>
      </div>

    </div>

    <!-- Боковое меню -->
    <div class="header-drawer" id="drawer" :class="{'open': isDrawerOpen}" ref="drawer">
      <div class="header-drawer-content">
        <div class="header-drawer-links">
          <div class="header-drawer-section s1">
            <a href="/login" class="header-drawer-link">
              <b>Вход</b> / регистрация пользователя
            </a>
            <a href="/connect" class="header-drawer-link">
              Подключить организацию
            </a>
          </div>

          <div class="header-drawer-section s2">
            <a href="#features" v-scroll="{ offset: 80 }"  class="header-drawer-link">Преимущества</a>
            <a href="#link2" v-scroll="{ offset: 80 }" class="header-drawer-link">Как это работает</a>
            <a href="#link3" v-scroll="{ offset: 80 }" class="header-drawer-link">Стоимость подключения</a>
            <a href="#link4" v-scroll="{ offset: 80 }" class="header-drawer-link">Частые вопросы</a>
            <a href="#link5" v-scroll="{ offset: 80 }" class="header-drawer-link">Контакты</a>
          </div>
        </div>

        <div class="header-drawer-footer">
          <a href="mailto:mastera@yagoda.team" class="header-drawer-email">
            mastera@yagoda.team
          </a>
          <div class="header-drawer-social">
            <a href="https://t.me/+79153690251" target="_blank">
              <img src="img/landing/tg.svg" alt="Telegram" class="header-drawer-social-icon" />
            </a>
            <a href="https://wa.me/79153690251" target="_blank">
              <img src="img/landing/ws.svg" alt="WhatsApp" class="header-drawer-social-icon" />
            </a>
          </div>
          <a href="/connect"><button class="menu-connect-button">Подключить организацию</button></a>

          <div class="header-drawer-social__links">
            <p>
              <a href="/pages/privacy" target="_blank" class="fs12">Политика конфиденциальности</a>
            </p>
            <p>
              <a href="/pages/user_agreement" target="_blank" class="fs12">Пользовательское соглашение</a>
            </p>
          </div>
        </div>
      </div>

      <button class="header-drawer-close" id="closeButton" @click="closeDrawer">
        <img src="img/landing/header/close.svg" alt="Закрыть" class="header-drawer-close-icon" />
      </button>
    </div>

  </header>
</template>
<script>
import scroll from '@/directives/scroll';

export default {
  name: "FrontHeaderComponent",
  directives: {
    scroll
  },
  data() {
    return {
      isDrawerOpen: false,
    }
  },
  mounted() {
    //region Из вёрстки
    document.addEventListener('click', this.handleDocumentClick);
    //endregion
  },

  beforeUnmount() {
    //region Из вёрстки
    document.removeEventListener('click', this.handleDocumentClick);
    //endregion
  },
  methods: {
    // Бургер
    openDrawer() {
      this.isDrawerOpen = true;
      // Добавляем класс no-scroll на корневой элемент (если он с id="root")
      document.getElementById("root")?.classList.add("no-scroll");
    },
    closeDrawer() {
      this.isDrawerOpen = false;
      document.getElementById("root")?.classList.remove("no-scroll");
    },
    handleDocumentClick(event) {
      const menuButton = this.$refs.menuButton;
      const drawer = this.$refs.drawer;
      // Если клик произошёл вне кнопки и панели, закрываем панель
      if (drawer && menuButton && !drawer.contains(event.target) && !menuButton.contains(event.target)) {
        this.closeDrawer();
      }
    },
  }
}
</script>
<style lang="scss" scoped>
@import "@scss/Landing/_mixins.scss";
@import "@scss/Landing/_variables.scss";
@import "@scss/Landing/buttons.scss";

html {
  scroll-behavior: smooth;
}

// Header

.header {
  background-color: $backgroundWhite;
  box-shadow: none;
  padding: 16px 0;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  z-index: 1000;


  .header-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
  }

  .menu-logo-container {
    display: flex;
    gap: 40px;
    @media (max-width: 768px) {
      gap: 8px;
    }
  }

  .right-container {
    display: flex;
    gap: 40px;
  }

  .header-menu-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
    margin-right: 16px;
  }

  .header-menu-icon {
    width: 24px;
    height: 24px;
  }

  .header-logo {
    display: flex;
    align-items: center;
    gap: 24px;
    @media (max-width: 768px) {
      gap: 16px;
    }
  }

  .header-logo-image {
    width: 180px;
    height: auto;
    @media (max-width: 768px) {
      width: 100px;
    }
  }

  .header-logo-text {
    font-size: 12px;
    line-height: 1.2;
    color: $textSecondary;

    @media (min-width: 768px) {
      font-size: 16px;
    }
  }

  .header-connect-button {
    display: none;

    @media (min-width: 1200px) {
      display: block;
    }
  }

  .header-connect-link {
    background-color: $darkBlue;
    color: $textInverted;
    border-radius: 10px;
    padding: 8px 16px;
    font-size: 14px;
    text-decoration: none;
  }

  .header-login {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .header-login-link {
    color: $textPrimary;
    text-decoration: none;
    font-size: 14px;
    display: none;
    font-weight: 600;

    @media (min-width: 768px) {
      display: block;
    }
  }

  .header-login-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
  }

  .header-login-icon {
    width: 24px;
    height: 24px;
  }

  .header-drawer {
    position: fixed;
    top: 0;
    left: -100%;
    width: 90vw;
    max-width: 400px;
    height: 100dvh;
    background-color: $greenLight;
    transition: left 0.3s ease;
    z-index: 1001;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }

  .header-drawer.open {
    left: 0;
  }

  .header-drawer-content {
    padding: 40px 32px 100px;
    display: flex;
    flex-direction: column;
    height: 100vh;
    max-height: 100vh;
    justify-content: space-between;
    gap: 40px;
  }

  .header-drawer-section {
    display: flex;
    flex-direction: column;
    gap: 26px;
  }

  .s1 {
    color: $darkBlue;
  }

  .s2 {
    color: $textInverted;
  }

  .header-drawer-links {
    display: flex;
    flex-direction: column;
    gap: 80px;
    margin-top: 60px;
  }

  .header-drawer-link {
    color: inherit;
    text-decoration: none;
    font-size: 16px;
  }

  .header-drawer-footer {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .header-drawer-email {
    color: $textInverted;
    text-decoration: none;
    font-size: 16px;
  }

  .header-drawer-social {
    display: flex;
    gap: 16px;
  }

  .header-drawer-social-icon {
    width: 24px;
    height: 24px;
  }

  .header-drawer-connect-button {
    background-color: $darkBlue;
    color: $textInverted;
    border-radius: 10px;
    padding: 16px;
    font-size: 14px;
    text-decoration: none;
    text-align: center;
  }

  .header-drawer-close {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0;
  }

  .header-drawer-close-icon {
    width: 56px;
    height: 56px;
  }
}
</style>
