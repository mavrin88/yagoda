<template>
  <div class="client__total">
    <span>Итого:</span>
    <b>{{ $formatNumber(total) }} <span>₽</span></b>
  </div>
</template>

<script>
export default {
  props: {
    total: {
      type: [Number, String],
    },
    items: {
      type: Array,
      required: true
    },
    discount: {
      type: [Number, String],
      default: '0'
    }
  },
  computed: {
    totalOld() {
      // Вычисляем сумму до скидки
      const subtotal = this.items.reduce((acc, product) => {
        // Вычисляем цену товара с учетом количества и округляем
        const productTotal = Math.floor(product.price) * product.quantity;
        return acc + productTotal;
      }, 0);

      // Вычисляем скидку
      const discountAmount = (parseFloat(this.discount) / 100) * subtotal;

      // Возвращаем итоговую сумму, округляя до меньшего значения
      return Math.floor(subtotal - discountAmount);
    }
  }

}
</script>
