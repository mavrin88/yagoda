<template>
  <div class="admin" v-if="!visibleNewOrder && !visibleShowOrder">
    <Head :title="meta.title" />

    <div class="admin__container">
      <div class="admin-orders">
        <div class="admin-orders__top">
          <div></div>
          <img class="close-icon" @click="back" src="/img/icon_close.svg" alt="">
        </div>

        <div class="admin-orders__top">
          <div></div>
          <!--          <Link class="back" href="/">назад</Link>-->
          <div class="admin-orders__total">{{ dataServer.todayOrdersCount }} / {{ dataServer.todayOrdersTotalSum }} <span>₽</span>
          </div>
        </div>
        <div class="admin-orders__data">
          <span title="заказов с чаем">{{ dataServer.todayOrdersWithTipsCount }}</span>
          <i>/</i>
          <span title="% от всех">{{ dataServer.todayOrdersWithTipsPercentage }} %</span>
          <i>/</i>
          <span title="средний чай">{{ dataServer.mediumTips }} %</span>
          <i>/</i>
          <b title="итого чаевых">{{ dataServer.totalTips }}
            <span>₽</span>
          </b>
        </div>
        <div class="admin-orders__info">
          <Datepicker v-model="date" locale="ru" :format="format" auto-apply :clearable="false" position="left"
                      :enable-time-picker="false" @update:model-value="filterOrders" />
          <!--          <flat-pickr v-model="date" :config="config" @on-change="doSomethingOnChange"/>-->
          <div @click="viewMastersShift()" class="masters">Мастеров: <b>{{ dataServer.masterCount }}</b></div>
        </div>
        <div class="admin-orders__list">
          <OrdersItem v-for="(item, index) in dataServer.orders" :key="index" :item="item"
                      @showOrder="showOrder(item)" />
        </div>
        <div class="admin-orders__buttons">
          <div @click="paySum" class="admin-orders__button">
            <b>Произвольная</b>
            сумма
          </div>
          <div @click="handleNewOrderClick" class="admin-orders__button" :class="{ 'disabled': isNewOrderDisabled }">
            в<b> &nbspКаталог</b></div>
        </div>
      </div>
    </div>
  </div>

  <Masters v-if="visibleMasterShift" @closeMasterShift="hideMasterShift" :masters="masters"
           :administrators="administrators" :employees="employees" />

  <NewOrder v-if="visibleNewOrder && !visibleMasterShift"
            :categories="categories"
            :products="products"
            :qrCodes="qrCodes"
            :masters="masters"
            :administrators="administrators"
            :employees="employees"
            :paySum="visiblePaySum"
            :organizationName="organizationName"
            :organizationId="selectedOrganizationId"
            :bills="formattedArray"
            :selectBill="selectBill"
            :typeForMetrix="typeForMetrix"
            :editProducts="editProducts"
            :editArbitraryAmount="editArbitraryAmount"
  />

  <ShowOrder v-if="visibleShowOrder" :order="showOrderDetail" @back="closeShowOrder" @edit="editShowOrder"
             :qrCodes="qrCodes" />


</template>

<script>
import OrdersItem from './Components/OrdersItem.vue'
import { Head, Link } from '@inertiajs/vue3'
import NewOrder from './NewOrder/Index.vue'
//import flatPickr from 'vue-flatpickr-component'
//import 'flatpickr/dist/flatpickr.css';
//import { Russian } from "flatpickr/dist/l10n/ru.js"
import QrCode from './NewOrder/Components/QrCode.vue'
import Masters from './MastersShift/Index.vue'
import axios from 'axios'
import ShowOrder from './ShowOrder/Index.vue'
import Datepicker from '@vuepic/vue-datepicker'
import '@vuepic/vue-datepicker/dist/main.css'


export default {
  components: {
    Head,
    QrCode,
    //flatPickr,
    OrdersItem,
    Link,
    NewOrder,
    Masters,
    ShowOrder,
    Datepicker,
  },
  props: {
    data: {
      type: Object,
      required: true,
    },
    categories: {
      type: Array,
      required: true,
    },
    products: {
      type: Object,
      required: true,
    },
    qrCodes: {
      type: Array,
      required: true,
    },
    masterShift: {
      type: Array,
      required: true,
    },
    masters: {
      type: Array,
      required: true,
    },
    administrators: {
      type: Array,
      required: true,
    },
    employees: {
      type: Array,
      required: true,
    },
    organizationName: {
      type: String,
      required: false,
    },
    selectedOrganizationId: {
      type: Number,
      required: true,
    },
    formattedArray: {
      type: Object,
      required: true,
    },
    selectBill: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      meta: {
        title: 'Продажи'
      },
      dataServer: this.data,
      showOrderDetail: {},
      editArbitraryAmount: {},
      visibleNewOrder: false,
      visibleShowOrder: false,
      visiblePaySum: false,
      visibleMasterShift: false,
      date: new Date(),
      // config: {
      //   wrap: true,
      //   altFormat: 'd.m.Y',
      //   dateFormat: 'd.m.Y',
      //   locale: 'ru',
      //   utc: false,
      //   time_24hr: true,
      //   enableTime: false,
      // },
      format: this.format,
      typeForMetrix: '',
      editProducts: {},
    }
  },
  mounted() {
    this.checkOrderStatuses()
    this.checkOrderComment()
  },
  computed: {
    visibleCategories() {
      return this.categories.filter(category => category.hide !== 1)
    },
    isNewOrderDisabled() {
      return !this.visibleCategories.length
    },
  },
  methods: {
    handleNewOrderClick() {
      if (this.visibleCategories.length > 0) {
        this.newOrder()
        this.typeForMetrix = 'order'
      }
    },
    checkOrderStatuses() {
      // const pusher = new Pusher('678e7526ac706c68caef', {
      //   cluster: 'eu',
      // })
      //
      // const channel = pusher.subscribe('my-channel')
      //
      // channel.bind('my-event', (data) => {
      //   this.filterOrders();
      //   this.changeOrderStatus(data.orderId)
      // })

      window.Echo.channel('reverb-channel')
        .listen('.order-status-updated', (data) => {
          this.filterOrders();
          this.changeOrderStatus(data.orderId);
        });
    },
    changeOrderStatus(orderId) {
      const orderIndex = this.dataServer.orders.findIndex(order => order.id === orderId)

      if (orderIndex !== -1) {
        this.dataServer.orders[orderIndex].status = 'ok'
        // console.log('Order status updated:', this.dataServer.orders[orderIndex])
      } else {
        console.error('Order not found')
      }
    },
    checkOrderComment() {
      // const pusher = new Pusher('678e7526ac706c68caef', {
      //   cluster: 'eu',
      // })
      //
      // const channel = pusher.subscribe('my-channel')
      //
      // channel.bind('comment-update', (data) => {
      //   this.filterOrders();
      //   this.updateOrderComment(data.orderId, data.comment)
      // })

      window.Echo.channel('reverb-channel')
        .listen('.comment-update', (data) => {
          this.filterOrders();
          this.updateOrderComment(data.orderId, data.comment);
        });

    },
    updateOrderComment(orderId, newComment) {
      const orderIndex = this.dataServer.orders.findIndex(order => order.id === orderId)
      if (orderIndex !== -1) {
        this.dataServer.orders[orderIndex].comment = newComment
        // console.log('Order comment updated:', this.dataServer.orders[orderIndex])
      } else {
        console.error('Order not found for comment update')
      }
    },
    newOrder() {
      this.visibleNewOrder = true
      // this.$inertia.get('/admin/new_order');
    },
    paySum() {
      this.typeForMetrix = 'paySum'
      this.visibleNewOrder = true
      this.visiblePaySum = true
      console.log('visiblePaySum изменился:', this.visiblePaySum)
    },
    // showOrder(item) {
    //   this.$inertia.get(`/orders/${item.id}`);
    // },
    showOrder(item) {
      axios.get(`/orders/${item.id}`)
        .then(response => {
          // Записываем полученный заказ в переменную
          this.showOrderDetail = response.data.order

          this.visibleShowOrder = true

          if (typeof ym === 'function') {
            ym(98232814, 'reachGoal', 'The saved order has been opened')
          }

          // Передаем заказ в необходимый компонент
          // this.$emit('order-selected', order);
        })
        .catch(error => {
          console.error('Ошибка при получении заказа:', error)
        })
    },
    format(date) {
      const day = String(date.getDate()).padStart(2, '0')
      const monthNames = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек']
      const month = monthNames[date.getMonth()]
      const year = date.getFullYear()

      return `${day} ${month}.${year}г`
    },
    // Фильтрация и обновление заказов при изменении статуса
    filterOrders() {
      const selectedDate = new Date(this.date);
      selectedDate.setUTCHours(0, 0, 0, 0);
      const formattedDate = selectedDate.toISOString().split('T')[0];

      axios.post('/orders/filter', {
        filter: {
          date: formattedDate,
        },
        timezone: 'UTC'
      })
        .then((response) => {
          this.dataServer = response.data.data
        })
        .catch((error) => {
          console.error('Ошибка при фильтрации заказов:', error)
        })
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
      this.$inertia.get(`/`)
    },
    closeShowOrder(order) {
      this.visibleShowOrder = false
      if (order && order.is_arbitrary_amount === 1){
        this.visibleNewOrder = true
        this.visiblePaySum = true
        this.editArbitraryAmount = order
      }
    },
    editShowOrder(order) {
      this.visibleShowOrder = false
      this.editProducts = order
      this.newOrder()
    },
  },
}
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
.disabled {
  cursor: default;
  opacity: 0.5;
}
.dp__theme_light {
  --dp-input-padding: 0px 0px 0px 0px;
}
.dp__main {
  width: 150px;
}
</style>
