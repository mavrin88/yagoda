<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back"></a>
          <div class="ytips-top__title">QR-КОДЫ МАСТЕРА</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-text ytips-text_12 ytips-text_padding">
          <p>Сканирование привязанных к Мастеру QR кодов, будет направлять Гостя на страницу перевода чаевых непосредственно этому Мастеру. <br>Количество QR кодов может быть любое.</p><br>
          <p class="text-center"><a href="#">+ Добавить QR код со ссылкой на страницу перевод а чаевых</a></p>
        </div>
      </div>
      <div class="ytips__container">
        <div class="list-standard">
          <div class="list-standard__item">
            <div class="list-standard__image"><img src="/img/content/icon_qr.svg" alt=""></div>
            <div class="list-standard__name">Барбершоп</div>
          </div>
        </div><br><br>
        <div class="text-center"><a class="c-blue tdu fw800" href="#">+ Привязать свою страницу перевода чаевых к QR-коду со стойки Ягода.</a></div>
        <div class="list-standard">
          <div class="list-standard__item">
            <div class="list-standard__image"><img src="/img/content/icon_qr.svg" alt=""></div>
            <div class="list-standard__name">На прикассовой зоне</div>
          </div>
          <div class="list-standard__item">
            <div class="list-standard__image"><img src="/img/content/icon_qr.svg" alt=""></div>
            <div class="list-standard__name">QR / Личный</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TipsHeader from '../../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { TipsHeader, Head },
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
  }
}
</script>
