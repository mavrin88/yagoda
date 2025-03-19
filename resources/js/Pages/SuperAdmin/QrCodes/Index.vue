<template>
  <div id="app" v-if="!visibleAddQrCodeCamera && !visibleAddQrCodeName">
    <Head title="QR-коды оплаты" />
    <div class="superadmin">
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" href="/">назад</Link>
          <h2>QR-КОДЫ ОПЛАТЫ</h2>
        </div>
        <div class="superadmin__note">Для удобства Клиента и Администратора, используйте статичные QR-коды. Располагайте их на стойке возле администратора.
          <br>Варианты стендов смотрите <a href="#">тут</a>.<br>Для привязки статичного QR-кода, просто отсканируйте его.</div>
        <br>
        <div @click="visibleAddQrCodeCamera = true" class="btn btn_add btn_light btn_low">Привязать новый QR-код</div>
        <QrList :qr_codes="qr_codes" @delete-qr-code="deleteQrCode"/>
      </div>
    </div>
  </div>

  <AddQrCodeCamera v-if="visibleAddQrCodeCamera && !visibleAddQrCodeName"  @detected-qr-code="onDetect" @back="back"/>
  <AddQrCodeName v-if="visibleAddQrCodeName" @save-qr-code="saveQrCode" @back="back"/>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import QrList from './Components/QrList.vue'
import AddQrCodeCamera from './AddQrCodeCamera.vue'
import AddQrCodeName from './AddQrCodeName.vue'

export default {
  components: {
    Head,
    Link,
    QrList,
    AddQrCodeCamera,
    AddQrCodeName
  },
  props: {
    auth: Object,
    qr_codes: Object,
  },
  data() {
    return {
      visibleAddQrCodeCamera: false,
      visibleAddQrCodeName: false,
      scanQrCode: null,
    };
  },
  methods: {
    deleteQrCode(index) {
      this.$inertia.delete('/super_admin/qr_codes/' + this.qr_codes[index].id);

      this.qr_codes.splice(index, 1);
    },
    onDetect(name) {
      this.scanQrCode = name
      this.visibleAddQrCodeCamera = false
      this.visibleAddQrCodeName = true
    },
    saveQrCode(name) {
      this.$inertia.post('/super_admin/save_qr_code', {
        name: name,
        link: this.scanQrCode
      });

      this.back()
    },
    back() {
      this.visibleAddQrCodeCamera = false
      this.visibleAddQrCodeName = false
    }
  },
}
</script>
