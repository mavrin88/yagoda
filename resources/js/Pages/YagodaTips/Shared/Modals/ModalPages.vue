<template>
  <div class="modal-pages" @click.self="close">
    <div class="modal-pages__overlay" @click.self="close"></div>
    <div class="modal-pages__body">
      <div class="modal-pages__close" @click="close"></div>
      <div class="modal-pages__content">
        <div class="modal-pages__text" v-if="modalData && modalData.title">
          <h1>{{modalData.title}}</h1>
          <div v-html="modalData.text"></div>
        </div>
        <div class="modal-pages__text" v-else>
          <p>К сожалению, что-то пошло не так. Попробуйте позже</p>
        </div>
      </div>
    </div>
  </div>

</template>

<style scoped lang="scss">
.modal-pages {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  //background: #fff;
  z-index: 99999;
  display: flex;
  align-items: center;
  justify-content: center;
  &__overlay {
    background: #000;
    opacity: .4;
    z-index: 1;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
  }
  &__body {
    background: #fff;
    border-radius: 16px;
    padding: 21px;
    position: relative;
    min-width: 300px;
    max-height: 95vh;
    max-width: 95vw;
    z-index: 9;
    overflow: hidden;
  }
  &__close {
    position: absolute;
    top: 17px;
    right: 17px;
    width: 26px;
    height: 26px;
    cursor: pointer;
    &::before,
    &::after {
      content: '';
      width: 26px;
      height: 1px;
      background: #000;
      display: block;
      position: absolute;
      top: 12px;
      left: 0;
    }
    &::before {
      transform: rotate(45deg);
    }
    &::after {
      transform: rotate(-45deg);
    }
    &:hover {
      &::before {
        transform: rotate(-45deg);
      }
      &::after {
        transform: rotate(45deg);
      }
    }
  }
  &__content {
    padding-top: 50px;
  }
  &__text {
    margin-bottom: 50px;
    overflow: auto;
    max-height: 100vh;
    padding-bottom: 200px;
  }

  &__bottom {
    margin-bottom: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
  }
}
</style>

<script>
export default {
  props: {
    modalData: {
      type: Object,
      required: true
    }
  },
  methods: {
    close() {
      this.$emit('close');
    }
  }
}
</script>
