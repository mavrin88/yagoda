<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/tips/coord"></Link>
          <div class="ytips-top__title">СТОЙКИ YAGODA TIPS</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-text ytips-text_14 ytips-text_padding">
          <p>
            Закажите доставку бесплатной стойки Ягода с QR-кодом.
            Используя стойку Ягода, клиент быстрее найдет ее глазами. Ему будет проще отсканировать QR-код и перевести вам чаевые.
          </p>
          <p>
            Выберите стойку, которая лучше подойдет к вашему интерьеру.
            Расположите её рядом с вашим рабочим столом или месом оплаты за услуги.
          </p>
        </div>
        <div class="ytips-stands">

          <Swiper :modules="modules" :slidesPerView="swiper.slidesPerView" :spaceBetween="swiper.spaceBetween" :pagination="{ clickable: true }">
            <SwiperSlide v-for="(slide,index) in swiper.slides1" :key="index">
              <img :src="slide">
            </SwiperSlide>
          </Swiper>

          <p>Дубовая стойка с пластиковой вставкой.</p>
          <div class="btn btn_low" @click="orderStand('Дубовая стойка с пластиковой вставкой')" v-if="!isFormSent">Заказать</div>
          <div class="btn btn_low btn_grey" v-if="isFormSent" @click="isModalActive = !isModalActive">Стойка заказана</div>
          <p class="text-red-700" v-if="formErrorText"><br>{{formErrorText}}</p>
        </div>

        <div class="ytips-stands" v-if="swiper.slides2.length">
          <!--          todo: когда появится, добавить все нужные переменные -->
          <Swiper :modules="modules" :slidesPerView="swiper.slidesPerView" :spaceBetween="swiper.spaceBetween" :pagination="{ clickable: true }">
            <SwiperSlide v-for="(slide,index) in swiper.slides2" :key="index">
              <img :src="slide">
            </SwiperSlide>
          </Swiper>
          <p>Дубовая стойка с пластиковой вставкой.</p>
          <div class="btn btn_low btn_grey">Заказать</div>
        </div>
      </div>
    </div>

    <Modal type="text" :message="modalMessage" :isActive="isModalActive" :isMessageButton="false" :isHtml="true" @close="modalClose"/>

  </main>
</template>
<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../../../Components/TipsHeader.vue'
import { Pagination } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css/pagination';
import 'swiper/css'
import Modal from '@/Shared/Modal.vue'
import axios from 'axios'

export default {
  name: 'Index',
  components: {
    Modal,
    Link,
    TipsHeader,
    Head,
    Swiper,
    SwiperSlide,
  },
  setup() {
    return {
      modules: [Pagination],
    };
  },
  data() {
    return {
      title: '',
      swiper: {
        slidesPerView: 1,
        spaceBetween: 0,
        slides1: [
          '/img/content/stands/1_1.png',
        ],
        slides2: [
          // '/img/content/stands/2_1.png',
        ],
      },
      isModalActive: false,
      isFormSent: false,
      modalMessage: '<p>Спасибо!</p>' +
        '<br>' +
        '<p><b>Заявка на доставку стойки<br> принята</b></p>' +
        '<br>' +
        '<p class="fs14">В ближайшее время сотрудник отдела<br> Заботы свяжется с вами для <br>согласования деталей доставки.</p>',
      form: {
        type: null
      },
      formErrorText: ''
    }
  },
  props: {
    data: Object,
  },
  mounted() {

  },
  methods: {
    orderStand(type) {
      this.form.type = type;

      axios.post('/tips/form/order-stand', this.form)
        .then((response) => {
          if (!response.data.success) {
            this.formErrorText = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          }

          if (response.data.success) {
            this.isModalActive = true;
            this.isFormSent = true;
            this.formModalMessage = response.data.message || 'Форма успешно отправлена'
            this.formErrorText = null;
          }
        })
        .catch((error, response) => {
          if (response && response.data && response.data.message) {
            this.formErrorText = response.data.message
          } else {
            this.formErrorText = 'Произошла ошибка. Повторите попытку позже.'

          }
        })
        .finally(() => {
          this.isFormModalSubmitting = false;
        })
    },
    modalClose() {
      this.isModalActive = false;
    }
  },
}
</script>
