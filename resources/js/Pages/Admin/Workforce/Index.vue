<template>
  <div class="superadmin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>СОТРУДНИКИ</h2>
      </div>

      <div class="admin__container">
        <div class="admin__links">
          <Link href="/admin_masters">
            <div>МАСТЕРА ({{ workforce.masters }})</div>
          </Link>
          <Link href="/admin_staff">
            <div>ПЕРСОНАЛ ({{ workforce.staff }})</div>
          </Link>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import 'vue-select/dist/vue-select.css'

export default {
  components: {
    Head,
    Link
  },
  props: {
    workforce: Object
  },
  computed: {},
  data() {
    return {
      meta: {
        title: 'Сотрудники'
      }
    }
  },
  methods: {

  },
  watch: {

  },
}
</script>
