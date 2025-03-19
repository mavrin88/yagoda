<template>
  <div class="admin-services__list">
    <div v-for="service in services" :key="service.name" :class="['admin-services__item', {'admin-services__item_active': service.active}]">
      <div class="admin-services__icon"><img :src="service.icon"></div>
      <div class="admin-services__name">{{ service.name }}</div>
      <div class="admin-services__right">
        <div class="admin-services__price">{{ service.price }} <span>₽</span></div>
        <div class="admin-services__count">{{ service.quantity }}</div>
      </div>
      <div :class="['admin-services__action', {'admin-services__action_active': service.active}]">
        <div class="admin-services__minus" @click="decrementService(service)"></div>
        <div class="admin-services__input">
          <input type="number" v-model.number="service.quantity" @input="updateService(service)">
        </div>
        <div class="admin-services__plus" @click="incrementService(service)"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    services: {
      type: Array,
      required: true,
    },
  },
  methods: {
    decrementService(service) {
      // Реализуйте логику уменьшения количества услуги
      service.quantity--;
      this.$emit('updateService', service);
    },
    incrementService(service) {
      // Реализуйте логику увеличения количества услуги
      service.quantity++;
      this.$emit('updateService', service);
    },
    updateService(service) {
      // Реализуйте логику обновления услуги при изменении ввода
      this.$emit('updateService', service);
    },
  },
};
</script>
