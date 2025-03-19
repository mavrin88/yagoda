<template>
    <div class="superadmin">
      <Head title="Чаевые" />
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" @click="goBack">назад</Link>
          <h2>Чаевые</h2>
        </div>
        <div class="superadmin-tips">
          <Date @filteredTipsData="filteredTipsData"/>
          <form :options="localData.options" />
          <filter :date="localData.date" />
          <content
            :sumTips="localData.sumTips"
            :buyersCount="localData.buyersCount"
            :averageTip="localData.averageTip"
            :revenue="localData.revenue"
            :mastersCount="localData.mastersCount"
            :percentageWithTips="localData.percentageWithTips"
          />
          <masters-list :masters="localData.masters" />
        </div>
      </div>
    </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

import Form from './Components/Form.vue'
import Filter from './Components/Filter.vue'
import Content from './Components/Content.vue'
import MastersList from './Components/MastersList.vue'
import Date from '../../../Shared/Date.vue'


export default {
  components: {
    Head,
    Date,
    Link,
    Form,
    Filter,
    Content,
    MastersList
  },
  props: {
    data: Object
  },
  data() {
    return {
      localData: { ...this.data },
    };
  },
  methods: {
    goBack() {
        this.$inertia.get(`/super_admin/reports`);
    },
    filteredTipsData(data) {
      this.localData = data
    }
  },
}
</script>
