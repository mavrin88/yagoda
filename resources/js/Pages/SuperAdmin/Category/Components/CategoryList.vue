<template>
  <div class="superadmin-catalog__list">
    <div class="superadmin-catalog__item" v-for="category in categories" :key="category.id" @click="goToCategory(category.id)">
      <div class="superadmin-catalog__image img-w-text">
        <img :src="category.image">
        <span>{{ category.name }}</span>
      </div>
      <div class="superadmin-catalog__name">{{ category.name }}</div>
      <div class="superadmin-catalog__right">
        <div v-if="category.parent_category_id" class="superadmin-catalog__sync"></div>
        <div @click.stop="editCategory(category.id)" class="superadmin-catalog__settings"></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {

    };
  },
  methods: {
    goToCategory(categoryId) {
      this.$inertia.visit(`category_products/${categoryId}`);
    },
    editCategory(categoryId) {
      this.$inertia.visit(`/super_admin/edit_category/${categoryId}`);
    },
  },
  props: {
    categories: Object
  },
}
</script>
