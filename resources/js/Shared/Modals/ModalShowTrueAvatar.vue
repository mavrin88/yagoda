<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
            <div class="confirmation-message">
              <p v-html="message"></p>
              <div class="spacer"></div>
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

<style scoped lang="scss">
.modal {
  &__body {
    max-width: 343px;
  }
}
.text-center {
  text-align: center;
  margin-top: 20px;
}

.text-red-500 {
  color: red;
}
.confirmation-message a {
  text-decoration: underline;
}
</style>

