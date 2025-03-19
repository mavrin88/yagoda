<template>
  <div class="admin admin_white">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin__container">
      <div class="admin__columns">
        <div class="admin__column">
          <div class="admin__logo2"><img src="/img/content/logo.svg"></div>
        </div>
        <div class="admin__column">
          <div class="admin__role">Администратор<br>Салон Бритва</div>
          <Link class="back" href="organizations/choose">назад</Link>
        </div>
      </div>
      <div class="admin__links">
        <Link href="/">
          <div>ОРГАНИЦАЦИИ</div>
        </Link>
        <Link href="/root_admin/settings">
          <div>НАСТРОЙКИ</div>
        </Link>
        <Link href="/root_admin/orders">
          <div>ПРОДАЖИ</div>
        </Link>
        <Link href="/root_admin/tochka_statistics">
          <div>СТАТИСТИКА ОПЛАТ ТОЧКИ</div>
        </Link>
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
  methods: {

  },
  props: {
    auth: Object,
  },
  data() {
    return {
      meta: {
        title: 'РутАдмин'
      },
    };
  },
}
</script>
