<template>
  <div class="superadmin" v-if="mainShow && !arbitraryAmount">
    <Head title="Услуги и товары" />
    <div class="admin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>УСЛУГИ И ТОВАРЫ</h2>
      </div>
      <div class="admin__links">
        <button  @click="goToCategory">КАТАЛОГ</button>
        <button  @click="showArbitraryAmount">ПРОИЗВОЛЬНАЯ СУММА</button>
      </div>
    </div>
  </div>

  <div class="superadmin" v-if="arbitraryAmount && !mainShow">
    <div class="superadmin__container">
      <div class="superadmin__top">
        <button class="back" @click="showMainContent">назад</button>
        <h2>ПРОИЗВОЛЬНАЯ СУММА</h2>
      </div>
      <div class="admin-new-sum">
        <div class="admin-new-sum__field">
          <input
            ref="nameInput"
            v-model="productsName"
            :placeholder="getPlaceholder"
            @focus="onFocus"
            @blur="restorePlaceholder"
          />
          <span v-if="isFocused" class="input-placeholder">{{ products.name }}</span>
          <label>Наименование товара в заказе при вводе произвольной суммы в заказе</label>
        </div>

        <button class="btn" :disabled="isNameInvalid" @click="saveName">Сохранить</button>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import CategoryList from './Components/CategoryList.vue'
import axios from 'axios'
import Spiner from '@/Shared/Spiner.vue'
import OrdersItem from '../../Admin/Orders/Components/OrdersItem.vue'

export default {
  components: {
    Head,
    OrdersItem,
    Spiner,
    Link,
    CategoryList
  },
  props: {
    products: Object,
  },
  data() {
    return {
      mainShow: true,
      arbitraryAmount: false,
      productsName: '',
      isFocused: false,
    };
  },
  computed: {
    isNameInvalid() {
      return this.productsName === ''
    },
    getPlaceholder() {
      return this.isFocused ? '' : (this.products && this.products.name ? this.products.name : 'Услуги/товары');
    }
  },
  watch: {
    arbitraryAmount(newVal) {
      if (newVal) {
        this.productsName = '';
      }
    }
  },
  methods: {
    goToCategory() {
      this.$inertia.visit(`/super_admin/categories`);
    },
    showMainContent() {
      this.mainShow = true
      this.arbitraryAmount = false
    },
    showArbitraryAmount() {
      this.arbitraryAmount = true
      this.mainShow = false
    },
    onFocus() {
      this.isFocused = true;
    },
    restorePlaceholder() {
      this.isFocused = false;
    },
    saveName(){
      axios.post('/api/productsNames', {
        name: this.productsName
      })
        .then((response) => {
          this.$inertia.visit("/");
        })
        .catch((error, response) => {

        })
    }
  },
}
</script>

<style scoped>
input {
  background: none;
  background: transparent;
}
.admin__links {
  display: flex;
  flex-direction: column;
  gap: 10px;
  align-items: flex-start;
}
.admin__links button {
  color: black;
}
.admin-new-sum .btn {
  margin-top: 40px !important;
  position: relative !important;
}
.admin-new-sum__field input {
  text-align: center;
  font-size: 29px;
}
.admin-new-sum__field span{
  top: -28px;
}
</style>
