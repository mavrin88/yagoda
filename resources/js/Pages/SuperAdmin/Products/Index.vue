<template>
  <div class="superadmin">
    <Head :title="category.name" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/super_admin/categories">назад</Link>
        <h2>{{ category.name }}</h2>
      </div>
      <div class="superadmin-category">
        <Category :category="category"/>
        <ProductsList :products="category.products"/>
        <div class="superadmin-category__add">
          <button class="btn btn_add btn_light btn_w100" @click="goToAddProduct">Добавить</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import ProductsList from './Components/ProductsList.vue'
import Category from './Components/Category.vue'

export default {
  components: {
    Head,
    Link,
    ProductsList,
    Category
  },
  data() {
    return {

    };
  },
  methods: {
    goToAddProduct() {
      this.$inertia.visit(`/super_admin/add_product/${this.category.id}`);
    },
  },
  props: {
    auth: Object,
    category: Object,
    products: Object
  },
}
</script>
