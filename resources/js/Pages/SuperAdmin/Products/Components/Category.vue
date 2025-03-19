<template>
  <div class="superadmin-category__top">
    <div class="img-w-text">
      <img :src="category.image">
      <span>{{ category.name }}</span>
    </div>
    <div v-if="category.parent_category_id" class="superadmin-category__sync">связанный<br>раздел</div>
  </div>
</template>

<script>

export default {
  components: {

  },
  data() {
    return {

    };
  },
  methods: {

  },
  props: {
    category: Object
  },
}
</script>
