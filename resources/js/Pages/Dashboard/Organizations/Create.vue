<template>
  <div>
    <Head title="Создать Организацию" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Организации</Link>
      <span class="text-indigo-400 font-medium">/</span> Создать
    </h1>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="store">
        <div class="flex flex-wrap -mb-8 -mr-6 p-8">
          <text-input v-model="form.name" :error="form.errors.name" class="pb-8 pr-6 w-full lg:w-1/2" label="Наименование организации" />
          <text-input v-model="form.full_name" :error="form.errors.full_name" class="pb-8 pr-6 w-full lg:w-1/2" label="Полное наименование" />
          <text-input v-model="form.phone" :error="form.errors.phone" class="pb-8 pr-6 w-full lg:w-1/2" label="Телефон организации" />
          <text-input v-model="form.contact_name" :error="form.errors.contact_name" class="pb-8 pr-6 w-full lg:w-1/2" label="ФИО контактного лица" />
          <text-input v-model="form.contact_phone" :error="form.errors.contact_phone" class="pb-8 pr-6 w-full lg:w-1/2" label="Телефон контактного лица" />
          <text-input v-model="form.inn" :error="form.errors.inn" class="pb-8 pr-6 w-full lg:w-1/2" label="ИНН" />
          <text-input v-model="form.legal_address" :error="form.errors.legal_address" class="pb-8 pr-6 w-full lg:w-1/2" label="Юридический адрес" />
          <text-input v-model="form.registration_number" :error="form.errors.registration_number" class="pb-8 pr-6 w-full lg:w-1/2" label="ОГРН/ОГРНИП" />
          <text-input v-model="form.acquiring_fee" :error="form.errors.acquiring_fee" class="pb-8 pr-6 w-full lg:w-1/2" label="Процент за эквайринг" />
          <text-input v-model="form.tips_1" :error="form.errors.tips_1" class="pb-8 pr-6 w-full lg:w-1/2" label="Процент Чаевые 1" />
          <text-input v-model="form.tips_2" :error="form.errors.tips_2" class="pb-8 pr-6 w-full lg:w-1/2" label="Процент Чаевые 2" />
          <text-input v-model="form.tips_3" :error="form.errors.tips_3" class="pb-8 pr-6 w-full lg:w-1/2" label="Процент Чаевые 3" />
          <text-input v-model="form.tips_4" :error="form.errors.tips_4" class="pb-8 pr-6 w-full lg:w-1/2" label="Процент Чаевые 4" />
          <file-input v-model="form.photo_path" :error="form.errors.photo_path" class="pb-8 pr-6 w-full lg:w-1/2" type="file" accept="image/*" label="Логотип" />
          <text-input v-model="form.email" :error="form.errors.email" class="pb-8 pr-6 w-full lg:w-1/2" label="Email для отчетов" />

          <select-input v-model="form.activity_type_id" :error="form.errors.activity_type_id" class="pb-8 pr-6 w-full lg:w-1/2" label="Вид деятельности">
            <option v-for="activityType in activityTypes" :value="activityType.id">
              {{ activityType.name }}
            </option>
          </select-input>

        </div>
        <div class="flex items-center justify-end px-8 py-4 bg-gray-50 border-t border-gray-100">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Создать организацию</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import FileInput from '@/Shared/FileInput.vue'

export default {
  components: {
    Head,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    FileInput,
  },
  props: {
    activityTypes: {
      type: Array,
      required: true
    }
  },
  layout: Layout,
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: null,
        full_name: null,
        phone: null,
        contact_name: null,
        contact_phone: null,
        inn: null,
        legal_address: null,
        registration_number: null,
        acquiring_fee: null,
        tips_1: null,
        tips_2: null,
        tips_3: null,
        tips_4: null,
        photo_path: null,
        activity_type_id: null,
        email: null,
      }),
    }
  },
  methods: {
    store() {
      this.form.post('/organizations')
    },
  },
}
</script>
