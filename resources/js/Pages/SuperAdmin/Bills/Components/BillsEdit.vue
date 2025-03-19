<template>
  <div id="app">
    <div class="superadmin">
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" href="/super_admin/bills">назад</Link>
          <h2>РЕДАКТИРОВАТЬ СЧЕТ</h2>
        </div>
        <div class="superadmin__block">
          <div class="form">
            <div class="form__item">
              <input type="text" :id="'name' + bill.id" v-model="bill.name">
              <label :for="'name' + bill.id">Наименование счета</label>
              <span class="form__error" v-if="bill.errorNameMessage">{{ bill.errorNameMessage }}</span>
            </div>
            <div class="form__item">
              <input type="text" :id="'num' + bill.id" v-model="bill.number">
              <label :for="'num' + bill.id">Номер счета</label>
              <span class="form__error" v-if="bill.errorAccountMessage">{{ bill.errorAccountMessage }}</span>
            </div>
            <div class="form__item">
              <input type="text" :id="'bik' + bill.id" v-model="bill.bik">
              <label :for="'bik' + bill.id">Бик</label>
              <span class="form__error" v-if="bill.errorBikMessage">{{ bill.errorBikMessage }}</span>
            </div>
            <div class="form__item">
              <input type="text" :id="'bik' + bill.bank_name" v-model="bill.bank_name">
              <label :for="'bik' + bill.bank_name">Название банка</label>
              <span class="form__error" v-if="bill.errorBikMessage">{{ bill.errorBikMessage }}</span>
            </div>
            <div class="form__actions form__actions_columns">
<!--              <button class="icon icon_delete" @click="deleteBill(bill.id)"></button>-->
              <button class="btn btn_text-right" @click="updateBill(bill)">Сохранить</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

import { Link } from '@inertiajs/vue3'

export default {
  data() {
   return {
     errorGlobal: '',
     superadminError: false,
     isLoading: false,
     number: '',
   }
  },
  components: {
    Link
  },
  props: {
    bill: {
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
        // if (!/^\d+$/.test(number)) {
        //   return false;
        // }
        // // Проверяем значения ХХХ, VV, ZZZ
        // const xxx = parseInt(number.slice(0, 3));
        // const vv = parseInt(number.slice(3, 5));
        // const zzz = parseInt(number.slice(5, 8));
        //
        // if (!(xxx === 406 || xxx === 407 || xxx === 408)) {
        //   return false;
        // }
        // if (!(vv === 1 || vv === 2 || vv === 3)) {
        //   return false;
        // }
        // // Проверка на валюту (пока только рубли)
        // if (!(zzz === 643 || zzz === 810)) {
        //   return false;
        // }

        return true;
      }
      return false;
    },
    validateBik(bik) {
      bik = bik.toString();
      if (bik.toString().length !== 9) {
        return false;
      }
      // const regex = /^04\d{7}$/;
      // return regex.test(bik);
      return true;
    },
    updateBill(bill) {
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
        'bank_name': bill.bank_name,
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

</style>
