<template>
  <div class="admin-qr__total">
    <p>к оплате: <b>{{ total }}</b> ₽</p>
    <p>мастер: <b>{{ master }}</b></p>
  </div>
</template>

<script>
export default {
  props: {
    total: {
      type: Number,
      required: true,
    },
    master: {
      type: String,
      required: true,
    },
  },
};
</script>
