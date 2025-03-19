<template>
  <div>
    <Head title="Dashboard" />
    <h1 class="mb-8 text-3xl font-bold">Административная панель</h1>
    <p class="mb-8 leading-normal">Главная страница административной панели</p>
  </div>
</template>

<script>
// Для главной страницы /dashboard
import { Head } from '@inertiajs/vue3'
import Layout from '@/Shared/Layout.vue'

export default {
  components: {
    Head,
  },
  layout: Layout,
}
</script>
