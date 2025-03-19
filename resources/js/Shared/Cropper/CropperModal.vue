<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">

                    <cropper
                      v-if="type === 'none'"
                      :src="imageToEdit"
                      ref="cropper"
                      @change="change"
                    />

        </div>
      </div>
      <!-- Кнопка Применить и закрытие модалки -->
      <div class="modal__bottom">
        <button class="btn btn_text" @click="saveCropped">ПРИМЕНИТЬ</button>
      </div>
    </div>
  </div>
</template>

<script>
import { Cropper } from 'vue-advanced-cropper';

export default {
  components: {
    Cropper,
  },
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    imageToEdit: {
      type: String,
      required: true
    },
    messageButton: {
      type: String,
      default: 'OK'
    },
    croppedWidth: {
      type: Number,
      default: 1600
    },
    croppedHeight: {
      type: Number,
      default: 1600
    },
    type: {
      type: Number,
      default: 'none'
    }
  },
  data() {
    return {
      show: true,
      coordinates: {
        width: 0,
        height: 0,
        left: 0,
        top: 0,
      },
      image: null,
    }
  },
  watch: {
    isActive(newVal) {
      if (newVal) {
        this.setViewportScale(1);
      } else {
        this.resetViewport(null, true);
      }
    }
  },
  methods: {
    close() {
      this.$emit('close');
    },
    change({ coordinates, canvas }) {
      this.$emit('change', { coordinates, canvas });
    },
    saveCropped() {
      const { coordinates, canvas, } = this.$refs.cropper.getResult();
      this.coordinates = coordinates;
      this.image = canvas.toDataURL();
      this.$emit('save', this.image);
      this.close();
    },
  // Возвращаю зум телефона перед открытием попапа, иначе кроп ломается
    setViewportScale(scale, isReset = false) {
      let viewport = document.querySelector('meta[name=viewport]')
      if (!viewport) {
        viewport = document.createElement('meta')
        viewport.name = 'viewport'
        document.head.appendChild(viewport)
      }
      if (isReset) {
        viewport.setAttribute('content', `initial-scale=1.0, width=device-width`)
      } else {
        viewport.setAttribute('content', `width=device-width, initial-scale=${scale}, maximum-scale=${scale}, user-scalable=no`)
      }
    },
    resetViewport() {
      this.setViewportScale(1, true)
    },
  }
}
</script>

<style scoped>
.modal__body {
  max-width: 550px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.modal__content {
  width: 100%;
  height: 100%;
  overflow: hidden;
  padding-top: 1.5rem;
}

.modal__bottom {
  margin-top: 15px;
  text-align: center;
}

.modal__close {
  position: absolute;
  top: 10px;
  right: 10px;
  cursor: pointer;
}
</style>


