<template>
  <div class="list-standard">
    <div class="list-standard__item"
         v-for="product in products"
         :key="product.id"
         @click="editProduct(product.id)"
         c
    >
      <div class="list-standard__image" v-if="product.image">
        <img v-if="product.image" :src="product.image">
        <img v-else :src="'/img/content/icon_service.svg'">
      </div>
      <div class="list-standard__title">{{ product.name }}</div>
      <div class="list-standard__right">
        <div class="list-standard__price">{{ product.price }} <span>₽</span></div>
      </div>
      <div class="list-standard__action">
        <div class="list-standard__edit"></div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link
  },
  data() {
    return {

    };
  },
  methods: {
    editProduct(id) {
      this.$inertia.visit(`/super_admin/edit_product/${id}`);
    }
  },
  props: {
    products: Object
  },
}
</script>

<style scoped>
.padding_product{
  padding: 1.6rem 0.875rem 1.6rem 0.875rem;
}

.list-standard__image img {
  max-width: 42px;
  height: auto;
  max-height: 42px;
  border-radius: 0.6  rem;
}
</style>
