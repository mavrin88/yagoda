<template>
  <div class="ytips">
    <Head title="Персонал" />
    <TipsHeader />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" :href="url"></Link>
        <div class="ytips-top__title"><b>ПЕРСОНАЛ</b></div>
      </div>
    </div>

    <div class="ytips__container">
      <div class="ytips-text ytips-text_padding ytips-text_12"><b>К персоналу, относятся сотрудники, осуществляющие иные функции.</b> Уборщик, охранник, кассир...</div>
      <UserForm @addStaffInUserList="addStaffInUserList"/>
      <UserList :users="filteredStaff" />
      <br>
      <div class="ytips-text ytips-text_padding ytips-text_12">
        Для добавления сотрудника в подгруппу введите номер телефона и нажмите Добавить.
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import UserList from './Components/UserList.vue'
import UserForm from './Components/UserForm.vue'
import TipsHeader from '../../Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
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
