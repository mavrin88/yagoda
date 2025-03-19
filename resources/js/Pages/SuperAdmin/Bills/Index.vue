<template>
  <div id="app">
    <Head title="Счета" />
    <div class="superadmin">
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" href="/">назад</Link>
          <h2>СЧЕТА</h2>
        </div>
        <BillsForm :user="auth.user"/>
        <BillsList :bills="bills" @bill-deleted="handleBillDeleted"/>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import BillsList from './Components/BillsList.vue'
import BillsForm from './Components/BillsForm.vue'

export default {
  components: {
    Head,
    Link,
    BillsList,
    BillsForm
  },
  props: {
    auth: Object,
    bills: Object,
  },
  data() {
    return {

    };
  },
  methods: {
    handleBillDeleted(id) {
      const index = this.bills.findIndex(bill => bill.id === id);
      if (index !== -1) {
        this.bills.splice(index, 1);
      }
    },
  },
}
</script>
