<template>
  <div class="admin-qr__link"><span>{{ link }}</span>
    <div class="admin-qr__share" title="Копировать" @click="copyLink"></div>
  </div>
</template>

<script>
export default {
  props: {
    link: {
      type: String,
      required: true,
    },
  },
  methods: {
    copyLink() {
      this.$emit('copyLink');
    },
  },
};
</script>
