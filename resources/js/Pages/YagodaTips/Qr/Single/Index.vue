<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a @click="goBack" class="ytips-top__back"></a>
          <div class="ytips-top__title">QR код на страницу мастеров</div>
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
          <div class="ytips-qr__text mb-4">
            <p @click="openAndPrintPdf" class="cursor-pointer mb-2">Открыть <b>файл PDF</b> с  этим QR-кодом</p>
              <p>Распечатайте его для удобства использования.</p>
          </div>
          <div class="ytips-qr__text"><b>Ссылка</b> с этого QR кода на страницу перевода чаевых<br>
            <a class="tdu" :href="data.qrCode.link">{{ data.qrCode.link }}</a>
          </div>
          <div class="ytips-qr__icons">
            <div @click="handleDelete" class="ytips-qr__icon ytips-qr__icon_delete"></div>
            <div @click="copyLink" class="ytips-qr__icon ytips-qr__icon_copy"></div>
            <div @click="shareLink" class="ytips-qr__icon ytips-qr__icon_share"></div>
          </div>
        </div>
        <div class="ytips-text ytips-text_14 ytips-text_padding">
          <p>Сканирование этого QR-кода переводит плательщика на страницу оплаты чаевых. Деньги поступят прямо на счет вашей карты, установленной в <a href="#">настройках аккаунта</a>.</p>
          <p>Вы можете распечатать этот QR-код на бумаге или другом носителе. Разместите его в месте, где покупатели оплачивают услуги или ожидают выполнения услуги, так, чтобы он попадал в поле зрения клиентов.</p>
          <p>При оплате чаевых через СБП - в течение 1-5 минут. При оплате Картой - на утро следующего дня.</p>
          <p>Или <span @click="goToStands" class="cursor-pointer underline">закажите наши стационарные стойки Yagoda.</span></p>
          <p class="text-center"><img src="/img/content/stands.png" alt=""></p>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TipsHeader from '../../Components/TipsHeader.vue'
import QrcodeVue from 'qrcode.vue'

export default {
  name: "Index",
  components: { QrcodeVue, TipsHeader, Head },
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
    goToStands() {
      this.$inertia.visit('/tips/qr/stands');
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
