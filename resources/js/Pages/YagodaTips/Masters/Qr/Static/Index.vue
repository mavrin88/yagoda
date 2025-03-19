<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back"></a>
          <div class="ytips-top__title">Стойка ЯГОДА с QR-кодом</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-qr">
          <div class="form">
            <div class="form__item ytips-qr__field">
              <input id="name" type="text" v-model="title">
              <label for="name"></label>
            </div>
          </div>
          <div class="ytips-qr__text fs14">Ссылка на вашу страницу перевода чаевых.</div>
          <div class="ytips-qr__image"><img src="/img/demo/qr-big.png"></div>
          <div class="ytips-qr__text">
            <p><a class="tdu" href="#">Открыть <b>файл PDF</b> с  этим QR-кодом</a><br>Распечатайте его для удобства использования.</p>
          </div><br><br><br>
          <div class="ytips-qr__text"><b>Ссылка</b> с этого QR кода на страницу перевода чаевых<br><a class="tdu" href="#">https://sharepay.ru/vaddf45456536po12321</a></div>
          <div class="ytips-qr__icons">
            <div class="ytips-qr__icon ytips-qr__icon_delete"></div>
            <div class="ytips-qr__icon ytips-qr__icon_copy"></div>
            <div class="ytips-qr__icon ytips-qr__icon_share"></div>
          </div><a class="ytips-qr__unlink" href="#">Отвязать Стойку от аккаунта.</a>
        </div>
        <div class="ytips-text ytips-text_14 ytips-text_padding">
          <p>
            Закажите доставку бесплатной стойки Ягода с QR кодом.
            Используя стойку Ягода, клиент быстрее найдет ее глазами. Ему будет проще отсканировать QR-код и перевести вам чаевые.
          </p>
          <p>
            Выберите стойку, которая лучше подойдет к вашему интерьеру.
            Расположите её рядом с вашим рабочим столом или местом оплаты за услуги
          </p>

          <p><Link href="/tips/master/stands">ВЫБРАТЬ СТОЙКУ</Link></p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../../../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { Link, TipsHeader, Head },
  data() {
    return {
      title: ''
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
