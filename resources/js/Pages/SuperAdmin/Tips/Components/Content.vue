<template>
  <div class="superadmin-tips__content">
    <div class="superadmin-results">
      <p class="superadmin-results__price">Сумма чаевых: <b>{{ sumTips }}</b> ₽</p>
      <p>Покупателей оставили чаевые: <b>{{ buyersCount }}</b>  <span>({{ percentageWithTips }}%)</span></p>
      <p>Средние чаевые: <b>{{ averageTip }}</b>%</p>
      <p>Выручка: <b>{{ revenue }}</b> ₽</p>
      <p>Мастеров: <b>{{ mastersCount }}</b></p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    sumTips: {
      type: Number,
      required: true
    },
    buyersCount: {
      type: Number,
      required: true
    },
    averageTip: {
      type: Number,
      required: true
    },
    revenue: {
      type: Number,
      required: true
    },
    mastersCount: {
      type: Number,
      required: true
    },
    percentageWithTips: {
      type: Number,
      required: true
    }
  }
}
</script>
