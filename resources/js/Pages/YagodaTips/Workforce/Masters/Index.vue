<template>
  <div class="ytips">
    <Head title="Мастера" />
    <TipsHeader />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" :href="url"></Link>
        <div class="ytips-top__title"><b>МАСТЕРА</b></div>
      </div>
    </div>

    <div class="ytips__container">
      <div class="ytips-text ytips-text_padding ytips-text_12"><b>Мастер - непосредственный исполнитель услуг. </b> Гости будут переводить чаевые за работу Мастерам.</div>

      <UserForm @addMasterInUserList="addMasterInUserList"/>
      <UserList :users="filteredMasters" />

      <br>
      <div class="ytips-text ytips-text_padding ytips-text_12">
        <p>Для добавления мастера в подгруппу введите номер телефона и нажмите Добавить.</p>
        <p>Для присвоения мастеру индивидуального QR кода - выделите мастера и нажмите на QR код, далее, следуйте инструкциям.</p>
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
