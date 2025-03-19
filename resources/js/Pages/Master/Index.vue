<template>
  <div class="admin">
    <Head title="Мастер" />
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
        <Link @click="goToStatistics">
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
    goToStatistics() {
      this.$inertia.post('/master/statistics', {
        'master_id': this.auth.user.id
      })
    },
  },
  props: {
    auth: Object,
  },
}
</script>
