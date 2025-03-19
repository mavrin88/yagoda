<template>
  <div class="admin-orders__item" @click="openSingle(item.id)">
    <div class="admin-orders__line">
      <div class="admin-orders__price">{{ formatPrice(item.price) }} ₽</div>
      <div class="admin-orders__master">{{ item.master }}</div>
      <div class="admin-orders__tips">{{ item.tips }} ₽</div>
      <div :class="['admin-orders__status', `admin-orders__status_${item.status}`]"></div>
    </div>
    <div class="admin-orders__services">
      <p v-for="item in item.items" :key="item.product_name">{{ item.product_name }}</p>
    </div>
    <div class="admin-orders__comment" v-if="item.comment">
      <p>{{ item.comment.length > 66 ? item.comment.slice(0, 66) + '...' : item.comment }}</p>
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
    },
    formatPrice(price) {
      // Преобразуем число в строку с разделителями
      return parseFloat(price).toLocaleString('ru-RU', {
        minimumFractionDigits: 2, // Всегда два знака после запятой
        maximumFractionDigits: 2
      });
    }
  },
};
</script>ы

<style scoped>
.admin-orders__item {
  cursor: pointer;
}
.admin-orders__line {
  display: flex;
  justify-content: space-between;
}
.admin-orders__price,
.admin-orders__master,
.admin-orders__tips {
  margin-right: 10px;
}
.admin-orders__comment {
  margin-top: 5px;
  padding-right: 8px;
  font-size: 14px;
  font-weight: 700;
  line-height: 17.07px;
  color: #000000;
  display: flex;
  justify-content: flex-end;
  word-wrap: break-word;
  white-space: normal;
  overflow-wrap: break-word;
  max-width: 100%;
  word-break: break-word;
}
</style>
