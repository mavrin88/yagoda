<template>
  <div>
    <Head title="Организации" />
    <div class="flex items-center justify-between mb-6">
      <!--      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">-->
      <!--&lt;!&ndash;        <label class="block text-gray-700">Trashed:</label>&ndash;&gt;-->
      <!--&lt;!&ndash;        <select v-model="form.trashed" class="form-select mt-1 w-full">&ndash;&gt;-->
      <!--&lt;!&ndash;          <option :value="null" />&ndash;&gt;-->
      <!--&lt;!&ndash;          <option value="with">With Trashed</option>&ndash;&gt;-->
      <!--&lt;!&ndash;          <option value="only">Only Trashed</option>&ndash;&gt;-->
      <!--&lt;!&ndash;        </select>&ndash;&gt;-->
      <!--      </search-filter>-->

      <div class="relative mr-4 w-full max-w-md">
        <input type="text" class="border-b-2 border-gray-300 pl-3 pr-10 py-2 w-full focus:outline-none focus:border-blue-500" placeholder="Поиск...">
        <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0 0l6 6"></path>
        </svg>
        <!--        <svg class="absolute right-3 top-1/2 transform " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">-->
        <!--          <g fill="none" stroke="currentColor">-->
        <!--            <circle cx="11" cy="11" r="6" />-->
        <!--            <path stroke-linecap="round" d="m20 20l-3-3" />-->
        <!--          </g>-->
        <!--        </svg>-->
      </div>

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
