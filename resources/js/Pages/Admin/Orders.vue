<template>
  <div class="admin">

    <Head :title="meta.title" />

    <div class="admin__container">
      <div class="admin-orders">
        <div class="admin-orders__top">
          <Link class="back" href="/">назад</Link>
          <div class="admin-orders__total">{{ data.total_orders_sale }} <span>₽</span></div>
        </div>
        <div class="admin-orders__data">
          <span title="заказов с чаем">{{ data.total_orders_tips }}</span>
          <i>/</i>
          <span title="% от всех">{{ data.percent_tips }} %</span>
          <i>/</i>
          <span title="средний чай">{{ data.medium_tips }} %</span>
          <i>/</i>
          <b title="итого чаевых">{{ data.total_tips }}
            <span>₽</span>
          </b>
        </div>
        <div class="admin-orders__info">
          <div>{{ data.date }}</div>
          <div>Мастеров: <b>{{ data.master_count }}</b></div>
        </div>
        <div class="admin-orders__list">
          <AdminOrdersItem v-for="(item, index) in data.orders" :key="index" :item="item" />
        </div>
        <div class="admin-orders__buttons">
          <div class="admin-orders__button">
            Ввести&nbsp;<b>сумму</b>
            для оплаты
          </div>
          <div class="admin-orders__button"><b>Новый</b>&nbsp;заказ</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AdminMasters from './Components/AdminMasters.vue';
import AdminOrdersItem from './Components/AdminOrdersItem.vue'
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    AdminMasters,
    AdminOrdersItem,
    Link
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
        title: 'Продажи'
      },
    };
  },
  methods: {

  },
};
</script>
