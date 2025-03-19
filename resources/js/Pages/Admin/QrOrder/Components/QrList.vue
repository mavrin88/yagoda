<template>
  <div class="admin-qr__list">
    <div v-for="item in qrItems" :key="item.name" class="admin-qr__item">
      <div class="admin-qr__count">{{ item.count }}</div>
      <div class="admin-qr__name">{{ item.name }}</div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    qrItems: {
      type: Array,
      required: true,
    },
  },
};
</script>
