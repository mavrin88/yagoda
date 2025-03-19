<template>
  <div class="admin-orders__item">
    <div class="admin-orders__line">
      <div class="admin-orders__price">{{ item.price }} ₽</div>
      <div class="admin-orders__master">{{ item.master }}</div>
      <div class="admin-orders__tips">{{ item.tips }} ₽</div>
      <div :class="['admin-orders__status', `admin-orders__status_${item.status}`]"></div>
    </div>
    <!--todo: Тут вставь названия всех услуг по одной в <p>-->
    <div class="admin-orders__services" v-if="item.services">
      <p>Название услуги</p>
      <p>Название второй услуги</p>
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
};
</script>
