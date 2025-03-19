<template>
  <div class="admin-qr__static">QR <b>{{ staticQr }}</b></div>
</template>

<script>
export default {
  props: {
    staticQr: {
      type: String,
      required: true,
    },
  },
};
</script>
