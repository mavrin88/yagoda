<!--<template>-->
<!--  <div class="client__item">-->
<!--    <div v-if ="product.image" class="client__icon">-->
<!--      <img :src="product.image">-->
<!--    </div>-->
<!--    <div v-if ="!product.image" class="client__icon__none"></div>-->
<!--    <div class="client__name">{{ product.name }}</div>-->
<!--    <div class="client__price" v-if="product.quantity > 1"><b>{{ product.quantity }}</b> x {{ $formatNumber(discountedPrice) }} <span>₽</span></div>-->
<!--    <div class="client__price" v-else>{{ $formatNumber(discountedPrice) }} <span>₽</span></div>-->
<!--  </div>-->
<!--</template>-->

<template>
  <div class="client__item">
    <div v-if="product.image" class="client__icon">
      <img :src="product.image" />
    </div>
    <div v-if="!product.image" class="client__icon__none"></div>
    <div class="client__name">{{ product.name }}</div>

    <div class="client__price" v-if="itemsTotal === 1 && product.quantity == 1"><span class="weight_price">{{ product.price }}</span> <span>₽</span></div>
    <div class="client__price" v-else><b v-if="product.quantity > 1">{{ product.quantity }}</b> <span v-if="product.quantity > 1">x</span> {{ product.price }} <span>₽</span></div>
  </div>
</template>


<script>
export default {
  props: {
    product: {
      type: Object,
      required: true
    },
    discount: {
      type: [Number, String],
      default: '0'
    },
    itemsTotal: {
      type: Number,
      default: 0
    }
  },
  computed: {
    discountedPrice() {
      const discountValue = parseFloat(this.discount) || 0;
      const priceAfterDiscount = this.product.price * ((100 - discountValue) / 100);
      const flooredPrice = Math.floor(priceAfterDiscount * 100) / 100; // Округляем в меньшую сторону до двух десятичных знаков
      return flooredPrice; // Форматируем до двух знаков после запятой
    }
  }
}
</script>

<style scoped>
.client__name {
  font-size: 14px;
  font-weight: 600;
  max-width: 196px;
  word-break: break-all;
}

.weight_price {
  font-size: 14px;
  font-weight: bold;
  color: black;
}

.client__icon__none {
  padding: 1.6rem 0.1rem 0.875rem 0.875rem;
}
</style>
