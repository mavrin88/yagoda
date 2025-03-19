<template>
  <Head title="Login" />

      <div class="auth__step">
        <div class="superadmin__top">
          <Link></Link>
          <Icon @click="close" icon="system-uicons:close" width="41" height="41" class="close_icon"/>
        </div>
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

    <Sms :phone="form.phone" v-if="!visibleAuth" @getNewSmsCode="submit" :isUserNew="isUserNew" :dashboard="true"/>
</template>

<script>
import axios from 'axios'
import { Head, Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import vSelect from 'vue-select'
import Sms from '../../../Auth/Sms.vue'

export default {
  components: {
    Sms, Head,
    vSelect,
    Icon,
    Link,
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

      axios.post('/dashboard/checkUserExists', this.form)
        .then((response) => {
          if (!response.data.success) {
            console.log(response.data)
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
    close() {
      this.$emit('showAddOrganizationsBlock', false)
    },
  },
}
</script>

<style lang="scss" scoped>
.close_icon{
  cursor: pointer;
}
.auth__step {
  max-width: 22rem !important;
}
</style>
