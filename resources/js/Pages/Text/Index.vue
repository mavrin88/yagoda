<template>
  <div class="page-text client">
    <Head :title="data?.title ? `${data.title} - Yagoda team` : 'Yagoda team'" />

    <div v-if="data.title" class="client__container">
      <p>&nbsp;</p>
      <Link class="back" href="/organizations/choose">назад</Link>
      <p>&nbsp;</p>
      <h1>{{ data.title }}</h1>
      <div class="page-text__body" v-html="data.text"></div>
    </div>
    <div v-else>
      <div class="client__container">
        <p>&nbsp;</p>
        <Link class="back" href="/organizations/choose">назад</Link>
        <p>&nbsp;</p>
        <p>Ничего не найдено :(</p>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'

export default {
  components: {
    Link,
    Head
  },
  props: {
    data: {
      type: Object,
      default: null,
    },
  },
}
</script>

<style>
.page-text h1 {
  font-size: 1.5em;
}
.page-text h2 {
  font-size: 1.1em;
}
.page-text .page-text__body p {
  margin-bottom: 10px;
}
</style>
