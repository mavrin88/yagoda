<template>
  <div class="admin" v-if="visibleMasterChoice">
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
              <p>{{ totalSumm }} <span>₽</span></p>
            </div>
          </div>
          <div @click="openModal" class="admin-bill__block" style="cursor: pointer">
            <div class="admin-bill__total">
              <div>Скидка (%): <b><u>{{ discount }}</u></b></div>
              <div><b>{{ $formatNumber(discountAmount) }}</b><span> ₽</span></div>
            </div>
          </div>
          <div class="admin-bill__block">
            <div class="admin-bill__total">
              <div>Заказ</div>
              <div>
                <b>{{ finalTotalAsDiscount }}</b>
                <span> ₽</span>
              </div>
            </div>
          </div>
        </div>
        <div class="admin-masters">
          <div class="admin-masters__total">
            <div @click="viewMastersShift()" class="masters">Мастеров: <b>{{ countShiftsTrue }}</b></div>
          </div>
          <div class="admin-masters__list">
            <div v-for="master in mastersShiftsTrue" :key="master.id">
<!--              <span v-if="isSelectedMaster(master)">6666</span>-->

              <label :class="{ 'selected': isSelectedMaster(master) }" class="admin-masters__item" :for="master.id" @click="selectMaster(master)">
                <div class="admin-masters__avatar" v-if="master.avatar">
                  <img :src="master.avatar">
                </div>
                <div class="admin-masters__name">{{ master.first_name }} {{ master.last_name }}
                  <span v-if="!master.first_name && !master.last_name">Без имени</span>
                </div>
              </label>
            </div>
          </div>
        </div>
        <div class="admin-choose-masters__bottom fixed-bottom">
          <div class="admin-choose-masters__info">
            <div class="admin-choose-masters__items">Позиций: <span>{{ selectedProducts.length }}</span></div>
            <div class="admin-choose-masters__total">На сумму: <span>{{ $formatNumber(finalTotal) }}</span></div>
          </div>
          <div class="admin-choose-masters__next">
            <!--button.btn.btn_arrow <span>без мастера</span>-->
            <button @click="showQrCode" :disabled="!selectedMasters.length" class="btn btn_arrow">Мастеров: {{  selectedMasters.length }}</button>
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

</template>

<script>
import { Link } from '@inertiajs/vue3'
import Modal from '../../../../../Shared/Modal.vue'
import Masters from '../../MastersShift/Index.vue'
import Spiner from '@/Shared/Spiner.vue'

export default {
  components: {
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
    discount: Number,
  },
  data() {
    return {
      Discount: 0,
      DiscountPrice: 0,
      isModalActive: false,
      totalSumm : 0,
      visibleMasterChoice : true,
      visibleMasterShift : false,
      isLoading : false
    }
  },
  mounted() {
    this.totalSumm = this.totalPrice
  },
  computed: {
    discountAmount() {
      return Math.ceil(this.totalSumm * (this.discount / 100));
    },

    finalTotal() {
      return Math.ceil(this.totalSumm - this.discountAmount);
    },

    finalTotalAsDiscount() {
      return Math.ceil(this.totalSumm - this.discountAmount);
    },
    countShiftsTrue() {
      return this.masters.filter(item => item.shift).length;
    },
    mastersShiftsTrue() {
      return this.masters.filter(item => item.shift);
    }
  },
  methods: {
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

      this.$emit('setDiscount', data)

      this.isModalActive = false
    },
    closeModal() {
      this.isModalActive = false
    },
    updateValue() {
      this.value = this.$refs.editableSpan.textContent
    },
    selectMaster(master) {
      this.isSelectedMaster(master)
      this.$emit('selectMaster', master);

      // const index = this.selectedMasters.findIndex(m => m.id === master.id);
      // if (index === -1) {
      //
      //   this.selectedMasters.push(master);
      // } else {
      //   this.selectedMasters.splice(index, 1);
      // }
    },

    goBack(){
      this.$emit('goBack')
    },
    showQrCode() {
      this.$emit('showQrCode')
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
/* Дополнительные стили при необходимости */
</style>
