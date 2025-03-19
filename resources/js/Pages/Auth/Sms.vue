<template>
  <div class="auth__wrapper">
    <Head title="Авторизация" />
    <div class="auth__step">
      <Link class="auth__back" href="/login">назад</Link>
      <!--      <div class="auth__title">Введите последние 4 символа входящего звонка</div>-->
      <div class="auth__title">Введите код из СМС</div>
      <!--      <div class="auth__subtitle">звоним на номер <span>{{ phone }}</span></div>-->
      <div class="auth__subtitle">отправлена на номер <span>{{ phone }}</span></div>
      <form class="form">
        <div class="code">

          <div class="code__inputs">
            <div class="otp-wrapper" @click="focusInput">
              <input
                ref="otpInput"
                v-model="code"
                @input="handleInput"
                @keydown="handleKeydown"
                @paste="handlePaste"
                maxlength="4"
                type="text"
                inputmode="numeric"
                class="otp-field"
                autocomplete="off"
              />

              <div class="underlines">
  <span
    v-for="(item, index) in underscores"
    :key="index"
    class="underline"
    :style="getDynamicStyles(index)">
    {{ item }}
  </span>
              </div>

            </div>

          </div>
          <div class="code__error" v-if="errorMessage">{{ errorMessage }}</div>
          <div class="code__text" v-if="isTimerActive">Запросить новый код через <span>{{ timerValue }}</span> сек</div>
          <div class="code__new" v-if="!isTimerActive && !errorMessage" @click="requestNewCode"
               :class="{ 'disabled': isTimerActive }">Запросить новый код
          </div>
        </div>
        <div class="auth__rules" v-if="isUserNew">
          <input type="checkbox" id="rules" required v-model="rules">
          <label for="rules" :class="{ 'red-label': (code[0] || code[1] || code[2] || code[3]) && !rules}">Продолжая, я
            соглашаюсь на обработку моих персональных данных в соответствии с <a href="#"
                                                                                 @click.prevent="modal('privacy')">Политикой
              конфиденциальности</a> и принимаю условия <a href="#" @click.prevent="modal('user_agreement')">Пользовательского
              соглашения</a>.</label>
        </div>
      </form>
    </div>
    <Choose :organizations="organizations" v-if="visibleChoose" />
    <ModalPages :modalData="modalData" v-if="isShowModal" @close="closeModal" />
  </div>
</template>

<script>
import axios from 'axios'
import { Head, Link } from '@inertiajs/vue3'
import Choose from './Choose.vue'
import ModalPages from './../../Shared/Modals/ModalPages.vue'

export default {
  components: {
    Head,
    ModalPages,
    Link,
    Choose,
  },
  props: {
    phone: {
      type: String,
      required: true,
    },
    isUserNew: {
      type: Boolean,
      required: false,
    },
    dashboard: {
      type: Boolean,
      required: false,
    },
  },
  data() {
    return {
      code: '',
      codeError: false,
      errorMessage: '',
      timerValue: 30,
      isTimerActive: false,
      intervalId: null,
      visibleSms: true,
      visibleChoose: false,
      organizations: [],
      isShowModal: false,
      modalData: null,
      rules: false,
    }
  },
  mounted() {
    this.startTimer()
  },
  beforeUnmount() {
    this.stopTimer()
  },
  computed: {
    underscores() {
      const filled = this.code.length
      const total = 4
      return Array.from({ length: total }, (_, index) => index < filled ? '' : '_')
    },
  },
  methods: {
    getDynamicStyles(index) {
      let styles = {
        marginLeft: '0px',
        letterSpacing: '20px',
      }
      if (this.code.length === 3) {
        styles.marginLeft = (index === 0) ? '36px' : '30px'
        styles.letterSpacing = '1px'
      } else if (this.code.length === 2) {
        styles.marginLeft = (index === 0) ? '36px' : '26px'
        styles.letterSpacing = '-12px'
      } else if (this.code.length === 1) {
        styles.marginLeft = (index === 0) ? '30px' : '22px'
        styles.letterSpacing = '-3px'
      } else {
        styles.marginLeft = '20px'
        styles.letterSpacing = '7px'
      }
      return styles
    },
    focusInput() {
      this.$nextTick(() => {
        this.$refs.otpInput.focus();
      });
    },
    startTimer() {
      this.isTimerActive = true
      this.intervalId = setInterval(() => {
        this.timerValue--
        if (this.timerValue === 0) {
          this.stopTimer()
        }
      }, 1000)
    },
    stopTimer() {
      clearInterval(this.intervalId)
      this.timerValue = 30
      this.isTimerActive = false
    },
    handleInput() {
      this.code = this.code.replace(/\D/g, '');
    },
    handleKeydown() {

    },
    handlePaste() {

    },
    requestNewCode() {
      if (!this.rules && this.isUserNew) {
        this.code = ''
        this.errorMessage = 'Для продолжения необходимо согласиться с условиями.'
        return
      }
      if (!this.isTimerActive) {
        this.startTimer()
      }
      this.$emit('getNewSmsCode')
      this.code = ''
    },
    submit() {
      this.checkAndSubmitCode()
    },

    checkAndSubmitCode() {
      if (this.code.length === 4) {
        const url = this.dashboard ? '/dashboard/verifySmsCode' : '/verifySmsCode'

        axios.post(url, {
          phone: this.phone,
          code: this.code,
        })
          .then((response) => {
            if (!response.data.success) {
              this.errorMessage = response.data.message || 'Произошла ошибка. Повторите попытку позже.'

              setTimeout(() => {
                this.errorMessage = null
                this.code = ''
              }, 1000)
            }

            if (response.data.success) {

              if (response.data.loginCount == 1 && response.data.is_registered == 0) {
                this.$inertia.visit('/registration')

              } else {
                this.visibleSms = false
                this.organizations = response.data.organizations
                // this.visibleChoose = true
                this.$inertia.visit('/organizations/choose')
              }
            }
          })
          .catch((error, response) => {
            this.errorMessage = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          })
      }
    },
    modal(page) {
      axios.get('/api/pages/' + page, {})
        .then((response) => {
          if (response.data.success) {
            this.isShowModal = true
            this.modalData = response.data.data
          }
        })
        .catch((error, response) => {
          console.log('error', error)
        })
    },
    closeModal() {
      this.isShowModal = false
    },
  },
  watch: {
    code: {
      handler(newCode) {
        if (this.isUserNew) {
          if (newCode.length === 4 && this.rules) {
            this.submit()
          }
        } else {
          if (newCode.length === 4) {
            this.submit()
          }
        }
      },
      deep: true,
    },
    rules(newRules) {
      if (newRules) {
        this.errorMessage = ''
      }
      if (newRules && !this.errorMessage && this.code.length === 4) {
        this.submit()
      }
    },
  },
}
</script>

<style scoped lang="scss">
.disabled {
  opacity: 0.5;
  pointer-events: none;
}
.red-label {
  color: red;

  a {
    color: red;
    text-decoration: underline;
    text-decoration-color: gray;
  }
}
.otp-inputs {
  display: flex;
  gap: 10px;
}
.code__inputs input {
  background-color: transparent !important;
  text-align: start !important;
  height: 40px !important;
  padding-left: 60px;
}
.code__inputs.otp-wrapper input {
  position: relative;
  width: 300px;
}
.otp-field {
  width: 300px !important;
  font-size: 36px !important;
  font-weight: 500;
  caret-color: black;
  letter-spacing: 29px !important;
}
.underlines {
  display: flex;
  padding-left: 30px;
  left: 60px;
  transform: translateY(-50%);
  gap: 12px;
  align-items: flex-start;
  top: 0;
}
.underline {
  height: 40px;
  font-size: 40px;
  font-weight: bold;
  color: black;
  text-decoration: none;
}
</style>
