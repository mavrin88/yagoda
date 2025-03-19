<template>
    <div class="superadmin">
      <Head title="Полезные видео" />
      <div class="superadmin__container">
        <div class="superadmin__top">
          <h2>ПОЛЕЗНЫЕ ВИДЕО</h2>
          <img class="close-icon cursor-pointer" @click="back" src="/img/icon_close.svg" alt="">
        </div>

        <div class="superadmin__links mt-6 space-y-2">
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
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
  },
  data() {
    return {};
  },
  methods: {
    back() {
      this.$inertia.get(`/`);
    },
  }
}
</script>

<style scoped>
.superadmin__links {
  display: flex;
  position: relative;
  flex-direction: column;
  color: #000000;
  left: 7px;

}
.superadmin__link {
  font-size: 12px;
  font-weight: 400;
  text-decoration: underline;
}
</style>

