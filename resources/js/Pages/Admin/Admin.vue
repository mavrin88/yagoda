<template>
  <div class="admin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin__cover">
      <img v-if="auth.organization.photo_path" :src="auth.organization.photo_path">
      <div v-if="auth.organization.logo_path" class="admin__logo">
        <img :src="auth.organization.logo_path">
      </div>
    </div>
    <div class="admin__container">
      <div class="admin__role">{{ auth.user.selected_organization_role_name }}<br>{{ auth.user.selected_organization_name }}</div>
      <Link class="back" href="organizations/choose">назад</Link>
      <div class="admin__links">
        <div @click="handleNavigation" class="orders_link" v-if="!isRoleArchived">
          <div class="circle" v-if="!checkOrders"></div>
          ПРОДАЖИ
        </div>
        <Link v-if="!auth.organization.deleted && !isRoleArchived" href="/staff_shift">
          <div>СОТРУДНИКИ В СМЕНЕ</div>
        </Link>
        <Link v-if="!auth.organization.deleted && !isRoleArchived" href="/admin/workforce">
          <div>СОТРУДНИКИ</div>
        </Link>
        <Link v-if="!auth.organization.deleted && !isRoleArchived" href="super_admin/services_products">
          <div>УСЛУГИ И ТОВАРЫ</div>
        </Link>
        <Link v-if="!auth.organization.deleted && !isRoleArchived" href="/super_admin/qr_codes">
          <div>QR-КОДЫ</div>
        </Link>
        <Link href="/admin/statistics">
          <div>СТАТИСТИКА</div>
        </Link>
        <Link v-if="isRoleArchived" @click="handleDeleteRole">
          <div>УДАЛИТЬ РОЛЬ</div>
        </Link>
<!--        <Link href="/orders">-->
<!--          <div>УСЛУГИ, ТОВАРЫ</div>-->
<!--        </Link>-->
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
import axios from 'axios'

export default {
  components: {
    Head,
    ModalCreateOrder,
    Link,
  },
  computed: {
    checkOrders() {
      return (this.auth.organization.status === 'active' || this.auth.organization.status === null) && this.auth.organization.agency_agreement_number
    },
    isRoleArchived() {
      const roleData = this.auth.user.data.find(
        (data) =>
          data.organization_id === this.auth.user.selected_organization_id &&
          data.role_id === this.auth.user.selected_organization_role_id
      );
      return roleData ? roleData.role_status === 'archived' : false;
    }
  },
  data() {
    return {
      meta: {
        title: 'Администратор'
      },
      isModalCreateOrderActive: false,
      modalCreateOrderText: 'Формирование заказов временно недоступно. Свяжитесь с отделом заботы: <br><a href="tel:+74992132233">+7 (499) 213-22-33</a>, <a href="https://wa.me/79153690251" target="_blank">WhatsApp</a>, <a href="https://t.me/+79153690251" target="_blank">Телеграм</a>',
    }
  },
  methods: {
    async handleDeleteRole() {
      axios.post('/deleteArchivedRole', {
        organization_id: this.auth.user.selected_organization_id,
        role_id: this.auth.user.selected_organization_role_id,
      })
        .then(response => {
          this.$inertia.visit('/organizations/choose');
        })
        .catch(error => {

        });
    },
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
}
</script>

<style>
.orders_link{
  font-size: 16px;
  font-weight: 500;
  display: block;
  margin-bottom: 1.25rem;
  text-transform: uppercase;
  cursor: pointer;
}
</style>
