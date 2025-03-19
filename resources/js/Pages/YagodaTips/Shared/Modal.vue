<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
          <div v-if="form" class="form">
            <div class="form__item">
              <div class="form__input-w-suffix">
                <input :type="type" id="tip"
                       :value="value"
                       v-mask="'####'"
                       @keyup="onInputChange"
                       ref="inputField"
                       class="large-input"
                ><span>₽</span>
              </div>
              <label class="form__label form__label_center" for="tip">{{ formMessage }}</label>
            </div>
          </div>
          <div v-else>{{ message }}</div>
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
      type: Number
    },
    type: {
      default: 'text'
    },
    actionType: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      isEdited: false,
      input: this.value
    }
  },
  mounted() {
    if (this.isActive) {
      this.focusInput();
    }
  },
  methods: {
    onInputChange(event) {
      if (event.target.value) {
        this.input = event.target.value;
        if (parseInt(this.value) !== parseInt(event.target.value)) {
          this.isEdited = true;
        } else {
          this.isEdited = false;
        }
      }
    },
    confirm() {
      if (this.input && this.isEdited && this.actionType === '') {
        this.$emit('confirmed', this.input);
        this.input = '';
        this.isEdited = false;
      } else if (this.actionType === 'delete'){
        this.$emit('confirmed', this.input);
      }
      this.close();
    },
    close() {
      this.isEdited = false;
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
 .large-input {
   display: inline-flex;
   width: 65px;
   text-align: center;
   border: 0;
 }
 .form {
   input[type="text"] {
     &.large-input {
       width: 65px;
     }
   }
 }
</style>
