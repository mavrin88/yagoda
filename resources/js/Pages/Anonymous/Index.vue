<template>
  <div class="lp">
    <header class="header">
      <div class="container">
        <div class="header__logo">
          <img src="/img/content/lp-logo.svg" alt="">
          <p>оплата и чаевые в один клик</p>
        </div>
        <Link class="header__login" href="/login"><img src="/img/content/lp-login.svg" alt=""></Link>
        <div class="header__burger" @click="isBurgerOpened = !isBurgerOpened"><span></span><span></span><span></span></div>
      </div>
    </header>

    <div class="lp__block" id="about">
      <div class="container">
        <div class="lp-1">
          <div class="lp-1__item">
            <div class="lp-1__title"><b>уменьшение</b> комиссии за эквайринг</div>
            <div class="text-center">
              <div class="lp-1__text">вместо <b> 2,5%+</b></div>
              <div class="lp-1__special">0,69<span>%</span></div>
              <div class="lp-1__text">одинаково для карт и СБП</div>
            </div>
          </div>
          <div class="lp-1__item">
            <div class="lp-1__title"><b>УВЕЛИЧЕНИЕ</b> ЧАЕВЫХ</div>
            <div class="text-center">
              <div class="lp-1__text">вместо <b>10-30%</b></div>
              <div class="lp-1__special">70-80<span>%</span></div>
              <div class="lp-1__text">клиентов оставят чаевые</div>
            </div>
            <div class="lp-1__button">
              <a class="btn" href="#" @click.prevent="formModalOpen()">Подать заявку</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="lp__block lp__block_nopb lp__block_grey" id="how">
      <div class="lp-2">
        <div class="container">
          <div class="lp-2__top">для оплаты</div>
          <div class="lp-2__slider">
          <swiper
            :modules="modules"
            :slides-per-view="1"
            :space-between="50"
            :autoHeight="true"
            @swiper="onSwiper"
            @slideChange="onSlideChange"

            :pagination="{ clickable: true }"
          >
            <swiper-slide class="lp-2__slide">
              <div class="lp-2__text">КЛИЕНТ СКАНИРУЕТ QR<br>
                на стойке администратора<br>
                или с его телефона</div>
              <div class="lp-2__image">
                <img src="/img/content/slide1-1.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-2__slide">
              <div class="lp-2__text">ВЫБИРАЕТ РАЗМЕР<br>
                ЧАЕВЫХ</div>
              <div class="lp-2__image">
                <img src="/img/content/slide1-2.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-2__slide">
              <div class="lp-2__text">ОПЛАЧИВАЕТ<br>
                УСЛУГИ И ЧАЕВЫЕ ОДНИМ<br>
                ПЛАТЕЖОМ</div>
              <div class="lp-2__image">
                <img src="/img/content/slide1-3.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-2__slide">
              <div class="lp-2__text">ДЕНЬГИ ПОСТУПАЮТ<br>
                ОРГАНИЗАЦИИ И МАСТЕРА<br>
                сразу, без комиссий</div>
              <div class="lp-2__image">
                <img src="/img/content/slide1-4.png" alt="">
              </div>
            </swiper-slide>
          </swiper>
          </div>



        </div>
      </div>
    </div>

    <div class="lp__block lp__block_nopb">
      <div class="lp-3">
        <div class="container">
          <div class="lp-3__top">
            <b>для создания заказа</b><br>
            через нашу CRM
          </div>
          <div class="lp-3__slider">
          <swiper
            :modules="modules"
            :slides-per-view="1"
            :space-between="50"
            :autoHeight="true"
            @swiper="onSwiper"
            @slideChange="onSlideChange"

            :pagination="{ clickable: true }"
          >
            <swiper-slide class="lp-3__slide">
              <div class="lp-3__text">ВЫБРАТЬ УСЛУГИ<br>
                или указать произвольную<br>
                сумму для оплаты</div>
              <div class="lp-3__image">
                <img src="/img/content/slider2-1.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-3__slide">
              <div class="lp-3__text">ВЫБРАТЬ МАСТЕРА/<br>
                ИСПОЛНИТЕЛЯ</div>
              <div class="lp-3__image">
                <img src="/img/content/slider2-2.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-3__slide">
              <div class="lp-3__text">ПОПРОСИТЬ КЛИЕНТА<br>
                ОТСКАНИРОВАТЬ QR</div>
              <div class="lp-3__image">
                <img src="/img/content/slider2-3.png" alt="">
              </div>
            </swiper-slide>
            <swiper-slide class="lp-3__slide">
              <div class="lp-3__text">ПРОКОНТРОЛИРОВАТЬ<br> ОПЛАТУ</div>
              <div class="lp-3__image">
                <img src="/img/content/slider2-4.png" alt="">
              </div>
            </swiper-slide>
          </swiper>
          </div>



        </div>
      </div>
    </div>

    <div class="lp__block lp__block_green" id="features">
      <div class="lp-4">
        <div class="container">
          <div class="lp-4__text">
            <p><b>Оплата через телефон</b> хоть и чуть дольше, чем просто приложить карту, зато:</p>
            <p>
              <ul>
                <li>Вы <b>экономите 1.8%+</b> от выручки!</li>
                <li>Мастер получает <b>чаевых в разы больше</b>.</li>
                <li><b>Лояльность</b> сотрудников к компании повышается.</li>
                <li><b>Качество работы повышается</b>, для получения больших чаевых.</li>
                <li><b>Удовлетворенность клиентов растет</b> через большую вовлеченность сотрудников в работу.</li>
            </ul>
            </p>
            <p>В итоге, все становятся немножко более <b>счастливыми</b>.)</p>
          </div>
          <div class="lp-4__image">
            <img src="/img/content/lp-payment.png" alt="">
          </div>
          <div class="lp-4__button">
            <a href="#" class="btn btn_low" @click.prevent="formModalOpen()">Подать заявку</a>
          </div>
        </div>
      </div>
    </div>

    <div class="lp__block lp__block_grey">
      <div class="lp-5">
        <div class="container">
          <div class="lp-5__border">
            <div class="lp-5__title">Обратите внимание</div>
            <div class="lp-5__text">
              Подключение к сервису <b>не ограничивает вас</b>  использовать  привычный платежный терминал для приема оплаты.
            </div>

          </div>
        </div>
      </div>
    </div>

    <div class="lp__block" id="service">
      <div class="lp-6">
        <div class="container">
          <div class="lp-6__title">Условия для вас</div>
          <div class="lp-6__columns">
            <div class="lp-6__column">
              <div class="lp-6__percent">0,69<span>%</span></div>
            </div>
            <div class="lp-6__column">
              <div class="lp-6__text">
                эквайринг<br>
                <span>при оплате картой или СБП</span>
              </div>
            </div>
          </div>

          <div class="lp-6__columns">
            <div class="lp-6__column">
              <div class="lp-6__null"><b>0</b> <span>₽</span></div>
            </div>
            <div class="lp-6__column">
              <div class="lp-6__text">
                <p>абонентская<br>плата </p>
                <p>вывод чаевых</p>
                <p>подключение,<br> обучение и <br>сопровождение</p>
              </div>
            </div>
          </div>

          <div class="lp-6__image">
            <img src="/img/content/lp6.png" alt="">
          </div>
          <div class="lp-6__button">
            <a href="#form" class="btn btn_low" @click.prevent="formModalOpen()">Подать заявку</a>
          </div>

        </div>
      </div>
    </div>

    <div class="lp__block lp__block_grey" id="steps">
      <div class="lp-7">
        <div class="container">
          <div class="lp-7__title">
            Этапы интеграции
          </div>
          <div class="lp-7__text">
            <ol>
              <li>Подать заявку на подключение</li>
              <li>Подписать договор</li>
              <li>Обучение и поддержка</li>
            </ol>
            <p>стойки с QR-кодами предоставляются бесплатно</p>
          </div>
          <div class="lp-7__image">
            <img src="/img/content/lp7.png" alt="">
          </div>
        </div>
      </div>
    </div>

    <div class="lp__block" id="crm" v-if="isHasIntegration">
      <div class="lp-8">
        <div class="container">
          <div class="lp-8__title">
            Интеграция с CRM
          </div>
          <div class="lp-8__text">
            <p>На даный момент возможна с:</p>
            <div class="lp-8__firms">
              <img src="/img/content/yclients.svg" alt="">
            </div>
            <p>Или используйте <b>нашу мини-CRM</b>.</p>
            <p>В ней возможно завести каталог товаров/услуг или создать заказ на произвольную сумму.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="lp__block lp__block_green" id="form" v-if="isHasFullForm">
      <!-- todo: Добавить сюда галочку с политикой конфиденциальности!!! -->
      <div class="lp-9">
        <div class="container">
          <div class="lp-9__title">
            Подать заявку <br>на подключение
          </div>
          <div class="lp-9__form form" v-if="!isFormSent">
            <div class="form__item">
              <input type="text" id="firm" v-model="formConnect.firm" :class="{error: isSubmitted && !formConnect.firm}">
              <label for="firm">Название организации</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.firm">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="text" id="inn" v-model="formConnect.inn" :class="{error: isSubmitted && !formConnect.inn}">
              <label for="inn">ИНН</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.inn">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="text" id="name"  v-model="formConnect.name" :class="{error: isSubmitted && !formConnect.name}">
              <label for="name">Контактное лицо</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.name">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="tel" id="phone" v-mask="'+7 (###) ###-##-##'"  v-model="formConnect.phone" :class="{error: isSubmitted && !formConnect.phone}">
              <label for="phone">Телефон</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.phone">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="text" id="city"  v-model="formConnect.city" :class="{error: isSubmitted && !formConnect.city}">
              <label for="city">Город</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.city">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="text" id="crm"  v-model="formConnect.crm" :class="{error: isSubmitted && !formConnect.crm}">
              <label for="crm">CRM (текущая ваша учетная система)</label>
              <div class="form__error" v-if="isSubmitted && !formConnect.crm">Поле не может быть пустым</div>
            </div>
            <div class="form__actions">
              <button type="submit" class="btn btn_low" @click="submitForm">Отправить заявку</button>
            </div>
          </div>

          <div v-if="isFormSent">{{formMessage}}</div>

        </div>
      </div>
    </div>

    <div class="lp__block" id="contacts">
      <div class="lp-footer">
        <div class="container">
          <div class="lp-footer__text">
            <p>ООО “Ягода”</p>
            <p>ИНН 0800024353</p>
            <p>ОГРН 1240800010604</p>
            <p>ОКПО 96757038</p>
            <p>Юр. адрес: 358000, Республика Калмыкия, г. Элиста, ул. В.И. Ленина, дом 268, помещ. 3, рабочее место 1.</p>
            <p>Контакты: welcome@yagoda.team</p>
          </div>
          <div class="lp-footer__social">
            <a href="https://t.me/+79153690251">
              <img src="/img/content/i_tg.svg" alt="">
            </a>
            <a href="https://wa.me/79153690251">
              <img src="/img/content/i_wa.svg" alt="">
            </a>
            <a class="lp-footer__phone" href="tel:+74992132233">
              +7 (499) 213-22-33
            </a>
          </div>
        </div>
      </div>
    </div>


    <div class="burger-block" v-if="isBurgerOpened">
      <div class="burger-block__overlay"></div>
      <div class="burger-block__content">
        <div class="burger-block__close" @click="closeBurger()"></div>
        <div class="burger-block__menu">
          <ul>
            <li><Link class="burger-block__login" href="/login">Войти</Link></li>
            <li><a href="#about" @click="closeBurger">О сервисе</a></li>
            <li><a href="#how" @click="closeBurger">Как пользоваться</a></li>
            <li><a href="#features" @click="closeBurger">Преимущества</a></li>
            <li><a href="#service" @click="closeBurger">Условия для вас</a></li>
            <li><a href="#steps" @click="closeBurger">Этапы интеграции</a></li>
            <li><a href="#crm" @click="closeBurger">Интеграция с CRM</a></li>
            <li><a href="#contacts" @click="closeBurger">Контакты</a></li>
          </ul>
        </div>

        <div class="burger-block__contacts">
          <p><a href="mailto:welcome@yagoda.team">welcome@yagoda.team</a></p>
          <div class="burger-block__socials">
            <a href="https://t.me/+79153690251 ">
              <img src="/img/content/i_tg.svg" alt="">
            </a>
            <a href="https://wa.me/+79153690251 ">
              <img src="/img/content/i_wa.svg" alt="">
            </a>
          </div>
          <a href="#form" class="btn btn_low" @click="closeBurger; formModalOpen();">Подать заявку</a>
        </div>
        <div class="burger-block__links">
          <p><a href="/pages/privacy" target="_blank">Политика конфиденциальности</a></p>
          <p><a href="/pages/user_agreement" target="_blank">Пользовательское соглашение</a></p>
        </div>


      </div>
    </div>


    <div class="modal-landing" v-if="isFormModalOpened">
      <div class="modal-landing__wrapper">
        <div class="modal-landing__close" @click="isFormModalOpened = !isFormModalOpened"></div>
        <div class="modal-landing__content">
          <h2>Подать заявку</h2>
          <div class="lp-9__form form" v-if="!isFormModalSent">
            <div class="form__item">
              <input type="text" id="name"  v-model="formModal.name" :class="{error: isFormModalSubmitted && !formModal.name}">
              <label for="name">Контактное лицо</label>
              <div class="form__error" v-if="isFormModalSubmitted && !formModal.name">Поле не может быть пустым</div>
            </div>
            <div class="form__item">
              <input type="tel" id="phone" v-mask="'+7 (###) ###-##-##'"  v-model="formModal.phone" :class="{error: isFormModalSubmitted && !formModal.phone}">
              <label for="phone">Телефон</label>
              <div class="form__error" v-if="isFormModalSubmitted && !formModal.phone">{{formModalPhoneError1}}</div>
              <div class="form__error" v-if="isFormModalSubmitted && (formModal.phone && formModal.phone.length < 18)">{{formModalPhoneError2}}</div>
            </div>
            <div class="form__item form__item_inline">
              <input type="checkbox" required id="privacy" v-model="formModal.privacy" :class="{error: isFormModalSubmitted && !formModal.privacy}">
              <label for="privacy">Согласен с&nbsp; <a href="/pages/privacy" target="_blank">политикой конфиденциальности</a></label>
              <div class="form__error" v-if="isFormModalSubmitted && !formModal.privacy">{{ formModalPrivacyError }}</div>
            </div>
            <div class="form__actions">
              <button type="submit" class="btn btn_low" @click="submitFormModal">Отправить заявку</button>
            </div>
            <div class="form__error" v-if="formModalError">{{formModalError}}</div>
          </div>

          <div v-if="isFormModalSent">{{formModalMessage}}</div>
        </div>
      </div>
      <div class="modal-landing__overlay" @click="isFormModalOpened = !isFormModalOpened"></div>
    </div>


    <div class="lp__phone-icon" @click="formModalOpen()"><img src="/img/content/icon_phone.png" alt=""></div>
    <a href="tel:+74992132233" class="lp__phone-icon lp__phone-icon_mobile"><img src="/img/content/icon_phone.png" alt=""></a>


  </div>
</template>
<script>

// import Swiper core and required modules
import {Pagination} from 'swiper/modules';

// Import Swiper Vue.js components
import { Swiper, SwiperSlide } from 'swiper/vue';
// Import Swiper styles
import 'swiper/css';
import 'swiper/css/pagination';
import { Link } from '@inertiajs/vue3'
import axios from 'axios'

export default {
  components: {
    Link,
    Swiper,
    SwiperSlide,
  },
  setup() {
    const onSwiper = (swiper) => {

    };
    const onSlideChange = () => {

    };
    return {
      onSwiper,
      onSlideChange,
      modules: [Pagination],
    };
  },

  data() {
    return {
      isHasIntegration: false,
      isHasFullForm: false,
      isBurgerOpened: false,
      isSubmitted: false,
      formConnect: {
        firm: null,
        inn: null,
        name: null,
        phone: null,
        city: null,
        crm: null,
        privacy: null
      },
      isFormSent: false,
      isSubmitting: false,
      formError: '',
      formMessage: '',
      // Popup form
      isFormModalOpened: false,
      isFormModalSubmitted: false,
      formModal: {
        name: null,
        phone: null,
        privacy: null
      },
      isFormModalSent: false,
      isFormModalSubmitting: false,
      formModalError: '',
      formModalMessage: '',
      formModalPhoneError1: 'Поле не может быть пустым',
      formModalPhoneError2: 'Пожалуйста, проверьте номер телефона',
      formModalPrivacyError: 'Обязательное поле'
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
    isCanSendForm() {
      // При изменении поменять валидацию и в FormController.php
      return this.formConnect.firm &&
        this.formConnect.inn && this.formConnect.name && this.formConnect.phone && this.formConnect.city && this.formConnect.crm;
    },
    isCanSendFormModal() {
      // При изменении поменять валидацию и в FormController.php
      return this.formModal.phone && this.formModal.phone.length === 18;
    }
  },

  methods: {
    submitForm() {
      // Если запрос уже выполняется, блокируем
      if (this.isSubmitting) return;

      this.isSubmitting = true;

      this.isSubmitted = true;

      if (this.isCanSendForm && !this.isFormSent) {

        axios.post('/form/connect', this.formConnect)
          .then((response) => {
            if (!response.data.success) {
              this.formError = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
              this.isFormSent = false
            }

            if (response.data.success) {
              this.isFormSent = true
              this.formMessage = response.data.message || 'Форма успешно отправлена'
              this.formConnect.firm = ''
              this.formConnect.inn = ''
              this.formConnect.name = ''
              this.formConnect.phone = ''
              this.formConnect.city = ''
              this.formConnect.crm = ''

            }
          })
          .catch((error, response) => {
            this.isFormSent = false
            this.formError = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          })
          .finally(() => {
            this.isSubmitting = false;
          })
      }

    },
    closeBurger() {
      this.isBurgerOpened = false;
    },
    formModalOpen() {
      this.isFormModalOpened = true;
      if (typeof ym === 'function') {
        ym(98232814, 'reachGoal', 'form_modal_click');
      }
    },
    submitFormModal() {
      // Если запрос уже выполняется, блокируем
      if (this.isFormModalSubmitting) return;

      this.isFormModalSubmitted = true;

      if (this.isCanSendFormModal && !this.isFormModalSent) {

        this.isFormModalSubmitting = true;

        axios.post('/form/front-modal', this.formModal)
          .then((response) => {
            if (!response.data.success) {
              this.formModalError = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
              this.isFormModalSent = false
            }

            if (response.data.success) {
              this.formModalPhoneError1 = '';
              this.formModalPhoneError2 = '';
              this.isFormModalSent = true;
              this.formModalMessage = response.data.message || 'Форма успешно отправлена'
              if (this.isFormModalSent && this.formModalMessage) {
                this.formModal.name = '';
                this.formModal.phone = '';
              }
              if (typeof ym === 'function') {
                ym(98232814, 'reachGoal', 'form_modal_submit');
              }
            }
          })
          .catch((error, response) => {
            this.isFormModalSent = false
            this.formModalError = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          })
          .finally(() => {
            this.isFormModalSubmitting = false;
          })
      }

    }
  }
};

</script>
<style lang="scss" scoped>
@use "sass:math";@use "sass:meta";
@function rem($pixels, $context: 16px) {
  // Если $pixels не имеет единиц измерения, явно добавляем px
  @if meta.type-of($pixels) == 'number' and math.is-unitless($pixels) {
    $pixels: $pixels * 1px;
  }

  // Если $context не имеет единиц измерения, явно добавляем px
  @if meta.type-of($context) == 'number' and math.is-unitless($context) {
    $context: $context * 1px;
  }

  // Вычисляем и возвращаем результат
  @return calc($pixels / $context) * 1rem;
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


.lp {
  font-family: 'Montserrat', Arial, sans-serif;

  * {
    -webkit-box-sizing: border-box;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
  }


  .header {
    background: rgba(56, 176, 176, 1);
    .container {
      display: flex;
      align-items: center;
      justify-content: space-between;
      min-height: rem(111);
    }
    &__logo {
      img {

      }
      p {
        margin: 4px 0 0 0;
        padding: 0;
        font-size: 11px;
        font-weight: 600;
        color: #fff;
      }
    }
    &__login {
      margin-left: auto;
      margin-right: rem(30);
      &:hover {
        opacity: .8;
      }
    }
    &__burger {
      display: flex;
      flex-wrap: wrap;
      width: rem(33);
      max-width: rem(33);
      gap: rem(7);
      margin-top: rem(-4px);
      cursor: pointer;
      span {
        display: block;
        min-width: 100%;
        width: 100%;
        background: #fff;
        border-radius: rem(3);
        height: 3px;
      }
    }
  }


  .container {
    width: 100%;
    max-width: 375px;
    margin: 0 auto;
  }

  &__block {
    padding: rem(23) 0;
    &_grey {
      background: rgba(237, 237, 237, 1);

    }
    &_green {
      background: rgba(56, 176, 176, 1);
    }
    &_nopb,
    &_2 {
      padding-bottom: 0;
    }
  }


  .lp-1 {
    &__item {
      margin-bottom: rem(53);
      &:last-child {
        margin-bottom: 0;
      }
    }

    &__title {
      font-size: rem(22);
      text-transform: uppercase;
      margin-bottom: rem(7);
    }

    &__text {
      color: rgba(153, 153, 153, 1);
      b {
        font-size: 22px;
        color: #000;
        text-decoration: line-through;
      }
    }
    &__special {
      font-size: 64px;
      color: rgba(56, 176, 176, 1);
      font-weight: 700;
    }
    &__button {
      margin-top: rem(34);
    }
  }

  .lp-2 {
    &__top {
      text-align: center;
      font-weight: 700;
      margin-bottom: rem(36);
    }
    &__slider {
      text-align: center;
    }
    &__text {
      min-height: rem(81);
      margin-bottom: rem(32);
      font-size: 22px;
    }
    &__image {

    }
    img {
      vertical-align: bottom;
    }
  }

  .lp-3 {
    &__top {
      text-align: center;
      margin-bottom: rem(36);
    }
    &__slider {
      text-align: center;
    }
    &__text {
      min-height: rem(81);
      margin-bottom: rem(32);
      font-size: 22px;
    }
    &__image {

    }
    img {
      vertical-align: bottom;
    }
  }


  .lp-4 {
    color: #fff;
    line-height: 140%;
    &__image {
      margin-top: rem(13);
      img {
        vertical-align: bottom;
      }
    }
    ul {
      list-style: disc;
      padding-left: 24px;
    }
  }


  .lp-5 {
    line-height: 140%;
    &__border {
      border: 2px solid #F0CA57;
      border-radius: rem(8);
      padding: rem(30) rem(21);
    }
    &__title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: rem(20);
      text-transform: uppercase;
    }
    &__text {

    }
  }


  .lp-6 {

    &__title {
      font-size: 22px;
      font-weight: 700;
      text-transform: uppercase;
      margin-bottom: rem(34);
    }
    &__columns {
      display: flex;
      align-items: flex-start;
      gap: rem(36);
      margin-bottom: rem(40);
      &:last-child {
        margin-bottom: 0;
      }
    }
    &__column {
      &:nth-child(1) {
        min-width: rem(129);
      }
    }

    &__percent {
      font-size: 41px;
      font-weight: 700;
      color: #38B0B0;
      span {
        font-weight: 400;
        font-size: 20px;
        color: #000;
      }
    }
    &__null {
      font-size: 20px;
      color: #000;
      position: relative;
      b {
        font-size: 334px;
        font-weight: 700;
        color: #38B0B0;
        margin-left: rem(-97);
        line-height: rem(237px);
      }
      span {
        position: absolute;
        bottom: 0;
        right: 0;
      }
    }

    &__text {
      font-size: 20px;
      line-height: 120%;
      span {
        font-size: 12px;
      }
      p {
        margin-bottom: rem(30);
      }
    }

    &__image {
      margin-top: rem(13);
      text-align: center;
      img {
        vertical-align: bottom;
      }
    }
  }


  .lp-7 {
    padding-top: rem(60);
    padding-bottom: rem(80);
    &__title {
      font-size: 22px;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: rem(16);
    }
    &__text {
      margin-bottom: rem(50);
      line-height: 160%;
      ol {
        padding-left: rem(20);
        margin-bottom: rem(12);
      }
    }
    &__image {
      margin-top: rem(50);
      text-align: center;
    }
  }


  .lp-8 {
    padding-top: rem(60);
    padding-bottom: rem(40);
    &__title {
      font-size: 22px;
      font-weight: bold;
      text-transform: uppercase;
      margin-bottom: rem(18);
    }
    &__text {
      p {
        margin-bottom: rem(12);
      }
    }
    &__firms {
      margin: rem(16) 0;
    }
  }


  .lp-9 {
    color: #fff;
    padding-top: rem(60);
    padding-bottom: rem(90);

    &__title {
      font-size: 22px;
      font-weight: 700;
      margin-bottom: rem(30);
      text-transform: uppercase;
      line-height: 160%;
    }

    &__form {

    }

    .form {
      &__item {
        input {
          background: none;
          border: 0;
          border-bottom: 1px solid #fff;
          color: #fff;
          font-size: 20px;
        }
        label {
          color: #fff;
          margin-top: rem(6);
        }
      }

      &__actions {
        margin-top: rem(40);
      }

      .btn {

      }

    }
  }


  .lp-footer {
    padding-top: rem(10);
    &__text {
      line-height: 140%;
      font-size: 12px;
      p {
        margin-bottom: rem(4);
      }
    }
    &__social {
      margin-top: rem(26);
      display: flex;
      align-items: center;
      gap: rem(19)
    }
  }

  .burger-block {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    z-index: 99;

    &__overlay {
      background: rgba(255, 255, 255, 1);
      opacity: .5;
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: 1;
    }

    &__close {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      position: absolute;
      top: rem(16);
      right: rem(16);
      cursor: pointer;
      &::before,
      &::after {
        content: '';
        display: block;
        width: rem(20);
        height: rem(2);
        background: rgba(64, 72, 84, 1);
        position: absolute;
        top: rem(10);
        left: 0;
      }
      &::before {
        transform: rotate(45deg);
      }
      &::after {
        transform: rotate(-45deg);
      }
      &:hover {
        opacity: .7;
      }
    }

    &__content {
      max-width: 261px;
      min-width: 261px;
      background: rgba(56, 176, 176, 1);
      color: #000;
      z-index: 2;
      position: relative;
      margin-left: auto;
      padding: rem(81) rem(28);
      overflow: auto;
      max-height: 100vh;

      @media only screen and (max-height: 665px) {
        padding-top: rem(30);
      }

    }

    &__menu {
      ul {
        li {
          margin-bottom: rem(18);
          a {
            color: #000;
          }
        }
      }
    }

    &__login {
      font-size: 24px;
      font-weight: 700;
    }

    &__contacts {
      margin-top: rem(156);
      @media only screen and (max-height: 665px) {
        margin-top: rem(50);
      }
    }

    &__socials {
      display: flex;
      gap: rem(19);
      margin-top: rem(22);
      margin-bottom: rem(26);
    }

    &__contacts {
      a {
        color: #fff;
        font-size: 14px;
      }
    }

    &__links {
      margin-top: 10px;
      p {
        margin-bottom: 6px;
      }
      a {
        font-size: 12px;
        color: #fff;
      }
    }

  }

  @keyframes pulse {
    0% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(56, 176, 176, 0.7);
    }
    50% {
      transform: scale(1.1);
      box-shadow: 0 0 10px 10px rgba(56, 176, 176, 0.3);
    }
    100% {
      transform: scale(1);
      box-shadow: 0 0 0 0 rgba(56, 176, 176, 0);
    }
  }

  &__phone-icon {
    border-radius: 50%;
    background: rgb(56, 176, 176);
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    bottom: 50px;
    right: 50px;
    cursor: pointer;
    padding: 20px;
    width: 80px;
    height: 80px;
    animation: pulse 2s infinite ease-in-out;
    animation-delay: 3s;
    border: 1px solid #fff;
    z-index: 99998;
    @media only screen and (max-width: 768px) {
      display: none;
    }
    img {
      max-width: 100%;
      height: auto;
    }
    &_mobile {
      display: none;
      width: 60px;
      height: 60px;
      bottom: 30px;
      right: 30px;
      padding: 12px;
      @media only screen and (max-width: 768px) {
        display: flex;
      }
    }
  }

}

.swiper {
  .swiper-pagination {
    background: rgba(56, 176, 176, 1);
    min-height: 26px;
    display: inline-flex;
    width: auto;
    margin-left: auto;
    margin-right: auto;
    align-items: center;
    border-radius: rem(12);
    padding-left: rem(15);
    padding-right: rem(15);
    transform: translateX(-50%);
    left: 50%;

    .swiper-pagination-bullet {
      background: #fff;
      width: 12px;
      height: 12px;
      opacity: 1;

      &.swiper-pagination-bullet-active {
        background: #000;
      }
    }

  }
}

.text-center {
  text-align: center;
}

</style>
