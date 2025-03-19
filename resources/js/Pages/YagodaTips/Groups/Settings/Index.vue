<template>
  <div class="superadmin">

    <TipsHeader />

    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" href="/tips/coord"></Link>
        <div class="ytips-top__title">НАСТРОЙКИ</div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="admin__container">
        <div class="admin__links">
          <Link href="/tips/group">
            <div>НАСТРОЙКИ ГРУППЫ</div>
          </Link>
          <Link href="/tips/map_links">
            <div>ССЫЛКИ НА КАРТЫ</div>
          </Link>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import TipsHeader from '@/Pages/YagodaTips/Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
    Link,
    Head
  },
  props: {

  },
  computed: {},
  data() {
    return {
      meta: {
        title: "Настройки"
      }
    }
  },
  methods: {
    goBack() {
      this.$emit('goBack')
    }
  },
  watch: {

  },
}
</script>
<style lang="scss" scoped>
  .superadmin {
    padding-top: 0;
    &__top {
      margin-top: 10px;
    }
  }
</style>
