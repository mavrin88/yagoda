<template>
  <div class="superadmin__note">
    Возможно добавить несколько счетов.<br>
    При реализации товаров или услуг деньги будут поступать на тот счет, который привязан к товару или услуге.
  </div>
  <div class="superadmin__block">
    <form class="form" @submit.prevent="submitForm">
      <div class="form__item">
        <input type="text" id="name" v-model="name" required>
        <label for="name">Наименование счета. Например "Основной"</label>
        <span class="form__error" v-if="errorNameMessage">{{ errorNameMessage }}</span>
      </div>
      <div class="form__item">
        <input type="text" id="bik" v-model="bik" inputmode="numeric" maxlength="9" required @keydown.enter="submitBik" @blur="submitBik" @input="clearError">
        <div class="form__error" v-if="daDataErrors">По введенному БИК - банк не найден</div>
        <label for="bik">БИК</label>
        <span class="form__error" v-if="errorBikMessage">{{ errorBikMessage }}</span>
      </div>
      <div class="form__item">
<!--        <input type="text" id="name" v-model="bank_name" required>-->
        <div class="bank_name">{{bank_name}}</div>
        <label for="name">Наименование банка</label>
        <span class="form__error" v-if="errorNameMessage">{{ errorNameMessage }}</span>
      </div>
      <div class="form__item">
        <input type="text" id="number" v-model="number" inputmode="numeric" maxlength="20" required>
        <label for="number">Номер расчетного счета</label>
        <span class="form__error" v-if="errorAccountMessage">{{ errorAccountMessage }}</span>
      </div>
      <div class="form__actions">
        <button class="btn btn_text">Добавить счет</button>
        <span class="form__error" v-if="errorMessage">{{ errorMessage }}</span>
      </div>
    </form>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  props: {
    user: Object,
  },
  data() {
    return {
      daData: [],
      name: '',
      number: '',
      bik: '',
      bank_name: '',
      errorMessage: '',
      errorNameMessage: '',
      errorBikMessage: '',
      errorAccountMessage: '',
      daDataErrors: false,
      isDemo: false
    };
  },
  mounted() {
    if (this.isDemo) {
      this.name = 'test'
      this.number = '40801643001234567890'
      this.bik = '041234567'
      this.bank_name = 'РогаБанк'
    }
  },

  methods: {
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
      // const regex = /^04\d{7}$/;
      const regex = /^\d+$/;
      return regex.test(bik);
    },
    submitBik() {
      axios.post('/api/daData', {
        type: 'bank',
        number: this.bik
      })
        .then((response) => {
          if (response.data && response.data[0] && response.data[0].data && response.data[0].data.name && response.data[0].data.name.payment) {
            this.daDataErrors = false
            this.bank_name = response.data[0].data.name.payment;
            this.daData = response.data[0].data
          } else {
            this.daDataErrors = true
            this.organization.name = '';
          }
        })
        .catch((error) => {

        });
    },
    clearError() {
      if (this.bik === '') {
        this.daDataErrors = false;
        this.errorBikMessage = '';
      }
    },
    submitForm() {
      this.errorMessage = '';

      //todo: Добавить валидацию наименования во vue
      if (!this.validateAccountName(this.name)) {
        this.errorNameMessage = 'Название счета должно быть уникальным';
      } else {
        this.errorNameMessage = '';
      }

      if (!this.validateAccountNumber(this.number)) {
        this.errorAccountMessage = 'Проверьте правильность написания. 20 цифр';
        //this.errorAccountMessage = 'Номер счета не может содержать буквы';
      } else {
        this.errorAccountMessage = '';
      }


      if (!this.validateBik(this.bik)) {
        // this.errorBikMessage = 'Проверьте правильность написания. 9 цифр';
        this.errorBikMessage = 'БИК не может содержать буквы';
      } else {
        this.errorBikMessage = '';
      }

      if ((!this.validateAccountNumber(this.number)) || (!this.validateBik(this.bik) || (this.daDataErrors))) {
        return;
      }


      let data = {
        name: this.name,
        number: this.number,
        bik: this.bik,
        bank_name: this.bank_name
      }

      this.$inertia.post('/super_admin/bills', data, {
        preserveScroll: true,
        onSuccess: () => {
          this.name = '';
          this.number = '';
          this.bik = '';
          this.bank_name = '';
          if(this.user.is_own_organization){
            this.$inertia.visit('/super_admin/after_registration');
          }
        },
        onError: (errors) => {
          if (errors) {
            this.errorNameMessage = errors;
          } else {
            this.errorMessage = 'Произошла ошибка при добавлении счета.';
          }
        }
      });
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
.bank_name {
  font-size: 24px;
  border-bottom: 1px solid #d5d5d5;
  letter-spacing: 1px;
  min-height: 30px;
  display: flex;
  align-items: center;
}
.bank_name:hover {
  cursor: not-allowed;
}
</style>
