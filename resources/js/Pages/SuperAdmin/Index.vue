<template>
  <div class="admin">
    <Head title="Суперадмин" />
    <div class="admin__cover">
      <img v-if="auth.organization.photo_path" :src="auth.organization.photo_path">
      <div v-if="auth.organization.logo_path" class="admin__logo">
        <img :src="auth.organization.logo_path">
      </div>
    </div>
    <div class="admin__container">
      <div class="admin__role">{{ auth.user.selected_organization_role_name }}<br>{{ auth.user.selected_organization_name }}</div>
      <Link class="back" href="organizations/choose">назад</Link>
      <div class="admin__links" v-if="auth.user.super_admin_block.settings && auth.user.super_admin_block.bills">
        <div @click="handleNavigation" class="orders_link">
          <div class="circle" v-if="!checkOrders"></div>
          ПРОДАЖИ
        </div>
        <Link href="/staff_shift">
          <div>СОТРУДНИКИ В СМЕНЕ</div>
        </Link>
        <Link href="/super_admin/workforce">
          <div>СОТРУДНИКИ</div>
        </Link>
          <Link href="super_admin/services_products">
          <div>УСЛУГИ И ТОВАРЫ</div>
        </Link>
        <Link href="/super_admin/qr_codes">
          <div>QR-КОДЫ</div>
        </Link>
        <Link href="/super_admin/reports">
          <div>Отчеты</div>
        </Link>
        <Link href="/tip_distribution">
          <div class="circle" v-if="!checkTipDistribution"></div>
          <div>ЧАЕВЫЕ РАСПРЕДЕЛЕНИЕ</div>
        </Link>
        <Link href="/super_admin/organization">
          <div>ОРГАНИЗАЦИЯ</div>
        </Link>
        <Link href="/super_admin/bills">
          <div>СЧЕТА</div>
        </Link>
        <Link href="/super_admin/settings">
          <div>НАСТРОЙКИ</div>
        </Link>
      </div>

      <div class="admin__links" v-else>
        <Link href="/super_admin/bills">
          <div class="circle" v-if="!auth.user.super_admin_block.bills"></div>
          <div class="text">СЧЕТА</div>
        </Link>
        <Link href="/super_admin/organization">
          <div class="circle" v-if="!auth.user.super_admin_block.settings"></div>
          <div class="text">ОРГАНИЗАЦИЯ</div>
        </Link>
      </div>

      <div class="admin__links-last admin__links">
        <Link href="/super_admin/useful_videos">
          <div class="text-[#9CA4B5]">ПОЛЕЗНЫЕ ВИДЕО</div>
        </Link>
        <Link href="/super_admin/after_registration">
          <div class="text-[#9CA4B5]">ПОСЛЕ РЕГИСТРАЦИИ</div>
        </Link>
      </div>
    </div>
  </div>

  <ModalCreateOrder
    :is-active="isModalCreateOrderActive"
    @close="hideModalCreateOrder"
    :message="modalCreateOrderText"
  />
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import ModalCreateOrder from '../../Shared/Modals/ModalCreateOrder.vue'

export default {
  components: {
    Head,
    ModalCreateOrder,
    Link,
  },
  computed: {
    checkTipDistribution() {
      const settings = this.auth.user.super_admin_block.settings;
      return (
        parseFloat(settings.admin_percentage) > 0 ||
        parseFloat(settings.staff_percentage) > 0 ||
        parseFloat(settings.organization_percentage) > 0
      );
    },
    checkOrders() {
      return (this.auth.organization.status === 'active' || this.auth.organization.status === null) && this.auth.organization.agency_agreement_number
    }
  },
  methods: {
    showModalCreateOrder() {
      this.isModalCreateOrderActive = true;
    },
    hideModalCreateOrder() {
      this.isModalCreateOrderActive = false;
    },
    async handleNavigation() {
      if (this.checkOrders) {
        this.$inertia.visit('/orders');
      } else {
        this.showModalCreateOrder();
      }
    }
  },
  props: {
    auth: Object,
  },
  data() {
    return {
      isModalCreateOrderActive: false,
      modalCreateOrderText: 'Формирование заказов временно недоступно. Свяжитесь с отделом заботы: <br><a href="tel:+74992132233">+7 (499) 213-22-33</a>, <a href="https://wa.me/79153690251" target="_blank">WhatsApp</a>, <a href="https://t.me/+79153690251" target="_blank">Телеграм</a>',
    }
  },
}
</script>

<style scoped>
.link {
  display: flex;                   /* Используем flexbox */
  align-items: center;             /* Центрируем элементы по вертикали */
  text-decoration: none;           /* Убираем подчеркивание */
  color: inherit;                  /* Наследуем цвет текста */
}

.circle {
  width: 13px;                     /* Ширина круга */
  height: 13px;                    /* Высота круга */
  background-color: red;           /* Цвет круга */
  border-radius: 50%;              /* Делаем круг */
  margin-right: 8px;               /* Отступ между кругом и текстом */
  flex-shrink: 0;                  /* Предотвращаем сжатие элемента */
  float: left;
  margin-top: 3px;
}

.text {
  font-size: 16px;                 /* Устанавливаем размер шрифта */
  display: flex;                   /* Добавляем flex для правильного выравнивания */
  align-items: center;             /* Для выравнивания текста в центр */
}

.admin__links-last {
margin-top: 40px;
}

.orders_link{
    font-size: 16px;
    font-weight: 500;
    display: block;
    margin-bottom: 1.25rem;
    text-transform: uppercase;
  cursor: pointer;
}
</style>
