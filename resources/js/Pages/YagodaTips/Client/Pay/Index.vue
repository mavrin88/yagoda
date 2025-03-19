<template>
  <div class="ytips-pay">
    <div class="ytips__container">
      <div class="ytips-pay__pay">
        <div class="ytips-pay__top">
          <div class="ytips-pay-master">

            <div class="ytips-pay-master__image">
              <img :src="master.photo_path" width="44" height="44" alt="">
            </div>
            <div class="ytips-pay-master__info">
              <div class="ytips-pay-master__title">{{  master.first_name }}</div>
              <div class="ytips-pay-master__text">{{  master.purpose_tips }}</div>
            </div>
          </div>


          <ClientTop
            :amount="tipsAmount"
            @update:amount="tipsAmount = $event"

            :settings="settings"
            :activeTip="activeTip"
            :total="5000"
            @tipSelected="uncheckActiveTip"
            @customTipsAmount="setCustomTipsAmount"/>

          <ClientTips :group="group" @tipSelected="setActiveTip" ref="tipsRef"/>

        </div>
        <div class="ytips-pay__note">При оплате <span>СБП</span>, деньги мастеру поступят <span>сразу</span>.</div>
      </div>
      <div class="ytips-pay__check-wrapper">
        <!--Шаг 1-->
        <a class="ytips-pay__check flex justify-center items-center text-center" href="#" @click="formShow">
          Хочу получить квитанцию об оплате
        </a>
        <!--Шаг 2-->
        <form class="client-result__form-wrapper" v-if="form.isShow" @submit.prevent>
          <div class="form">
            <div class="form__item">
              <input type="email"
                     placeholder="Email"
                     id="email"
                     v-model="form.email"
                     required
                     :class="{error: form.isTrySending && !form.email}"
              >
              <label for="email">Укажите, куда направить квитанцию</label>
              <div class="form__error" v-if="form.isTrySending && !form.email.length">Это обязательное поле</div>
            </div>
          </div>
          <button class="btn btn_link" type="submit" @click="formSend">Получить квитанцию</button>
        </form>
        <!-- Шаг 3-->
        <p v-if="!form.isShow && form.isSent">Квитанция будет отправлена в ближайшее время.</p>
      </div>

      <ClientRules
        :tips="tipsAmount"
        :group="group"
        @setCommission="setCommission"
        @setFeeConsent="setFeeConsent"
        :globalCommission="globalCommission"
        @modalClose="modalClose"
        @modalOpen="modalOpen"
        @modalData="modalDataSet"
      />

    </div>
  </div>
  <div class="client__cards">
    <div class="client__container">
      <div class="client__sbp">
        <div :class="{'disabled': isButtonDisabled}"
             @click="makePayment('sbp')">
          <span>ОПЛАТИТЬ</span>
          <img src="/img/content/sbp.svg">
        </div>
      </div>
      <div @click="makePayment('card')"
           :class="{'disabled': isButtonDisabled}"
           class="client__add">
        <span>добавить<br>карту</span>
        <img src="/img/content/add-card.svg">
      </div>
    </div>
  </div>

  <ModalPages
    :modalData="modalData"
    v-if="isShowModal"
    @close="modalClose"
  />

</template>

<script>

import ClientTips from '../Pay/Components/ClientTips.vue'
import ClientRules from '../Pay/Components/ClientRules.vue'
import ClientTop from '../Pay/Components/ClientTop.vue'
import axios from 'axios'
import ModalPages from '@/Pages/YagodaTips/Shared/Modals/ModalPages.vue'

export default {
  name: "Index",
  components: {
    ModalPages,
    ClientRules,
    ClientTips,
    ClientTop
  },
  data() {
    return {
      activeTip: 0,
      tipsAmount: 0,
      commissionAmount: 0,
      globalCommission: this.group.commission_for_using_the_service,
      form: {
        email: '',
        isShow: false,
        isSent: false,
        isTrySending: false
      },
      isShowModal: false,
      modalData: '',
      feeConsent: 0
    }
  },
  props: {
    master: Object,
    group: Object,
    settings: Object,
    orderId: Number,
  },
  mounted() {
    if (this.activeTip && this.activeTip.amount) {
      this.tipsAmount = this.activeTip.amount;
    }
  },

  computed: {
    isCanSendForm() {
      return this.form.email.length && this.validateEmail(this.form.email)
    },
    isButtonDisabled() {
      return this.tipsAmount === 0 || this.tipsAmount === null || isNaN(this.tipsAmount);
    },
  },
  methods: {
    setFeeConsent(sum) {
      this.feeConsent = sum
    },
    setCustomTipsAmount(amount) {
      this.tipsAmount = parseInt(amount);
    },
    setCommission(sum) {
      this.commissionAmount = sum
    },
    uncheckActiveTip() {
      const childComponent = this.$refs.tipsRef;
      if (childComponent) {
        childComponent.uncheckedAll();
      }
    },
    setActiveTip(index) {
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
    makePayment(type) {
      axios.post('/tips/make_payment', {
        tip: this.tipsAmount,
        orderId: this.orderId,
        commissionAmount: this.commissionAmount,
        feeConsent: this.feeConsent,
        type: type,
        email_receipt: this.form.email,
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
    },

    //region Form
    formShow() {
      this.form.isShow = true;
    },
    formSend() {
      if (this.isCanSendForm && !this.form.isSent) {
      } else {
        this.form.isTrySending = true
      }
    },
    validateEmail(email) {
      return (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(email));
    },

    modalOpen() {
      this.isShowModal = true
    },
    modalClose() {
      this.isShowModal = false
    },
    modalDataSet(data) {
      this.modalData = data;
    }
  },
  watch: {
    activeTip(newVal) {
      if (newVal && newVal !== '-1' && newVal.amount) {
        this.tipsAmount = newVal.amount;
      }
    },
  }
}
</script>

<style>
.disabled {
  opacity: 0.5; /* Затенение кнопки */
  pointer-events: none; /* Отключение взаимодействия */
}
</style>
