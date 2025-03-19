<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/tips/qr"></Link>
          <div class="ytips-top__title">Привязать стойку ЯГОДА с QR</div>
        </div>
      </div>

      <div class="ytips__container" v-if="!afterScan">
        <div class="form__item form__item_qr">
          <qrcode-stream @camera-on="onCameraOn" :track="paintBoundingBox" @detect="onDetect">
            <div v-if="validationPending" class="validation-pending">
              Получение QR-кода...
            </div>

            <div v-if="validationUnique" class="validation-pending-unique">
              QR-код уже привязан к другой организации...
            </div>
          </qrcode-stream>
        </div>

        <div class="validation-pending" v-if="loading">
          Идет загрузка камеры...
        </div>
      </div>



      <div v-if="afterScan" class="ytips__container">
        <div class="ytips-text ytips-text_12">
          <p>Укажите название QR кода для простоты идентификации. Например: Стойка ресепшен, Маникюр, у администратора</p>
        </div>
        <div class="form">
          <div class="form__item">
            <input class="bg-transparent" type="text" id="name" v-model="title">
            <label for="name" style="color: rgba(0, 0, 0, 1)">Наименование QR-кода</label>
          </div><br>
          <div @click="saveQrCode" class="btn btn_low">Сохранить</div>
        </div>
      </div>

    </div>
  </main>
</template>

<script>
import TipsHeader from '../../../Components/TipsHeader.vue'
import { QrcodeStream } from 'vue-qrcode-reader'
import axios from 'axios';
import { Link } from '@inertiajs/vue3'

export default {
  name: "Index",
  components: {
    Link,
    QrcodeStream,
    TipsHeader
  },
  data() {
    return {
      title: '',
      afterScan: false,
      result: null,
      error: null,
      loading: true,
      paused: false,
      isValid: undefined,
      validationUnique: false,
      response: {},
      detectedCodes: {}
    }
  },
  props: {
    data: Object
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

      }, 500)

      this.paused = true
      this.validationUnique = false
      this.detectedCodes = detectedCodes

      axios.post('/tips/qr/check_uniqueness', { link: detectedCodes[0].rawValue })
        .then(response => {

          this.response = response.data

          if (response.data.unique) {
            setTimeout(() => {
              this.afterScan = true
              this.paused = false
              this.validationUnique = false
            }, 1000)
          } else {

            this.validationUnique = true
            this.paused = false
          }
        })
        .catch(error => {
          console.error('Error checking QR code uniqueness:', error)
        })
        .finally(() => {

        })
    },
    saveQrCode() {
      axios.post('/tips/qr/save', {
        name: this.title,
        link: this.detectedCodes[0].rawValue,
        type: 'static'
      })
        .then((response) => {
          this.$inertia.visit('/tips/qr/');
        })
        .catch((error) => {
          console.error('Error saving QR code:', error);
        })
    }
  }
}
</script>
