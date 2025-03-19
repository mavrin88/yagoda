<template>
    <div class="superadmin">
      <Head title="После регистрации" />
      <div class="superadmin__container">
        <div class="superadmin__top">
          <h2>ПОСЛЕ РЕГИСТРАЦИИ</h2>
          <img class="close-icon cursor-pointer" @click="back" src="/img/icon_close.svg" alt="Закрыть">
        </div>

        <div class="superadmin__content">
          <p class="first-paragraph"><strong>1. Заключаем договор.</strong> Для оперативности, свяжитесь с нашим отделом заботы. Вам пришлют договор. От вас распечатать, подписать, поставить печать и отправить нам скан договора. Оригинал почтой.</p>
          <p class="first-paragraph">Отдел заботы: <a href="tel:+74992132233" class="underline">+7 (499) 213-22-33</a>, <a href="https://wa.me/79153690251" target="_blank" class="underline decoration-black">WhatsApp</a>, <a href="https://t.me/+79153690251" target="_blank" class="underline decoration-black">Telegram</a>.</p>
          <p class="second-paragraph"><strong>2.</strong> Отдел заботы получает договор, проверяет данные и <strong>выдает доступ</strong> к возможности проводить платежи.</p>
          <p class="third-paragraph">Все готово. <strong>Пользуйтесь</strong>.</p>

          <p class="fourth-paragraph">Для удобной работы, выберите и закажите стойки или наклейки с QR кодами Yagoda. Это бесплатно. Мы доставим их так быстро, как сможем.</p>
          <p>А пока, ваши клиенты могут сканировать QR код с экрана телефона администратора, или мы можем прислать вам временный QR код. Распечатайте его и положите возле администратора.</p>

          <p class="five-paragraph">3. Ваши сотрудники, для получения чаевых, должны <strong>пройти короткую регистрацию.</strong> Ссылка на страницу регистрации по QR коду на этом экране.</p>
          <p class="six-paragraph">4. После того, как сотрудники зарегистрируются, <strong>добавьте их в соответствующие роли:</strong></p>
          <ul>
            <li>АДМИНИСТРАТОР — тот, кто будет иметь возможность формировать заказы, добавлять сотрудников, заполнять каталог услуг...</li>
            <li>МАСТЕР — исполнитель услуг.</li>
            <li>ПЕРСОНАЛ — клиенты и другие подсобные сотрудники. Они также смогут получать часть чаевых Мастеров (долю чаевых настраивает руководитель).</li>
          </ul>
        </div>

        <div class="text-center my-8 qr_code">
          <p class="">Регистрация пользователя</p>
          <img src="/img/qr_code.png" class="mx-auto w-[calc(100%-152px)]" alt="QR код">
        </div>

        <div class="superadmin__down_content">
          <p class="seven-paragraph">Короткие <strong>информационные видео:</strong></p>

          <div class="superadmin__down_links mt-6 space-y-2">
            <a class="superadmin__link"  href="https://yagoda.team/video/registraciya_novogo_polzovatelya.mp4">1. Регистрация нового пользователя</a>
            <a class="superadmin__link"  href="https://yagoda.team/video/sozdaniye_zakaza.mp4">2. Создание заказа</a>
            <a class="superadmin__link" href="https://yagoda.team/video/raspredeleniye_chaevih.mp4">3. Распределение чаевых</a>
            <a class="superadmin__link" href="https://yagoda.team/video/dobavleniye_administratora.mp4">4. Добавление Администратора</a>
            <a class="superadmin__link" href="https://yagoda.team/video/dobavleniye_mastera_i_personala.mp4">5. Добавление Мастера и Персоналаа</a>
            <a class="superadmin__link" href="https://yagoda.team/video/zapolneniye_kataloga.mp4">6. Заполнение каталога</a>
            <a class="superadmin__link" href="https://yagoda.team/video/imya_proizv_platezgey.mp4">7. Установка наименования товара для Произвольных платежей</a>
            <a class="superadmin__link" href="https://yagoda.team/video/privyazka_qr_koda.mp4">8. Привязка QR кода</a>
            <a class="superadmin__link" href="https://yagoda.team/video/dobavleniye_scheta_org.mp4">9. Добавление счета</a>
            <a class="superadmin__link" href="https://yagoda.team/video/zapolneniy_dannih_organizacii.mp4">10. Заполнение данных организации</a>
            <a class="superadmin__link" href="https://yagoda.team/video/karta_ust_i_zamena.mp4">11. Установка и замена карты Пользователя</a>
            <a class="superadmin__link" href="https://yagoda.team/video/registraciya_novoy_organizacii.mp4 ">12. Регистрация новой организации</a>
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
  data() {
    return {

    };
  },
  methods: {
    back() {
      this.$inertia.get(`/`);
    },
  }
}
</script>

<style lang="scss" scoped>
.superadmin__content {
  display: flex;
  position: relative;
  flex-direction: column;
  color: #000000;
  font-size: 12px;
 line-height: 14.63px;

  .first-paragraph {
    margin-left: 7px;
  }

  .second-paragraph {
    margin-left: 7px;
  }
  .third-paragraph {
   padding-top: 20px;
  }
  .fourth-paragraph {
   padding-top: 20px;
  }
  .five-paragraph {
   padding-top: 20px;
  }
}
.superadmin__link {
  font-size: 12px;
  font-weight: 400;
  text-decoration: underline;
}
.qr_code {
  font-size: 12px;
  line-height: 14.63px;
}

.superadmin__down_content {

  .seven-paragraph {
    padding-top: 10px;
  }
  .superadmin__down_links {
    display: flex;
    position: relative;
    flex-direction: column;
    color: #000000;
    font-size: 12px;
    line-height: 14.63px;
  }
}
</style>
