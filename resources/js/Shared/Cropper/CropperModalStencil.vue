<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">

          <cropper
            v-show="type === 'stencil'"
            :src="imageToEdit"
            ref="cropperStencil"
            :stencil-props="{
              handlers: {},
              movable: false,
              resizable: false,
            }"
            :stencil-size="{
              width: croppedWidth,
              height: croppedHeight
            }"
            image-restriction="stencil"
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
      type: String,
      default: 'none'
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
  methods: {
    close() {
      this.$emit('close');
    },
    change({ coordinates, canvas }) {
      this.$emit('change', { coordinates, canvas });
    },
    saveCropped() {
      let result;

      // Определяем, какой cropper использовать в зависимости от типа
      if (this.type === 'none') {
        result = this.$refs.cropperNone.getResult();
      } else if (this.type === 'stencil') {
        result = this.$refs.cropperStencil.getResult();
      } else if (this.type === 'static') {
        result = this.$refs.cropperStatic.getResult();
      }

      const { coordinates, canvas } = result;
      this.coordinates = coordinates;
      this.image = canvas.toDataURL();  // Получаем изображение в формате base64
      this.$emit('save', this.image);  // Отправляем откадрированное изображение
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

