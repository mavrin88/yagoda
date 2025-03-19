<template>
    <div class="client">
      <Head title="Заказ" />
      <div class="client__cover">
        <img class="photo_organization" v-if="data.organization.photo_path" :src="data.organization.photo_path">
        <div class="client__logo" v-if="data.organization.logo_path">
          <img :src="data.organization.logo_path">
        </div>
      </div>
      <div class="client__container">
        <div class="client__services">
          <ClientItem
            v-for="product in data.orderItems"
            :key="product.id"
            :product="product"
            :discount="data.order.discount"
            :itemsTotal="data.orderItems.length"
          />
        </div>

        <ClientTotal v-if="itemsTotal" :total="data.order.total_amount" :items="data.orderItems" :discount="data.order.discount" />

        <div v-if="data.orderParticipants.length > 0" class="client__tips">

          <ClientTop :orderParticipants="data.orderParticipants"
                     :activeTip="activeTip"
                     :settings="data.settings"
                     :total="data.order.total_amount"

                     :amount="tipsAmount"
                     @update:amount="tipsAmount = $event"

                     @tipSelected="uncheckActiveTip"
                     @customTipsAmount="setCustomTipsAmount"
                     ref="topRef"/>

          <ClientTips @tipSelected="setActiveTip"
                      :organization="data.organization"
                      :totalPrice="data.order.total_amount"
                      :discount="data.order.discount"
                      ref="tipsRef"/>

          <span class="text-xs font-light">При оплате <span class="font-medium">СБП</span>, деньги мастеру поступят <span class="font-medium">сразу</span>.</span>

<!--          <ClientRating @rating="ratingEvent"/>-->
        </div>

        <div class="send_email_block">
          <a v-if="!emailField && !emailSendSuccess" @click="showEmailField">Хочу получить квитанцию об оплате</a>

          <form v-if="emailField" class="client-result__form-wrapper" @submit.prevent="checkEmail">
            <div class="form">
              <div class="form__item">
                <input class="form_input" type="email" placeholder="укажите E-mail" id="email" v-model="form.email">
                <a v-if="!error" @click="checkEmail">Отправить на этот E-mail</a>
                <div class="form__error">{{ error }}</div>

              </div>
            </div>
          </form>
          <p v-if="emailSendSuccess" class="form_input">
            Квитанция придет на <span @click="editEmail" class="inline-text">{{ form.email }}</span>
          </p>
        </div>

        <ClientRules
          :discount="data.order.discount"
          :tips="tipsAmount"
          :totalSum="data.order.total_amount"
          :organization="data.organization"
          @setCommission="setCommission"
          @setFeeConsent="setFeeConsent"
          :globalCommission="globalCommission"
        />

        <div class="client__cards">
          <div class="client__container">
            <div class="client__sbp">
              <div @click="makePayment('sbp')" class="payment-container">
                <span class="payment-title">БЫСТРАЯ</span>
                <span class="payment-subtitle">ОПЛАТА</span>
                <img class="sbp" src="/img/content/СБП_логотип.svg">
              </div>
            </div>
<!--            <div @click="showPaymentPopup" class="client__add"><span>добавить<br>карту</span><img src="/img/content/add-card.svg"></div>-->
            <div @click="makePayment('card')" class="client__add"><span>добавить<br>карту</span><img src="/img/content/add-card.svg"></div>
          </div>
        </div>
        <div class="client-popup-overlay" style="display: none;"></div>
        <ClientPopup ref="clientPopup" :total="fullAmount" @payment-made="makePayment"/>
        <div>
          <ClientReview ref="clientReview" />
<!--          <button @click="showReviewForm">Оставить отзыв</button>-->
        </div>
      </div>
    </div>

  <Spiner :isLoading="isLoading"></Spiner>
</template>

<script>

import ClientItem from '@/Pages/Client/Components/ClientItem.vue'
import ClientRating from '@/Pages/Client/Components/ClientRating.vue'
import ClientRules from '@/Pages/Client/Components/ClientRules.vue'
import ClientTips from '@/Pages/Client/Components/ClientTips.vue'
import ClientReview from '@/Pages/Client/Components/ClientReview.vue'
import ClientPopup from '@/Pages/Client/Components/ClientPopup.vue'
import ClientTotal from '@/Pages/Client/Components/ClientTotal.vue'
import ClientTop from '@/Pages/Client/Components/ClientTop.vue'
import Spiner from  '@/Shared/Spiner.vue'
import axios from 'axios'
import { Head } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Spiner,
    ClientItem,
    ClientRating,
    ClientRules,
    ClientTips,
    ClientReview,
    ClientPopup,
    ClientTotal,
    ClientTop
  },
  props: {
    data: {
      type: Object,
      required: true
    },
    // userData: {
    //   type: Object,
    //   required: true
    // },
  },
  data() {
    return {
      isLoading: false,
      emailField: false,
      emailSendSuccess: false,
      error: null,
      form: this.$inertia.form({
        email: '',
      }),
      activeTip: 0,
      rating: 0,
      comment: '',
      tipsAmount: 0,
      commissionAmount: 0,
      feeConsent: 0,
      globalCommission: this.data.settings.commission_for_using_the_service ? this.data.settings.commission_for_using_the_service : '2.5',
      geo: {
        latitude: '',
        longitude: ''
      }
    }
  },
  computed: {
    total() {
      return this.data.orderItems.reduce((acc, product) => parseFloat(acc + product.price), 0);
    },
    fullAmount() {
      return this.total + this.tipsAmount + this.commissionAmount
    },
    totalWithoutCommission() {
      return this.total + this.tipsAmount
    },
    itemsTotal() {
      if (this.data.orderItems.length > 0) {
        // return this.data.orderItems.some(item => item.quantity > 1);
        return true;
      }

      return false;
    }

  },
  mounted() {
    if (this.data && this.data.order.status === 'new') {
      this.getGeoLocation();
    }
  },
  methods: {
    // Получаем гео координаты
    getGeoLocation() {
      console.log('getGeoLocation');
      // Проверяем, отправляли ли мы уже координаты
      if (this.geo.latitude && this.geo.longitude) {
        return;
      }
      if (!navigator.geolocation) {
        console.error('Геолокация не поддерживается');
        return;
      }
      navigator.geolocation.getCurrentPosition(async (position) => {
        this.geo.latitude = position.coords.latitude;
        this.geo.longitude = position.coords.longitude;
      }, (error) => {
        console.error('Ошибка получения геолокации:', error);
      });
    },

    // Сохраняем гео к заказу
    async sendGeoLocation(orderId) {
      console.log('sendGeoLocation');
      if (orderId == null || localStorage.getItem(`geo_saved_${orderId}`) === 'true') {
        return;
      }
      const latitude = this.geo.latitude;
      const longitude = this.geo.longitude;
      if (latitude && longitude) {
        try {
          await axios.post('/api/order/geo', {
            order_id: orderId,
            latitude: latitude,
            longitude: longitude
          });
          // console.error('Геолокация сохранена');
          localStorage.setItem(`geo_saved_${orderId}`, 'true');
        } catch (error) {
          console.error('Ошибка при сохранении геолокации:', error);
        }
      }
    },

    setFeeConsent(sum) {
      this.feeConsent = sum
    },
    setCommission(sum) {
      console.log('setCommission');
      this.commissionAmount = sum
    },
    showReviewForm() {
      console.log('showReviewForm');
      this.$refs.clientReview.showReview()
    },

    showPaymentPopup() {
      console.log('showPaymentPopup');
      // this.$refs.clientPopup.showPopup()
      this.sendOrder()

    },

    sendOrder() {
      console.log('sendOrder');
      this.startLoading();
      this.makePayment()

      setTimeout(() => {
        this.stopLoading()
      }, 1000);
    },

    startLoading() {
      console.log('startLoading');
      this.isLoading = true;
    },
    stopLoading() {
      console.log('stopLoading');
      this.isLoading = false;
    },

    setActiveTip(index) {
      console.log('setActiveTip');
      if (index) {
        this.activeTip = index
        if (index == '-1') {
          this.activeTip = -1;
          this.tipsAmount = 0;
          const childComponent = this.$refs.topRef;
          if (childComponent) {
            childComponent.disableEdited();
          }
        } else {
          this.tipsAmount = this.activeTip.amount
        }
      }
    },
    // Снять все виды чаевых
    uncheckActiveTip() {
      console.log('uncheckActiveTip');
      const childComponent = this.$refs.tipsRef;
      if (childComponent) {
        childComponent.uncheckedAll();
      }

    },
    setCustomTipsAmount(amount) {
      console.log('setCustomTipsAmount');
      this.tipsAmount = parseInt(amount);
    },
    makePayment(type) {
      console.log('makePayment');
      this.checkEmail()

      if (this.error) {
        return
      }

      // Сохраняем гео
      this.sendGeoLocation(this.data.order.id);

      axios.post('/client/makePaymentTest', {
        rating: this.rating,
        comment: this.comment,
        tip: (this.tipsAmount || this.activeTip) === -1 ? 0 : this.tipsAmount || this.activeTip,
        tip_percent: this.activeTip === -1 ? 0 : this.activeTip.percent,
        totalSum: this.total,
        // totalTips: paymentData.total,
        orderId: this.data.order.id,
        commissionAmount: this.commissionAmount,
        feeConsent: this.feeConsent,
        discount: this.data.order.discount,
        type: type,
        email_receipt: this.form.email,
        comp_source: this.data.organization.comp_source,
      })
        .then((response) => {
          // Обработка ответа от сервера
          if (response.data.Data.paymentLink) {
            setTimeout(() => {
              window.location.href = response.data.Data.paymentLink;
            }, 100);
          }
        })
        .catch((error) => {
          alert('Ошибка при оплате');
        });


      this.$refs.clientPopup.hidePopup()
    },
    ratingEvent(rating, text) {
      console.log('ratingEvent');
      // alert(`Rating selected: ${rating}, comment: ${text}`)
      this.rating = rating
      this.comment = text
    },
    showEmailField() {
      console.log('showEmailField');
      this.emailField = !this.emailField;
      this.emailSendSuccess = false;
    },
    checkEmail() {
      console.log('checkEmail');
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;

      // Разрешаем пустую строку
      if (this.form.email.trim() === '') {
        this.error = '';
        this.emailSendSuccess = false; // Или вы можете установить false, если необходимо
        this.emailField = false; // Если нужно скрыть поле, когда оно пустое
      } else if (!emailRegex.test(this.form.email)) {
        this.error = 'Введите правильный адрес электронной почты';
        setTimeout(() => {
          this.error = '';
        }, 2300);
        this.emailSendSuccess = false;
      } else {
        this.error = '';
        this.emailSendSuccess = true;
        this.emailField = false;
      }
    },
    editEmail() {
      console.log('editEmail');
      this.emailSendSuccess = false;
      this.emailField = true;
    },
  },
}
</script>

<style scoped lang="scss">
.service__logo {
  position: absolute;
  top: -0.5rem;
  left: 0.313rem;
  display: flex;
  justify-content: center;
  align-items: center;
}

.client__cover {
  margin-top: 20px;
  max-width: 28rem;
}

.photo_organization {
  width: 100%;
  height: 175px;
  object-fit: cover
}

.client__cards {
  max-width: 28rem;
}

.client__logo img {
  width: auto;
  max-width: 100%;
  height: auto;
  max-height: 44px;
}

.sbp {
  border-style: none;
  max-width: 100%;
  height: auto;
  width: 60px;
}

.payment-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  cursor: pointer;
  text-align: center;
  width: 100%;
}

.payment-title {
  font-size: 14px;
}

.payment-subtitle {
  font-size: 14px;
}

.sbp {
  width: 50px;
  height: auto;
}
.send_email_block {
  margin-top: 20px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;

  a {
    font-size: 12px;
    font-weight: 400;
    line-height: 14.63px;
    cursor: pointer;
    margin-bottom: 15px;
    text-decoration: underline;
  }

  .client-result__form-wrapper {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
    border-radius: 16px !important;
    height: 88px;

    .form__item {
      display: flex;
      flex-direction: column;
      gap: 10px;
      align-items: center;

      input {
        font-weight: 500;
        border-bottom: none !important;
      }
    }
  }
}
.form_input {
  font-size: 12px;
  font-weight: 400;
  line-height: 14.63px;
  color: #333;
  text-decoration: none;
}
.inline-container {
  display: inline-flex;
  align-items: center;
}

.inline-text {
  display: inline-block;
  font-size: 12px;
  font-weight: 400;
  line-height: 14.63px;
  color: #333;
}
.inline-text {
  text-decoration: underline;
  font-weight: 500;
  cursor: pointer;
}

</style>
