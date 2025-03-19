<template>
  <div class="client__rates">
    <div class="client-result__rates">
      <input
        id="star5"
        type="radio"
        name="rating"
        value="5"
        v-model="rating"
        @change="setRating"
      >
      <label class="star" for="star5" title="Awesome" aria-hidden="true"></label>

      <input
        id="star4"
        type="radio"
        name="rating"
        value="4"
        v-model="rating"
        @change="setRating"
      >
      <label class="star" for="star4" title="Great" aria-hidden="true"></label>

      <input
        id="star3"
        type="radio"
        name="rating"
        value="3"
        v-model="rating"
        @change="setRating"
      >
      <label class="star" for="star3" title="Very good" aria-hidden="true"></label>

      <input
        id="star2"
        type="radio"
        name="rating"
        value="2"
        v-model="rating"
        @change="setRating"
      >
      <label class="star" for="star2" title="Good" aria-hidden="true"></label>

      <input
        id="star1"
        type="radio"
        name="rating"
        value="1"
        v-model="rating"
        @change="setRating"
      >
      <label class="star" for="star1" title="Bad" aria-hidden="true"></label>
    </div>
  </div>

  <div v-if="rating > 0" class="client__textarea">
    <textarea @blur="handleBlur" v-model="textContent" placeholder="Оставьте отзыв..."></textarea>
  </div>
</template>

<script>
export default {
  data() {
    return {
      rating: 0,
      textContent: '',
    };
  },
  methods: {
    setRating() {
      this.$emit('rating', this.rating, this.textContent);
    },
    handleBlur() {
      this.$emit('rating', this.rating, this.textContent);
    }
  }
};
</script>

<style scoped>
.client__rates {
  display: block;
}
</style>

