<template>
  <div>
    <div class="role-select-container pl-10 pt-10">
      <v-select :options="[ ...Object.values(roles.roles)]" v-model="selectingRole" :clearable="false" class="wide-select"></v-select>
    </div>

    <div class="mb-4 pl-10" :class="isUrl('dashboard/organizations') ? 'active-link' : ''">
      <Link class="group flex items-center py-3" href="/dashboard/organizations">
        <div>ОРГАНИЗАЦИИ</div>
      </Link>
    </div>

    <div class="mb-4 pl-10" :class="isUrl('dashboard/categories') ? 'active-link' : ''">
      <Link class="group flex items-center py-3" href="/dashboard/categories">
        <div>КАТЕГОРИИ</div>
      </Link>
    </div>

    <div class="mb-4 pl-10" :class="isUrl('dashboard/users') ? 'active-link' : ''">
      <Link class="group flex items-center py-3" href="/dashboard/users">
        <div>ПОЛЬЗОВАТЕЛИ</div>
      </Link>
    </div>
    <div class="mb-4 pl-10" :class="isUrl('dashboard/tests') ? 'active-link' : ''">
      <Link class="group flex items-center py-3" href="/dashboard/tests">
        <div>ТЕСТИРОВАНИЯ</div>
      </Link>
    </div>
    <div class="mb-4 pl-10" :class="isUrl('dashboard/roles') ? 'active-link' : ''">
      <Link class="group flex items-center py-3 " href="/dashboard/roles">
        <div>РОЛИ</div>
      </Link>
    </div>

    <div class="mb-4 pl-10" :class="isUrl('dashboard/service_settings') ? 'active-link' : ''">
      <Link class="group flex items-center py-3 w-full" href="/dashboard/service_settings">
        <div>НАСТРОЙКИ СЕРВИСА</div>
      </Link>
    </div>


  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import vSelect from 'vue-select'

export default {
  components: {
    vSelect,
    Icon,
    Link,
  },
  props: {
    roles: Object,
  },
  data() {
    return {
      selectingItems: '',
      selectingRole: '',
    };
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
    // handleDopMenuOpen() {
    //   let currentUrl = this.$page.url.substr(1)
    //   this.selectingItems = currentUrl === 'dashboard/roles';
    //   this.$emit('menuItemClicked', this.selectingItems);
    // }
  },
  watch: {
    '$page.url': function(newUrl) {
      this.selectingItems = newUrl === '/dashboard/roles';
      this.$emit('menuItemClicked', this.selectingItems, newUrl);
    }
  }
}
</script>

<style>
.role-select-container {
  border-bottom: 1px solid black;
  margin-bottom: 16px;
}
.role-select-container .v-select .vs__selected {
  font-size: 17px !important;
}
.active-link {
  background-color: rgba(56, 176, 176, 0.3);
  color: black;
}
.group:hover .active-link {
  background-color: #38b0b0;
}
</style>
