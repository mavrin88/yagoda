<template>
  <div>
    <Head title="Organizations" />
    <h1 class="mb-8 text-3xl font-bold">Организации</h1>
    <div class="flex items-center justify-between mb-6">
      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="form-select mt-1 w-full">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <Link class="btn-white" href="/organizations/create">
        <span>Добавить</span>
        <span class="hidden md:inline">&nbsp;Организацию</span>
      </Link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">№</th>
            <th class="pb-4 pt-6 px-6">бренд</th>
            <th class="pb-4 pt-6 px-6">организация</th>

            <th class="pb-4 pt-6 px-6" colspan="2">
              <div class="flex flex-col bg-gray-200 p-4 rounded">
                <span class="font-semibold">прибыль</span>
                <div class="flex flex-col mt-2 space-y-2">
                  <span class="text-sm text-gray-500">Месяц: Январь</span>
                  <span class="text-sm text-gray-500">100,000 ₽</span>
                </div>
              </div>
            </th>

            <th class="pb-4 pt-6 px-6" colspan="2">
              <div class="flex flex-col bg-gray-200 p-4 rounded">
                <span class="font-semibold">выручка</span>
                <div class="flex flex-col mt-2 space-y-2">
                  <span class="text-sm text-gray-500">Месяц: Январь</span>
                  <span class="text-sm text-gray-500">100,000 ₽</span>
                </div>
              </div>
            </th>
            <th class="pb-4 pt-6 px-6" colspan="2">
              <div class="flex flex-col bg-gray-200 p-4 rounded">
                <span class="font-semibold">чаевые</span>
                <div class="flex flex-col mt-2 space-y-2">
                  <span class="text-sm text-gray-500">Месяц: Январь</span>
                  <span class="text-sm text-gray-500">100,000 ₽</span>
                </div>
              </div>
            </th>

            <th class="pb-4 pt-6 px-6" colspan="2">
              <div class="flex flex-col bg-gray-200 p-4 rounded">
                <span class="font-semibold">сотрудники</span>
                <div class="flex flex-col mt-2 space-y-2">
                  <span class="text-sm text-gray-500">Месяц: Январь</span>
                  <span class="text-sm text-gray-500">100,000 ₽</span>
                </div>
              </div>
            </th>

          </tr>
        </thead>
        <tbody>
          <tr v-for="organization in organizations.data" :key="organization.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
            <td class="border-t">
              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/organizations/${organization.id}/edit`">
                {{ organization.id }}
                <icon v-if="organization.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6 py-4" :href="`/organizations/${organization.id}/edit`" tabindex="-1">
                {{ organization.full_name }}
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex flex-col items-start px-6 py-4 focus:text-indigo-500" :href="`/organizations/${organization.id}/edit`">
                <span>{{ organization.name }}</span>
                <span class="text-sm text-gray-500">{{ organization.phone }}</span>
              </Link>
            </td>
            <td class="border-t">

            </td>
            <td class="border-t">

            </td>
            <td class="border-t">

            </td>

            <td class="border-t">

            </td>
          </tr>
          <tr v-if="organizations.data.length === 0">
            <td class="px-6 py-4 border-t" colspan="4">Организаций не найдено.</td>
          </tr>
        </tbody>
      </table>
    </div>
    <pagination class="mt-6" :links="organizations.links" />
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout.vue'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination.vue'
import SearchFilter from '@/Shared/SearchFilter.vue'

export default {
  components: {
    Head,
    Icon,
    Link,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    organizations: Object,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get('/organizations', pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
  },
}
</script>
