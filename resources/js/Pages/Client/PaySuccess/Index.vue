<template>
  <body class="template-base">
  <main class="page-content">
    <Head title="Успешная оплата!" />
    <div class="client">
      <div class="client-result">
        <div class="client-result__top">
          <!--При ошибке: status-icon_cancel-->
          <div class="client-result__status status-icon"></div>
          <div class="client-result__text">Оплачено!</div>
          <div class="client-result__total">
            <div class="client-result__price">{{ order.full_amount }} <span>₽</span></div>
          </div>
        </div>

        <div class="client-result__review">
          <div class="client-result__block">
            <div class="form__item form__item_logo">
              <div class="form__wrapper">
                <label for="avatar">
                  <img :src="order.organization.logo_path">
                </label>
              </div>
            </div>
            <div>
              <div class="text-center text-green-600 font-bold">{{ messageSuccess }}</div>
            </div>
            <div class="review" v-if="showReviewBlock">
              <div class="client-result__about">Оставьте отзыв о <b>заведении</b></div>
              <div class="client-result__rates">
                <input id="star5" type="radio" name="rating" value="5" v-model="selectedRating">
                <label class="star" for="star5" title="Awesome" aria-hidden="true"></label>
                <input id="star4" type="radio" name="rating" value="4" v-model="selectedRating">
                <label class="star" for="star4" title="Great" aria-hidden="true"></label>
                <input id="star3" type="radio" name="rating" value="3" v-model="selectedRating">
                <label class="star" for="star3" title="Very good" aria-hidden="true"></label>
                <input id="star2" type="radio" name="rating" value="2" v-model="selectedRating">
                <label class="star" for="star2" title="Good" aria-hidden="true"></label>
                <input id="star1" type="radio" name="rating" value="1" v-model="selectedRating">
                <label class="star" for="star1" title="Bad" aria-hidden="true"></label>
              </div>
              <div class="client-result__textarea" v-if="showReviewInput || showReviewInputData">
                <textarea class="js-active" v-model="reviewText"></textarea>
              </div>
              <div class="client-result__action" v-if="showReviewInput || showReviewInputData">
                <button class="btn btn_low" @click="submitReview">Отправить</button>
              </div>
              <div class="client-result__additional-buttons" v-if="showAdditionalButtons">
                <button
                  v-if="showMapLinkYandex"
                  class="btn btn_low border mb-[12px] border-gray-400 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-500 hover:text-gray-700 transition-colors duration-200"
                  @click="openYandexMap(organization.map_link_yandex)"
                >
                  Оставьте отзыв на картах <b class="ml-[6px]"><b class="text-red-600">Я</b>ндекс</b>
                </button>
                <button
                  v-if="showMapLink2Gis"
                  class="btn btn_low border mb-[12px] border-gray-400 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-500 hover:text-gray-700 transition-colors duration-200"
                  @click="open2gisMap(organization.map_link_2gis)"
                >
                  Оставьте отзыв на картах <b class="ml-[6px]">2GIS</b>
                </button>
                <button
                  v-if="!showReviewInputData"
                  class="btn btn_low border mb-[12px] border-gray-400 bg-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-500 hover:text-gray-700 transition-colors duration-200"
                  @click="openYagodaMap()"
                >
                  Оставьте отзыв в <b class="ml-[6px]">Yagoda</b>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  </body>
</template>

<script>

import axios from 'axios'
import { Head } from '@inertiajs/vue3'

export default {
  components: { Head },
  data() {
    return {
      showMapLinkYandex: this.organization.map_link_yandex,
      showMapLink2Gis: this.organization.map_link_2gis,
      selectedRating: null,
      reviewText: '',
      emailField: false,
      emailSendSuccess: false,
      showReviewInputData: false,
      showReviewBlock: this.orderStatistic.rating == 0,
      messageSuccess: this.orderStatistic.rating > 0 ? 'Спасибо за отзыв!' : '',
      error: null,
      form: this.$inertia.form({
        email: '',
      }),
    }
  },
  props: {
    order: {
      type: Object,
      required: true
    },
    organization: {
      type: Object,
      required: true
    },
    orderStatistic: {
      type: Object,
      required: true
    },
  },
  computed: {
    isMaskValid() {
      return this.form.phone.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/) !== null
    },
    showReviewInput() {
      return this.selectedRating && this.selectedRating < 5;
    },
    showAdditionalButtons() {
      return (
        this.selectedRating == 5
      );
    }
  },
  watch: {
    selectedRating() {
      this.showReviewInputData = false
    }
  },
  methods: {
    async submitReview() {
      try {
        const response = await axios.post('/api/submit-review', {
          orderStatisticId: this.orderStatistic.id,
          rating: this.selectedRating,
          review: this.reviewText,
        });

        if (response.data.success) {
          this.reviewText = '';
          this.showReviewBlock = false;
          this.messageSuccess = 'Спасибо! Отзыв сохранён!'
        } else {
        }
      } catch (error) {
        console.error('Ошибка:', error);
      }
    },
    openYandexMap(url) {
        window.open(url, '_blank');
    },
    open2gisMap(url) {
        window.open(url, '_blank');
    },
    openYagodaMap() {
        this.showReviewInputData = true
        this.showMapLinkYandex = false
        this.showMapLink2Gis = false
    },
    // formatPhone() {
    //   if (!this.form.phone.startsWith('+7 ')) {
    //     this.form.phone = '+7 ' + this.form.phone.replace(/^\+7\s?/, '')
    //   }
    // },
    //
    // checkPhone() {
    //   if (!this.form.phone.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/)) {
    //     this.error = 'Номер телефона введен не полностью'
    //   } else {
    //     this.error = null
    //   }
    // },

    checkEmail() {
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

      if (!this.form.email || !emailRegex.test(this.form.email)) {
        this.error = 'Введите правильный адрес электронной почты';
        this.emailSendSuccess = false;
      } else {
        this.error = '';
        this.emailSendSuccess = true;
        this.emailField = false;
        this.sendReceipt();
      }
    },

    showEmailField() {
      this.emailField = !this.emailField;
      this.emailSendSuccess = false;
    },

    sendReceipt() {
      axios.post('/saveEmailInPaySuccess', {
        email: this.form.email,
        orderId: this.order.id
      })
        .then((response) => {
          if (!response.data.status) {

          }
          else {

          }
        })
        .catch((error) => {

        });
    },
  },
}
</script>

<style scoped lang="scss">
.form__item_logo label  {
  cursor: default;
}
.form__item_logo label img {
  max-width: 100%;
  height: auto;
  max-height: 44px;
}
.form__item_logo .form__wrapper {
  width: 139px;
  height: 53px;
  margin-right: auto;
  margin-left: auto;
  border: 1px solid #e5e5e5;
  border-radius: .5625rem;
  background: #fff;
}
.form__item_logo label::before {
  background: none !important;
}
.client-result__textarea textarea {
  padding: 10px;
}
</style>
