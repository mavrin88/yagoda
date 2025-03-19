<template>
  <div class="client-popup" :style="{ display: isVisible ? 'block' : 'none' }">
    <div class="client-popup__top">
      <div class="client-popup__total">К оплате:&nbsp;<b>{{ $formatNumber(total) }} <span>₽</span></b></div>
      <div class="client-popup__close" @click="hidePopup"></div>
    </div>
    <div class="client-popup__fields">
      <div class="client-popup__field">
        <label for="card">номер карты</label>
        <input type="text" id="card" v-mask="'#### #### #### ####'" placeholder="0000 0000 0000 0000" v-model="cardNumber">
      </div>
      <div class="client-popup__group">
        <div class="client-popup__field">
          <label for="date">дата</label>
          <input type="text" id="date" v-mask="'##/##'" placeholder="00/00" v-model="expirationDate">
        </div>
        <div class="client-popup__field">
          <label for="csv">CVC</label>
          <input type="text" id="csv" v-mask="'###'" placeholder="000" v-model="cvcCode">
        </div>
      </div>
    </div>
    <p>Оплатите в течение <b>{{ timeRemaining }}</b></p>
    <button
      class="btn"
      @click="makePayment"
      :disabled="!isFormValid"> <!-- Привязка к атрибуту disabled -->
      Оплатить&nbsp;<b>{{ $formatNumber(total) }} <span>₽</span></b>
    </button>
  </div>
</template>

<script>
export default {
  props: {
    total: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      isVisible: false,
      cardNumber: '',
      expirationDate: '',
      cvcCode: '',
      timeRemaining: '04:59',
      timer: null
    }
  },
  computed: {
    isFormValid() {
      return this.cardNumber.length === 19 &&
        this.expirationDate.length === 5 &&
        this.cvcCode.length === 3;
    }
  },
  methods: {
    showPopup() {
      this.isVisible = true;
      this.startTimer();
    },
    hidePopup() {
      this.isVisible = false;
      this.clearTimer();
    },
    makePayment() {
      const paymentData = {
        cardNumber: this.cardNumber,
        expirationDate: this.expirationDate,
        cvcCode: this.cvcCode,
        total: this.total
      };
      this.$emit('payment-made', paymentData);
      this.hidePopup();
    },
    startTimer() {
      let time = 299;

      this.timer = setInterval(() => {
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;

        this.timeRemaining = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

        if (time <= 0) {
          this.clearTimer();
        }
        time--;
      }, 1000);
    },
    clearTimer() {
      if (this.timer) {
        clearInterval(this.timer);
        this.timer = null;
      }
    }
  },
  beforeDestroy() {
    this.clearTimer();
  }
}
</script>
