<template>
  <div class="admin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin__container">
      <div class="admin-bill">
        <div class="admin-bill__top">
          <Link class="back" href="/">назад</Link>
        </div>
        <BillList :items="data.billItems" :total="data.total"></BillList>
        <BillDiscount :discount="data.discount" :discountedTotal="data.discountedTotal"></BillDiscount>
        <BillMaster :user="data.user" :tips="data.tips"></BillMaster>
        <BillFooter :date="data.date" :status="data.status"></BillFooter>
      </div>
    </div>
  </div>
</template>

<script>

import { Head, Link } from '@inertiajs/vue3'
import BillList from './Components/BillList.vue';
import BillDiscount from './Components/BillDiscount.vue';
import BillMaster from './Components/BillMaster.vue';
import BillFooter from './Components/BillFooter.vue';

export default {
  name: 'AdminBillComponent',
  components: {
    Head,
    Link,
    BillList,
    BillDiscount,
    BillMaster,
    BillFooter,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      meta: {
        title: 'Счета'
      },
      total: 10400,
      discount: 10,
      discountedTotal: 1440,
      user: {
        avatar: '/img/content/avatar.png',
        name: 'Василий Сергеевич Рюриков-Корсаковский-Аполлонов',
      },
      tips: 890,
      date: '10 июля 2024 16:56',
      status: 'оплачено',
    };
  },
  methods: {

  },
};
</script>
