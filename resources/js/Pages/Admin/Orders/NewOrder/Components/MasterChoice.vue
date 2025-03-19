<template>
  <div class="admin" v-show="visibleMasterChoice">
    <div class="admin__container">
      <div class="admin-choose-masters">
        <div class="admin-choose-masters__top">
<!--          <div class="back" @click="goBack">назад</div>-->
          <img class="line-icon" @click="goBack" src="/img/icon_line.svg" alt="">
          <h2>Выбор мастера</h2>
          <img class="close-icon" @click="cancelOrder" src="/img/icon_close.svg" alt="">
        </div>
        <div class="admin-bill">
          <div class="admin-bill__block">
            <div class="admin-bill__list">
              <ul>
                <li v-for="product in selectedProducts" :key="product.id">
                  <span class="admin-bill__title">{{ product.name }}</span>
                  <span>{{  $formatNumber(product.price) }}</span>
                  <span>{{ product.quantity }}</span>
                  <span><b>{{ product.price * product.quantity }}</b></span>
                </li>
              </ul>
              <p>{{ calculateDiscount.totalAmount }} <span>₽</span></p>
            </div>
          </div>
          <div @click="openModal" class="admin-bill__block" style="cursor: pointer">
            <div class="admin-bill__total">
              <div>Скидка (%): <b><u>{{ Discount }}</u></b></div>
              <div><b>{{ calculateDiscount.totalAfterDiscount }}</b><span> ₽</span></div>
            </div>
          </div>
          <div class="admin-bill__block">
            <div class="admin-bill__total">
              <div>Заказ</div>
              <div>
                <b>{{ calculateDiscount.totalDiscount }}</b>
                <span> ₽</span>
              </div>
            </div>
          </div>
        </div>
        <div class="admin-masters">
          <div class="admin-masters__total">
            <div @click="viewMastersShift()" class="masters">Мастеров в смене: <b>{{ countShiftsTrue }}</b>
              <span v-if="!errorMessage && selectedMasters.length == 0" style="float: right" class="text-red-600 font-bold text-sm">ВЫБЕРИТЕ МАСТЕРА</span>
              <span v-if="errorMessage" class="error-message">{{ errorMessage }}</span>
            </div>
          </div>

          <div class="empty_masters" v-if="mastersShiftsTrue.length === 0">
            <p class="text-red-600 text-center font-bold text-sm">ОТСУТСТВУЮТ МАСТЕРА В СМЕНЕ !</p>
              <div class=" text-center">Чтоб указать кто выполнянл работу, добавьте
                <span @click="viewMastersShift()" class="underline cursor-pointer">Мастеров в смену.</span></div>
          </div>

          <div class="admin-masters__list">
            <div v-for="master in mastersShiftsTrue" :key="master.id">
<!--              <span v-if="isSelectedMaster(master)">6666</span>-->

              <label :class="{ 'selected': isSelectedMaster(master) }" class="admin-masters__item" :for="master.id" @click="selectMaster(master)">
                <div class="admin-masters__avatar" v-if="master.photo_path">
                  <img :src="master.photo_path">
                </div>
                <div class="admin-masters__name">{{ master.first_name }}
                  <span v-if="!master.first_name && !master.last_name">Без имени</span>
                </div>
              </label>
            </div>
          </div>
        </div>

        <div class="admin-orders__buttons">
          <div class="admin__container">
            <button @click="onlySaveOrder" class="btn ">СОХРАНИТЬ<br> ЗАКАЗ</button>
<!--            <button @click="showQrCode" :disabled="!selectedMasters.length" class="btn btn_arrow">ОПЛАТА</button>-->
            <button @click="showQrCode" class="btn btn_arrow">ДАЛЕЕ</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  <Modal
    :is-active="isModalActive"
    @confirmed="confirmedTips"
    @close="closeModal"
    :form="true"
    formMessage="введите размер скидки"
    messageButton="УСТАНОВИТЬ"
    type="tel"
  />

  <Masters v-if="visibleMasterShift" @closeMasterShift="hideMasterShift" :masters="masters" :administrators="administrators" :employees="employees"/>

  <Spiner :isLoading="isLoading"></Spiner>

  <ClientSearch v-show="visibleClientSearch"
                @showQrCode="showQrCode"
                @saveOrder="onlySaveOrder"
                @goBack="goToFirstStep"
                @cancelOrder="cancelOrder"
                @showMasterChoice="showMasterChoice"
                :editOrder="editOrder">
  </ClientSearch>

</template>

<script>
import { Link } from '@inertiajs/vue3'
import Modal from '../../../../../Shared/Modal.vue'
import Masters from '../../MastersShift/Index.vue'
import Spiner from '@/Shared/Spiner.vue'
import ClientSearch from './ClientSearch.vue'
import axios from 'axios'

export default {
  components: {
    ClientSearch,
    Masters,
    Link,
    Modal,
    Spiner
  },
  props: {
    selectedProducts: Object,
    totalPrice: Number,
    masters: Object,
    administrators: Object,
    employees: Object,
    draftOrder: Object,
    selectedMasters: Object,
    selectingBill: Object,
    editOrder: Object,
    discount: Number,
  },
  data() {
    return {
      Discount: this.discount,
      DiscountPrice: 0,
      isModalActive: false,
      // totalSumm : 0,
      visibleMasterChoice : false,
      visibleMasterShift : false,
      visibleClientSearch : true,
      isLoading : false,
      errorMessage: '',
      dopOrderDataInMasterChoice: {},
      discountSum: 0,
      calculateDiscountData: {},
    }
  },
  mounted() {
    // this.totalSumm = this.totalPrice
  },
  watch: {
    'dopOrderDataInMasterChoice.newClientData.discount': {
      immediate: true, // Выполнить сразу при создании компонента
      handler(newDiscount) {
        if (newDiscount) {
          this.Discount = newDiscount;
        }
      }
    },
    Discount(newDiscount) {
      // При изменении Discount обновляем discountSum
      this.dopOrderDataInMasterChoice.calculateDiscountSum = this.calculateDiscount.totalAfterDiscount;
      this.dopOrderDataInMasterChoice.totalSummFromDiscount = this.calculateDiscount.totalDiscount;
    }
  },
  computed: {
    calculateDiscount() {
      let totalDiscount = 0; // Переменная для хранения общей суммы скидок
      let totalAmount = 0; // Переменная для хранения общей суммы товаров

      const productsWithDiscount = this.selectedProducts.map(item => {
        const itemTotal = item.price * item.quantity; // Общая стоимость товара
        const itemDiscount = Math.floor(itemTotal * ((100 - this.Discount) / 100) * 100) / 100; // Скидка на товар
        const finalAmount = Math.floor((itemTotal - itemDiscount) * 100) / 100; // Итоговая сумма после скидки

        totalDiscount += itemDiscount; // Добавляем скидку товара к общей сумме скидок
        totalAmount += itemTotal; // Добавляем стоимость товара к общей сумме

        return {
          ...item,
          discount: itemDiscount,
          finalAmount: finalAmount
        };
      });

      // Общая сумма товаров минус общая скидка
      const totalAfterDiscount = parseFloat((totalAmount - totalDiscount).toFixed(2));

      // Возвращаем объект с общей суммой скидок, общей суммой товаров и итоговой суммой
      return {
        totalDiscount: parseFloat(totalDiscount.toFixed(2)), // Округляем общую скидку до двух знаков
        totalAmount: parseFloat(totalAmount.toFixed(2)), // Округляем общую сумму товаров до двух знаков
        totalAfterDiscount: totalAfterDiscount, // Общая сумма после скидки
        products: productsWithDiscount // Список товаров с их скидками и итоговыми суммами
      };
    },
    calculateDiscountSum() {
        const totalAmount = this.totalPrice;
        const discountPercentage = this.Discount || 0;

        // Вычисляем сумму скидки
        const discountAmount = (discountPercentage / 100) * totalAmount;

        // Возвращаем значение скидки
        return Math.floor(discountAmount * 100) / 100; // Округляем до 2-х знаков после запятой
    },

    totalPrice() {
      return this.selectedProducts.reduce((acc, product) => {
        return acc + product.price * product.quantity; // Суммируем цену товара, умноженную на его количество
      }, 0); // Начальное значение аккамулятора - 0
    },

    totalSumm() {
      // Суммируем итоговые цены с учетом скидки для всех продуктов
      return this.selectedProducts.reduce((acc, product) => {
        // return acc + this.getDiscountedTotal(product);
        console.log(this.getDiscountedTotal(product))
        return acc + this.getDiscountedTotal(product);
      }, 0);
    },

    totalWithoutDiscount() {
      return this.selectedProducts.reduce((sum, product) => {
        return sum + (product.price * product.quantity);
      }, 0);
    },

    discountAmount() {
      // Вычисляем общую скидку
      const originalTotalPrice = this.selectedProducts.reduce((acc, product) => {
        return acc + (product.price * product.quantity);
      }, 0);
      return Math.floor(originalTotalPrice * (this.Discount / 100));
    },

    finalTotal() {
      return this.totalSumm;
    },




    // discountAmount() {
    //   return Math.ceil(this.totalSumm * (this.discount / 100));
    // },

    // finalTotal() {
    //   return Math.ceil(this.totalSumm - this.discountAmount);
    // },

    finalTotalAsDiscount() {
      return Math.ceil(this.calculateDiscount.totalDiscount - this.discountAmount);
    },
    countShiftsTrue() {
      return this.masters.filter(item => item.shift).length;
    },
    mastersShiftsTrue() {
      return this.masters.filter(item => item.shift);
    }
  },
  methods: {
    showVisibleClientSearch() {
      this.visibleClientSearch = true
      this.visibleMasterChoice = false
    },
    closeVisibleMasterChoice() {
      this.visibleClientSearch = false
      this.visibleMasterChoice = true
    },
    getDiscountedTotal(product) {
      const discountValue = (product.price * this.Discount) / 100;
      const discountedPrice = product.price - discountValue;

      // Округляем до двух знаков после запятой
      const roundedDiscountedPrice = Math.round(discountedPrice * 100) / 100;

      // Вычисляем общую цену с учетом количества товаров
      const total = roundedDiscountedPrice * product.quantity;

      // Округляем итоговую стоимость до двух знаков после запятой и обеспечиваем минимальную цену в 1 рубль
      const finalTotal = Math.max(Math.round(total * 100) / 100, 1); // Минимальная цена 1 рубль

      return finalTotal;
    },

    viewMastersShift() {
      this.visibleMasterShift = true
      this.visibleMasterChoice = false
    },
    hideMasterShift() {
      this.visibleMasterShift = false
      this.visibleMasterChoice = true
    },
    isSelectedMaster(master) {
      return this.selectedMasters && this.selectedMasters.some(selectedMaster => selectedMaster.id === master.id);
    },
    openModal() {
      this.isModalActive = true
    },
    confirmedTips(data) {
      this.Discount = data

      this.dopOrderDataInMasterChoice.newClientData.discount = data;

      this.$emit('setDiscount', data)

      this.isModalActive = false
    },
    calculateDiscount() {
      axios.post('/api/calculateDiscount', {
        products: this.selectedProducts,
        discount: this.Discount
      }).then((response) => {
        this.calculateDiscountData = response.data
      })
    },
    closeModal() {
      this.isModalActive = false
    },
    updateValue() {
      this.value = this.$refs.editableSpan.textContent
    },
    selectMaster(master) {
      if (!this.isSelectedMaster(master) && this.selectedMasters.length >= 3) {
        this.showErrorMessage('Не более 3-х мастеров');
        return;
      }

      this.isSelectedMaster(master)
      this.$emit('selectMaster', master);
    },

    showErrorMessage(message) {
      this.errorMessage = message;
      setTimeout(() => {
        this.errorMessage = '';
      }, 2000);
    },

    goToFirstStep() {
      this.$emit('goBack')
    },

    goBack(){
      this.showVisibleClientSearch()
      // this.$emit('goBack')
    },
    showQrCode() {
      this.$emit('showQrCode', this.dopOrderDataInMasterChoice)
    },

    showMasterChoice(formData) {
      this.closeVisibleMasterChoice()
      formData.organization_id = this.draftOrder.order.organization_id

      this.dopOrderDataInMasterChoice = {
        calculateDiscountSum: this.calculateDiscount.totalAfterDiscount,
        totalSummFromDiscount: this.calculateDiscount.totalDiscount,
        newClientData: formData
      };
    },

    onlySaveOrder() {
      this.$emit('onlySaveOrder', this.dopOrderDataInMasterChoice)
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
  },
};
</script>

<style scoped>
.admin-bill__price,
.admin-bill__quantity,
.admin-bill__list,
.admin-bill__total {
  text-decoration: none; /* Отменяем подчеркивание */
}

.admin-masters__item.selected {
  background-color: rgba(0, 0, 0, 0.1); /* Пример: полупрозрачный черный фон */
  /* Или другие стили для затемнения */
}

.masters {
  cursor: pointer;
}

.close-icon {
  cursor: pointer;
}

.line-icon {
  cursor: pointer;
  margin-top: 5px;
}

.admin-choose-masters__top h2 {
  font-size: 16px;
  font-weight: 300;
  margin-right: -120px;
  padding: 0;
  text-transform: uppercase;
}

.error-message {
  color: red; /* Цвет сообщения об ошибке */
  margin: 10px;
  text-decoration: none;
}

img {
  border-radius: 20%;
}
.admin-orders__buttons {
  background-color: #ffffff;
  padding: 10px;
  bottom: 0;
  max-height: 90px;
  max-width: 100%;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
.admin-orders__buttons .admin__container {
  display: flex;
  justify-content: space-between;
  gap: .8125rem;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
</style>
