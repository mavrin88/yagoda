<template>
  <div class="superadmin">
    <Head title="Мастера" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" :href="url">назад</Link>
        <h2>МАСТЕРА</h2>
      </div>
      <div class="superadmin__note">Администратор формирует заказы на оплату. Устанавливает Мастеров и других Сотрудников в смену.</div>
      <UserForm @addMasterInUserList="addMasterInUserList"/>
      <UserList :users="filteredMasters" />
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import UserList from './Components/UserList.vue'
import UserForm from './Components/UserForm.vue'

export default {
  components: {
    Head,
    Link,
    UserList,
    UserForm
  },
  data() {
    return {

    };
  },
  props: {
    auth: Object,
    masters: Object,
    url: String,
  },
  computed: {
    filteredMasters() {
      return this.masters.filter(master => master.status !== 'deleted');
    }
  },
  methods: {
    addMasterInUserList(master) {
      this.masters.push(master)
    }
  }
}
</script>
