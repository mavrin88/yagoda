<template>
  <div class="modal" :class="{ 'js-active': isActive }" @click.self="close">
    <div class="modal__body">
      <div class="modal__close" @click="close"></div>
      <div class="modal__content">
        <div class="modal__text modal__text_center">
            <div class="price_block">
<!--              <span class="product-price">{{ product.price }}</span>-->
                <input
                  v-model="product.price"
                  type="text"
                  inputmode="numeric"
                  class="product-price"
                  ref="inputField"
                  @input="updateInput"
                />
              <div class="spacer"></div>
            </div>
          <div class="product-name">{{ product.name }}</div>
        </div>
        <div class="modal__bottom">
          <button class="btn btn_text" @click="confirm">{{ messageButton }}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    isActive: {
      type: Boolean,
      default: false
    },
    product: {
      type: Object,
    },
    messageButton: {
      type: String,
      default: 'УСТАНОВИТЬ'
    },
  },
  data() {
    return {
      updatedPrice: this.product.price || '',
    }
  },
  mounted() {
    if (this.isActive) {
      this.focusInput();
    }
  },
  methods: {
    updateInput(event) {
      this.updatedPrice = Number(event.target.value);
    },
    confirm() {
      const updatedProduct = {
        ...this.product,
        new_price: this.updatedPrice,
      };
      this.$emit('confirmed', updatedProduct);
      this.close();
    },
    close() {
      this.$emit('close');
    },
    focusInput() {
      this.$nextTick(() => {
        this.$refs.inputField.focus();
      });
    },
  },
  watch: {
    isActive(newValue) {
      if (newValue && this.$refs.inputField) {
        this.focusInput();
      }
    }
  },
};
</script>

<style>
.price_block {
  max-width: 343px;
  margin: 0 auto;
  padding: 10px 0;
  border-bottom: 1px solid #9CA4B5;
  text-align: center;
}
.product-price {
  font-size: 36px;
  font-weight: 400;
  line-height: 43.88px;
  width: 100%;
  border: none;
  outline: none;
  text-align: center;
}
.product-name {
  padding-top: 10px;
  font-size: 14px;
  font-weight: 400;
  line-height: 17.07px;
  color: #000000;
}
.product-price:focus {
  outline: none; /* Убираем обводку при фокусе */
  box-shadow: none; /* Убираем тень при фокусе */
}
.product-price[type="number"] {
  -moz-appearance: textfield; /* Для Firefox */
}
</style>

