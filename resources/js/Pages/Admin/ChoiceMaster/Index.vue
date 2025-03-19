<template>
  <div class="admin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin__container">
      <div class="admin-choose-masters">
        <div class="admin-choose-masters__top">
          <Link class="back" href="/">назад</Link>
          <h2>Выбор мастера</h2>
        </div>
        <ChoiceBill :billItems="data.billItems" :total="data.total" :discount="data.discount" :discountedTotal="data.discountedTotal"></ChoiceBill>
        <ChoiceMasters :masters="data.masters" :total="data.mastersTotal"></ChoiceMasters>
        <ChoiceFooter :positions="data.positions" :total="data.total" :mastersCount="data.masters.length"></ChoiceFooter>
      </div>
    </div>
  </div>
</template>

<script>
import ChoiceBill from './Components/ChoiceBill.vue';
import ChoiceMasters from './Components/ChoiceMasters.vue';
import ChoiceFooter from './Components/ChoiceFooter.vue'
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
    ChoiceBill,
    ChoiceMasters,
    ChoiceFooter
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      meta: {
        title: 'Выбор мастера'
      },
      selectedMasters: [],
    };
  },
  methods: {

  },
};
</script>
