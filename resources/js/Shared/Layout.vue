<template>
  <Spiner :isLoading="isLoading"></Spiner>

  <div class="md:flex md:flex-col">
    <div class="md:flex md:flex-col md:h-screen">

      <div class="md:flex md:shrink-0">
        <div class="md:text-md flex items-center justify-between text-sm bg-[#38b0b0] border-b">
          <div class="text-white">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 256 256">
              <path fill="currentColor"
                    d="M162.83 205.17a4 4 0 0 1-5.66 5.66l-80-80a4 4 0 0 1 0-5.66l80-80a4 4 0 1 1 5.66 5.66L85.66 128Z" />
            </svg>
          </div>
          <div class="flex flex-col space-y-0 pl-4 text-sm w-[250px]">
            <span class="text-white">Рутадминистратор</span>
            <span class="text-white">{{ auth.user.last_name }} {{ auth.user.first_name }}</span>
          </div>
        </div>
        <div
          class="md:text-md flex items-center justify-between p-4 w-full text-sm bg-[#38b0b0] border-b md:px-12 md:py-0">
          <div class="logo_container">
            <img src="/img/content/lp-logo.svg" alt="" class="w-20 h-20">
          </div>
        </div>
      </div>

      <div class="md:flex md:grow ">
        <!-- Основное меню слева -->
        <div class="bg-white main-menu">
          <main-menu @menuItemClicked="toggleDopMenu" :roles="dashboard_roles"
                     class="hidden shrink-0 bg-white overflow-y-auto md:block border-r border-custom-color" />
        </div>

        <!-- Дополнительное меню (справа от главного) -->
        <div v-show="dopMenuVisible" class="dop-menu-transition bg-white dop-menu">
          <dop-menu :roles="dashboard_roles"
                    class="hidden shrink-0 p-10 bg-white overflow-y-auto md:block border-r border-custom-color" />
        </div>

        <!-- Основной контент -->
        <div class="px-4 py-8 md:flex-1 md:p-12 md:overflow-y-auto bg-white" scroll-region>
          <flash-messages />

          <Organization v-if="showOrganizations" :organizations="organizations" :isFilteringStatusActive="isFilteringStatusActive"
                        @showAddOrganizationBlock="visibleOrganizationBlock"
                        @showFilterBlock="visibleFilterBlock"
                        @showInfoBlock="visibleInfoBlock"></Organization>
          <Roles v-if="showRoles"></Roles>
        </div>

        <!-- Дополнительное меню (слева от контента) -->
        <div v-if="dopRightMenuVisible" class="dop-right-menu">

          <AddOrganizationRightMenu v-if="showAddOrganizations" @showAddOrganizationsBlock="toggleDopRightMenu"></AddOrganizationRightMenu>
          <OrganizationFilter v-if="showOrganizationFilter" @showFilterBlock="toggleDopRightMenu" @filterChanged="handleFilterChange"></OrganizationFilter>
          <ShowOrganizationInfo v-if="showOrganizationInfo" :selectedOrganization="selectOrganization" @showInfoBlock="toggleDopRightMenu" @saveOrganization="fetchOrganizations"></ShowOrganizationInfo>

        </div>
      </div>

    </div>
  </div>
</template>

<script>
import axios from 'axios'
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import Spiner from '../Pages/Dashboard/UI/Spiner.vue'
import Logo from '@/Shared/Logo.vue'
import MainMenu from '../Pages/Dashboard/MenuComponent/MainMenu.vue'
import DopMenu from '../Pages/Dashboard/MenuComponent/DopMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'
import vSelect from 'vue-select'
import Organization from '../Pages/Dashboard/Organization/Index.vue'
import AddOrganizationRightMenu from '../Pages/Dashboard/Organization/DopRightMenu/Index.vue'
import OrganizationFilter from '../Pages/Dashboard/Organization/DopRightMenu/FilterBlock.vue'
import ShowOrganizationInfo from '../Pages/Dashboard/Organization/DopRightMenu/InfoBlock.vue'
import Roles from '../Pages/Dashboard/Roles/Index.vue'

export default {
  components: {
    Spiner,
    vSelect,
    FlashMessages,
    Icon,
    Link,
    Logo,
    MainMenu,
    DopMenu,
    Organization,
    AddOrganizationRightMenu,
    OrganizationFilter,
    ShowOrganizationInfo,
    Roles,
  },
  props: {
    auth: Object,
    dashboard_roles: Object,
  },
  data() {
    return {
      isLoading : false,
      dopMenuVisible: false,
      dopRightMenuVisible: false,
      currentUrl: '',
      organizations: {},
      selectOrganization: {},
      filters: {},
      isFilteringStatusActive: false,
      showOrganizations: false,
      showAddOrganizations: false,
      showOrganizationFilter: false,
      showOrganizationInfo: false,
      showRoles: false,
      totalPages: 0,
      lastPage: 0,
      totalPage: 0,
      currentPage: 3,
      page: 1,
    }
  },
  mounted() {
    const currentUrl = window.location.pathname
    this.currentUrl = currentUrl
    if (currentUrl === '/dashboard/roles') {
      this.showRoles = true
      this.dopMenuVisible = true
    }
    if (currentUrl === '/dashboard/organizations') {
      this.fetchOrganizations()
      this.showOrganizations = true
    }

    // Подписка на вебсокет
    window.Echo.channel('organizations')
      .listen('OrganizationCreated', (event) => {
        this.organizations.push(event.organization)
      });

  },
  methods: {
    toggleDopMenu(menuType, newUrl) {
      if (newUrl === '/dashboard/organizations') {
        this.fetchOrganizations()
        this.showRoles = false
        this.showOrganizations = true
      }
      if (newUrl === '/dashboard/roles') {
        this.showOrganizations = false
        this.showRoles = true
        this.dopRightMenuVisible = false
      }
      this.dopMenuVisible = menuType
    },
    visibleOrganizationBlock() {
      this.showOrganizationFilter = false
      this.showOrganizationInfo = false
      this.dopRightMenuVisible = true
      this.showAddOrganizations = true
    },
    visibleFilterBlock() {
      this.showAddOrganizations = false
      this.showOrganizationInfo = false
      this.dopRightMenuVisible = true
      this.showOrganizationFilter = true
    },
    visibleInfoBlock(organization) {
      this.selectOrganization = organization
      this.showAddOrganizations = false
      this.showOrganizationFilter = false
      this.dopRightMenuVisible = true
      this.showOrganizationInfo = true
    },
    toggleDopRightMenu(visibility) {
      this.dopRightMenuVisible = visibility
    },
    fetchOrganizations(visibleSpiner) {
      console.log('fetchOrganizations');
      // if (visibleSpiner){
      //   this.startLoadingSpiner()
      // }
      this.startLoadingSpiner()
      axios.get('/dashboard/organizations/all', {
        params: this.filters,
        // page: this.currentPage
      })
        .then((response) => {
          this.organizations = response.data.data
          // this.lastPage = response.data.last_page;
          // this.currentPage = response.data.current_page;
        })
        .catch((error) => {
          console.error('Ошибка при получении организаций:', error)
        })
    },
    handleFilterChange(filterData) {
      this.filters = filterData;
      // if (filterData.status.length >0){
      //   this.isFilteringStatusActive = true
      // }
      this.fetchOrganizations()
    },
    startLoadingSpiner() {
      this.isLoading = true;
      setTimeout(() => {
        this.isLoading = false;
      }, 700);
    },
  }
}
</script>

<style scoped>
.main-menu {
  width: 297px;
  border-right: 1px solid rgb(56, 176, 176);
  z-index: 1010;
}

.dop-menu {
  width: 297px;
  border-right: 1px solid rgb(56, 176, 176);
  background-color: white;
  z-index: 1000;
  padding-top: 47px;
}

.dop-right-menu {
  width: 450px;
  border-left: 1px solid rgb(56, 176, 176);
  background-color: white;
}

.overlay {
  opacity: 0;
  transition: opacity 0.1s ease-in-out;
  z-index: 1000;
}

.logo_container {
  margin-left: 60px
}
</style>
