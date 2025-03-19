<template>
      <div class="superadmin">
        <Head title="Каталог" />
        <div class="superadmin__container">
          <div class="superadmin__top">
            <button  class="back" @click="showServiceProducts">назад</button>
            <h2>КАТАЛОГ</h2>
          </div>
          <div class="superadmin-catalog">
            <button class="btn btn_w100 btn_light btn_add" @click="goToAddCategory">Добавить раздел каталога</button>
            <br>
<!--            <button class="btn btn_w100 btn_light">Привязать каталог</button>-->
            <CategoryList :categories="filteredCategories"/>
          </div>
        </div>
      </div>
</template>
<script>
import { Head, Link } from '@inertiajs/vue3'
import CategoryList from '../Components/CategoryList.vue'

export default {
  components: {
    Head,
    Link,
    CategoryList
  },
  props: {
    auth: Object,
    categories: Object,
  },
  data() {
    return {
      mainShow: true,
      arbitraryAmount: false,
      form: this.$inertia.form({
        name: '',
        paySum: ''
      }),
    };
  },
  computed: {
    filteredCategories() {
      return this.categories.filter(category => category.hide === 0);
    }
  },
  methods: {
    goToAddCategory() {
      this.$inertia.visit(`/super_admin/add_category`);
    },
    showServiceProducts() {
      this.$inertia.visit(`/super_admin/services_products`);
    },
  },
}
</script>
