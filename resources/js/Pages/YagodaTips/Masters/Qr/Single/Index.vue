<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back"></a>
          <div class="ytips-top__title">QR код на страницу перевода</div>
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
          </div>
        </div>
        <div class="ytips-text ytips-text_14 ytips-text_padding">
          <p>Сканирование этого QR-кода переводит плательщика на страницу оплаты чаевых. Деньги поступят прямо на счет вашей карты, установленной в <a href="#">настройках аккаунта</a>.</p>
          <p>Вы можете распечатать этот QR-код на бумаге или другом носителе. Разместите его в месте, где покупатели оплачивают услуги или ожидают выполнения услуги, так, чтобы он попадал в поле зрения клиентов.</p>
          <p>При оплате чаевых через СБП - в течение 1-5 минут. При оплате Картой - на утро следующего дня.</p>
          <p>Или <a href="#">закажите наши стационарные стойки Yagoda.</a></p>
          <p class="text-center"><img src="/img/content/stands.png" alt=""></p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TipsHeader from '../../../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { TipsHeader, Head },
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
