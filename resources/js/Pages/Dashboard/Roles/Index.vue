<template>

  <div class="bg-white rounded-md shadow overflow-x-auto">
  <Head title="Роли" />

  <div class="button-container">
    <button
      @click="UsersBlock('users')" class="users-button" id="usersButton"
      :class="{'active': isActive === 'users'}">
      <span>Сотрудники</span>
    </button>

    <button
      @click="InterfaceBlock('interfaces')" class="interfaces-button"
      :class="{'active': isActive === 'interfaces'}">
      <span>Интерфейсы</span>
    </button>
  </div>


  <div class="flex mb-6 mt-8">
    <UsesBlock v-if="visibleUsersBlock" />
    <InterfaceBlock v-if="visibleInterfaceBlock" />
  </div>
</div>


</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import UsesBlock from './Components/UsesBlock.vue'
import InterfaceBlock from './Components/InterfacesBlock.vue'
import ShowOrganizationInfo from '../Organization/DopRightMenu/InfoBlock.vue'
import Organization from '../Organization/Index.vue'
import DopMenu from '../MenuComponent/DopMenu.vue'
import OrganizationFilter from '../Organization/DopRightMenu/FilterBlock.vue'
import AddOrganizationRightMenu from '../Organization/DopRightMenu/Index.vue'
import MainMenu from '../MenuComponent/MainMenu.vue'
import FlashMessages from '@/Shared/FlashMessages.vue'

export default {
  components: {
    MainMenu, AddOrganizationRightMenu, FlashMessages, OrganizationFilter, DopMenu, Organization, ShowOrganizationInfo,
    InterfaceBlock,
    UsesBlock,
    Head,
    Icon,
    Link,
  },
  props: {
    dashboard_roles: Object,
  },
  data() {
    return {
      dopMenuVisible: false,
      isActive: 'users',
      visibleUsersBlock: true,
      visibleInterfaceBlock: false,
    }
  },
  methods: {

    UsersBlock(button) {
      this.isActive = button;
      this.visibleInterfaceBlock = false
      this.visibleUsersBlock = true
    },
    InterfaceBlock(button) {
      this.isActive = button;
      this.visibleInterfaceBlock = true
      this.visibleUsersBlock = false
    },
  },
}
</script>

<style>
.button-container {
  display: flex;
  gap: 30px;
}

.users-button,
.interfaces-button {
  background-color: white;
  color: black;
  padding: 20px 90px;
  border: 1px solid black;
  border-radius: 20px;
  font-size: 16px;
  cursor: pointer;
  transition: all 0.3s ease;
  margin-right: 16px;
}

.users-button.active,
.interfaces-button.active {
  background-color: black;
  color: white;
}

</style>
