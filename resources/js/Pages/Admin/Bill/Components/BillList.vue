<template>
  <div class="admin-bill__block">
    <div class="admin-bill__list">
      <ul>
        <li v-for="item in items" :key="item.title">
          <span class="admin-bill__title">{{ item.title }}</span>
          <span>{{ item.price }}</span>
          <span>{{ item.quantity }}</span>
          <span><b>{{ item.amount }}</b></span>
        </li>
      </ul>
      <p>{{ total }} <span>₽</span></p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminBillList',
  props: {
    items: {
      type: Array,
      required: true,
    },
    total: {
      type: Number,
      required: true,
    },
  },
};
</script>
