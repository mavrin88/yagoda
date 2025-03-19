<template>
  <div class="superadmin-tips__content">
    <div class="superadmin-results">
      <p class="superadmin-results__price">Сумма чаевых: <b>{{ revenue }}</b> ₽</p>
      <p>Покупателей оставили чаевые: <b>{{ withTips }}</b>  <span>({{ percentageWithTips }}%)</span></p>
<!--      <p>Комиссия за экваринг: <b>{{ equalizationFee }}</b></p>-->
<!--      <p>Сэкономлено на экваринге: <b class="savedEqualization">{{ savedEqualization }}</b> ₽</p>-->
<!--      <p>Заказов: <b>{{ totalOrders }}</b></p>-->
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
    withTips: {
      type: Number,
      required: true
    },
    percentageWithTips: {
      type: Number,
      required: true
    },
  }
}
</script>

<style scoped>
.savedEqualization {
  color: green;
}
</style>
