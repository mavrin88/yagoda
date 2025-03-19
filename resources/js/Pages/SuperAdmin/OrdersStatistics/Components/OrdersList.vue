<template>
  <button
    class="btn btn_w100 btn_trigger"
    :class="{ 'btn_trigger_active': showMasters }"
    @click="toggleMasters"
  >
    {{ showMasters ? 'Скрыть заказы' : 'Показать заказы' }}
  </button>
  <div class="masters-full" :class="{ 'masters-full--hidden': !showMasters }">
    <div class="masters-full__item" v-for="order in orders" :key="order.name">
      <div class="masters-full__image"><img src="/img/content/avatar.png" alt="Avatar"></div>
      <div class="masters-full__content">
        <div class="masters-full__top">{{ order.ordersWithTips }} / {{ order.percentageWithTips }}% / {{ order.averageTip }} / <b>{{ order.totalTips }}</b> <span> ₽</span></div>
        <div class="masters-full__bottom">
          <div class="masters-full__name">{{ order.name }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    orders: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      showMasters: false,
    }
  },

  methods: {
    toggleMasters() {
      this.showMasters = !this.showMasters
    }
  }
}
</script>

<style scoped>
.masters-full {
  max-height: 1000px;
  overflow: hidden;
  transition: max-height 0.5s ease-in-out;
}

.masters-full--hidden {
  max-height: 0;
}
</style>
