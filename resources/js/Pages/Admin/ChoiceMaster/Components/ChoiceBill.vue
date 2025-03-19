<template>
  <div class="admin-bill">
    <div class="admin-bill__block">
      <div class="admin-bill__list">
        <ul>
          <li v-for="item in billItems" :key="item.title">
            <span class="admin-bill__title">{{ item.title }}</span>
            <span>{{ item.price }}</span>
            <span>{{ item.quantity }}</span>
            <span><b>{{ item.amount }}</b></span>
          </li>
        </ul>
        <p>{{ total }} <span>₽</span></p>
      </div>
    </div>
    <div class="admin-bill__block">
      <div class="admin-bill__total">
        <div>Скидка (%): <b>{{ discount }}</b></div>
        <div><b>{{ discountedTotal }}</b><span>₽</span></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    billItems: {
      type: Array,
      required: true,
    },
    total: {
      type: Number,
      required: true,
    },
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
