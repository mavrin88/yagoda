<template>
  <div>
    <Head title="Организации" />
    <div class="flex items-center  mb-6">
<!--      <search-filter v-model="form.search" class="mr-4 w-full max-w-md" @reset="reset">-->
<!--&lt;!&ndash;        <label class="block text-gray-700">Trashed:</label>&ndash;&gt;-->
<!--&lt;!&ndash;        <select v-model="form.trashed" class="form-select mt-1 w-full">&ndash;&gt;-->
<!--&lt;!&ndash;          <option :value="null" />&ndash;&gt;-->
<!--&lt;!&ndash;          <option value="with">With Trashed</option>&ndash;&gt;-->
<!--&lt;!&ndash;          <option value="only">Only Trashed</option>&ndash;&gt;-->
<!--&lt;!&ndash;        </select>&ndash;&gt;-->
<!--      </search-filter>-->

      <div class="relative mr-4 search_block ml-5">
        <input type="text" class="pl-3 pr-10 py-2 focus:outline-none">
        <svg class="absolute right-3 top-1/4 transform " xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <g fill="none" stroke="currentColor">
            <circle cx="11" cy="11" r="6" />
            <path stroke-linecap="round" d="m20 20l-3-3" />
          </g>
        </svg>
      </div>

      <div @click="addOrganization" class="add-organization">
        <span>+ Добавить Организацию</span>
      </div>

      <div @click="showFilter" class="filter">
        <span>фильтр</span>
      </div>
    </div>


    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <thead>
          <tr class="text-left font-bold">
            <th class="pb-4 pt-6 px-6">№</th>
            <th class="pb-4 pt-6 px-6">бренд</th>
            <th class="pb-4 pt-6 px-6">организация</th>

<!--            <th class="pb-4 pt-6 px-6" colspan="2">-->
<!--              <div class="flex flex-col bg-gray-200 p-4 rounded">-->
<!--                <span class="font-semibold">прибыль</span>-->
<!--                <div class="flex flex-col mt-2 space-y-2">-->
<!--                  <span class="text-sm text-gray-500">Месяц: Январь</span>-->
<!--                  <span class="text-sm text-gray-500">100,000 ₽</span>-->
<!--                </div>-->
<!--              </div>-->
<!--            </th>-->

<!--            <th class="pb-4 pt-6 px-6" colspan="2">-->
<!--              <div class="flex flex-col bg-gray-200 p-4 rounded">-->
<!--                <span class="font-semibold">выручка</span>-->
<!--                <div class="flex flex-col mt-2 space-y-2">-->
<!--                  <span class="text-sm text-gray-500">Месяц: Январь</span>-->
<!--                  <span class="text-sm text-gray-500">100,000 ₽</span>-->
<!--                </div>-->
<!--              </div>-->
<!--            </th>-->
<!--            <th class="pb-4 pt-6 px-6" colspan="2">-->
<!--              <div class="flex flex-col bg-gray-200 p-4 rounded">-->
<!--                <span class="font-semibold">чаевые</span>-->
<!--                <div class="flex flex-col mt-2 space-y-2">-->
<!--                  <span class="text-sm text-gray-500">Месяц: Январь</span>-->
<!--                  <span class="text-sm text-gray-500">100,000 ₽</span>-->
<!--                </div>-->
<!--              </div>-->
<!--            </th>-->

<!--            <th class="pb-4 pt-6 px-6" colspan="2">-->
<!--              <div class="flex flex-col bg-gray-200 p-4 rounded">-->
<!--                <span class="font-semibold">сотрудники</span>-->
<!--                <div class="flex flex-col mt-2 space-y-2">-->
<!--                  <span class="text-sm text-gray-500">Месяц: Январь</span>-->
<!--                  <span class="text-sm text-gray-500">100,000 ₽</span>-->
<!--                </div>-->
<!--              </div>-->
<!--            </th>-->

          </tr>
        </thead>
        <tbody>
          <tr v-for="organization in filteredOrganizations" :key="organization.id" class="hover:bg-gray-100 focus-within:bg-gray-100" @click="showInfo(organization)">
            <td class="border-t">
<!--              <Link class="flex items-center px-6 py-4 focus:text-indigo-500" :href="`/organizations/${organization.id}/edit`">-->
              <Link class="flex items-center px-6 py-4">
                {{ organization.id }}
<!--                <icon v-if="organization.deleted_at" name="trash" class="shrink-0 ml-2 w-3 h-3 fill-gray-400" />-->
              </Link>
            </td>
            <td class="border-t">
              <Link class="flex items-center px-6" tabindex="-1">
                {{ organization.full_name }}
              </Link>
              <span :class="getStatusClass(organization.status)" class="organization_status ml-6">{{ translateStatus(organization.status) }}</span>
            </td>
            <td class="border-t">
              <Link class="flex flex-col items-start px-6 py-4">
                <span>{{ organization.name }}</span>
                <span v-show="organization.status !== 'deleted'" class="text-sm text-gray-500">{{ organization.phone }}</span>
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
          <tr v-if="filteredOrganizations.length === 0">
            <td class="px-6 py-4 border-t" colspan="4">Организаций не найдено.</td>
          </tr>
        </tbody>
      </table>
    </div>
<!--    <pagination class="mt-6" :links="organizations.links" />-->
<!--    <Pagination @click="update(page)" url="users" :current-page="page" :limit="limit" :total="total"/>-->
<!--    <Pagination-->
<!--      :links="organizations.links"-->
<!--      :current-page="organizations.current_page"-->
<!--      :total="organizations.total"-->
<!--      @page-changed="loadPage"-->
<!--    />-->
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
// import pickBy from 'lodash/pickBy'
// import throttle from 'lodash/throttle'
// import mapValues from 'lodash/mapValues'
// import Pagination from '@/Shared/Pagination_2.vue'
// import SearchFilter from '@/Shared/SearchFilter.vue'

export default {
  components: {
    Head,
    Link,
    // Pagination,
    // SearchFilter,
  },
  props: {
    organizations: Object,
    isFilteringStatusActive: Boolean,
  },
  data() {
    return {
      form: {
        // search: this.filters.search,
      },
      statusTranslations: {
        active: 'Активная',
        new: 'Новая',
        stoped: 'Приостановлена',
        deleted: 'Удалена',
      },
      statusTranslationsForFiltering: {
        active: 'Активная',
        new: 'Новая',
        stoped: 'Приостановлена',
        deleted: 'Удалена',
      },
    }
  },
computed: {
    filteredOrganizations() {
      const organizationsArray = Array.isArray(this.organizations) ? this.organizations : Object.values(this.organizations);
      return organizationsArray.filter(org => org.status !== 'new_without_save');
    }
},
  methods: {
    addOrganization(){
      this.$emit('showAddOrganizationBlock')
    },
    showFilter(){
      this.$emit('showFilterBlock')
    },
    showInfo(organization){
      this.$emit('showInfoBlock',  organization)
    },
    getStatusClass(status) {
      switch (status) {
        case 'active':
          return 'text-[#38b0b0]';
          case 'new':
          return 'text-[#00008B]';
        case 'stoped':
          return 'text-[#FFA500]';
        case 'deleted':
          return 'text-[#FF0000]';
        default:
          return 'text-black';
      }
    },
    translateStatus(status) {
      if (this.isFilteringStatusActive){
        return this.statusTranslations[status] || status;
      }else{
        return this.statusTranslations[status] || status;
      }
    },
  },
}
</script>
<style>
.add-organization {
  position: relative;
  font-weight: 400;
  color: #9CA4B5;
  font-size: 12px;
  top: 15px;
  left: 30px;
  cursor: pointer;
  text-decoration: underline;
}
.filter {
  position: relative;
  cursor: pointer;
  text-decoration: underline;
  left: 540px;
  font-size: 14px;
  font-weight: 400;
color: #000000}
.search_block input{
  border-bottom: 1px solid #9CA4B5;
  width: 236px;
}
.organization_status {
  font-size: 12px;
  cursor: pointer;
}
</style>
