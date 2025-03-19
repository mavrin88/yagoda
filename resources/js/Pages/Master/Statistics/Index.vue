<template>
  <div class="superadmin">
    <Head title="Чаевые" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>Чаевые</h2>
      </div>
      <div class="superadmin-tips">
        <Date />
        <div class="superadmin-tips__content">
          <div class="superadmin-results">
            <p class="superadmin-results__price">Сумма чаевых: <b>0</b>₽</p>
            <p>Средние чаевые: <b>0</b>%</p>
            <p>Выполнено заказов: <b>0</b></p>
            <p>Покупателей оставили чаевые: <b>0</b>  <span>(0%)</span></p>
          </div>
        </div>
        <button @click="openBurger" class="btn btn_w100 btn_light">Показать поступления</button>
      </div>
    </div>
    <div class="sliding-block js-active" v-if="showStatistic">
      <div class="sliding-block__overlay"></div>
      <div class="sliding-block__content">
        <div class="sliding-block__top">
          <div @click="openBurger" class="sliding-block__close"></div>
          <div class="sliding-block__title">ПОСТУПЛЕНИЯ</div>
        </div>
        <div class="sliding-block__body">
          <div class="master-receipts">
            <p>05.09.2024</p>
            <div class="columns">
              <div class="columns__column">18:35</div>
              <div class="columns__column">500 ₽</div>
            </div>
          </div>
          <a class="btn btn_low btn_light">Показать ещё 40</a>
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
