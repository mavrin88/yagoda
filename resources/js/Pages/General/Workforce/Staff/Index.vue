<template>
  <div class="superadmin">
    <Head title="Персонал" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" :href="url">назад</Link>
        <h2>ПЕРСОНАЛ</h2>
      </div>
      <div class="superadmin__note">Администратор формирует заказы на оплату. Устанавливает Мастеров и других Сотрудников в смену.</div>
      <UserForm @addStaffInUserList="addStaffInUserList"/>
      <UserList :users="filteredStaff" />
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
  methods: {
    addStaffInUserList(staff) {
      this.staff.push(staff)
    }
  },
  props: {
    auth: Object,
    staff: Object,
    url: String,
  },
  computed: {
    filteredStaff() {
      return this.staff.filter(staff => staff.status !== 'deleted');
    }
  }
}
</script>
