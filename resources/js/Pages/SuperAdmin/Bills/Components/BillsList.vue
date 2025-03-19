<template>
  <div class="superadmin__block" v-for="bill in bills" :key="bill.id">
    <div class="form">
      <div class="form__item">
        <span class="bill_fields">{{ bill.name }}</span>
        <label :for="'name' + bill.id">Наименование счета</label>
<!--        <span class="form__error" v-if="bill.errorNameMessage">{{ bill.errorNameMessage }}</span>-->
      </div>
      <div class="form__item">
        <span class="bill_fields">{{ bill.number }}</span>
        <label :for="'num' + bill.id">Номер счета</label>
<!--        <span class="form__error" v-if="bill.errorAccountMessage">{{ bill.errorAccountMessage }}</span>-->
      </div>
      <div class="form__item">
        <span class="bill_fields">{{ bill.bik }}</span>
        <label :for="'bik' + bill.id">Бик</label>
<!--        <span class="form__error" v-if="bill.errorBikMessage">{{ bill.errorBikMessage }}</span>-->
      </div>
      <div class="form__item">
        <span class="bill_fields">{{ bill.bank_name }}</span>
        <label :for="'bik' + bill.id">Название банка</label>
<!--        <span class="form__error" v-if="bill.errorBikMessage">{{ bill.errorBikMessage }}</span>-->
      </div>
      <div class="form__actions form__actions_columns">
        <button class="icon icon_delete" @click="showConfirmDeleteModal(bill.id)"></button>
        <div class="button-container">
          <button class="btn btn_text-right" @click="updateBill(bill)">Изменить</button>
        </div>
      </div>
      <div class="form__actions" v-if="bill.errorsGlobal">
        <ul>
          <li class="form__error" v-for="item in bill.errorsGlobal" :key="bill.errorsGlobal">
            {{ item }}
          </li>
        </ul>
      </div>
    </div>
    <div class="superadmin__error" v-if="superadminError">
      Пока счет привязан к одному или нескольким разделам каталога, его удалить нельзя.<br>
      Отвяжите это счет от каталога и повторите попытку.
    </div>
    <div class="superadmin__error" v-if="errorCount">
      Единственный счет удалить нельзя. У организации должен быть хотя бы один номер счета.
    </div>
  </div>

  <Spiner :isLoading="isLoading"></Spiner>

  <ModalDeleteConfirm
    :is-active="isModalDeleteConfirm"
    @confirmed="deleteBill"
    @close="closeConfirmDeleteModal"
    :message="isModalDeleteConfirmMessage"
  />
</template>

<script>
import axios from 'axios'

import Spiner from  '@/Shared/Spiner.vue'
import ModalDeleteConfirm from '../../../../Shared/Modals/ModalDeleteBillConfirm.vue'

export default {
  data() {
   return {
     errorGlobal: '',
     superadminError: false,
     errorCount: false,
     isLoading: false,
     selectBillForDelete: '',
     isModalDeleteConfirm: false,
     isModalDeleteConfirmMessage: '',
   }
  },
  components: {
    ModalDeleteConfirm,
    Spiner
  },
  props: {
    errors: Object,
    errorsArr: Object,
    bills: {
      type: Array,
      required: true,
    },
  },
  methods: {
    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },
    //todo: Добавить валидацию наименования во vue
    validateAccountName(accountName) {
      return true;
    },
    validateAccountNumber(number) {
      if (number) {
        number = number.toString();
        if (number.toString().length !== 20) {
          return false;
        }

        // все символы - цифры
        if (!/^\d+$/.test(number)) {
          return false;
        }
        // Проверяем значения ХХХ, VV, ZZZ
        const xxx = parseInt(number.slice(0, 3));
        const vv = parseInt(number.slice(3, 5));
        const zzz = parseInt(number.slice(5, 8));

        if (!(xxx === 406 || xxx === 407 || xxx === 408)) {
          return false;
        }
        if (!(vv === 1 || vv === 2 || vv === 3)) {
          return false;
        }
        // Проверка на валюту (пока только рубли)
        if (!(zzz === 643 || zzz === 810)) {
          return false;
        }

        return true;
      }
      return false;
    },
    validateBik(bik) {
      const regex = /^04\d{7}$/;
      return regex.test(bik);
    },
    updateBill(bill) {
      this.$inertia.visit(`/super_admin/bills/${bill.id}`);


      //todo: Добавить валидацию наименования во vue
      if (!this.validateAccountName(bill.name)) {
        bill.errorNameMessage = 'Название счета должно быть уникальным';
      } else {
        bill.errorNameMessage = '';
      }

      if (!this.validateAccountNumber(bill.number)) {
        bill.errorAccountMessage = 'Проверьте правильность написания. 20 цифр';
      } else {
        bill.errorAccountMessage = '';
      }


      if (!this.validateBik(bill.bik)) {
        bill.errorBikMessage = 'Проверьте правильность написания. 9 цифр';
      } else {
        bill.errorBikMessage = '';
      }

      if ((!this.validateAccountNumber(bill.number)) || (!this.validateBik(bill.bik))) {
        return;
      }

      let data = {
        'name': bill.name,
        'number': bill.number.toString(),
        'bik': bill.bik,
      }

      if (confirm('Вы уверены, что хотите обновить этот счет?')) {
        this.startLoading();
        // Отправьте запрос на обновление счета на сервер
        axios.put(`/super_admin/bills/${bill.id}`, data)
          .then(response => {
            setTimeout(() => {
              this.stopLoading();
              if (response.data.errors) {
                if (response.data.errors.name) {
                  bill.errorNameMessage = response.data.errors.name;
                } else {
                  bill.errorsGlobal = response.data.errors;
                }
              }
            }, 1000);

          })
          .catch(error => {
            bill.errorsGlobal = error;
          })
          .finally(() => {

          });
      }
    },

    deleteBill() {
        this.startLoading();
        axios.post('/super_admin/bills/delete', {
          'billId': this.selectBillForDelete
        })
          .then(response => {
            setTimeout(() => {
              this.stopLoading();

              // Инициализация переменной ошибки
              let hasError = false;

              // Проверка на наличие ошибок
              if (response.data.status === 'errorCount') {
                this.errorCount = true;
                hasError = true;
              }

              if (response.data.status === 'error') {
                this.superadminError = true;
                hasError = true;
              }

              // Если нет ошибок, эмитируем событие
              if (!hasError) {
                this.$emit('bill-deleted', this.selectBillForDelete);
              }
            }, 500);
          })
          .catch(error => {
            // Обработка ошибок, если нужно
            console.error(error);
          })
          .finally(() => {
            // this.stopLoading();
          });
    },
    showConfirmDeleteModal(id) {
      this.selectBillForDelete = id;
      this.isModalDeleteConfirm = true;
    },
    closeConfirmDeleteModal() {
      this.isModalDeleteConfirm = false;
    },
  },
};
</script>
<style>
.superadmin__block input[type="number"]::-webkit-outer-spin-button,
.superadmin__block input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

.superadmin__block input[type="number"] {
  -moz-appearance: textfield;
}

.bill_fields {
  font-size: 24px;
}

.button-container {
  margin-left: auto; /* Смещение кнопки 'Изменить' вправо */
}
</style>
