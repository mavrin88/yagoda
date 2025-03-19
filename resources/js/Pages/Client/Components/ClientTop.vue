<template>
  <div class="client__top flex flex-col gap-4 items-start"> <!-- Добавлены классы flex, flex-col и gap-4 -->
    <div class="client__user" v-for="orderParticipant in orderParticipants" :key="orderParticipant.id">
      <div class="client__avatar">
        <img :src="orderParticipant.image">
      </div>
      <div class="client__text">
        <span class="font-normal" v-if="orderParticipant.first_name">{{ orderParticipant.first_name }}</span>
        <p class="text-[#9CA4B5] text-xs" v-if="orderParticipant.purpose_tips">{{ orderParticipant.purpose_tips }}</p>
      </div>
    </div>
  </div>
  <div class="client__top">
    <div class="client__top">
      <div class="client__user">
        <div class="text-sm font-semibold mt-2">
          Чаевые для мастера
        </div>
      </div>
      <div class="client__field ml-6">
        <input type="text"
               v-model="localAmount"
               v-mask="'#####'"
               @input="updateAmount"
               @focus="onFocus"
               @focusout="onFocusOut"
               ref="amountInput"
               inputmode="numeric">
        <span>₽</span>
      </div>
      <div class="client__edit" :class="{ 'client__edit_active': isEditing }" @click="toggleEdit"></div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isEditing: false,
      localAmount: this.amount,
      // Флаг закрытия клавиатуры
      isKeyboardClosing: false,
    }
  },
  props: {
    amount: {
      type: [Number, String],
      required: true
    },
    orderParticipants: {
      type: Array,
      required: true
    },
    activeTip: {
      type: [Number,Object],
      required: true
    },
    settings: {
      // todo: проверить, по факту приходит Object всегда, вроде
      type: [Array, Object],
      required: true
    },
    total: {
      // todo: по факту приходит String
      type: [Number, String],
    },
  },
  mounted() {
    // Слушаем событие закрытия клавиатуры
    this.onFocus = () => {
      this.isKeyboardClosing = false;
    };
    this.onBlur = () => {
      setTimeout(() => {
        if (!document.activeElement || document.activeElement.tagName !== 'INPUT') {
          this.isKeyboardClosing = true;
        }
      }, 100);
    };

    document.addEventListener('focusin', this.onFocus);
    document.addEventListener('focusout', this.onBlur);
  },
  unmounted() {
    document.removeEventListener('focusin', this.onFocus);
    document.removeEventListener('focusout', this.onBlur);
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
    // Ограничиваем сумму. Защита от обнала, чтобы выводилось не более 30% чаевых
    // restrictInput(event) {
    //   // Блокируем ввод, если сумма уже достигла лимита
    //   const maxTipAmount = Math.floor((this.total * this.settings.maximum_tip_amount) / 100);
    //   const newValue = (this.localAmount + event.key).replace(/\D/g, ''); // Удаляем всё, кроме цифр
    //   if (parseInt(newValue, 10) > maxTipAmount) {
    //     // Блокируем ввод
    //     event.preventDefault();
    //   }
    // },
    updateAmount(event) {
      this.$emit('customTipsAmount',  event.target.value);
      this.localAmount = event.target.value;
    },
    onFocus() {
      // if (this.amount == 0) {
      //   this.amount = '';
      // }
      // Сбрасываем флаг, если фокус снова активен
      this.isKeyboardClosing = false;

      this.toggleEdit()
    },
    onFocusOut() {
      // Если клавиатура закрывается, не обрабатываем blur
      if (this.isKeyboardClosing) {
        return;
      }

      if (this.localAmount === '') {
        this.localAmount = 0;
      }

      this.checkAmount()
    },
    checkAmount(event) {
      const checkAmount = this.total;
      const maxTipPercentage = this.settings.maximum_tip_amount;
      const maxTipAmount = Math.floor((checkAmount * maxTipPercentage) / 100);

      // Проверяем, если значение пустое, устанавливаем его в 0
      const amount = this.localAmount === '' ? 0 : parseFloat(this.localAmount);

      // Защита от обнала, чтобы выводилось не более 30% чаевых
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
