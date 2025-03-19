<template>
  <div class="admin">
    <Head title="Заказ не найден" />
    <div class="admin__container">
      <div class="admin__top">
        <Link href="/" class="back">вернуться</Link>
      </div>

      <p>&nbsp;</p>
      <p>Заказ не найден</p>
    </div>
  </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
export default {
  components: {
    Head,
    Link
  },
  props: {
    data: {
      type: Object,
      required: true
    },
  },
  data() {
    return {

    }
  },
  computed: {

  },
  methods: {

  },
}
</script>
