<template>

  <div class="ytips-pay__title">Чаевые для мастера</div>

  <div class="ytips-pay__middle">
    <div class="ytips-pay__input">
      <input type="text"
             v-model="localAmount"
             v-mask="'#####'"
             @input="updateAmount"
             @focus="onFocus"
             @focusout="onFocusOut"
             ref="amountInput"
             inputmode="numeric"
      >
      <span>₽</span>
    </div>
    <div class="ytips-pay__custom" @click="toggleEdit">своя сумма</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isEditing: false,
    }
  },
  computed: {
    localAmount: {
      get() {
        if (this.amount) {
          return this.amount;
        }

      },
      set(value) {
        this.$emit('update:amount', value); // Обновляем проп
      }
    }
  },
  props: {
    activeTip: {
      type: [Number,Object],
      required: true
    },
    settings: {
      type: Array,
      required: true
    },
    total: {
      type: Number,
    },
    amount: {
      type: [Number, String],
      required: true
    },
  },
  mounted() {
    this.localAmount = this.amount;
    this.$nextTick(() => {
      this.$refs.amountInput.value = this.localAmount;
    });
  },
  created() {

  },
  methods: {
    toggleEdit() {
      this.localAmount = 0;
      this.isEditing = !this.isEditing;
      if (this.isEditing) {
        this.$emit('tipSelected', -1);
        this.$refs.amountInput.value = '';
        this.$refs.amountInput.focus();

        setTimeout(() => {
          this.localAmount = '';
        }, 10);
        this.tipSelected();

      } else {
        this.$refs.amountInput.value = this.localAmount;
      }
    },
    disableEdited() {
      this.isEditing = false;
    },
    tipSelected() {
      this.$emit('customTipsAmount', this.localAmount);
      this.$emit('tipSelected', -1);
    },
    updateAmount(event) {
      this.$emit('customTipsAmount',  event.target.value);
      this.localAmount = event.target.value;
    },
    onFocus() {
      this.toggleEdit()
    },
    onFocusOut() {
      if (this.localAmount === '') {
        this.localAmount = 0;
      }

      this.checkAmount()
    },
    checkAmount(event) {
      const maxTipAmount = this.settings.maximum_tip_amount;

      // Проверяем, если значение пустое, устанавливаем его в 0
      const amount = this.localAmount === '' ? 0 : parseFloat(this.localAmount);

      // Защита от обнала, чтобы выводилось не более 4000 чаевых
      if (amount >= maxTipAmount) {
        this.localAmount = maxTipAmount;
        this.$emit('customTipsAmount', this.localAmount);
        this.$emit('update:amount', this.localAmount);
      }
    }
  },
  watch: {
    // Обновляем поле из родительского компонента
    amount(newVal) {
      this.localAmount = newVal;
    },
    activeTip(activeTip) {
      if (activeTip == '-1') {
        if (this.isEditing) {
          this.localAmount = '';
        } else {
          this.localAmount = 0;
        }
      } else {
        this.localAmount = activeTip.amount
      }
    }
  },
}
</script>

<style scoped>
img {
  border-radius: 6px;
}
</style>
