<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/tips/coord"></Link>
          <div class="ytips-top__title">QR КОДЫ ГРУППЫ</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-text ytips-text_12 ytips-text_padding">
          <p>Сканирование привязанных к Мастеру QR кодов, будет направлять Гостя на страницу перевода чаевых непосредственно этому Мастеру. <br>Количество QR кодов может быть любое.</p><br>
          <p class="text-center">
            <span @click="addQrCodeName()" class="cursor-pointer">+ Добавить QR код со ссылкой на страницу перевод а чаевых</span>
          </p>
        </div>
      </div>
      <div class="ytips__container">

        <div class="list-standard">
          <div @click="visitQrDinamic(qr.id)" class="list-standard__item" v-for="qr in data.dinamic" :key="qr.id">
            <div class="list-standard__image">
              <img :src="qr.image" alt="">
            </div>
            <div class="list-standard__name">{{ qr.name }}</div>
          </div>
        </div>

        <br><br>
        <div class="text-center">
          <p @click="addQrCodeScan" class="c-blue tdu fw800 cursor-pointer">+ Привязать свою страницу перевода чаевых к QR-коду со стойки Ягода.</p>
        </div>
        <div class="list-standard">
          <div @click="visitQrStatic(qr.id)" class="list-standard__item" v-for="qr in data.static">
            <div class="list-standard__image">
              <img :src="qr.image" alt="">
            </div>
            <div class="list-standard__name">{{ qr.name }}</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { Link, TipsHeader, Head },
  data() {
    return {
    }
  },
  props: {
    data: Object
  },
  mounted() {

  },
  methods: {
    visitQrDinamic(id) {
      this.$inertia.visit('/tips/qr/show/' + id);
    },
    visitQrStatic(id) {
      this.$inertia.visit('/tips/qr/static/' + id);
    },
    addQrCodeName() {
      this.$inertia.visit('/tips/qr_add/name');
    },
    addQrCodeScan() {
      this.$inertia.visit('/tips/qr_add/scan');
    }
  }
}
</script>
