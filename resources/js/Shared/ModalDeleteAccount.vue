<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
          <div v-if="form" class="form">
            <div class="form__item">
              <div class="form__input-w-suffix">
                <input type="text" id="tip" :value="value" @input="updateInput" v-mask="'##'" ref="inputField"><span>%</span>
              </div>
              <label class="form__label form__label_center" for="tip">{{ formMessage }}</label>
            </div>
          </div>
          <div v-else>
            <div class="confirmation-message">
              <p>Вы уверены, что хотите удалить <br>аккаунт <b>безвозвратно</b>?</p>
              <p class="text-center">будет удалена вся Ваша контактная <br> информация, телефон, имя, аватар <br> и другое.</p>
              <p class="text-center text-red-500">Восстановить аккаунт будет <br> <b>невозможно</b></p>
              <div class="spacer"></div>
            </div>
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
</style>

