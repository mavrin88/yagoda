<template>
  <div class="admin-services__categories">
    <div v-for="category in categories" :key="category.title" :class="['admin-services__category', {'admin-services__category_active': category.active}]" @click="selectCategory(category)">
      <div class="admin-services__image"><img :src="category.image"></div>
      <div class="admin-services__title">{{ category.title }}</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminServicesCategories',
  props: {
    categories: {
      type: Array,
      required: true,
    },
  },
  methods: {
    selectCategory(category) {
      // Реализуйте логику выбора категории
      this.categories.forEach(cat => {
        cat.active = cat === category;
      });
      this.$emit('categoryClick', category);
    },
  },
};
</script>
