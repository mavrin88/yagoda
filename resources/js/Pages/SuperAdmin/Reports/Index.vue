<template>
  <div class="superadmin">
    <Head title="Отчёты" />
    <div class="admin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>ОТЧЁТЫ</h2>
      </div>
      <div class="">
        <button class="font-medium mt-5"  @click="goToSales">ПРОДАЖИ</button>
<!--        <button  @click="goTips">ЧАЕВЫЕ</button>-->
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
  },
  data() {
    return {
    };
  },
  methods: {
    goToSales() {
      this.$inertia.visit(`/super_admin/orders_statistics`);
    },
    goTips() {
      this.$inertia.visit(`/super_admin/tips`);
    },
  },
}
</script>

<style scoped>
input {
  background: none;
  background: transparent;
}
.admin__links {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-start;
}
.admin__links button {
  color: black;
}
.admin-new-sum .btn {
  margin-top: 40px !important;
  position: relative !important;
}
.admin-new-sum__field input {
  text-align: center;
  font-size: 29px;
}
.admin-new-sum__field span{
  top: -28px;
}
</style>
