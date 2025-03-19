<template>
  <div class="superadmin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>Статистика</h2>
      </div>
      <div class="superadmin-tips">
        <Date />
        <div class="superadmin-tips__content">
          <div class="superadmin-results">
            <p class="superadmin-results__price">Сумма чаевых: <b>0</b>₽</p>
            <p>Покупателей оставили чаевые: <b>0</b>  <span>(0%)</span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<script >
import { Head, Link } from '@inertiajs/vue3'

import axios from 'axios'
import Date from '../../../Shared/Date.vue'

export default {
  components: {
    Head,
    Link,
    Date
  },

  data() {
    return {
      meta: {
        title: "Статистика"
      },
      showStatistic: false,
    }
  },
  methods: {
    openBurger() {
      this.showStatistic = !this.showStatistic
    },
  },
}
</script>

<style>

.v-select .vs__selected {
  font-size: 14px;
  margin-left: 0;
  padding-left: 0;
  letter-spacing: 1px;
  color: #000;
}
</style>
