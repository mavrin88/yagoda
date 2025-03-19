<template>
  <section class="block-04-section" id="features">
    <h1 class="block-04-title">Преимущества</h1>
    <div class="tabs-container">
      <div
        class="tab"
        data-tab="1"
        :data-active="activeTab === '1' ? 'true' : 'false'"
        @click="setActiveTab('1')"
      >
        владелец
      </div>
      <div
        class="tab"
        data-tab="2"
        :data-active="activeTab === '2' ? 'true' : 'false'"
        @click="setActiveTab('2')"
      >
        мастер
      </div>
      <div
        class="tab"
        data-tab="3"
        :data-active="activeTab === '3' ? 'true' : 'false'"
        @click="setActiveTab('3')"
      >
        администратор
      </div>
      <div
        class="tab"
        data-tab="4"
        :data-active="activeTab === '4' ? 'true' : 'false'"
        @click="setActiveTab('4')"
      >
        клиент
      </div>
    </div>

    <div class="tabs">
      <!-- Вкладка 1 -->
      <div
        class="block-04-tab block-04-tab-01"
        :class="{ 'block-04-tab-open': activeTab === '1' }"
      >
        <div class="block-04-tab-01-content">
          <div class="block-04-tab-01-left">
            <div class="block-04-tab-01-left-title">
              <b>Экономия</b> на&nbsp;эквайринге <b>1,8%</b>
            </div>
            <div class="block-04-tab-01-left-subtitle">
              вместо <span class="block-04-tab-01-left-strikethrough">2,5%</span>
            </div>
            <div class="block-04-tab-01-left-percent">
              <b>0</b>,69<span class="block-04-tab-01-left-percent-symbol">%</span>
            </div>
            <div class="block-04-tab-01-left-text">
              одинаково для карт и СБП
            </div>
          </div>
          <div class="block-04-tab-01-middle">
            <div class="block-04-tab-01-middle-title">
              <b>Повышение дохода персонала</b> <u>без</u> вашего участия
            </div>
            <div class="block-04-tab-01-middle-text">
              <p><b>Мастера получат чаевых в 2-5 раз больше.</b></p>
              <p>
                Потому что клиент оплачивает услуги и чаевые одним платежом, с
                предустановленными на экране чаевыми.
              </p>
              <p>
                Возможность разделить повышенные чаевые еще и между администратором
                и персоналом, используя нашу уникальную систему распределения чаевых.
              </p>
            </div>
          </div>
          <div class="block-04-tab-01-right">
            <div class="block-04-tab-01-right-title">
              <b>Уникальная</b> функция нашего сервиса
            </div>
            <div class="block-04-tab-01-right-subtitle">
              <img src="img/landing/block-04/icon-scales.svg" alt="Весы" />
              <span>Разделение чаевых между сотрудниками</span>
            </div>
            <div class="block-04-tab-01-right-text">
              <p>
                Возможность разделить повышенные чаевые еще и между администратором и
                персоналом, используя нашу <b>уникальную систему шеринга чаевых</b>.
              </p>
              <p>
                К примеру Мастер получит <b>80%</b> от чаевых, Администратор <b>15%</b>,
                Уборщица <b>5%</b>.
              </p>
              <p>Все это - настраиваете вы на свое усмотрение.</p>
              <p>
                Таким образом, <b>все от кого зависит результат - начинают уделять клиенту больше внимания и заботы</b>.
              </p>
              <p>
                Это помогает укрепить доверие клиентов и желание вернуться, что приведет к росту вашей прибыли.
              </p>
            </div>
          </div>
        </div>
        <div class="block-04-tab-01-bottom"></div>
        <img src="img/landing/block-04/bg1.png" class="block-04-tab-01-bg-image" />
      </div>

      <!-- Вкладка 2 -->
      <div
        class="block-04-tab block-04-tab-02"
        :class="{ 'block-04-tab-open': activeTab === '2' }"
      >
        <div class="block-04-tab-02-content">
          <div class="block-04-tab-02-left">
            <div class="block-04-tab-02-left-top">
              <img src="img/landing/block-04/icon-cup.svg" alt="Кубок" />
              <div class="block-04-tab-02-left-top-title">
                Увеличение объема чаевых <b>от 2 до 5 раз!</b>
              </div>
              <div class="block-04-tab-02-left-top-subtitle">
                За счет того что оплата услуг и чаевых производится одним платежом.
              </div>
            </div>
            <div class="block-04-tab-02-left-bottom">
              <p>
                Зачастую, клиент оплатив услуги <b>уходит не желая тратить время</b> на оплату чаевых.
              </p>
              <p>
                Для клиента барьер - совершать отдельные, дополнительные действия по оплате чаевых.
              </p>
              <p>
                <b>В нашем случае, барьера нет.</b> Клиент оплачивая услугу, тут же указывает сколько чаевых оставить.
              </p>
              <p>И оплачивает все одним платежом. Быстро и удобно.</p>
            </div>
          </div>
          <div class="block-04-tab-02-right">
            <img src="img/landing/block-04/4-1.png" alt="Фон" class="block-04-tab-02-bg" />
          </div>
        </div>
      </div>

      <!-- Вкладка 3 -->
      <div
        class="block-04-tab block-04-tab-03"
        :class="{ 'block-04-tab-open': activeTab === '3' }"
      >
        <div class="block-04-tab-03-content">
          <div class="block-04-tab-03-left">
            <div class="block-04-tab-03-left-title">
              Получайте дополнительно <b>10&nbsp;000 - 50&nbsp;000₽</b>, как&nbsp;часть от&nbsp;общих чаевых.
            </div>
            <div class="block-04-tab-03-left-text">
              <p>
                Клиенты, оплачивающие через Yagoda, оставляют чаевые чаще от <b>2 до 5 раз. В 80-90% случаях</b>.
              </p>
              <p>
                Рекомендуя оплачивать услуги через Yagoda - ты влияешь на объем общих чаевых, заслуживая их часть.
              </p>
              <p>
                В среднем, дополнительный доход Администратора составляет от 1,5 до 2% от всей выручки в смену Администратора.
              </p>
            </div>
            <div class="block-04-tab-03-left-bottom">
              <p>Не справедливо, когда мастер получает чаевые, а вы нет.</p>
              <p>Мы сделали возможным, часть от общих чаевых распределять на сотрудников.</p>
            </div>
          </div>
          <div class="block-04-tab-03-right">
            <img src="img/landing/block-04/2.png" alt="Фон" class="block-04-tab-03-bg" />
          </div>
        </div>
        <div class="block-04-tab-03-bottom"></div>
      </div>

      <!-- Вкладка 4 -->
      <div
        class="block-04-tab block-04-tab-04"
        :class="{ 'block-04-tab-open': activeTab === '4' }"
      >
        <div class="block-04-tab-04-content">
          <div class="block-04-tab-04-left">
            <div class="block-04-tab-04-left-title">
              Оплата услуг и чаевых <b>одним платежом</b>
            </div>
            <ul class="block-04-tab-04-left-list">
              <li>• Не нужно переводить на карту</li>
              <li>• Не нужно искать как оставить чаевые и отзыв</li>
              <li>• Все на одном экране</li>
              <li>• СБП или Картой (если вам интересны кэшбэки)</li>
            </ul>
            <div class="block-04-tab-04-left-icons">
              <img src="img/landing/block-04/icon-card.svg" alt="Банковская карта" />
              <img src="img/landing/block-04/icon-sbp.svg" alt="Система быстрых платежей" />
            </div>
          </div>
          <div class="block-04-tab-04-right">
            <img src="img/landing/block-04/3.png" alt="Фон" class="block-04-tab-04-bg" />
            <img src="img/landing/block-04/4.png" alt="Телефон" class="block-04-tab-04-phone" />
          </div>
        </div>
      </div>
    </div>

    <div class="block-04-section-connect-button">
      <a href="/connect">
        <button class="connect-button">подключить организацию</button>
      </a>
    </div>
    <div class="block-04-section-bottom-bg"></div>
  </section>
</template>

<script>
export default {
  name: "Block4Component",
  data() {
    return {
      activeTab: "1"
    };
  },
  methods: {
    setActiveTab(tabNumber) {
      this.activeTab = tabNumber;
    }
  }
};
</script>

<style lang="scss" scoped>

#features {
  scroll-margin-top: 80px;
}

@mixin container {
  max-width: 1280px;
  margin: 0 auto;
}
$backgroundWhite: #FFFFFF;
$backgroundBlueDark: #2A8796;
$backgroundBlueLight: #E8F1F1;
$backgroundGreyDark: #404854;
$backgroundGreyDarkest: #1B2424;
$textInverted: #FFFFFF;
$textPrimary: #404854;
$textSecondary: #7D8595;
$textTertiary: #1E2530;
$strokeDark: #1E2530;
$strokeLight: #bac2c8;
$greenLight: #38B0B0;
$darkBlue: #1E2530;
$totalBlack: #000000;
$darkGrey: #333333;

.connect-button-container {
  text-align: center;
}

.connect-button-container-dark-bg {
  background: linear-gradient(to top,  $greenLight 0%, $greenLight  50%, white 50%, white 100%);
}

.connect-button-container-dark-bg-alternate {
  background: linear-gradient(to bottom,  $greenLight 0%, $greenLight  50%, white 50%, white 100%);
}

.connect-button-container-light-bg {
  background: linear-gradient(to bottom,  $backgroundBlueLight 0%, $backgroundBlueLight  50%, white 50%, white 100%);
}

.connect-button {
  background-color: $darkBlue;
  color: $textInverted;
  font-family: 'Montserrat', sans-serif;
  text-transform: uppercase;
  padding: 20px 32px;
  border: none;
  border-radius: 40px;
  font-size: 16px;
  cursor: pointer;
  width: calc(100% - 32px);
  margin: 0 16px;
  text-decoration: none;
  @media (min-width: 768px) {
    padding: 24px 53px;
    width: unset;
  }
}

.block-04-section {
  padding-top: 40px;
  background-color: $backgroundBlueLight;
  position: relative;
  overflow: hidden;

  @media (min-width: 768px) {
    padding-top: 64px;
  }

  .block-04-title {
    @include container;
    position: relative;
    z-index: 5;
    margin-bottom: 32px;
    color: $greenLight;
    padding: 0 16px;
    font-size: 30px;
    text-align: left;

    @media (min-width: 768px) {
      font-size: 40px;
      padding: 0 32px;
    }
  }

  .tabs-container {
    @include container;
    position: relative;
    z-index: 10;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    padding: 0 16px;

    @media (min-width: 768px) {
      padding: 0 32px;
      gap: 16px;
      margin-bottom: 40px;
    }

    .tab {
      font-size: 16px;
      color: $textPrimary;
      cursor: pointer;
      padding: 8px 24px;
      background-color: $backgroundWhite;
      border-radius: 50px;
      transition: background-color 0.3s, color 0.3s;
      @media (max-width: 768px) {
        font-size: 15px;
        background-color: unset;
        padding: unset;
        text-decoration: underline;
      }

      &[data-active="true"] {
        font-weight: bold;
        color: $textInverted;
        background-color: $greenLight;
        @media (max-width: 768px) {
          font-size: 15px;
          background-color: unset;
          padding: unset;
          color: $greenLight;
          text-decoration: unset;
        }
      }
    }
  }

  .block-04-tab {
    display: none;
  }
  .block-04-tab-open {
    display: block;
  }

  .tab-content {
    @include container;
    padding: 0 16px;

    @media (min-width: 768px) {
      padding: 0 32px;
    }

  }

  .background-overlay {
    position: absolute;
    height: 100%;
    width: 5000px;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    background-color: $backgroundBlueLight; // Светлый синий
    z-index: -1;
  }

  .tabs {
    padding: 0 32px;
    @media (max-width: 768px) {
      padding: 0 16px;
    }
  }

  // Вкладка 1
  .block-04-tab-01 {
    @include container;
    &-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
      position: relative;
      z-index: 5;
      @media (min-width: 900px) {
        flex-direction: row;
      }
    }

    &-left {
      background-color: $backgroundGreyDark;
      color: $textInverted;
      padding: 32px 20px;
      border-radius: 20px;
      flex: 1;

      &-title {
        font-size: 25px;
        line-height: 1.2;
        text-transform: uppercase;
      }

      &-subtitle {
        font-size: 20px;
        margin-top: 10px;
        padding-left: 40px;
      }

      &-strikethrough {
        font-size: 28px;
        font-weight: 700;
        text-decoration: line-through;
      }

      &-percent {
        font-size: 80px;
        font-weight: 400;
        color: $greenLight;
        text-align: center;
        @media (min-width: 480px) {
          text-align: unset;
        }
        @media (min-width: 900px) {
          text-align: unset;
        }

        &-symbol {
          font-size: 80px;
          font-weight: 400;
        }
      }

      &-text {
        font-size: 20px;
        text-align: center;
        @media (max-width: 900px) {
          text-align: unset;
        }
      }
    }

    &-middle {
      background-color: $greenLight;
      color: $textInverted;
      padding: 20px;
      border-radius: 20px;
      flex: 1;

      &-title {
        font-size: 20px;
        line-height: 1.2;
        text-transform: uppercase;
      }

      &-text {
        font-size: 16px;
        margin-top: 10px;
      }
    }

    &-right {
      border: 1px solid $strokeLight;
      background-color: $backgroundWhite;
      padding: 32px 20px;
      border-radius: 20px;
      flex: 1;

      &-title {
        font-size: 14px;
        line-height: 1.2;
        text-transform: uppercase;
        margin-bottom: 10px;
      }

      &-subtitle {
        font-size: 20px;
        line-height: 1.2;
        color: $greenLight;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      &-text {
        font-size: 12px;
        line-height: 1.2;
        margin-top: 10px;
        color: $textPrimary;
      }
    }

    &-bottom {
      background-color: $backgroundBlueLight;
      height: 40px;
    }

    .block-04-tab-01-bg-image {
      display: none;
      @media (min-width: 1280px) {
        display: block;
        position: absolute;
        bottom: 0;
        right: 80%;
        z-index: 0;
        width: auto;
        height: 80%;
      }
    }

  }

  // Вкладка 2
  .block-04-tab-02 {
    @include container;
    &-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
      padding-bottom: 40px;

      @media (min-width: 768px) {
        flex-direction: row;
      }
    }

    &-left {
      display: flex;
      flex-direction: row;
      gap: 20px;
      position: relative;
      z-index: 5;
      @media (max-width: 768px) {
        flex-direction: column;
      }
      &-top {
        background-color: $greenLight;
        color: $totalBlack;
        padding: 20px;
        border-radius: 16px;

        &-title {
          font-size: 25px;
          line-height: 1.2;
          text-transform: uppercase;
          margin-top: 10px;
          margin-bottom: 20px;
        }

        &-subtitle {
          font-size: 16px;
          line-height: 1.2;
          text-transform: uppercase;
        }
      }

      &-bottom {
        background-color: $backgroundGreyDark;
        color: $textInverted;
        padding: 20px;
        border-radius: 16px;
      }
    }

    &-right {
      height: 300px;
      width: 100%;
      position: relative;
      overflow: visible;

      @media (min-width: 768px) {
        width: 800px;
      }
    }

    &-bottom {
      background-color: $backgroundBlueLight;
      height: 40px;
      display: none;

      @media (min-width: 768px) {
        display: block;
      }
    }
  }

  // Вкладка 3
  .block-04-tab-03 {
    @include container;
    overflow: visible;
    &-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
      overflow: visible;

      @media (min-width: 768px) {
        flex-direction: row;
        align-items: center;
      }
    }

    &-left {
      p {
        margin: 8px 0;
      }
      display: flex;
      flex-direction: column;
      gap: 24px;
      position: relative;
      z-index: 5;
      &-title {
        font-size: 25px;
        line-height: 1.2;
        text-transform: uppercase;
        color: $darkBlue;

        @media (min-width: 768px) {
          font-size: 32px;
        }

        @media (min-width: 1024px) {
          font-size: 40px;
        }
      }

      &-text {
        background-color: $backgroundGreyDark;
        color: $textInverted;
        padding: 20px;
        border-radius: 16px;
        font-size: 16px;
      }

      &-bottom {
        background-color: $greenLight;
        color: $textInverted;
        padding: 20px;
        border-radius: 16px;
        font-size: 16px;
        line-height: 1.2;
      }
    }

    &-right {
      height: 400px;
      width: 100%;
      position: relative;
      overflow: visible;

      @media (min-width: 768px) {
        width: 800px;
      }
    }

    &-bottom {
      background-color: $backgroundBlueLight;
      height: 40px;
      display: none;

      @media (min-width: 768px) {
        display: block;
      }
    }
  }

  // Вкладка 4
  .block-04-tab-04 {
    @include container;
    &-content {
      display: flex;
      flex-direction: column;
      gap: 20px;
      @media (min-width: 768px) {
        flex-direction: row;
      }
    }

    &-left {
      position: relative;
      z-index: 5;
      &-title {
        font-size: 25px;
        line-height: 1.2;
        text-transform: uppercase;
        color: $darkBlue;

        @media (min-width: 768px) {
          font-size: 32px;
        }

        @media (min-width: 1024px) {
          font-size: 40px;
        }
      }

      &-list {
        list-style: none;
        padding: 0;
        font-size: 16px;

        @media (min-width: 768px) {
          font-size: 18px;
        }

        @media (min-width: 1024px) {
          font-size: 20px;
        }
      }

      &-icons {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        padding-bottom: 20px;
      }
    }

    &-right {
      height: 600px;
      width: 100%;
      position: relative;
      overflow: visible;

      @media (min-width: 768px) {
        height: 300px;
        width: 800px;
      }
    }
  }

  // Стили для фоновых изображений
  .block-04-tab-02-bg,
  .block-04-tab-03-bg,
  .block-04-tab-04-bg,
  .block-04-tab-04-phone {
    position: absolute;
    z-index: 0;
  }

  .block-04-tab-02-bg {
    right: -10%;
    width: 140%;
    top: 0;
    height: auto;

    @media (min-width: 480px) {
      width: 450px;
      right: 0;
      bottom: -30%;
      top: unset;
    }

    @media (min-width: 768px) {
      width: 160%;
      right: -5%;
      bottom: -60%;
      top: unset;
    }

    @media (min-width: 1300px) {
      right: -10%;
      width: 600px;
      top: unset;
    }
  }

  .block-04-tab-03-bg {
    left: -24%;
    top: 0;
    width: 120%;
    height: auto;

    @media (min-width: 768px) {
      top: 15%;
      left: unset;
      right: -20%;
      width: 450px;
    }

    @media (min-width: 1024px) {
      right: -10%;
    }
  }

  .block-04-tab-04-bg {
    top: 0;
    right: -20%;
    width: 120%;

    @media (min-width: 768px) {
      top: -3%;
      width: auto;
      max-height: 400px;
      right: 10%;
    }

    @media (min-width: 1024px) {
      top: -40%;
      max-height: 600px;
    }
  }

  .block-04-tab-04-phone {
    left: 5%;
    top: 15%;
    transform: rotate(-3deg);
    width: 50%;
    height: auto;
    z-index: 1;

    @media (min-width: 480px) {
      width: 40%;
    }

    @media (min-width: 768px) {
      top: 20%;
      left: unset;
      right: 70%;
      width: auto;
      max-height: 250px;
    }

    @media (min-width: 1024px) {
      top: 0;
      left: unset;
      right: 70%;
      width: auto;
      max-height: 300px;
    }

  }

  .block-04-section-connect-button {
    position: relative;
    z-index: 5;
    text-align: center;
    margin-bottom: -30px;
  }

  .block-04-section-bottom-bg {
    position: relative;
    z-index: 4;
    height: 40px;
    background-color: $backgroundWhite;
  }

}
</style>
