<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
            <div class="confirmation-message">
              <p>Удалить аккаунт невозможно.</p>
              <p>К вашему номеру привязана организация</p>
              <p>Замените в организации ваш номер на другой.</p>
              <p>И после этого вы сможете удалить свой аккаунт.</p>
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
      default: ''
    },
    messageButton: {
      type: String,
      default: 'OK'
    },
  },
  data() {
    return {

    }
  },
  mounted() {

  },
  methods: {
    confirm() {
      this.close();
    },
    close() {
      this.$emit('close');
    },
  },
};
</script>

