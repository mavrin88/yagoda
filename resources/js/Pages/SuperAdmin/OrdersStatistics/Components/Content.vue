<template>
  <div class="superadmin-tips__content">
    <div class="superadmin-results">
      <p class="superadmin-results__price">выручка: <b>{{ convertToDecimal(revenue) }}</b> ₽</p>
      <div class="admin-orders__data">
        <span title="заказов с чаем">{{ todayOrdersWithTipsCount }}</span>
        <i>/</i>
        <span title="% от всех">{{ convertToInteger(todayOrdersWithTipsPercentage) }} %</span>
        <i>/</i>
        <span title="средний чай">{{ medium_tips }} %</span>
        <i>/</i>
        <b title="итого чаевых">{{ totalTips }}
          <span style="font-weight: normal; color: #9ca4b5"> ₽</span>
        </b>
      </div>
      <p v-if="false">Покупателей оставили чаевые: <b>{{ buyersCount }}</b>  <span>({{ percentageWithTips }}%)</span></p>
      <p>Комиссия за экваринг Yagoda: <b>{{ equalizationFee }}</b> ₽</p>
      <p>Сэкономлено на экваринге: <b class="savedEqualization">{{ savedEqualization }}</b> ₽</p>
      <p>Заказов: <b>{{ totalOrders }}</b></p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    revenue: {
      type: Number,
      required: true
    },
    equalizationFee: {
      type: Number,
      required: true
    },
    savedEqualization: {
      type: Number,
      required: true
    },
    totalOrders: {
      type: Number,
      required: true
    },
    todayOrdersWithTipsCount: {
      type: Number,
      required: true
    },
    todayOrdersWithTipsPercentage: {
      type: Number,
      required: true
    },
    medium_tips: {
      type: Number,
      required: true
    },
    totalTips: {
      type: Number,
      required: true
    }
  },
  methods: {
    // 2 знака после точки
    convertToDecimal(num) {
      return Math.round(num * 100) / 100
    },
    // Целое число
    convertToInteger(num) {
      return Math.round(num);
    }
  }
}
</script>

<style scoped>
.savedEqualization {
  color: green;
}
</style>
