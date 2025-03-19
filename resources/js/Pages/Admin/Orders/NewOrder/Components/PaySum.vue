<template>
  <div class="admin admin_white">
    <div class="admin__container">
      <div class="admin-new">
        <div class="admin-choose-masters__top">
          <div></div>
          <img class="close-icon" @click="closePaySum" src="/img/icon_close.svg" alt="">
        </div>
        <div class="admin-new-sum">
          <div class="admin-new-sum__field">
            <input
              type="text"
              v-model="paySum"
              @input="formatPaySum"
              @focus="setCursorToEnd"
              ref="payInput"
              autofocus
              inputmode="numeric"
            />
            <label for="sum">введите сумму для формирования оплаты</label><span>₽</span>
            <label style="color: red" v-if="isPaySumInvalid && paySum">Неверная сумма</label><span>₽</span>

          </div>
          <div class="admin-new-sum__select">
            <div v-if="bills.length > 1" class="form__item-centered">
              <v-select :options="[ ...Object.values(bills)]" v-model="selectingACashReceiptAccount" :clearable="false" class="wide-select"></v-select>
              <label>Счет получения денег</label>
            </div>
          </div>
          <button
            :disabled="isPaySumInvalid"
            @click="savePay"
            class="btn btn_spec btn_arrow"
          >
            далее
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select'
import axios from 'axios'

export default {
  components: { vSelect },

  props: {
    bills: Array,
    selectBill: Object,
    editOrder: Object,
  },
  data() {
    return {
      paySum: '',
      selectingACashReceiptAccount: this.selectBill,
    };
  },
  computed: {
    isPaySumInvalid() {
      // Проверяем, является ли значение недействительным для кнопки
      return this.paySum === '' ||
        this.paySum === '0' ||
        (this.paySum.startsWith('0') && this.paySum.length > 1);
    }
  },
  mounted() {
    this.paySum = String(this.editOrder.total_product_price);
    this.$refs.payInput.focus();
    this.$nextTick(() => {
      this.formatPaySum();
    });

    // Фиксация кнопки внизу
    const paySumButton = document.querySelector('.btn');
    if (paySumButton && window.visualViewport) {
      const vv = window.visualViewport;
      function fixPosition() {
        paySumButton.style.top = `${vv.height - 50}px`;
      }
      vv.addEventListener('resize', fixPosition);
      fixPosition();
    }
  },
  watch: {
    selectingACashReceiptAccount(newValue) {
      this.sendSelectedBell(newValue);
    }
  },
  methods: {
    formatPaySum() {
      let formatted = this.paySum.replace(/[^0-9,]/g, '');
      if ((formatted.match(/,/g) || []).length > 1) {
        formatted = formatted.replace(/,+$/, '');
      }
      const parts = formatted.split(',');
      if (parts[1]) {
        parts[1] = parts[1].slice(0, 2);
        formatted = parts.join(',');
      }

      this.paySum = formatted;
    },
    savePay() {
      let formattedPaySum = this.paySum.replace(',', '.');
      this.$emit('savePay', formattedPaySum, this.selectingACashReceiptAccount, this.editOrder);
    },
    sendSelectedBell(value) {
      axios.post('api/selectedBill', {
        selectedBill: value
      })
        .then(response => {
        })
        .catch(error => {
          console.error(error);
        });
    },
    closePaySum() {
      this.$emit('closePaySum')
    },
    setCursorToEnd() {
      // Установим курсор в конец поля ввода
      const input = this.$refs.payInput;
      if (this.paySum === '0') {
        input.setSelectionRange(input.value.length, input.value.length);
      } else {
        input.setSelectionRange(1, 1);
      }
    }
  },
};
</script>

<style lang="scss">
.btn_spec {
  transform: translateY(-100%);
  margin-top: -60px;
  height: 72px;
  max-height: 72px;
  bottom: auto;
}

.close-icon {
  cursor: pointer;
}
.wide-select {
  width: 250px;
  max-width: 400px;
}
.form__item-centered {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-top: 20px;
  border: none;
}
.vs__dropdown-toggle {
  border: none !important;
}
.admin-new-sum__select {
  margin-top: 12px;
  .v-select {
    .vs__selected {
      font-size: 16px !important;
    }
    .vs__search,
    .vs__search:focus
    {
      padding-left: 0;
    }
  }
  label {
    font-size: 14px;
    display: block;
    margin-top: .5rem;
    text-align: center;
    color: #9ca4b5;
    border-top: 1px solid #9ca4b5;
    width: 100%;
    padding-top: 4px;
  }
}
</style>

