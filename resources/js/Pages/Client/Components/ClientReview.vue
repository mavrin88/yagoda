<template>
  <div class="client-review" :style="{ display: isVisible ? 'block' : 'none' }">
    <div class="client-review__logo">
<!--      <img src="/img/demo/logo_persona.svg" alt="Logo">-->
    </div>
    <p>Оставьте отзыв о <a href="#">заведении</a></p>
    <ClientRating />
    <div class="client-review__textarea">
      <textarea v-model="reviewText"></textarea>
    </div>
    <button class="btn btn_small btn_f-b" @click="submitReview">отправить</button>
  </div>
</template>

<script>
import ClientRating from './ClientRating.vue'

export default {
  components: {
    ClientRating
  },
  data() {
    return {
      isVisible: false,
      reviewText: ''
    }
  },
  methods: {
    showReview() {
      this.isVisible = true
    },
    hideReview() {
      this.isVisible = false
    },
    submitReview() {
      // Здесь можно добавить логику отправки отзыва
      console.log('Review submitted:', this.reviewText)
      this.reviewText = ''
    }
  }
}
</script>
