<template>
  <Head title="Авторизация" />
    <div class="auth">
      <div class="auth__wrapper" v-if="visibleAuth">
        <div class="auth__step">
          <div class="auth__title">Авторизация / регистрация</div>
          <div class="form">
            <div class="form__item">
              <input v-model="form.phone" type="tel" id="tel" v-mask="'+7 (###) ###-##-##'" @input="formatPhone">
              <label for="tel">Телефон</label>
              <div class="form__error">{{ error }}</div>
            </div>
            <div class="form__actions">
              <button @click="submit" class="btn" :disabled="!isMaskValid">Продолжить</button>
            </div>
          </div>
        </div>
      </div>

      <Sms :phone="form.phone" v-if="!visibleAuth" @getNewSmsCode="submit" :isUserNew="isUserNew"/>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import Sms from './Sms.vue'
import axios from 'axios'

export default {
  components: {
    Head,
    LoadingButton,
    Logo,
    TextInput,
    Sms
  },
  data() {
    return {
      isUserNew: false,
      error: null,
      visibleAuth: true,
      form: this.$inertia.form({
        phone: '+7 ',
        password: 'secret',
        remember: false,
        is_own_organization: true,
      }),
    }
  },
  computed: {
    isMaskValid() {
      return this.form.phone.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/) !== null
    },
  },
  methods: {

    formatPhone() {
      if (!this.form.phone.startsWith('+7 ')) {
        this.form.phone = '+7 ' + this.form.phone.replace(/^\+7\s?/, '');
      }
    },

    submit() {
      if (!this.isMaskValid) {
        return
      }

      axios.post('/checkUserExists', this.form)
        .then((response) => {
          if (!response.data.success) {
            this.error = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
          }

          if (response.data.success) {
            this.visibleAuth = false
            this.isUserNew = response.data.isUserNew
          }
        })
        .catch((error) => {
          this.error = 'Произошла ошибка. Повторите попытку позже.'
        })
    },
  },
}
</script>
<style>
.form__item input {
  background-color: transparent !important;
}
</style>
