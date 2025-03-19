<template>
  <div class="admin-choose-masters__bottom">
    <div class="admin-choose-masters__info">
      <div class="admin-choose-masters__items">Позиций: <span>{{ positions }}</span></div>
      <div class="admin-choose-masters__total">Позиций: <span>{{ total }}</span></div>
    </div>
    <div class="admin-choose-masters__next">
      <button class="btn btn_arrow">Мастеров: {{ mastersCount }}</button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminBillFooter',
  props: {
    positions: {
      type: String,
      required: true,
    },
    total: {
      type: String,
      required: true,
    },
    mastersCount: {
      type: String,
      required: true,
    },
  },
};
</script>
