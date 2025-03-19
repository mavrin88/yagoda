<template>
  <FrontHeaderComponent/>

  <div class="wrapper">

    <!-- Блок 1  -->

    <div class="block-01-utp">
      <div class="content">
        <div class="description">
          Снижайте затраты на&nbsp;эквайринг и&nbsp;увеличивайте доход
          сотрудников за&nbsp;счёт чаевых
        </div>
        <div class="card-bg-1"></div>
        <div class="card card1">
          <div class="card-title">Уменьшение комиссии за&nbsp;эквайринг</div>
          <div class="card-content">
            <div class="card-text">
              вместо <span class="strikethrough">2,5%</span>
            </div>
            <div class="card-value">
              0,69<span class="percentage">%</span>
            </div>
            <div class="card-text">(одинаково для карт и СБП)</div>
          </div>
        </div>
        <div class="card-bg-2"></div>
        <div class="card card2">
          <div class="card-title card-title-2">Увеличение чаевых</div>
          <div class="card-content">
            <div class="card-text">
              вместо <span class="strikethrough">10-30%</span>
            </div>
            <div class="card-value card-value--secondary">
              70-80<span class="percentage">%</span>
            </div>
            <div class="card-text">клиентов оставят чаевые</div>
          </div>
        </div>
      </div>
      <div class="block-01-bg-container">
        <img
          src="img/landing/block-01/bg.png"
          alt=""
          class="block-01-bg" />
      </div>

    </div>

    <!-- Блок 2 -->

    <section class="block-02-calculator">
      <h1 class="block-02-title">Калькулятор выгоды</h1>
      <div class="block-02-content">
        <div class="block-02-input-section">
          <div class="input-container">
            <input
              type="text"
              ref="revenueInput"
              class="revenue-input"
              :value="revenue"
              @input="handleRevenueInput"
              inputmode="numeric"
            />
            <div class="input-label">укажите вашу выручку в месяц (₽)</div>
          </div>
          <div class="profit-wrapper">
            <div class="profit-description">
              вы и ваши сотрудники <br /><strong>дополнительно</strong> получат в год до
            </div>
            <div class="total-profit">
              <span>{{ totalProfit }}</span> ₽
            </div>
          </div>
        </div>
        <div class="block-02-results">
          <div class="result-card">
            <img src="img/landing/block-02/1.svg" alt="" class="result-card-icon" />
            <div class="result-card-text">
              <div class="result-title">Экономия на эквайринге до:</div>
              <div class="result-value">
                <span>{{ monthlySaving }}</span> ₽ в месяц
              </div>
              <span>
              <span class="result-yearly"><span>{{ yearlySaving }}</span> ₽</span>
              <span> в год</span>
            </span>
            </div>
          </div>
          <div class="result-card">
            <img src="img/landing/block-02/2.svg" alt="" class="result-card-icon" />
            <div class="result-card-text">
              <div class="result-title">Объем чаевых в месяц до:</div>
              <div class="result-value">
                <span>{{ monthlyTips }}</span> ₽ в месяц
              </div>
              <span>
              <span class="result-yearly"><span>{{ yearlyTips }}</span> ₽</span>
              <span> в год</span>
            </span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Блок 3 -->

    <section class="block-03-section">
      <h1 class="block-03-title">Для кого</h1>
      <div class="block-03-cards">
        <div class="block-03-card">
          <div class="block-03-card-bg">
            <img src="img/landing/block-03/1.png" alt="салоны красоты" class="block-03-card-image" />
            <img src="img/landing/block-03/1.svg" alt="салоны красоты" class="block-03-card-icon" />
          </div>
          <div class="block-03-card-text">салоны красоты</div>
        </div>
        <div class="block-03-card">
          <div class="block-03-card-bg">
            <img src="img/landing/block-03/2.png" alt="услуги массажа" class="block-03-card-image" />
            <img src="img/landing/block-03/2.svg" alt="салоны красоты" class="block-03-card-icon" />

          </div>
          <div class="block-03-card-text">услуги массажа</div>
        </div>
        <div class="block-03-card">
          <div class="block-03-card-bg">
            <img src="img/landing/block-03/3.png" alt="флористы" class="block-03-card-image" />
            <img src="img/landing/block-03/3.svg" alt="салоны красоты" class="block-03-card-icon" />

          </div>
          <div class="block-03-card-text">флористы</div>
        </div>
        <div class="block-03-card">
          <div class="block-03-card-bg">
            <img src="img/landing/block-03/4.png" alt="автомойки" class="block-03-card-image" />
            <img src="img/landing/block-03/4.svg" alt="салоны красоты" class="block-03-card-icon" />

          </div>
          <div class="block-03-card-text">автомойки</div>
        </div>
        <div class="block-03-card block-03-card--special">
          <div class="block-03-card-bg block-03-card-bg--special">
            <img src="img/landing/block-03/heart.svg" alt="другое"/>
          </div>
          <div class="block-03-card-text">другое</div>
        </div>
      </div>
    </section>

    <!-- Блок 4-->

    <Block4Component/>


    <!--  Блок 5-->

    <section class="block-05" id="link2">
      <h1 class="block-05-title">Как это работает</h1>

      <div class="block-05-content">
        <div class="block-05-steps">
          <div class="block-05-step">
            <img src="img/landing/block-05/icon-add-person.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              Администратор создает новый заказ. Выбирает оказанные услуги и
              покупателя. Указывает Мастера
            </p>
          </div>
          <div class="block-05-step">
            <img src="img/landing/block-05/icon-scan.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              Клиент сканирует QR код, открывается страница оплаты услуг и чаевых
            </p>
          </div>
          <div class="block-05-step">
            <img src="img/landing/block-05/icon-wallet.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">Выбирает размер чаевых. Оплачивает удобным способом</p>
          </div>
          <div class="block-05-step">
            <img src="img/landing/block-05/icon-star.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              На странице подтверждения платежа, оставляет отзыв о компании (на
              Яндекс картах или на 2Gis)
            </p>
          </div>
        </div>

        <div class="block-05-benefits">
          <div class="block-05-benefit-subtitle">Подключение за 1 час</div>
          <div class="block-05-benefit">
            <img src="img/landing/block-05/icon-phone.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              Быстрая регистрация и <strong>онлайн подтверждение</strong> аккаунта в
              течение 1 часа
            </p>
          </div>
          <div class="block-05-benefit">
            <img src="img/landing/block-05/icon-cup-2.svg" alt="" class="block-05-icon" />
            <p class="block-05-text block-05-text-uppercase">
              <strong>Без Абонентской</strong> платы <img src="img/landing/block-05/1.svg" class="block-05-decoration" />
            </p>
          </div>
          <div class="block-05-benefit">
            <img src="img/landing/block-05/icon-zero.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              <strong>Отсутствует комиссия</strong> при выводе чаевых
            </p>
          </div>
          <div class="block-05-benefit">
            <img src="img/landing/block-05/icon-question.svg" alt="" class="block-05-icon" />
            <p class="block-05-text">
              Постоянное <strong>сопровождение и обучение</strong> на весь период нашего
              сотрудничества!
            </p>
          </div>
        </div>
      </div>

      <div class="how-work-qr">
        <img src="img/landing/block-05/qr-stand.png" alt="QR" class="qr-stand-image" />
        <div class="qr-stand-text">
          <i>Для удобства пользования, пришлем вам нужное количество стоек с QR
            кодами (1-2 дня)</i>
        </div>
      </div>
    </section>

    <!-- Блок 6 -->

    <section class="block-06">
      <h1 class="to-pay-title">Чтобы оплатить:</h1>
      <div class="to-pay-desktop">
        <div class="slide">
          <div class="slide-text">КЛИЕНТ СКАНИРУЕТ QR</div>
          <img src="img/landing/block-06/1-2.png" alt="Клиент сканирует QR" class="slide-image slide-image-1-desktop" />
        </div>
        <div class="slide">
          <div class="slide-text">выбирает размер чаевых</div>
          <img src="img/landing/block-06/2.png" alt="Выбор размера чаевых" class="slide-image" />
        </div>
        <div class="slide">
          <div class="slide-text"><span>ОПЛАЧИВАЕТ услуги и чаевые <b>одним</b> платежом</span></div>
          <img src="img/landing/block-06/3.png" alt="Оплата услуг и чаевых" class="slide-image" />
        </div>
        <div class="slide">
          <div class="slide-text">Деньги поступают организации и мастеру</div>
          <img src="img/landing/block-06/4.png" alt="Деньги поступают" class="slide-image" />
        </div>
      </div>
      <div class="to-pay-mobile">
        <Swiper
          v-bind="block6_swiperOptions"
          ref="block6_swiperInstance"
          class="swiper-container"
          :pagination="{ clickable: true }"
        >
          <SwiperSlide>
            <div class="slide-text">КЛИЕНТ СКАНИРУЕТ QR</div>
            <img src="img/landing/block-06/1.png" alt="Клиент сканирует QR" class="slide-image" />
          </SwiperSlide>
          <SwiperSlide>
            <div class="slide-text">выбирает размер чаевых</div>
            <img src="img/landing/block-06/2.png" alt="Выбор размера чаевых" class="slide-image" />
          </SwiperSlide>
          <SwiperSlide>
            <div class="slide-text">
              <span>ОПЛАЧИВАЕТ услуги и чаевые <b>одним</b> платежом</span>
            </div>
            <img src="img/landing/block-06/3.png" alt="Оплата услуг и чаевых" class="slide-image" />
          </SwiperSlide>
          <SwiperSlide>
            <div class="slide-text">Деньги поступают организации и мастеру</div>
            <img src="img/landing/block-06/4-2.png" alt="Деньги поступают" class="slide-image" />
          </SwiperSlide>
        </Swiper>
        <div ref="block6_prevEl" class="swiper-button-prev">
          <img src="img/landing/arrow-left.svg" alt="Предыдущий слайд">
        </div>
        <div ref="block6_nextEl" class="swiper-button-next">
          <img src="img/landing/arrow-right.svg" alt="Следующий слайд">
        </div>
      </div>
      <div class="background-overlay"></div>
    </section>


    <!-- Блок 7 -->

    <section class="block-07">
      <h1 class="to-order-title">Чтобы создать заказ:</h1>
      <div class="to-order-desktop">
        <div class="slide">
          <div class="slide-text">Выбрать услуги или указать произвольную сумму для оплаты</div>
          <img src="img/landing/block-07/1.png" alt="Выбор услуг" class="slide-image" />
        </div>
        <div class="slide">
          <div class="slide-text">Выбрать или добавить клиента</div>
          <img src="img/landing/block-07/2.png" alt="Выбор клиента" class="slide-image" />
        </div>
        <div class="slide">
          <div class="slide-text">Выбрать мастера / исполнителя</div>
          <img src="img/landing/block-07/3.png" alt="Выбор мастера" class="slide-image" />
        </div>
        <div class="slide">
          <div class="slide-text">Попросить клиента отсканировать QR</div>
          <img src="img/landing/block-07/4.png" alt="Сканирование QR" class="slide-image slide-image--special" />
        </div>
        <div class="slide">
          <div class="slide-text">Проконтролировать оплату</div>
          <img src="img/landing/block-07/5.png" alt="Контроль оплаты" class="slide-image" />
        </div>
      </div>
      <div class="to-order-mobile">
      <Swiper v-bind="block7_swiperOptions" class="swiper-container" ref="block7_swiperInstance">
        <SwiperSlide>
          <div class="slide-text">Выбрать услуги или указать произвольную сумму для оплаты</div>
          <img src="img/landing/block-07/1.png" alt="Выбор услуг" class="slide-image" />
        </SwiperSlide>
        <SwiperSlide>
          <div class="slide-text">Выбрать или добавить клиента</div>
          <img src="img/landing/block-07/2.png" alt="Выбор клиента" class="slide-image" />
        </SwiperSlide>
        <SwiperSlide>
          <div class="slide-text">Выбрать мастера / исполнителя</div>
          <img src="img/landing/block-07/3.png" alt="Выбор мастера" class="slide-image" />
        </SwiperSlide>
        <SwiperSlide>
          <div class="slide-text">Попросить клиента отсканировать QR</div>
          <img src="img/landing/block-07/4.png" alt="Сканирование QR" class="slide-image" />
        </SwiperSlide>
        <SwiperSlide>
          <div class="slide-text">Проконтролировать оплату</div>
          <img src="img/landing/block-07/5.png" alt="Контроль оплаты" class="slide-image" />
        </SwiperSlide>
      </Swiper>
        <div ref="block7_prevEl" class="swiper-button-prev">
          <img src="img/landing/arrow-left.svg" alt="Предыдущий слайд">
        </div>
        <div ref="block7_nextEl" class="swiper-button-next">
          <img src="img/landing/arrow-right.svg" alt="Следующий слайд">
        </div>
      </div>
    </section>

    <div class="connect-button-container connect-button-container-dark-bg">
      <a href="/connect"><button class="connect-button" style="width: unset">подключить организацию</button></a>
    </div>

    <!-- Блок 8 -->

    <section class="block-08-container">
      <p class="block-08-description">
        <b>Оплата через телефон</b> хоть и чуть дольше, чем просто приложить карту, зато:
      </p>
      <div class="block-08-cards">
        <div class="block-08-card">
          <img src="img/landing/block-08/1.svg" alt="Вы сохраняете от 1.8% к выручке!" class="block-08-card-image">
          <p>Вы <b>сохраняете от 1.8%</b> к выручке!</p>
        </div>
        <div class="block-08-card">
          <img src="img/landing/block-08/2.svg" alt="Качество работы повышается, ради больших чаевых" class="block-08-card-image">
          <p><b>Качество работы повышается</b>, ради больших чаевых</p>
        </div>
        <div class="block-08-card">
          <img src="img/landing/block-08/3.svg" alt="Чаевых больше в разы" class="block-08-card-image">
          <p><b>Чаевых больше в разы</b></p>
        </div>
        <div class="block-08-card">
          <img src="img/landing/block-08/4.svg" alt="Лояльность сотрудников к компании повышается" class="block-08-card-image">
          <p><b>Лояльность</b> сотрудников к компании повышается</p>
        </div>
        <div class="block-08-card">
          <img src="img/landing/block-08/5.svg" alt="Удовлетворенность клиентов растет" class="block-08-card-image">
          <p><b>Удовлетво&shy;рен&shy;ность клиентов растет</b></p>
        </div>
        <div class="block-08-card no-bg block-08-card--special">
          <img src="img/landing/block-08/6.svg" alt="В итоге, все становятся немножко более счастливыми" class="block-08-card-image">
          <p>В итоге, все становятся немножко более <b>счастливыми</b></p>
        </div>
      </div>
      <div class="block-08-text-special">В итоге, все становятся немножко более <b>счастливыми</b></div>
    </section>

    <!-- Блок 9 -->

    <section class="block-09" id="link3">
      <div class="block-09-content">
        <h1 class="block-09-title">Обратите внимание</h1>
        <div class="block-09-notice">
          Подключение к сервису <b>не ограничивает вас</b> использовать
          привычный платежный терминал для приема оплаты
        </div>

        <div class="block-09-card">
          <div class="card-title">Условия для организации</div>
          <div class="card-content">
            <div class="card-icon">
              <img src="img/landing/block-09/1.svg" alt="0,69% / 0₽" />
            </div>
            <div class="card-text">
              <p>эквайринг при оплате картой или СБП</p>
              <p></p>
              <p>абонентская плата</p>
              <p>вывод чаевых</p>
              <p>подключение, обучение и сопровождение</p>
            </div>
          </div>
        </div>

        <div class="block-09-card">
          <div class="card-title">
            <div>Этапы интеграции</div>
            <div class="card-subtitle">стандарт подключения — 1 час</div>
          </div>
          <div class="card-content card-content__secondary">
            <div class="card-text">
              <ol>
                <li>Самостоятельно зарегистрироваться, или связаться с нашим
                  менеджером</li>
                <li>Подписать договор (возможно удаленно)</li>
                <li>Начало работы, обучение и поддержка</li>
              </ol>
            </div>
            <div class="card-image">
              <img src="img/landing/block-09/2.png" alt="Стойка с QR-кодом" />
              <div class="image-caption">Стойки с QR кодами предоставляются бесплатно</div>
            </div>
          </div>
        </div>
      </div>

      <img src="img/landing/block-09/bg.png" class="block-09-bg-image" />

      <div class="block-09-footer">
        <a href="/connect"><button class="connect-button">подключить организацию</button></a>
      </div>

    </section>

    <!-- Блок 10 -->

    <section class="block-10">
      <h1 class="block-10-title">Все необходимые функции в одном кабинете</h1>

      <div class="block-10-content">
        <div class="block-10-section">
          <div class="block-10-section-text">
            <div class="block-10-section-title">Владелец</div>
            <div class="block-10-section-description">
              Интуитивный личный <br/>кабинет и простая система <br/>распределения чаевых
            </div>
          </div>
          <div class="block-10-images">
            <img src="img/landing/block-10/1.png" alt="" class="block-10-image" />
            <img src="img/landing/block-10/2.png" alt="" class="block-10-image" />
          </div>
        </div>

        <div class="block-10-section">
          <div class="block-10-section-text">
            <div class="block-10-section-title">Администратор</div>
            <div class="block-10-section-description">
              Формирование счета <br/>и генерация QR на оплату
            </div>
          </div>
          <div class="block-10-images">
            <img src="img/landing/block-10/3.png" alt="" class="block-10-image" />
            <img src="img/landing/block-10/4.png" alt="" class="block-10-image" />
            <img src="img/landing/block-10/5.png" alt="" class="block-10-image" />
          </div>
        </div>

        <div class="block-10-section">
          <div class="block-10-section-text">
            <div class="block-10-section-title">Клиент</div>
            <div class="block-10-section-description">
              Оплата заказа и отзыв о компании
            </div>
          </div>
          <div class="block-10-images">
            <img src="img/landing/block-10/6.png" alt="" class="block-10-image" />
            <img src="img/landing/block-10/7.png" alt="" class="block-10-image" />
          </div>
        </div>
      </div>
    </section>

    <!-- Блок 11 -->

    <FaqComponent/>

    <!-- Footer -->
    <FrontFooterComponent/>

  </div>
</template>
<script>

import { ref, nextTick } from 'vue';

// import Swiper core and required modules
import {Pagination, Navigation} from 'swiper/modules';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css/bundle';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import FaqComponent from './Components/Front/FaqComponent.vue'
import Block4Component from './Components/Front/Block4Component.vue'
import FrontHeaderComponent from './Components/Front/FrontHeaderComponent.vue'
import FrontFooterComponent from './Components/Front/FrontFooterComponent.vue'

export default {
  components: {
    FrontFooterComponent,
    FrontHeaderComponent,
    Block4Component,
    FaqComponent,
    Link,
    Swiper,
    SwiperSlide,
  },
  setup() {
    const block6_swiperInstance = ref(null);
    const block6_prevEl = ref(null);
    const block6_nextEl = ref(null);
    const block6_swiperOptions = ref({
      modules: [Pagination, Navigation],
      pagination: { clickable: true },
      navigation: {
        prevEl: null,
        nextEl: null,
      },
      loop: true,
    });

    nextTick(() => {
      if (block6_prevEl.value && block6_nextEl.value) {
        block6_swiperOptions.value.navigation.prevEl = block6_prevEl.value;
        block6_swiperOptions.value.navigation.nextEl = block6_nextEl.value;
      }

      if (block6_swiperInstance.value?.swiper) {
        block6_swiperInstance.value.swiper.update(); // Обновляем Swiper после привязки кнопок
      }
    });

    const block7_swiperInstance = ref(null);
    const block7_prevEl = ref(null);
    const block7_nextEl = ref(null);
    const block7_swiperOptions = ref({
      modules: [Pagination, Navigation],
      pagination: { clickable: true },
      navigation: {
        prevEl: null,
        nextEl: null,
      },
      loop: true,
    });

    nextTick(() => {
      if (block7_prevEl.value && block7_nextEl.value) {
        block7_swiperOptions.value.navigation.prevEl = block7_prevEl.value;
        block7_swiperOptions.value.navigation.nextEl = block7_nextEl.value;
      }

      if (block7_swiperInstance.value?.swiper) {
        block7_swiperInstance.value.swiper.update(); // Обновляем Swiper после привязки кнопок
      }
    });


    return {
      block6_swiperInstance,
      block6_prevEl,
      block6_nextEl,
      block6_swiperOptions,

      block7_swiperInstance,
      block7_prevEl,
      block7_nextEl,
      block7_swiperOptions,
      modules: [Pagination, Navigation],
    };
  },

  data() {
    return {
      isHasIntegration: false,
      //region Из вёрстки
      revenue: "1 000 000"
      //endregion

    }
  },

  mounted() {
    setTimeout(() => {
      if (typeof ym === 'function') {
        ym(98232814, 'reachGoal', 'landing_time_out_40');
      }
    }, 40000);

    setTimeout(() => {
      if (typeof ym === 'function') {
        ym(98232814, 'reachGoal', 'landing_time_out_60');
      }
    }, 60000);
  },
  computed: {
    //region Из вёрстки
    // Числовое значение выручки, очищенное от пробелов и нецифровых символов
    numericRevenue() {
      const cleaned = this.revenue.replace(/\s/g, '').replace(/\D/g, '');
      return parseFloat(cleaned) || 0;
    },
    monthlySavingValue() {
      return this.numericRevenue * 0.015;
    },
    yearlySavingValue() {
      return this.monthlySavingValue * 12;
    },
    monthlyTipsValue() {
      return this.numericRevenue * 0.15;
    },
    yearlyTipsValue() {
      return this.monthlyTipsValue * 12;
    },
    totalProfitValue() {
      return this.yearlySavingValue + this.yearlyTipsValue;
    },
    // Форматируем результаты с учётом локали
    monthlySaving() {
      return Number(this.monthlySavingValue.toFixed()).toLocaleString('ru-RU');
    },
    yearlySaving() {
      return Number(this.yearlySavingValue.toFixed()).toLocaleString('ru-RU');
    },
    monthlyTips() {
      return Number(this.monthlyTipsValue.toFixed()).toLocaleString('ru-RU');
    },
    yearlyTips() {
      return Number(this.yearlyTipsValue.toFixed()).toLocaleString('ru-RU');
    },
    totalProfit() {
      return Number(this.totalProfitValue.toFixed()).toLocaleString('ru-RU');
    }
    //endregion
  },

  methods: {
    //region Из вёрстки
    // Калькулятор
    formatNumberWithSpaces(value) {
      const cleanedValue = value.replace(/\s/g, '').replace(/\D/g, '');
      return cleanedValue.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
    },
    handleRevenueInput(e) {
      const input = e.target;
      const cursorPosition = input.selectionStart;
      const originalValue = input.value;
      const formattedValue = this.formatNumberWithSpaces(originalValue);
      this.revenue = formattedValue;
      this.$nextTick(() => {
        let newCursorPosition = cursorPosition;
        if (formattedValue.length > originalValue.length) {
          newCursorPosition += formattedValue.length - originalValue.length;
        }
        input.setSelectionRange(newCursorPosition, newCursorPosition);
      });
    }
    //endregion
  }
};

</script>
<style lang="scss" scoped>
@import "@scss/Landing/_mixins";
@import "@scss/Landing/_variables";
@import "@scss/Landing/buttons";

//region Не из вёрстки
.bg-gray-100 {
  background: transparent !important;
}
html,body {
  background: transparent;
}

.modal-landing {
  display: flex;
  align-items: center;
  justify-content: center;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 99999;
  &__overlay {
    background: #000;
    opacity: .3;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1;
  }
  &__wrapper {
    background: #fff;
    padding: 40px;
    z-index: 2;
    position: relative;
  }
  &__close {
    position: absolute;
    top: 10px;
    right: 14px;
    width: 20px;
    height: 20px;
    opacity: 0.3;
    cursor: pointer;
    &:hover {
      opacity: 1;
    }
    &:before, &:after {
      position: absolute;
      left: 15px;
      content: ' ';
      height: 20px;
      width: 2px;
      background-color: #333;
    }
    &:before {
      transform: rotate(45deg);
    }
    &:after {
      transform: rotate(-45deg);
    }
  }
  &__content {
    h2 {
      margin-bottom: 10px;
    }
    .form {
      &__item {
        &_inline {
          input {
            display: inline-block;
            vertical-align: middle;
          }
          label {
            display: inline-flex;
            vertical-align: middle;
            margin-left: 5px;
            margin-top: 0;
            padding-top: 0;
            a {
              text-decoration: underline;
            }
          }
        }
      }
    }

    .form__error {
      text-align: center;
      margin-top: 10px;
    }
  }
}

html {
  scroll-behavior: smooth;
}
.block-07 {
  position: relative;
}

img {
  max-width: none;
  display: inline;
}

//endregion








body {
  font-family: 'Montserrat', sans-serif;
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

h1 {
  font-weight: 400; /* Жирный шрифт */
}

p {
  font-weight: 400; /* Обычный шрифт */
}

:root {
  --color-green-light: #E8F1F1;
  --color-dark-blue: #1E2530;
  --color-green-medium: #38B0B0;
}

.no-scroll {
  overflow: hidden;
}



.menu-connect-button {
  background-color: $backgroundGreyDark;
  color: $textInverted;
  font-family: 'Montserrat', sans-serif;
  padding: 16px 16px;
  border: none;
  border-radius: 16px;
  font-size: 14px;
  cursor: pointer;
  width: 100%;
  text-decoration: none;
}

.connect-button-offset-top {
  margin-top: -50px;
}

.connect-button-offset-bottom {
  margin-bottom: -50px;
}





// Блок 1

.block-01-utp {
  @include container;
  padding: 120px 16px 112px;
  position: relative;
  overflow: visible;

  @media (max-width: 480px) {
    padding: 40px 16px 112px;
  }

  @media (max-width: 768px) {
    height: 680px;
  }

  @media (min-width: 768px) {
    padding-left: 32px;
    padding-right: 32px;
    padding-bottom: 0;
  }

  .content {
    width: 100%;
    height: 550px;
    display: inline-flex;
    flex-direction: column;
    gap: 16px;
    position: relative;
  }

  .description {
    margin-top: 60px;
    margin-bottom: 32px;
    font-size: 16px;
    line-height: 1.3;
    position: relative;
    z-index: 30;
    width: 70%;
    color: $textPrimary;
    @media (max-width: 768px) {
      width: 60%;
      margin-top: 32px;
    }
  }

  .card-bg-1 {
    position: absolute;
    z-index: -1;
    height: 200px;
    width: 100%;
    top: 22%;
    border-radius: 16px;
    background-color: $backgroundBlueLight;
    @media (max-width: 768px) {
      background-color: $backgroundBlueDark;
      z-index: 6;
      width: 70%;
      left: 0;
      height: 240px;
      top: 32%;
    }
    @media (max-width: 480px) {
      top: 28%;
    }
  }

  .card-bg-2 {
    position: absolute;
    z-index: -1;
    height: 200px;
    width: 500%;
    right: 0;
    bottom: 0;
    background-color: $backgroundBlueLight;
    border-radius: 16px;
    @media (max-width: 768px) {
      background-color: $backgroundBlueDark;
      z-index: 6;
      width: 70%;
      right: unset;
      left: 0;
      height: 210px;
      bottom: -16%;
    }
    @media (max-width: 480px) {
      bottom: -12%;
    }
  }

  .card {
    color: $textInverted;
    display: flex;
    flex-direction: column;
    gap: 24px;
    @media (min-width: 768px) {
      color: $darkGrey;
      flex-direction: row;
      align-items: center;
    }

    .card-title {
      text-transform: uppercase;
      font-size: 20px;
      line-height: 1.2;

      @media (min-width: 768px) {
        font-size: 30px;
        width: 280px;
      }

      @media (min-width: 1024px) {
        font-size: 40px;
        width: 360px;
      }
    }

    .card-title-2 {
      @media (min-width: 768px) {
        width: 240px;
      }

      @media (min-width: 1024px) {
        width: 310px;
      }
    }

    .card-content {
      display: flex;
      flex-direction: column;
      gap: 8px;
      .card-text {
        display: flex;
        gap: 8px;
        font-size: 12px;
        align-items: center;
        @media (min-width: 768px) {
          font-size: 16px;
        }
      }
      @media (min-width: 768px) {
        scale: 0.9;
      }
      @media (min-width: 1024px) {
        scale: unset;
      }

      .strikethrough {
        font-weight: 700;
        font-size: 16px;
        text-decoration-line: line-through;

        @media (min-width: 768px) {
          font-size: 24px;
        }
      }

      .card-value {
        font-weight: 700;
        font-size: 48px;
        line-height: 1;
        @media (min-width: 768px) {
          font-size: 97px;
        }
        .percentage {
          font-size: 12px;
          @media (min-width: 768px) {
            font-size: 42px;
          }
        }
        &--secondary {
          @media (min-width: 768px) {
            font-size: 85px;
          }
        }
      }
    }
  }

  .card1 {
    position: absolute;
    z-index: 10;
    top: 26%;
    left: 4%;
    padding-left: 20px;
    @media (max-width: 1024px) {
      padding-left: 40px;
    }
    @media (max-width: 768px) {
      padding-left: 0;
      width: 60%;
      top: 36%;
    }
    @media (max-width: 480px) {
      top: 32%;
    }
  }

  .card2 {
    position: absolute;
    z-index: 10;
    top: 68%;
    left: 0;
    justify-content: flex-end;
    @media (max-width: 768px) {
      padding-left: 0;
      width: 60%;
      top: 82%;
      left: 4%;
    }
    @media (max-width: 480px) {
      top: 78%;
    }
  }

  .block-01-bg-container {
    position: absolute;
    overflow: hidden;
    height: 566px;
    width: 110%;
    bottom: 0;
    right: 0;
    z-index: 5;
    @media (max-width: 768px) {
      height: 800px;
    }
  }


  .block-01-bg {
    position: absolute;
    top: 0;
    right: -104%;
    width: 210%;
    height: auto;
    z-index: 5;

    @media (min-width: 748px) {
      position: absolute;
      bottom: -3%;
      top: unset;
      right: -25%;
      width: 600px;
      height: auto;
    }

    @media (min-width: 1024px) {
      right: -10%;
      width: 600px;
      height: auto;
    }

    @media (min-width: 1280px) {
      right: -2%;
      width: 600px;
      height: auto;
    }
  }
}

// Блок 2

.block-02-calculator {
  @include container;
  padding: 64px 16px;
  background-color: $backgroundBlueLight;
  @media (max-width: 768px) {
    padding: 40px 32px 80px;
  }

  .block-02-title {
    font-size: 30px;
    margin-bottom: 40px;
    margin-top: 0;
    text-align: left;
    color: $textPrimary;
    @media (min-width: 768px) {
      font-size: 40px;
    }
  }

  .block-02-content {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 32px;

    @media (min-width: 768px) {
      flex-direction: row;
      gap: 8px;
    }
  }

  .block-02-input-section {
    display: flex;
    flex-direction: column;
    gap: 16px;
    flex-basis: 50%;
    justify-content: space-between;

    .input-container {
      display: flex;
      flex-direction: column;
      gap: 8px;

      .revenue-input {
        font-size: 55px;
        font-family: Montserrat, sans-serif;
        color: $darkBlue;
        border: none;
        border-bottom: 1px solid $textPrimary;
        background: transparent;
        outline: none;
        text-align: center;

        @media (min-width: 768px) {
          width: 100%;
          font-size: 80px;
          text-align: left;
        }
        @media (min-width: 1280px) {
          font-size: 100px;
        }
      }

      .input-label {
        font-size: 14px;
        color: $textSecondary;
        @media (max-width: 768px) {
          text-align: center;
        }
      }
    }

    .profit-wrapper {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 16px;
      text-wrap: nowrap;
      @media (min-width: 1024px) {
        flex-direction: row;
      }
    }

    .profit-description {
      font-size: 16px;
      color: $textPrimary;
    }

    .total-profit {
      background-color: $greenLight; // Светлый зеленый
      padding: 8px 16px;
      border-radius: 32px;
      font-size: 24px;
      color: $textInverted;
      text-wrap: nowrap;

      span {
        font-weight: bold;
      }
    }
  }

  .block-02-results {
    background-color: $backgroundBlueLight; // Светлый синий
    border-radius: 24px;
    display: grid;
    gap: 40px;
    margin-top: 20px;

    @media (min-width: 768px) {
      gap: 24px;
      flex-basis: 30%;
      padding: 32px;
    }

    .result-card {
      display: flex;
      flex-direction: row;
      align-items: flex-start;
      gap: 30px;

      .result-card-text {
        display: flex;
        flex-direction: column;
        gap: 12px;
        text-wrap: nowrap;
      }

      .result-card-icon {
        display: none;
        margin-top: 4px;
        width: 38px;
        height: auto;
        @media (min-width: 1024px) {
          display: block;
        }
      }

      .result-title {
        font-size: 16px;
        font-weight: bold;
        color: $textPrimary;
      }

      .result-value {
        font-size: 16px;
        margin-left: 16px;
        color: $textPrimary;
        #monthly-saving {
          color: $totalBlack;
        }
        #monthly-tips {
          color: $totalBlack;
        }

        span {
          font-weight: bold;
        }
      }

      .result-yearly {
        background-color: $backgroundGreyDark; // Темный серый
        padding: 4px 16px;
        border-radius: 32px;
        font-size: 16px;
        color: $textInverted;

        span {
          font-weight: bold;
        }
      }
    }
  }

  @media (min-width: 768px) {
    padding: 64px 32px;
    background-color: transparent;
  }
}

// Блок 3

.block-03-section {
  padding: 40px 32px 24px;
  background-color: #1B2424;
  background-image: url("/img/landing/block-03/bg.png");
  background-size: 120%;
  background-position: 50%;
  color: $textInverted;

  @media (min-width: 768px) {
    padding: 64px 32px;
  }

  @media (min-width: 1024px) {
    background-image: url("/img/landing/block-03/bg2.png");
  }

  .block-03-title {
    @include container;
    font-size: 30px;
    margin-bottom: 32px;
    text-align: left;
    @media (min-width: 768px) {
      font-size: 40px;
    }
  }

  .block-03-cards {
    @include container;
    display: flex;
    flex-wrap: wrap;
    column-gap: 24px;
    row-gap: 64px;
    justify-content: center;
    margin-top: 64px;

    @media (min-width: 768px) {
      flex-wrap: nowrap;
      gap: 32px;
    }
  }

  .block-03-card {
    display: flex;
    flex-direction: column;
    gap: 24px;
    text-align: center;
    font-size: 20px;
    width: 46%;

    &--special {
      width: 80px;
      img {
        position: absolute;
        width: 32px;
        height: auto;
        top: 25%;
      }
    }
  }

  .block-03-card-bg {
    height: 140px;
    border-radius: 40px;
    background-color: $greenLight;
    position: relative;
    margin: 0;

    &--special {
      height: 56px;
      width: 56px;
      align-self: center;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-top: -30px;

      @media (min-width: 768px) {
        height: 140px;
        width: 80px;
        margin-top: 0;
      }
    }
  }

  .block-03-card-image {
    width: auto;
    height: 180px;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    z-index: 5;
    @media (min-width: 1024px) {
      left: unset;
      transform: unset;
      right: 10%;
    }
  }

  .block-03-card-icon {
    display: none;
    @media (min-width: 1024px) {
      display: block;
      width: auto;
      height: 40px;
      position: absolute;
      top: 20%;
      left: 20%;
      transform: translateX(-50%);
      z-index: 4;
    }

  }

}


// Блок 5

.block-05 {
  @include container;
  padding: 80px 20px;
  background-color: $backgroundWhite;
  display: flex;
  flex-direction: column;
  position: relative;
  @media (min-width: 768px) {
    padding: 80px 40px;
  }
}

.block-05-title {
  color: $greenLight;
  font-size: 30px;
  margin-bottom: 24px;
  margin-top: 0;
  text-align: left;
  @media (min-width: 768px) {
    font-size: 32px;
  }
}

.block-05-content {
  display: flex;
  flex-direction: column;
  gap: 32px;

  @media (min-width: 768px) {
    flex-direction: row;
    gap: 80px;
  }
  @media (min-width: 1024px) {
    width: 70%;
    gap: 30px;
  }
}

.block-05-steps,
.block-05-benefits {
  display: flex;
  flex-direction: column;
}

.block-05-benefits {
  gap: 8px;
}

.block-05-step,
.block-05-benefit {
  display: flex;
  align-items: center;
  gap: 30px;
  font-size: 13px;
  line-height: 1.2;
  color: $textPrimary;
  @media (min-width: 1280px) {
    font-size: 16px;
  }
}

.block-05-icon {
  width: 32px;
  height: 32px;
}

.block-05-benefit {
  &:last-child {
    margin-top: -8px;
  }
  .block-05-icon {
    width: unset;
    height: unset;
  }
}

.block-05-text {
  color: $totalBlack;
  margin: 10px 0;
  position: relative;
  z-index: 2;
  &-uppercase {
    text-transform: uppercase;
  }
}

.block-05-decoration {
  position: absolute;
  left: 0;
  z-index: 1;
  top: -70%;
  @media (min-width: 1280px) {
    top: -50%;
  }
}

.block-05-benefit-subtitle {
  color: $greenLight;
  font-weight: 700;
  margin-bottom: 12px;
}

.block-05-benefits {
  background-color: $backgroundBlueLight;
  padding: 30px;
  border-radius: 16px;


  @media (min-width: 768px) {
    margin-top: -80px;
  }
}


.how-work-qr {
  position: relative;
  display: flex;
  align-items: center;
  gap: 16px;
  margin-top: 100px;
  margin-bottom: 10px;
  justify-content: center;
  align-self: center;
  @media (min-width: 480px) {
    width: 80%;
  }
  @media (min-width: 728px) {
    width: 70%;
  }
  @media (min-width: 1024px) {
    position: absolute;
    right: 0;
    bottom: 70px;
    width: 50%;
  }
}

.qr-stand-text {
  background-color: $greenLight;
  color: $textInverted;
  padding: 16px;
  padding-left: 20%;
  border-radius: 16px;
  font-size: 10px;
  line-height: 1.5;
  margin-left: 42%;
  width: 60%;
  @media (min-width: 1024px) {
    margin-right: 32px;
    margin-top: 80px;
    padding-top: 160px;
    padding-left: 5%;
    padding-right: 25%;
    padding-bottom: 5%;
  }
}

.qr-stand-image {
  position: absolute;
  left: 0;
  width: 60%;
  height: auto;
  align-content: center;
  float: left;
  @media (min-width: 480px) {
    width: 60%;
  }
  @media (min-width: 600px) {
    width: 50%;
    left: 10%;
  }
  @media (min-width: 748px) {
    width: 40%;
    left: 20%;
  }
  @media (min-width: 1024px) {
    width: 55%;
    left: 42%;
    bottom: 10%
  }
}

@media (min-width: 768px) {
  .how-work-content {
    flex-direction: row;
    gap: 64px;
  }

  .how-work-steps,
  .how-work-features {
    flex: 1;
  }

  .how-work-features {
    margin-top: -64px;
  }
}

// Блок 6

.block-06 {
  padding: 64px 16px 0;
  background-color: $greenLight;
  position: relative;
  overflow: hidden;
  @media (min-width: 768px) {
    padding: 64px 16px 40px;
  }

  .to-pay-title {
    @include container;
    margin-bottom: 32px;
    color: $textInverted;
    font-size: 30px;
    text-align: left;

    @media (min-width: 768px) {
      font-size: 40px;
      padding: 0 32px;
    }
  }

  .to-pay-desktop {
    @include container;
    background-image: url("/img/landing/block-06/bg-arrows.png");
    background-repeat: no-repeat;
    background-position: 0% 50%;
    background-size: 100%;
    display: flex;
    gap: 16px;
    padding: 0 16px;
    .slide-image {
      width: 110%;
    }
    .slide-image-1-desktop {
      width: 115%;
    }
  }

  .slide {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;

  }

  .slide-text {
    font-size: 16px;
    color: #ffffff;
    text-transform: uppercase;
    text-align: center;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .slide-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
  }

  .background-overlay {
    position: absolute;
    height: 100%;
    width: 5000px;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    background-color: #4caf50; /* Зеленый */
    z-index: -1;
  }

  .to-pay-mobile {
    display: none !important;
  }

  .swiper {
    width: 100%;
    height: 100%;
  }

  .swiper-slide {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .swiper-pagination {
    position: relative;
    margin-top: 16px;
    bottom: 40px !important;
  }

  @media (max-width: 768px) {
    .to-pay-desktop {
      display: none;
    }
    .to-pay-mobile {
      display: block !important;
    }
  }

  .slide-image {
    width: 100%;
    height: auto;
  }

  .swiper-pagination-bullet {
    scale: 1.5;
    margin: 0 6px !important;
  }

  .swiper-pagination-bullet-active {
    background-color: #ACFFFF !important;
    scale: 1.5;
  }

  .swiper-button-next, .swiper-button-prev {
    top: unset !important;
    bottom: -6px;
  }

  .swiper-button-prev {
    left: 22%;
    bottom: 26px !important;
  }

  .swiper-button-next {
    right: 22%;
    bottom: 26px !important;
  }

}

.swiper-button-prev:after, .swiper-button-next:after {
  display: none;
}

// Блок 7

.block-07 {
  padding: 40px 16px 0;
  .to-order-title {
    @include container;
    margin-bottom: 16px;
    color: $greenLight;
    padding: 0 16px;
    font-size: 30px;
    text-align: left;

    @media (min-width: 768px) {
      margin-bottom: 32px;
      font-size: 40px;
      padding: 0 32px;
    }
  }

  .to-order-desktop {
    @include container;
    background-image: url("/img/landing/block-07/bg.png");
    background-repeat: no-repeat;
    background-position: 0% 50%;
    background-size: 80%;
    display: flex;
    gap: 16px;
    padding: 0 16px;
    .slide-image {
      width: 120%;
    }
  }

  .slide {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 16px;
  }

  .slide-text {
    font-size: 16px;
    color: $totalBlack;
    text-align: center;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    word-break: break-word;
  }

  .slide-image {
    width: 100%;
    height: auto;
    border-radius: 8px;
    &--special {
      width: 130% !important;
    }
  }

  .to-order-mobile {
    display: none !important;
  }

  .swiper {
    width: 100%;
    height: 100%;
  }

  .swiper-slide {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .swiper-pagination {
    position: relative;
    margin-top: 16px;
    bottom: 60px !important;
  }

  /* Адаптивные стили */
  @media (max-width: 767px) {
    .to-order-desktop {
      display: none;
    }

    .to-order-mobile {
      display: block !important;
    }
  }

  .slide-image {
    width: 100%;
    height: auto;
  }

  .swiper-pagination-bullet {

  }

  .swiper-pagination-bullet {
    background-color: $backgroundBlueDark !important;
    scale: 1.5;
    margin: 0 6px !important;
    opacity: 1;
  }

  .swiper-pagination-bullet-active {
    background-color: #ACFFFF !important;
    scale: 1.5;
  }

  .swiper-button-next, .swiper-button-prev {
    top: unset !important;
    bottom: -6px;
  }

  .swiper-button-prev {
    left: 22%;
    bottom: 46px !important;
  }

  .swiper-button-next {
    right: 22%;
    bottom: 46px !important;
  }

}

// Блок 8

.block-08-container {
  padding: 40px 16px;
  position: relative;
  background-color: $greenLight;
  @media (min-width: 768px) {
    padding: 64px 32px;
  }

  .block-08-description {
    @include container;
    font-size: 16px;
    color: $textInverted;
    text-align: center;
    padding-bottom: 32px;
    @media (min-width: 1024px) {
      font-size: 20px;
    }
  }

  .block-08-cards {
    @include container;
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
  }

  .block-08-card {
    padding: 40px 24px;
    background-color: $darkBlue;
    display: flex;
    flex-direction: column;
    gap: 24px;
    align-items: center;
    border-radius: 32px;
    flex-grow: 1;
    flex-basis: 30%;
    flex-shrink: 1;
    color: $textInverted;
    font-size: 16px;
    @media (min-width: 768px) {
      flex-basis: 0;
      width: 0;
      padding: 40px 24px;
    }
    @media (min-width: 1024px) {
      font-size: 16px;
      gap: 30px;
      padding: 56px 40px;
    }

    p {
      margin: 0;
    }

    .block-08-card-image {
      width: 32px;
      height: auto;
      @media (min-width: 768px) {
        width: 32px;
        height: auto;
      }
      @media (min-width: 1024px) {
        width: 42px;
        height: auto;
      }
    }

    &.no-bg {
      background-color: unset;
    }
    &--special {
      @media (min-width: 768px) {
        display: none;
      }
    }
  }

  .block-08-text-special {
    display: none;
    font-size: 16px;
    @media (min-width: 768px) {
      display: block;
      color: $textInverted;
      text-align: center;
      padding-top: 40px;

    }
    @media (min-width: 1024px) {
      font-size: 20px;
    }
  }
}

// Блок 9

.block-09 {
  background: linear-gradient(to top,  $greenLight 0%, $greenLight  30%, white 30%, white 100%);
  position: relative;

  .block-09-content {
    @include container;
    padding: 64px 16px 0;
    @media (min-width: 768px) {
      padding-left: 32px;
      padding-right: 32px;
    }
    @media (min-width: 1024px) {
      max-width: 1000px;
    }
  }

  .block-09-bg-image {
    display: none;
    @media (min-width: 1024px) {
      display: block;
      position: absolute;
      top: 20%;
      left: -25%;
      z-index: 1;
      width: auto;
      height: 50%;
    }
    @media (min-width: 1200px) {
      left: -15%;
    }
    @media (min-width: 1500px) {
      left: 0;
    }
  }

  .block-09-title {
    font-size: 30px;
    margin-bottom: 32px;
    text-align: left;
    color: $textPrimary;
    @media (min-width: 768px) {
      font-size: 40px;
    }
  }

  .block-09-notice {
    margin-bottom: 32px;
    font-size: 16px;
    color: $textPrimary;
    @media (min-width: 1024px) {
      font-size: 20px;
    }
  }

  .block-09-card {
    background-color: $darkBlue;
    color: $textInverted;
    padding: 24px;
    border-radius: 16px;
    margin-bottom: 24px;
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: column;
    gap: 24px;

    @media (min-width: 768px) {
      display: grid;
      grid-template-columns: 1fr 2fr;
      align-items: center;
      padding: 80px 56px;
    }

    .card-title {
      display: flex;
      flex-direction: column;
      font-size: 20px;
      @media (min-width: 768px) {
        font-size: 32px;
      }

      .card-subtitle {
        margin-top: 4px;
        font-size: 12px;
        color: $textInverted;
        @media (min-width: 768px) {
          margin-top: 40px;
        }
      }
    }

    .card-content {
      display: flex;
      gap: 24px;
      flex-grow: 1;

      .card-icon {
      }

      .card-icon img {
        width: 105px;
        height: auto;
      }

      .card-text {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 14px;
        flex-grow: 1;
        margin-top: 4px;
        p {
          margin: 0;
          margin-bottom: 20px;
        }
        ol {
          padding-left: 16px;
          margin-bottom: 0;
        }
        li {
          margin-bottom: 16px;
          line-height: 24px;
        }
        @media (min-width: 768px) {
          margin-top: 0;
          ol {
            margin: 0;
          }
        }
        @media (min-width: 1024px) {
          margin-top: 12px;
        }
      }

      .card-image {
        text-align: center;
        @media (min-width: 768px) {
          text-align: left;
        }

        img {
          width: 210px;
          height: auto;
        }

        .image-caption {
          font-size: 12px;
          color: $textSecondary;
          margin-top: 16px;
        }
      }
      &__secondary {
        flex-direction: column;
        gap: 0;
      }

    }

  }

  .block-09-footer {
    background-color: $greenLight;
    padding-bottom: 32px;
    @media (min-width: 768px) {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 120px;
      padding-bottom: 52px;
    }
  }
}

// Блок 10

.block-10 {
  padding: 80px 20px;
  background-color: $backgroundBlueLight;
  background-image: url("/img/landing/block-10/bg.png");
  background-repeat: no-repeat;
  background-size: 130%;
  background-position: 50% 50%;
  @media (min-width: 768px) {
    padding: 64px 40px;
  }
  @media (max-width: 1024px) {
    background-image: none;
  }


  .block-10-title {
    @include container;
    font-size: 30px;
    margin-bottom: 56px;
    text-align: left;
    color: $textPrimary;
    @media (min-width: 768px) {
      font-size: 40px;
    }
  }

  .block-10-content {
    @include container;
    display: flex;
    flex-direction: column;
    gap: 40px;

    @media (min-width: 1024px) {
      flex-direction: row;
      align-items: flex-start;
      justify-content: flex-start;
    }
  }

  .block-10-section {
    display: flex;
    flex-direction: column;
    gap: 24px;
    flex-shrink: 1;
    flex-basis: 0;
    flex-grow: 0;
    @media (min-width: 600px) {
      flex-direction: column-reverse;
    }
  }

  .block-10-section-text {

    @media (min-width: 1350px) {
      text-align: left;
    }
  }

  .block-10-section-title {
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 8px;
  }

  .block-10-section-description {
    font-size: 14px;
    color: #666;
    @media (max-width: 768px) {
      br {
        display: none;
      }
    }
  }

  .block-10-images {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    @media (min-width: 1024px) {
      flex-wrap: nowrap;
      justify-content: flex-start;
    }
  }

  .block-10-image {
    width: 48%;
    max-width: 200px;
    height: auto;
  }

  @media (min-width: 1024px) {
    .block-10-image {
      width: 120px;
    }
  }

  @media (min-width: 1100px) {
    .block-10-image {
      width: 130px;
    }
  }

  @media (min-width: 1200px) {
    .block-10-image {
      width: 140px;
    }
  }

  @media (min-width: 1350px) {
    .block-10-image {
      width: 164px;
    }
  }

}

</style>
