<template>
  <div class="superadmin superadmin_white">
    <Head title="Привязка QR-кода" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <div @click="back" class="back">назад</div>
        <h2>ПРИВЯЗКА QR</h2>
      </div>
      <div class="superadmin__note superadmin__note_dark">Для привязки статичного QR-кода оплаты,  отсканируйте его.</div>
      <div class="form">
        <div class="form__item form__item_qr">
          <qrcode-stream @camera-on="onCameraOn" :track="paintBoundingBox" @detect="onDetect">
            <div
              v-if="validationPending"
              class="validation-pending"
            >
              Получение QR-кода...
            </div>

            <div
              v-if="validationUnique"
              class="validation-pending-unique"
            >
              QR-код уже привязан к другой организации...
            </div>

          </qrcode-stream>
        </div>
      </div>

      <div
        class="validation-pending"
        v-if="loading"
      >
        Идет загрузка камеры...
      </div>

    </div>
  </div>
</template>

<script>
import { QrcodeStream, QrcodeDropZone, QrcodeCapture } from 'vue-qrcode-reader'
import axios from 'axios';
import { Head } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    QrcodeStream,
    QrcodeDropZone,
    QrcodeCapture
  },
  data() {
    return {
      result: null,
      error: null,
      loading: true,
      paused: false,
      isValid: undefined,
      validationUnique: false,
      response: null
    }
  },
  computed: {
    validationPending() {
      return this.isValid === undefined && this.paused
    },
  },
  methods: {
    paintBoundingBox(detectedCodes, ctx) {
      for (const detectedCode of detectedCodes) {
        const {
          boundingBox: { x, y, width, height }
        } = detectedCode

        ctx.lineWidth = 2
        ctx.strokeStyle = '#b53434'
        ctx.strokeRect(x, y, width, height)
      }
    },

    onCameraOn() {
      this.loading = false
    },

    onDetect(detectedCodes) {

      setTimeout(() => {

      }, 500);

      this.paused = true;
      this.validationUnique = false

      // Отправляем запрос на бэк для проверки уникальности
      axios.post('/super_admin/qr_codes/check_uniqueness', { link: detectedCodes[0].rawValue })
        .then(response => {

          this.response = response.data
          if (response.data.unique) {

            setTimeout(() => {
              this.paused = false
              this.validationUnique = false
              this.$emit('detected-qr-code', detectedCodes.map(code => code.rawValue));
            }, 1000);
          } else {

            this.validationUnique = true
            this.paused = false
          }
        })
        .catch(error => {
          console.error('Error checking QR code uniqueness:', error);
        })
        .finally(() => {

        });
    },

    back() {
      this.$emit('back');
    }
  }
}
</script>

<style scoped>
.validation-pending {
  position: absolute;
  width: 100%;
  height: 100%;

  background-color: rgba(255, 255, 255, 0.8);
  padding: 10px;
  text-align: center;
  font-weight: bold;
  font-size: 1.2rem;
  color: black;

  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}

.validation-pending-unique{
  position: absolute;
  width: 100%;
  height: 100%;

  padding: 10px;
  text-align: center;
  font-weight: bold;
  font-size: 1.2rem;
  color: #ff0000;

  display: flex;
  flex-flow: column nowrap;
  justify-content: center;
}
</style>
