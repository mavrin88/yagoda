<template>
  <div class="admin" v-if="!visibleNewOrder && !visibleShowOrder">
    <div class="admin__container">
      <div class="admin-orders">
        <div class="admin-orders__top">
          <div></div>
          <img class="close-icon" @click="back" src="/img/icon_close.svg" alt="">
        </div>
        <div class="admin-orders__top">
          <div></div>
<!--          <Link class="back" href="/">назад</Link>-->
          <div class="admin-orders__total">{{ data.todayOrdersCount }} / {{ data.todayOrdersTotalSum }} <span>₽</span></div>
        </div>
        <div class="admin-orders__data">
          <span title="заказов с чаем">{{ data.todayOrdersWithTipsCount }}</span>
          <i>/</i>
          <span title="% от всех">{{ data.todayOrdersWithTipsPercentage }} %</span>
          <i>/</i>
          <span title="средний чай">{{ data.medium_tips }} %</span>
          <i>/</i>
          <b title="итого чаевых">{{ data.totalTips }}
            <span>₽</span>
          </b>
        </div>
        <div class="admin-orders__info">
          <flat-pickr v-model="date" :config="config" @on-change="doSomethingOnChange"/>
<!--          <div @click="viewMastersShift()" class="masters">Мастеров: <b>{{ data.masterCount }}</b></div>-->
        </div>
        <div class="admin-orders__list">
          <OrdersItem v-for="(item, index) in dataServer.orders" :key="index" :item="item" @showOrder="showOrder(item)"/>
        </div>
<!--        <div class="admin-orders__buttons">-->
<!--          <div @click="paySum" class="admin-orders__button">-->
<!--            Ввести&nbsp;<b>сумму</b>-->
<!--            для оплаты-->
<!--          </div>-->
<!--          <div @click="newOrder" class="admin-orders__button"><b>Новый</b>&nbsp;заказ</div>-->
<!--        </div>-->
      </div>
    </div>
  </div>

  <Masters v-if="visibleMasterShift" @closeMasterShift="hideMasterShift" :masters="masters" :administrators="administrators" :employees="employees"/>

  <NewOrder v-if="visibleNewOrder && !visibleMasterShift"
            :categories="categories"
            :products="products"
            :qrCodes="qrCodes"
            :masters="masters"
            :administrators="administrators"
            :employees="employees"
            :paySum="visiblePaySum"
            :organizationName="organizationName"
            :organizationId="selectedOrganizationId"/>

  <ShowOrder v-if="visibleShowOrder" :order="showOrderDetail" :orderStatistics="orderStatistics" @back="closeShowOrder" :qrCodes="qrCodes"/>

</template>

<script>
import OrdersItem from './Components/OrdersItem.vue';
import { Link } from '@inertiajs/vue3'
import NewOrder from './NewOrder/Index.vue'
import flatPickr from 'vue-flatpickr-component'
import 'flatpickr/dist/flatpickr.css';
import { Russian } from "flatpickr/dist/l10n/ru.js"
import QrCode from './NewOrder/Components/QrCode.vue'
import Masters from './MastersShift/Index.vue'
import axios from 'axios'
import ShowOrder from './ShowOrder/Index.vue'

export default {
  components: {
    QrCode,
    flatPickr,
    OrdersItem,
    Link,
    NewOrder,
    Masters,
    ShowOrder
  },
  props: {
    data: {
      type: Object,
      required: true
    },
    categories: {
      type: Array,
      required: true
    },
    products: {
      type: Object,
      required: true
    },
    qrCodes: {
      type: Array,
      required: true
    },
    masterShift: {
      type: Array,
      required: true
    },
    masters: {
      type: Array,
      required: true
    },
    administrators: {
      type: Array,
      required: true
    },
    employees: {
      type: Array,
      required: true
    },
    organizationName: {
      type: String,
      required: false,
    },
    selectedOrganizationId: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      dataServer: this.data,
      showOrderDetail: {},
      orderStatistics: {},
      visibleNewOrder: false,
      visibleShowOrder: false,
      visiblePaySum: false,
      visibleMasterShift: false,
      date: new Date(),
      config: {
        wrap: true,
        altFormat: 'd.m.Y',
        dateFormat: 'd.m.Y',
        locale: Russian
      }
    };
  },
  methods: {
    newOrder() {
      this.visibleNewOrder = true
      // this.$inertia.get('/admin/new_order');
    },
    paySum() {
      this.visibleNewOrder = true
      this.visiblePaySum = true
      console.log("visiblePaySum изменился:", this.visiblePaySum);
    },
    // showOrder(item) {
    //   this.$inertia.get(`/orders/${item.id}`);
    // },
    showOrder(item) {
      axios.get(`/orders/${item.id}`)
        .then(response => {
          // Записываем полученный заказ в переменную
          this.showOrderDetail = response.data.order;
          this.orderStatistics = response.data.orderStatistic;

          this.visibleShowOrder = true

          // Передаем заказ в необходимый компонент
          // this.$emit('order-selected', order);
        })
        .catch(error => {
          console.error('Ошибка при получении заказа:', error);
        });
    },
    doSomethingOnChange() {
      const selectedDate = new Date(this.date);
      selectedDate.setUTCHours(0, 0, 0, 0);
      const formattedDate = selectedDate.toISOString().split('T')[0];
      axios.post('/orders/filter', {
        filter: {
          date: formattedDate
        },
        timezone: 'UTC'
      })
        .then((response) => {
          this.dataServer = response.data.data
        })
        .catch((error) => {

        });
    },
    viewMastersShift() {
      // this.$inertia.visit('/admin/masters_shift');
      this.visibleMasterShift = true
      this.visibleNewOrder = true
    },
    hideMasterShift() {
      this.visibleMasterShift = false
      this.visibleNewOrder = false
    },
    back() {
      this.$inertia.get(`/`);
    },
    closeShowOrder() {
      this.visibleShowOrder = false
    }
  },
};
</script>

<style scoped>
.flatpickr-input[readonly] {
  border: none;
  background-color: transparent;
  font-size: inherit;
  padding: 0;
  cursor: default;
  max-width: 30%;
  color: #9ca4b5;
}

.masters {
  cursor: pointer;
}

.close-icon {
  cursor: pointer;
}
</style>
