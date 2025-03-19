<template>
  <div class="admin-orders__block">
    <div class="admin-bill__list">
      <ul>
        <li v-if="orders" v-for="order in orders">
          <span class="order-product-name">{{ order.name }} ({{order.product_id}})</span>
          <span class="order-product-value1">{{ order.count_items }}</span>

          <span class="order-product-value2">
            <span class="order-value">{{ order.product_price }}</span>
            <span class="currency">₽</span>
          </span>
        </li>
      </ul>

    </div>
  </div>
</template>
<script>
export default {
  props: {
    orders: {
      type: Array,
      required: true,
    },
  },

  data() {
    return {
      showMasters: false,
    }
  },

  methods: {
    toggleMasters() {
      this.showMasters = !this.showMasters
    },
  },
}
</script>

<style scoped lang="scss">
.admin-orders__block {
  margin-top: 15px;
}

.admin-bill__list {
  ul {
    li {
      justify-content: flex-start;
      .order-product-name {
        white-space: normal;
        background-color: transparent;
        min-width: 200px;
        max-width: 200px;
      }
      .order-product-value1 {
        min-width: 27px;
        text-align: center;
      }
      .order-product-value2 {
        margin-left: auto;
      }
    }
  }
}



.admin-bill__list span {
  background-color: transparent;
}

.order-product-value2 {
  background-color: transparent;
  display: inline-flex;
  align-items: baseline;
}

.order-value {
  font-weight: 600;
}

.currency {
  margin-left: 2px;
}
</style>

