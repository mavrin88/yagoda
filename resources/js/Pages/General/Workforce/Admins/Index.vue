<template>
  <div class="superadmin">
    <Head title="Администраторы" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" :href="url">назад</Link>
        <h2>АДМИНИСТРАТОРЫ</h2>
      </div>
      <div class="superadmin__note">Администратор формирует заказы на оплату. Устанавливает Мастеров и других Сотрудников в смену.</div>
      <UserForm @addAdminInUserList="addAdminInUserList"/>
      <UserList :users="admins" />
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
    admins: Object,
    url: String,
  },
  methods: {
    addAdminInUserList(admin) {
      this.admins.push(admin)
    }
  }
}
</script>
