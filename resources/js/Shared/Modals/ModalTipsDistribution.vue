<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
            <div class="confirmation-message">
              <p>Важно</p>
              <div class="spacer"></div>
              <p><b>Рекомендуем распределить часть от чаевых Администратру - тому с кем расплачивается Клиент.</b></p>
              <div class="spacer"></div>
              <p>У сотрудника, получающего часть от чаевых, больше мотивации предлагать Клиенту для оплаты сервис Yagoda.

                Как следствие: вы больше экономите на эквайринге, Мастера получают больше чаевых, все стараются выполнить свою работу чуть лучше.Все становятся немного счастливее.</p>
            </div>
        </div>
        <div class="modal__bottom">
          <button class="btn btn_text" @click="confirm">{{ messageButton }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    message: {
      type: String,
      default: 'Вы уверены?'
    },
    messageButton: {
      type: String,
      default: 'OK'
    },
    form: {
      type: Boolean,
      default: false
    },
    formMessage: {
      type: String
    },
    value: {
      type: Number,
      default: 0
    }
  },
  data() {
    return {
      input: this.value
    }
  },
  mounted() {
    if (this.isActive) {
      this.focusInput();
    }
  },
  methods: {
    updateInput(event) {
      this.input = event.target.value;
    },
    confirm() {
      this.$emit('confirmed', this.input);
      this.input = '';
      this.close();
    },
    close() {
      this.$emit('close');
    },
    focusInput() {
      this.$nextTick(() => {
        this.$refs.inputField.focus();
      });
    },
  },
  watch: {
    isActive(newValue) {
      if (newValue && this.$refs.inputField) {
        this.focusInput();
      }
    }
  },
};
</script>

<style scoped>
.text-center {
  text-align: center;
  margin-top: 20px;
}

.text-red-500 {
  color: red;
}

.spacer {
  margin-top: 30px;
}
</style>

