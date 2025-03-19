<template>
  <div class="admin-new" v-if="visibleNewOrder">
    <div class="admin-services">
      <div class="admin__container admin-pt" v-if="visiblePaySum">
        <div class="admin-services__top">
<!--          <div class="back" @click="cancelOrder">отменить заказ</div>-->
          <div></div>
          <div class="close-icon" @click="cancelOrder"></div>
          <!--            <h2>Выбор услуг</h2>-->
        </div>
      </div>
      <div v-if="!visiblePaySum">
        <div class="admin">
          <div class="admin__container">
            <div class="admin-services__top">
              <div></div>
              <h2>Выбор услуг</h2>
              <img class="close-icon" @click="cancelOrder" src="/img/icon_close.svg" alt="">
              <!--            <h2>Выбор услуг</h2>-->
            </div>

            <div class="admin-services__list">
              <div v-for="product in filteredProducts"
                   :key="product.id"
                   @click="selectProduct(product)"
                   :class="[
                        'admin-services__item',
                        (product === selectedProduct || product.quantity > 0) ? 'admin-services__item_active' : '',
                        !(product === selectedProduct || product.quantity > 0) && !product.image ? 'padding_product' : ''
                   ]"
              >
                <div class="admin-services__icon" v-if="product.image">
                  <img :src="product.image">
                </div>
                <div class="admin-services__name">{{ product.name }}</div>
                <div class="admin-services__right">
                  <div class="admin-services__price">{{ product.price }} <span>₽</span></div>
                  <div class="admin-services__count">{{ product.quantity }}</div>
                </div>

                <div
                  :class="['admin-services__action', product === selectedProduct ? 'admin-services__action_active' : '']">
                  <div class="admin-services__minus" @click.stop="decrementProduct(product)"></div>
                  <div class="admin-services__input">
                    <input type="number" :value="product.quantity">
                  </div>
                  <div class="admin-services__plus" @click.stop="incrementProduct(product)"></div>
                </div>
              </div>
            </div>

            <div class="admin-services__active">
              <div class="admin-services__categories">
                <div @click="selectCategory(category)"
                     v-for="category in categories"
                     :class="['admin-services__category', category === selectedCategory ? 'admin-services__category_active' : '']">
                  <div class="admin-services__image">
                    <img :src="category.image">
                  </div>
                  <div class="admin-services__title">{{ category.name }}</div>
                </div>
              </div>
              <div class="admin-services__bottom">
                <div class="admin-services__info">
                  <div class="admin-services__items">Позиций: <span>{{ selectedProducts.length }}</span></div>
                  <div class="admin-services__total">На сумму: <span>{{ totalPrice }} ₽</span></div>
                </div>
                <div class="admin-services__next">
                  <button :disabled="!selectedProducts.length" class="btn btn_arrow " @click="goNext">далее</button>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>

  <MasterChoice
    v-if="visibleMasterChoice"
    @goBack="goBack"
    :selectedProducts="selectedProducts"
    :totalPrice="totalPrice"
    :masters="masters"
    :administrators="administrators"
    :employees="employees"
    @selectMaster="selectMaster"
    @setDiscount="setDiscount"
    @showQrCode="showQrCode"
    :draftOrder="draftOrder"
    :selectedMasters="selectedMasters"
    :discount="Discount"
  />

  <QrCode
    v-if="visibleQrCode"
    @hideQrCode="hideQrCode"
    @saveOrder="saveOrder"
    @saveOrderGenerateHideQrCode="saveOrderGenerateHideQrCode"
    @setQrCode="setQrCode"
    :qrCodes="qrCodes"
    :totalPrice="totalPrice"
    :selectedMasters="selectedMasters"
    :organizationName="organizationName"
    :draftOrder="draftOrder"
    :discount="Discount"
  />

  <PaySum v-if="visiblePaySum" @savePay="savePay" @closePaySum="closePaySum"/>

  <Spiner :isLoading="isLoading"></Spiner>

</template>

<script>
import { Link } from '@inertiajs/vue3'
import MasterChoice from './Components/MasterChoice.vue'
import QrCode from './Components/QrCode.vue'
import PaySum from './Components/PaySum.vue'
import axios from 'axios'
import Spiner from '@/Shared/Spiner.vue'

export default {
  components: {
    Spiner,
    MasterChoice,
    QrCode,
    Link,
    PaySum
  },
  props: {
    categories: {
      type: Object,
      required: true,
    },
    products: {
      type: Object,
      required: true,
    },
    qrCodes: {
      type: Object,
      required: true,
    },
    masters: {
      type: Object,
      required: true,
    },
    administrators: {
      type: Array,
      required: true
    },
    employees: {
      type: Array,
      required: true
    },
    paySum: {
      type: Boolean,
      required: true,
      default: false
    },
    organizationName: {
      type: String,
      required: false,
    },
    organizationId: {
      type: Number,
    },
  },
  data() {
    return {
      selectedCategory: null,
      selectedProduct: null,
      filteredProducts: null,
      selectedProducts: [],
      selectedMasters: [],
      visibleNewOrder: true,
      visibleMasterChoice: false,
      visibleQrCode: false,
      visiblePaySum: false,
      qrCode: null,
      Discount: null,
      draftOrder: null,
      isLoading: false,
    }
  },
  mounted() {
    if (!this.categories || this.categories.length === 0) {
      return
    }

    const firstCategory = this.categories[0]

    this.selectCategory(firstCategory)

    if (this.paySum) {
      // todo: Не забыть это убрать
      this.selectedProducts = [
        {
          "id": 1,
          "category_id": 1,
          "name": "Товар/услуга",
          "price": 1000,
          "image": "http://127.0.0.1:8000/img/qr_codes/qr.png",
          "created_at": "2024-07-24T06:36:33.000000Z",
          "updated_at": "2024-07-24T06:36:33.000000Z",
          "quantity": 1
        }
      ]

      this.visiblePaySum = this.paySum
    }

    this.createDraftOrder()
  },
  computed: {
    totalPrice() {
      if (!Array.isArray(this.selectedProducts)) {
        return 0;
      }

      return this.selectedProducts.reduce((total, product) => {
        return total + (product.quantity * product.price);
      }, 0);
    },
  },
  methods: {
    selectCategory(category) {
      this.selectedCategory = category
      this.filteredProducts = this.products[this.selectedCategory.id]
    },
    selectProduct(product) {
      this.selectedProduct = product

      this.incrementProduct(product)
    },
    incrementProduct(product) {
      const index = this.selectedProducts.indexOf(product)

      if (index === -1) {
        product.quantity = 1
        this.selectedProducts.push(product)
      } else {
        this.selectedProducts[index].quantity++

        // setTimeout(() => {
        //   this.selectedProduct = null;
        // }, 1000);
      }
    },
    decrementProduct(product) {
      const index = this.selectedProducts.indexOf(product)

      if (index > -1) {
        this.selectedProducts[index].quantity--

        // setTimeout(() => {
        //   this.selectedProduct = null;
        // }, 1000);

        const productCopy = { ...product }
        productCopy.quantity--
        if (productCopy.quantity < 0) {
          this.selectedProducts.splice(index, 1)
          delete productCopy.quantity
          delete product.quantity
        }
      }
    },
    goNext() {
      this.visibleNewOrder = false
      this.visibleMasterChoice = true
      this.visiblePaySum = false
    },
    goBack() {
      this.visibleNewOrder = true
      this.visibleMasterChoice = false
      if (this.paySum) {
        this.visiblePaySum = true
      }
    },
    showQrCode() {
      this.visibleNewOrder = false
      this.visibleMasterChoice = false
      this.visibleQrCode = true
    },
    hideQrCode() {
      this.visibleQrCode = false
      this.visibleNewOrder = false
      this.visibleMasterChoice = true
      if (this.paySum) {
        this.visiblePaySum = false
      }
    },
    selectMaster(master) {
      const index = this.selectedMasters.findIndex(m => m.id === master.id);
      if (index === -1) {
        // Мастер не выбран, добавляем его в массив
        this.selectedMasters.push(master);
      } else {
        // Мастер уже выбран, удаляем его из массива
        this.selectedMasters.splice(index, 1);
      }
    },
    setDiscount(Discount) {
      this.Discount = Discount
    },
    setQrCode(qrCode) {
      this.qrCode = qrCode
    },
    saveOrder(draftOrder) {
      axios.post('/admin/save_order', {
        draftOrder: draftOrder,
        products: this.selectedProducts,
        masters: this.selectedMasters,
        totalPrice: this.totalPrice,
        qrCode: this.qrCode,
        discount: this.Discount,
      })
        .then(response => {
          if (response.data.error) {
              alert(response.data.error);
          }else {
            // this.$inertia.get('/orders');
          }
        })
        .catch(error => {
          console.error(error);
        });
    },

    saveOrderGenerateHideQrCode(draftOrder) {
      axios.post('/admin/save_order', {
        draftOrder: draftOrder,
        products: this.selectedProducts,
        masters: this.selectedMasters,
        totalPrice: this.totalPrice,
        qrCode: this.qrCode,
        discount: this.Discount,
      })
        .then(response => {

        })
        .catch(error => {
          console.error(error);
        });



      // this.$inertia.post('/admin/save_order', {
      //   draftOrder: draftOrder,
      //   products: this.selectedProducts,
      //   masters: this.selectedMasters,
      //   totalPrice: this.totalPrice,
      //   qrCode: this.qrCode,
      //   discount: this.Discount,
      // })
    },
    savePay(paySum) {
      this.selectedProducts[0].price = paySum
      this.hideQrCode()
      this.visiblePaySum = false
    },
    setCursorToEnd() {
      // Установим курсор в конец поля ввода
      const input = this.$refs.payInput;
      if (this.discount === '0') {
        input.setSelectionRange(input.value.length, input.value.length);
      } else {
        input.setSelectionRange(1, 1);
      }
    },
    createDraftOrder() {
      axios.post('api/createDraftOrder', {
        organizationId: this.organizationId,
      })
        .then(response => {
          this.draftOrder = response.data;
        })
        .catch(error => {
          console.error(error);
        });
    },

    startLoading() {
      this.isLoading = true;
    },

    stopLoading() {
      this.isLoading = false;
    },

    cancelOrder() {
      this.startLoading();

      this.$inertia.post('/admin/delete_order/', {
        orderId: this.draftOrder.order.id
      });

      setTimeout(() => {
        this.stopLoading();
        this.$inertia.visit("/orders");
      }, 300);
    },
    closePaySum() {

      this.$inertia.visit("/orders");

      setTimeout(() => {
        this.visiblePaySum = false
      }, 300);
    }
  },
}
</script>

<style scoped>
.padding_product{
  padding: 1.6rem 0.875rem 1.6rem 0.875rem;
}

.close-icon {
  cursor: pointer;
}

.admin-services__top h2 {
  font-size: 16px;
  font-weight: 300;
  margin-right: -170px;
  padding: 0;
  text-transform: uppercase;
}
</style>
