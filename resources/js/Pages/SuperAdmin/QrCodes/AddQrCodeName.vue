<template>
  <div class="superadmin superadmin_white">
    <Head title="Привязка QR-кода" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <div @click="back" class="back">назад</div>
        <h2>ПРИВЯЗКА QR</h2>
      </div>
      <div class="superadmin__note">Укажите название QR-кода для простоты идентификации. Например: Стойка ресепшен, Маникюр, у администратора</div>
      <div class="form">
        <div class="form__item">
          <input type="text" id="qrCodeName" v-model="qrCodeName">
          <label for="name">Наименование QR-кода</label>
<!--          <div class="password-trigger password-trigger_active"></div>-->
          <!--.password-trigger-->
        </div>
        <div class="form__actions">
          <button class="btn btn_low btn_w100" @click="saveQrCode">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
  },
  props: {
    qr_code: String
  },
  data() {
    return {
      qrCodeName: '',
    };
  },
  methods: {
    saveQrCode() {
      this.$emit('save-qr-code', this.qrCodeName);
    },
    back() {
      this.$emit('back');
    }
  },
}
</script>
