<template>
  <div class="auth__wrapper" v-if="visibleSms">
    <div class="auth__step">
      <span class="auth__back" @click="closeSms">отменить</span>
      <div class="auth__title">Введите код из телефоного звонка</div>
      <div class="auth__subtitle">звоним на номер <span>{{ phone }}</span></div>
      <form class="form">
        <div class="code">
          <div class="code__inputs">
            <input type="number" id="num1" maxlength="1" v-model="code[0]" @input="handleInput" inputmode="numeric">
            <input type="number" id="num2" maxlength="1" v-model="code[1]" @input="handleInput" inputmode="numeric">
            <input type="number" id="num3" maxlength="1" v-model="code[2]" @input="handleInput" inputmode="numeric">
            <input type="number" id="num4" maxlength="1" v-model="code[3]" @input="handleInput" inputmode="numeric">
          </div>
          <div class="code__error" v-if="errorMessage">{{ errorMessage }}</div>
          <div class="code__text">Запросить новый код через <span>{{ timerValue }}</span> сек</div>
          <div class="code__new" @click="requestNewCode" :class="{ 'disabled': isTimerActive }">Запросить новый код</div>
        </div>

        <div class="form__actions">
          <button class="btn btn_w100" @click.prevent="submit" :disabled="isButtonDisabled">Продолжить</button>
        </div>

      </form>
    </div>
  </div>

</template>

<script>
import axios from 'axios';
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
  },
  computed: {
    isButtonDisabled() {
      return !(this.code[0] !== '' && this.code[1] !== '' && this.code[2] !== '' && this.code[3] !== '');
    }
  },
  data() {
    return {
      code: ['', '', '', ''],
      codeError: false,
      errorMessage: '',
      timerValue: 60,
      isTimerActive: false,
      intervalId: null,
      visibleSms: true,
      visibleChoose: false,
      organizations: [],
      isShowModal: false,
      modalData: null,
      rules: false
    };
  },
  props: {
    phone: {
      type: String,
      required: true
    },
  },
  mounted() {
    this.startTimer();
    this.generateSms();
  },
  beforeUnmount() {
    this.stopTimer();
  },
  methods: {
    closeSms() {
      this.$emit('closeSms')
    },
    handleInput(e) {
      if (e.target.value.length > 1) {
        e.target.value = '';
      } else {
        if (!e.target.nextElementSibling) {

        }else {
          e.target.nextElementSibling.focus();
        }
      }
    },
    submit() {
      this.checkAndSubmitCode();
    },

    startTimer() {
      this.isTimerActive = true;
      this.intervalId = setInterval(() => {
        this.timerValue--;
        if (this.timerValue === 0) {
          this.stopTimer();
        }
      }, 1000);
    },
    stopTimer() {
      clearInterval(this.intervalId);
      this.timerValue = 60;
      this.isTimerActive = false;
    },
    requestNewCode() {
      if (this.isTimerActive) return;
      this.startTimer();
    },

    checkAndSubmitCode() {
      if (this.code.every(digit => digit !== '')) {
        const enteredCode = this.code.join('');

        axios.post('/verifySmsCodeEditNumber', {
          phone: this.phone,
          code: enteredCode
        })
          .then((response) => {
            if (!response.data.success) {
              this.errorMessage = response.data.message || 'Произошла ошибка. Повторите попытку позже.'

              setTimeout(() => {
                this.errorMessage = null;
                this.code = ['', '', '', ''];
              }, 1000);
              return
            }

            if (response.data.success) {
              this.closeSms()
            }
          })
          .catch((error, response) => {
            this.errorMessage = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          })
      }
    },
    generateSms() {
      axios.post('/getNewSmsCodeEditNumber', {
        phone: this.phone
      })
        .then((response) => {
          // Обработка успешного ответа
        })
        .catch((error) => {
          // Обработка ошибки
        });
    },
  },
};
</script>

<style scoped>
.disabled {
  opacity: 0.5;
  pointer-events: none;
}
</style>
