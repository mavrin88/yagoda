<template>
  <div class="admin-orders__item" @click="openSingle(item.id)">
    <div class="admin-orders__line">
      <div class="admin-orders__price">{{ item.price }} ₽</div>
      <div class="admin-orders__master">{{ item.master }}</div>
      <div class="admin-orders__tips">{{ item.tips }} ₽</div>
      <div :class="['admin-orders__status', `admin-orders__status_${item.status}`]"></div>
    </div>
    <div class="admin-orders__services">
      <p v-for="item in item.items" :key="item.product_name">{{ item.product_name }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    item: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {};
  },
  methods: {
    openSingle(id) {
      this.$emit("showOrder", id);
    }
  },
};
</script>ы

<style scoped>
.admin-orders__item {
  cursor: pointer;
}
</style>
