<template>
  <div class="ytips">
    <Head title="Администраторы" />
    <TipsHeader />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" :href="url"></Link>
        <div class="ytips-top__title"><b>АДМИНИСТРАТОРЫ</b></div>
      </div>
    </div>

    <div class="ytips__container">
      <div class="ytips-text ytips-text_padding ytips-text_12">
       <b>Администратор - ответственный сотрудник, активно взаимодействующий с клиентом.</b><br> Часть от чаевых будет равномерно распределена на Администраторов находящихся в смене.
      </div>
      <UserForm @addAdminInUserList="addAdminInUserList"/>
      <UserList :users="admins" />

      <br>
      <div class="ytips-text ytips-text_padding ytips-text_12">
        Для добавления Администратора в подгруппу введите номер телефона и нажмите Добавить.
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
