<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">

          <cropper
            v-show="type === 'none'"
            :src="imageToEdit"
            ref="cropper"
            @change="change"
          />

          <cropper
            v-show="type === 'stencil'"
            :src="imageToEdit"
            ref="cropper"
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

          <cropper
            v-show="type === 'static'"
            :src="imageToEdit"
            :auto-zoom="true"
            ref="cropper"
            :stencil-size="{
              width: croppedWidth,
              height: croppedHeight
            }"
            image-restriction="static"
          />

          <div class="modal__bottom">
            <button class="btn btn_text" @click="saveCropped">ПРИМЕНИТЬ</button>
          </div>

        </div>
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
  }
}
</script>
