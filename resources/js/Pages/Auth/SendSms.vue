<template>
    <div class="auth__wrapper">
      <Head title="Авторизация" />
      <div class="auth__step">
        <Link class="auth__back" href="/login">назад</Link>
        <div class="auth__title">Введите код из SMS</div>
        <div class="auth__subtitle">отправлен на номер <span>+{{ phone }}</span></div>
        <form class="form">
          <div class="code">
            <div class="code__inputs">
              <input type="number" id="num1" maxlength="1" v-model="code[0]" @input="checkAndSubmitCode" inputmode="numeric">
              <input type="number" id="num2" maxlength="1" v-model="code[1]" @input="checkAndSubmitCode" inputmode="numeric">
              <input type="number" id="num3" maxlength="1" v-model="code[2]" @input="checkAndSubmitCode" inputmode="numeric">
              <input type="number" id="num4" maxlength="1" v-model="code[3]" @input="checkAndSubmitCode" inputmode="numeric">
            </div>
            <div class="code__error" v-if="errorMessage">{{ errorMessage }}</div>
            <div class="code__text">Запросить новый код через <span>{{ timerValue }}</span> сек</div>
            <div class="code__new" @click="requestNewCode" :class="{ 'disabled': isTimerActive }">Запросить новый код</div>
          </div>
        </form>
      </div>
    </div>

</template>

<script>
import axios from 'axios';
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: { Head, Link },
  data() {
    return {
      code: ['', '', '', ''],
      codeError: false,
      errorMessage: '',
      timerValue: 30,
      isTimerActive: false,
      intervalId: null,
    };
  },
  props: {
    phone: {
      type: String,
      required: true
    }
  },
  mounted() {
    this.startTimer();
  },
  beforeUnmount() {
    this.stopTimer();
  },
  methods: {
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
      axios.post('/request-new-code')
        .then((response) => {
          // Обработка успешного ответа
        })
        .catch((error) => {
          // Обработка ошибки
        });
    },
    checkAndSubmitCode() {
      if (this.code.every(digit => digit !== '')) {
        const enteredCode = this.code.join('');
        axios.post('/login', {
          code: enteredCode,
          phone: this.phone,
          password: 'secret',
        })
          .then((response) => {
            if (!response.data.success) {
              this.errorMessage = response.data.message

              setTimeout(() => {
                this.errorMessage = null;
                this.code = ['', '', '', ''];
              }, 1000);
            }
          })
          .catch((error) => {
            this.codeError = true;
            this.code = ['', '', '', '']
          });
      }
    // },
    // verifyCode() {
    //   this.$inertia.post('/login', { code: 1233 })
    //     .then(() => {
    //       // Успешная авторизация
    //     })
    //     .catch((error) => {
    //       this.error = error.response.data.errors.code[0];
    //     });
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
