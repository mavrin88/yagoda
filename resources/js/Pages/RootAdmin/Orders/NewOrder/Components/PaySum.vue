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
              v-mask="'##########'"
              @focus="setCursorToEnd"
              ref="payInput"
              autofocus
              inputmode="numeric"
            />
            <label for="sum">введите сумму для формирования оплаты</label><span>₽</span>
            <label style="color: red" v-if="isPaySumInvalid && paySum">Неверная сумма</label><span>₽</span>
          </div>
          <button
            :disabled="isPaySumInvalid"
            @click="savePay"
            class="btn btn_arrow"
          >
            далее
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      paySum: '',
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
    this.$refs.payInput.focus();
    this.$nextTick(() => this.$refs.payInput.focus())

    // Фиксация кнопки внизу
    const footer = document.querySelector('.btn');
    if (window.visualViewport) {
      const vv = window.visualViewport;
      // let height = vv.height;
      // console.log('height1', height)
      // height = height - 50;
      // console.log('height2', height)
      function fixPosition() {
        footer.style.top = `${vv.height - 50}px`;
      }
      vv.addEventListener('resize', fixPosition);
      fixPosition();
    }


  },
  methods: {
    savePay() {
      this.$emit('savePay', this.paySum)
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

<style scoped>
.btn {
  transform: translateY(-100%);
  margin-top: -60px;
  height: 72px;
  max-height: 72px;
  bottom: auto;
}

.close-icon {
  cursor: pointer;
}
</style>

