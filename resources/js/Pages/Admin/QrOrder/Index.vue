<template>
  <div class="admin">
    <div class="admin__container">
      <div class="admin-qr">
        <div class="admin-qr__top">
          <Link class="back" href="/">назад</Link>
          <a href="/admin-orders.html">К ЗАКАЗАМ</a>
        </div>
        <QrNote v-if="!isStatic" :message="'Отсканируйте QR для оплаты.'"></QrNote>
        <QrStatic v-else :staticQr="'6 Ресепшен'"></QrStatic>
        <QrCode :qrImage="data.qrCode"></QrCode>
        <QrLink :link="'https://ratewell.ru/vaddfpo12321'" @copyLink="copyLink"></QrLink>
        <aQrTotal :total="9360" :master="'Леонардо'"></aQrTotal>
        <QrNote v-if="isStatic" :message="'Или выбери статичный QR находящийся возле тебя.'"></QrNote>
        <QrList :qrItems="data.qrItems"></QrList>
      </div>
    </div>
  </div>
</template>

<script>
import QrNote from './Components/QrNote.vue'
import QrStatic from './Components/QrStatic.vue'
import QrCode from './Components/QrCode.vue'
import QrLink from './Components/QrLink.vue'
import QrTotal from './Components/QrTotal.vue'
import QrList from './Components/QrList.vue'
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
    QrNote,
    QrStatic,
    QrCode,
    QrLink,
    QrTotal,
    QrList,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      isStatic: false,
    }
  },
  methods: {
    goBack() {
      // Реализуйте логику перехода назад
      console.log('Переход назад')
    },
    copyLink() {
      // Реализуйте логику копирования ссылки
      console.log('Ссылка скопирована')
    },
  },
}
</script>
