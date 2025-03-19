<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back" @click="goBack()"></a>
          <div class="ytips-top__title">Стойка ЯГОДА с QR-кодом</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-qr">
          <div class="form">
            <div class="form__item ytips-qr__field">
              <input id="name" type="text" v-model="title" @blur="saveName">
              <label for="name"></label>
            </div>
          </div>
          <div class="ytips-qr__text fs14">Ссылка на вашу страницу перевода чаевых.</div>
          <div class="flex justify-center items-center h-full my-4">
            <qrcode-vue :value="data.qrCode.link" size="150" level="H"></qrcode-vue>
          </div>
          <div class="ytips-qr__text">
            <p>
              <p @click="openAndPrintPdf" class="cursor-pointer mb-2">Открыть <b>файл PDF</b> с  этим QR-кодом</p><br>
              Распечатайте его для удобства использования.</p>
          </div>
          <br><br><br>
          <div class="ytips-qr__text"><b>Ссылка</b> с этого QR кода на страницу перевода чаевых<br>
            <a class="tdu" :href="data.qrCode.link">{{ data.qrCode.link }}</a>
          </div>
          <div class="ytips-qr__icons flex justify-center items-center gap-5 mt-5">
            <div @click="copyLink" class="ytips-qr__icon ytips-qr__icon_copy cursor-pointer"></div>
            <div @click="shareLink" class="ytips-qr__icon ytips-qr__icon_share"></div>
          </div>
          <a class="ytips-qr__unlink cursor-pointer" @click="handleDelete">Отвязать Стойку от аккаунта.</a>
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

          <p>
            <Link href="/tips/qr/stands">ВЫБРАТЬ СТОЙКУ</Link>
          </p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../../../Components/TipsHeader.vue'
import QrcodeVue from 'qrcode.vue'

export default {
  name: "Index",
  components: { QrcodeVue, Link, TipsHeader, Head },
  data() {
    return {
      title: this.data.qrCode.name
    }
  },
  props: {
    data: Object
  },
  mounted() {

  },
  methods: {
    goBack() {
      this.$inertia.visit('/tips/qr/');
    },
    openAndPrintPdf() {
      const pdfUrl = `/qr/${this.data.qrCode.id}/pdf`;

      const pdfWindow = window.open(pdfUrl, '_blank');

      if (pdfWindow) {
        pdfWindow.onload = () => {
          pdfWindow.focus();
          pdfWindow.print();
        };
      } else {
        alert('Пожалуйста, разрешите всплывающие окна для этого сайта.');
      }
    },
    handleDelete() {
      this.$inertia.delete(`/tips/qr/${this.data.qrCode.id}`, {
        onSuccess: () => {
          this.$inertia.visit('/tips/qr/');
        },
      })
    },
    copyLink() {
      navigator.clipboard.writeText(this.data.qrCode.link)
        .then(() => {
          console.error('Ссылка скопирована в буфер обмена!');
        })
        .catch((error) => {
          console.error('Ошибка при копировании:', error);
        });
    },
    shareLink() {
      if (navigator.share) {
        navigator.share({
          title: 'QR-код для чаевых',
          text: 'Перейдите по ссылке для оплаты чаевых.',
          url: this.data.qrCode.link,
        })
          .then(() => console.log('Ссылка успешно отправлена'))
          .catch((error) => console.error('Ошибка при шаринге ссылки:', error));
      } else {
        navigator.clipboard.writeText(this.data.qrCode.link)
          .then(() => {
            alert('Ваш браузер не поддерживает функцию. Ссылка скопирована в буфер обмена.');
          })
          .catch(() => {
            alert('Не удалось поделиться ссылкой.');
          });
      }
    },
    saveName() {
      if (!this.title || this.title.trim() === '') {
        this.title = this.data.qrCode.name
        return;
      }

      if (this.title === this.data.qrCode.name) {
        return;
      }

      this.$inertia.patch(`/tips/qr/${this.data.qrCode.id}`, {
        name: this.title,
      }, {
        onSuccess: () => {
          this.$toast?.success('Имя QR-кода обновлено!') || alert('Имя QR-кода обновлено!');
        },
        onError: (errors) => {
          console.error('Ошибка при сохранении:', errors);
          this.$toast?.error('Не удалось обновить имя.') || alert('Не удалось обновить имя.');
        },
      });
    },
  }
}
</script>
