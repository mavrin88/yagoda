<template>
  <div class="admin-bill__block">
    <div class="admin-bill__total">
      <div>Скидка (%): <b>{{ discount }}</b></div>
      <div><b>{{ discountedTotal }}</b><span>₽</span></div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminBillDiscount',
  props: {
    discount: {
      type: Number,
      required: true,
    },
    discountedTotal: {
      type: Number,
      required: true,
    },
  },
};
</script>
