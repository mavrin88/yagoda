<template>
  <div class="admin">
    <Head title="Сотрудники" />
    <div class="admin__cover">
      <img v-if="auth.organization.photo_path" :src="auth.organization.photo_path">
      <div class="admin__logo">
        <img v-if="auth.organization.logo_path" :src="auth.organization.logo_path">
      </div>
    </div>
    <div class="admin__container">
      <div class="admin__role">{{ auth.user.selected_organization_role_name }}<br>{{ auth.user.selected_organization_name }}</div>
      <Link class="back" href="organizations/choose">назад</Link>
      <div class="admin__links" v-if="auth.user.super_admin_block.settings && auth.user.super_admin_block.bills">
        <Link href="/employee/statistics">
          <div>СТАТИСТИКА</div>
        </Link>
        <Link v-if="isRoleArchived" @click="handleDeleteRole">
          <div>УДАЛИТЬ РОЛЬ</div>
        </Link>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'

export default {
  components: {
    Head,
    Link,
  },
  computed: {
    isRoleArchived() {
      const roleData = this.auth.user.data.find(
        (data) =>
          data.organization_id === this.auth.user.selected_organization_id &&
          data.role_id === this.auth.user.selected_organization_role_id
      );
      return roleData ? roleData.role_status === 'archived' : false;
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
  },
  props: {
    auth: Object,
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
</style>
