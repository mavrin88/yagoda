<template>
  <div class="admin-new" v-if="visibleNewOrder">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin-services">
      <div class="admin__container admin-pt" v-if="visiblePaySum">
        <div class="admin-services__top">
<!--          <div class="back" @click="cancelOrder">отменить заказ</div>-->
          <div></div>
          <div class="close-icon" @click="cancelOrder"></div>
        </div>
      </div>
      <div v-if="!visiblePaySum">
        <div class="admin">
          <div class="admin__container">
            <div class="admin-services__top">
              <div class="relative search_block ml-5 flex">
                <svg class="absolute top-1/10 pb-2 search_icon" xmlns="http://www.w3.org/2000/svg" width="35" height="35" viewBox="0 0 24 24" style="stroke: #9CA4B5;">
                  <g fill="none" stroke="">
                    <circle cx="11" cy="11" r="6" />
                    <path stroke-linecap="round" d="m20 20l-3-3" />
                  </g>
                </svg>

                <input v-model="searchText" type="text" class="pl-8 pr-10 py-1 focus:outline-none no-background flex-1">
                <svg v-if="searchText.length >= 2" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="absolute right-0 top-1/2 transform -translate-y-1/2 cursor-pointer" @click="cancelSearch">
                  <path fill="currentColor" d="m12 12.727l-3.244 3.252q-.161.16-.358.15t-.358-.17t-.16-.363t.16-.363L11.274 12L8.04 8.782q-.16-.161-.16-.367t.16-.368t.364-.16q.204 0 .363.16L12 11.298l3.219-3.252q.161-.16.358-.16t.358.16q.165.166.165.367t-.165.36L12.702 12l3.252 3.244q.16.161.16.358t-.16.358q-.166.165-.367.165t-.36-.165z"/>
                </svg>
              </div>
              <img class="close-icon" @click="cancelOrder" src="/img/icon_close.svg" alt="">
            </div>


            <div class="admin-services__list">
              <div v-for="product in filteredProducts"
                   :key="product.id"
                   @click="selectProduct(product)"
                   :class="[
            'admin-services__item',
            selectedProducts.some(item => item.id === product.id) ? 'admin-services__item_active' : '',
            !(selectedProducts.some(item => item.id === product.id)) && !product.image ? 'padding_product' : '']">
                <div class="admin-services__icon" v-if="product.image">
                  <img :src="product.image">
                </div>
                <div class="admin-services__name">{{ product.name }}</div>
                <div class="admin-services__right">
                  <div @click="openEditProductPrice(product)"
                       :class="['admin-services__price', boldAndLargeProductId === product.id ? 'bold-and-large' : '']">
                    {{ getProductPrice(product) }}<span>₽</span>
                  </div>

                  <div class="admin-services__count" v-if="selectedProducts.some(item => item.id === product.id)">
                    <span class="count-x">x </span> {{ getSelectedProductQuantity(product) }}
                  </div>
                  <div class="admin-services__count" v-else-if="product.quantity > 0">
                    <span class="count-x">x </span>{{ product.quantity }}
                  </div>

                </div>
                <div
                  :class="['admin-services__action', selectedProducts.some(item => item.id === product.id)? 'admin-services__action_active' : '']">
                  <div class="admin-services__minus mr-2" @click.stop="decrementProduct(product)"></div>
                  <div class="admin-services__input">
<!--                    <input type="number" :value="product.quantity">-->
                  </div>
                  <div class="admin-services__plus" @click.stop="incrementProduct(product)"></div>
                </div>
              </div>
            </div>
            <div class="admin-services__active">
              <div class="carousel">
                <Swiper :slidesPerView="slidesPerView" :spaceBetween="spaceBetween">
                  <SwiperSlide v-for="category in filteredCategories" :key="category.id">
                    <div @click="selectCategory(category)"
                         :class="['admin-services__category', category === selectedCategory ? 'admin-services__category_active' : '', searchText ? 'dimmed' : '' ]">
                                <div class="admin-services__image" :class="{ 'dimmed': category != selectedCategory }">
                                  <img :src="category.image">
                                </div>
                      <div class="admin-services__title">{{ category.name }}</div>
                    </div>
                  </SwiperSlide>
                </Swiper>
              </div>

              <div class="admin-services__bottom">
                <div class="admin__container">
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
    @onlySaveOrder="saveOrderAndReturnToOrdersPage"
    :draftOrder="draftOrder"
    :selectedMasters="selectedMasters"
    :discount="Discount"
    :editOrder ="editOrder"
  />

  <QrCode
    v-if="visibleQrCode"
    :selectedProducts="selectedProducts"
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
    :dopOrderData="dopOrderData"
    :isEditingOrder="isEditingOrder"
    :editedOrderId ="editedOrderId"
  />

  <PaySum v-if="visiblePaySum" :bills="bills" :selectBill="selectingBill" :editOrder="editArbitraryAmount" @savePay="savePay" @closePaySum="closePaySum"/>

  <Spiner :isLoading="isLoading"></Spiner>

  <ModalEditProductPrice
    :is-active="isModalEditProductPriceActive"
    :product="productForEditPrice"
    @close="hideModalEditProductPrice"
    @confirmed="saveNewProductPrice"
  />

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import MasterChoice from './Components/MasterChoice.vue'
import QrCode from './Components/QrCode.vue'
import PaySum from './Components/PaySum.vue'
import axios from 'axios'
import Spiner from '@/Shared/Spiner.vue'
import { Swiper, SwiperSlide } from 'swiper/vue'
import 'swiper/css'
import ModalEditProductPrice from '../../../../Shared/Modals/ModalEditProductPrice.vue'

export default {
  components: {
    Head,
    ModalEditProductPrice,
    Spiner,
    Swiper,
    SwiperSlide,
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
    bills: {
      type: Object,
      required: true,
    },
    selectBill: {
      type: Object,
      required: true,
    },
    typeForMetrix: {
      type: String,
      required: true,
    },
    editProducts: {
      type: Object,
    },
    editArbitraryAmount: {
      type: Object,
    },
  },
  data() {
    return {
      meta: {
        title: 'Заказ'
      },
      selectedCategory: null,
      selectedProduct: null,
      filteredProducts: null,
      selectedProducts: [],
      selectedMasters: [],
      selectingBill: this.selectBill,
      visibleNewOrder: true,
      visibleMasterChoice: false,
      visibleQrCode: false,
      visiblePaySum: false,
      qrCode: null,
      Discount: null,
      draftOrder: null,
      isLoading: false,
      slidesPerView: 4,
      editedOrderId: 0,
      spaceBetween: 0,
      dopOrderData: {},
      editOrder: {},
      searchText: '',
      isModalEditProductPriceActive: false,
      productForEditPrice: '',
      boldAndLargeProductId: null,
      isEditingOrder: false,
      isArbitraryAmount: false,
      newClient: false,
    }
  },
  mounted() {
    const visibleCategories = this.categories.filter(category => category.hide === 0);
    const firstCategory = visibleCategories[0];

    if (this.editProducts && this.editProducts.is_arbitrary_amount === 0 && firstCategory) {
      this.setSelectedProducts(this.editProducts);
      this.isEditingOrder = true
      this.Discount = this.editProducts.discount
      this.editedOrderId = this.editProducts.id
      this.editOrder = this.editProducts
      this.selectCategory(this.getCategoryFromFirstEditedProduct(visibleCategories))
      this.selectedProducts = this.selectedProducts.map(product => {
        // Ищем категорию, которая содержит этот продукт
        const matchingCategory = visibleCategories.find(visibleCategory =>
          visibleCategory.products.some(prod => prod.id === product.id)
        );
        if (matchingCategory) {
          return {
            ...product,
            category_id: matchingCategory.id,
          };
        }
        return product;
      });
    }else {
      if (firstCategory) {
        this.selectCategory(firstCategory)
      }
    }

    if (this.paySum) {
      this.selectedProducts = this.filteredProductsFromHiddenCategories
      this.visiblePaySum = this.paySum
    }
    this.createDraftOrder()
  },
  computed: {
    filteredCategories() {
      return this.categories.filter(category => category.hide === 0)
    },
    filteredProductsFromHiddenCategories() {
      const hiddenCategories = this.categories.filter(category => category.hide === 1)
      return hiddenCategories.flatMap(category =>
        category.products.map(product => ({
          ...product,
          quantity: 1,
        })),
      )
    },
    totalPrice() {
      if (!Array.isArray(this.selectedProducts)) {
        return 0;
      }

      return this.selectedProducts.reduce((total, product) => {
        return total + (product.quantity * product.price);
      }, 0);
    },
    filteredProducts() {
      if (!this.searchText && this.selectedCategory) {
        return this.selectedCategory.products;
      }

      const query = this.searchText.toLowerCase();
      return this.filteredCategories.flatMap(category =>
        category.products.filter(product =>
          product.name.toLowerCase().includes(query)
        )
      );
    },
  },
  watch: {
    qrCode(newValue) {
      if (newValue) {
        if (!this.visibleQrCode)
         this.onlySaveOrder();
      }
    }
  },
  methods: {
    getCategoryFromFirstEditedProduct(visibleCategories) {
      const firstProduct = this.selectedProducts[0];
      const matchingCategory = visibleCategories.find(category => {
        return category.products.some(prod => prod.id === firstProduct.id);
      });
      if (matchingCategory) {
        return matchingCategory;
      }
    },
    getProductPrice(product) {
      const selectedProduct = this.selectedProducts.find(item => item.id === product.id);
      if (selectedProduct && selectedProduct.new_price) {
        return selectedProduct.new_price;
      }
      return product.price;
    },
    getSelectedProductQuantity(product) {
      const selectedProduct = this.selectedProducts.find(item => item.id === product.id);
      if (selectedProduct) {
        return selectedProduct.quantity;
      }
      return product.quantity;
    },
    selectCategory(category) {
      this.selectedCategory = category
      this.filteredProducts = this.products[this.selectedCategory.id]
    },
    selectProduct(product) {
      this.selectedProduct = product

      this.firstIncrementProduct(product)
      this.boldAndLargeProductId = product.id;
    },

    setSelectedProducts(products) {
      products.participantsArray.forEach(participant => {
          this.selectMaster({ id: participant.id });
      });
      products.order_items.forEach(product => {
        if ('product_id' in product && 'product_price' in product) {
          const productWithIdAndPriceAndName = {
            ...product,
            id: product.product_id,
            price: product.product_price,
            name: product.product_name
          };
          delete productWithIdAndPriceAndName.product_id;
          delete productWithIdAndPriceAndName.product_price;
          delete productWithIdAndPriceAndName.product_name;
          this.selectedProducts.push(productWithIdAndPriceAndName);
        }
      });
    },
    firstIncrementProduct(product) {
      const index = this.selectedProducts.indexOf(product)
      if (index === -1) {
        product.quantity = 1
        this.selectedProducts.push(product)
      }
    },
    incrementProduct(product) {
      const index = this.selectedProducts.findIndex(p => p.id === product.id);
      if (index !== -1) {
        this.selectedProducts[index].quantity++;
      }
    },
    decrementProduct(product) {
      const index = this.selectedProducts.findIndex(p => p.id === product.id);

      if (index !== -1) {
        this.selectedProducts[index].quantity--;

        if (this.selectedProducts[index].quantity <= 0) {
          this.selectedProducts.splice(index, 1);

          if (this.selectedProduct === product) {
            this.selectedProduct = null;
          }
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
    showQrCode(dopOrderData) {
      this.dopOrderData = dopOrderData

      if (dopOrderData.newClientData.discount > 0) {
        this.Discount = dopOrderData.newClientData.discount
      }

      if(this.typeForMetrix === 'order'){
        if (typeof ym === 'function') {
          ym(98232814,'reachGoal','order_selection_from_the_catalog')
        }
      }else{
        if (typeof ym === 'function') {
          ym(98232814,'reachGoal','order_any_amount')
        }
      }
      this.visibleNewOrder = false
      this.visibleMasterChoice = false
      this.visibleQrCode = true
    },
    saveOrderAndReturnToOrdersPage(dopOrderData) {
      this.dopOrderData = dopOrderData
      if (dopOrderData.newClientData.discount > 0) {
        this.Discount = dopOrderData.newClientData.discount
      }
      this.generateQrCode()
      this.onlySaveOrder()
    },
    generateQrCode() {
      axios.get('generateHideQrCode')
        .then(response => {
          this.qrCode = response.data
        })
        .catch(error => {
          console.error(error)
        })
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
    onlySaveOrder() {
      axios.post('/admin/save_order', {
        arbitraryAmount: this.isArbitraryAmount,
        draftOrder: this.draftOrder,
        products: this.selectedProducts,
        masters: this.selectedMasters,
        totalPrice: this.totalPrice,
        qrCode: this.qrCode,
        discount: this.Discount,
        dopOrderData: this.dopOrderData
      })
        .then(response => {
          if (response.data.error) {
              alert(response.data.error);
          }else {
            this.returnToOrdersMainPage()
          }
        })
        .catch(error => {
          console.error(error);
        });
    },

    saveOrder(draftOrder) {
      const url = this.isEditingOrder? '/admin/edit_order' : '/admin/save_order';

      axios.post(url, {
        arbitraryAmount: this.isArbitraryAmount,
        draftOrder: draftOrder,
        order_id: this.editedOrderId,
        products: this.selectedProducts,
        masters: this.selectedMasters,
        totalPrice: this.totalPrice,
        qrCode: this.qrCode,
        discount: this.Discount,
        dopOrderData: this.dopOrderData
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
        arbitraryAmount: this.isArbitraryAmount,
        draftOrder: draftOrder,
        products: this.selectedProducts,
        masters: this.selectedMasters,
        totalPrice: this.totalPrice,
        qrCode: this.qrCode,
        discount: this.Discount,
        dopOrderData: this.dopOrderData
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
    savePay(paySum, selectingACashReceiptAccount, editOrder) {
      if (Object.keys(editOrder).length === 0 && editOrder.constructor === Object) {
        this.isArbitraryAmount = true
        this.isEditingOrder = false
      }else{
        this.editOrder = editOrder
        this.isArbitraryAmount = true
        this.isEditingOrder = true
        this.editedOrderId = editOrder.id
        this.Discount = editOrder.discount
        editOrder.participantsArray.forEach(participant => {
          this.selectMaster({ id: participant.id });
        });
      }

      this.selectedProducts[0].price = paySum
      this.hideQrCode()
      this.visiblePaySum = false
      this.selectingBill = selectingACashReceiptAccount
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
    },
    returnToOrdersMainPage() {
      this.startLoading();
      this.$inertia.visit("/orders");

      setTimeout(() => {
        this.stopLoading();
      }, 2500);
    },
    cancelSearch(){
      this.searchText = ''
      this.selectCategory(this.filteredCategories[0]);
    },
    openEditProductPrice(product) {
      this.productForEditPrice = { ...product };
      this.isModalEditProductPriceActive = true;
    },
    hideModalEditProductPrice() {
      this.isModalEditProductPriceActive = false;
    },
    saveNewProductPrice(updatedProduct) {
      const productIndex = this.selectedProducts.findIndex(product => product.id === updatedProduct.id);

      if (productIndex !== -1) {
        if (updatedProduct.new_price) {
          this.selectedProducts[productIndex].price = updatedProduct.new_price;
        }
        this.selectedProducts[productIndex].new_price = updatedProduct.new_price;
      }
    }
  },
}
</script>

<style scoped>
.padding_product{
  padding: 1.6rem 0.875rem 1.6rem 0.875rem;
}
.search_block input{
  border-bottom: 1px solid #9CA4B5;
}
.no-background {
  background-color: transparent !important;
  border: none;
}
.close-icon {
  cursor: pointer;
}

.admin-services__active{
  background-color: white;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  padding-bottom: 10px;
  max-width: 100%;
  bottom: 0rem;
}
.carousel{
  padding-top: 15px;
}
.admin-services__top h2 {
  font-size: 16px;
  font-weight: 300;
  margin-right: -170px;
  padding: 0;
  text-transform: uppercase;
}

.admin-services__image.dimmed::after {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.5);
  pointer-events: none;
}

.admin-services__bottom .admin__container {
  display: flex;
  justify-content: space-between;
  gap: .8125rem;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
.swiper-slide{
  width: 80px !important;
}
.admin-services__category.dimmed {
  opacity: 0.5;  /* Это делает элемент полупрозрачным */
  pointer-events: none; /* Это отключает взаимодействие с элементом (по желанию) */
  filter: grayscale(50%); /* Делаем элемент полусерым, если нужно */
}
.search_icon {
  right: 90%;
}
.admin-services__count .count-x {
  font-weight: normal;
}
.admin-services__action {
  right: 6.2rem;
}

.bold-and-large {
  font-weight: 700;
  font-size: 20px; /* Увеличьте размер шрифта по вашему усмотрению */
}
</style>
