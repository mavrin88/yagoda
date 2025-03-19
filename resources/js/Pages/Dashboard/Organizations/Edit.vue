<template>
  <div>
    <Head :title="form.name" />
    <h1 class="mb-8 text-3xl font-bold">
      <Link class="text-indigo-400 hover:text-indigo-600" href="/organizations">Организации</Link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="organization.deleted_at" class="mb-6" @restore="restore"> Эта организация была удалена. </trashed-message>
    <div class="max-w-3xl bg-white rounded-md shadow overflow-hidden">
      <form @submit.prevent="update">
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
            <option v-for="activityType in organization.activityTypes" :value="activityType.id">
              {{ activityType.name }}
            </option>
          </select-input>

        </div>
        <div class="flex items-center px-8 py-4 bg-gray-50 border-t border-gray-100">
          <!--          <button v-if="!organization.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Organization</button>-->
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Сохранить</loading-button>
        </div>
      </form>
    </div>
    <h2 class="mt-12 text-2xl font-bold">Contacts</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="pb-4 pt-6 px-6">Name</th>
          <th class="pb-4 pt-6 px-6">City</th>
          <th class="pb-4 pt-6 px-6" colspan="2">Phone</th>
        </tr>
        <tr v-for="contact in organization.contacts" :key="contact.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/contacts/${contact.id}/edit`">
              {{ contact.name }}
              <icon v-if="contact.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
              {{ contact.city }}
            </Link>
          </td>
          <td class="border-t">
            <Link class="flex items-center px-6 py-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
              {{ contact.phone }}
            </Link>
          </td>
          <td class="w-px border-t">
            <Link class="flex items-center px-4" :href="`/contacts/${contact.id}/edit`" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </Link>
          </td>
        </tr>
        <tr v-if="organization.contacts.length === 0">
          <td class="px-6 py-4 border-t" colspan="4">No contacts found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Layout from '@/Shared/Layout.vue'
import TextInput from '@/Shared/TextInput.vue'
import SelectInput from '@/Shared/SelectInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import TrashedMessage from '@/Shared/TrashedMessage.vue'
import FileInput from '@/Shared/FileInput.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
    FileInput
  },
  layout: Layout,
  props: {
    organization: Object,
    activityTypes: Array
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.organization.name,
        full_name: this.organization.full_name,
        phone: this.organization.phone,
        contact_name: this.organization.contact_name,
        contact_phone: this.organization.contact_phone,
        inn: this.organization.inn,
        legal_address: this.organization.legal_address,
        registration_number: this.organization.registration_number,
        acquiring_fee: this.organization.acquiring_fee,
        tips_1: this.organization.tips_1,
        tips_2: this.organization.tips_2,
        tips_3: this.organization.tips_3,
        tips_4: this.organization.tips_4,
        photo_path: this.organization.photo_path,
        activity_type_id: this.organization.activity_type_id,
        email: this.organization.email,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(`/organizations/${this.organization.id}`)
    },
    destroy() {
      if (confirm('Are you sure you want to delete this organization?')) {
        this.$inertia.delete(`/organizations/${this.organization.id}`)
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this organization?')) {
        this.$inertia.put(`/organizations/${this.organization.id}/restore`)
      }
    },
  },
}
</script>
