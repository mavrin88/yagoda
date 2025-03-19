<template>
  <Head title="Выбор организации" />
  <div class="flex items-center justify-center p-6 min-h-screen bg-indigo-800">
    <div class="w-full max-w-md">
      <logo class="block mx-auto w-full max-w-xs fill-white" height="50" />
      <form class="mt-8 bg-white rounded-lg shadow-xl overflow-hidden" @submit.prevent="login">
        <div class="px-10 py-12">
          <h1 class="text-center text-3xl font-bold">Выберите организацию</h1>
          <p v-for="organization in organizations"
             class="mt-4 text-sm text-gray-600 cursor-pointer"
             @click="selectOrganization(organization)">{{ organization.organization_name }} - {{ organization.role_id }}</p>

          <!--          <pre><code>{{ organizations }}</code></pre>-->
          <!--          <pre><code>{{ selected_organization_id }}</code></pre>-->
          <!--          <div class="mt-6 mx-auto w-24 border-b-2" />-->
          <!--          <text-input v-model="form.phone" :error="form.errors.phone" class="mt-10" label="Телефон" type="string" autofocus autocapitalize="off" />-->
          <!--          <text-input v-model="form.password" :error="form.errors.password" class="mt-6" label="Пароль" type="password" />-->
          <!--          <label class="flex items-center mt-6 select-none" for="remember">-->
          <!--            <input id="remember" v-model="form.remember" class="mr-1" type="checkbox" />-->
          <!--            <span class="text-sm">Запомнить меня</span>-->
          <!--          </label>-->
        </div>
        <!--        <div class="flex px-10 py-4 bg-gray-100 border-t border-gray-100">-->
        <!--          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Войти</loading-button>-->
        <!--        </div>-->
      </form>
    </div>
  </div>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import axios from 'axios';

export default {
  components: {
    Head,
    LoadingButton,
    Logo,
    TextInput,
  },
  props: {
    organizations: {
      type: Array,
      required: true
    },
    selected_organization_id: {

    }
  },
  data() {
    return {
      form: this.$inertia.form({
        phone: '',
        password: '',
        remember: false,
      }),
    }
  },
  methods: {
    login() {
      this.form.post('/login')
    },

    selectOrganization(organization) {
      axios.post('selectOrganization', {
        organization_id: organization.organization_id,
        role_id: organization.role_id,
      })
        .then(response => {
          // Обработка успешного ответа
          console.log(response.data);
        })
        .catch(error => {
          // Обработка ошибки
          console.error(error);
        });
    }
  },
}
</script>
