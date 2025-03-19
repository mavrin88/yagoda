<template>
  <div class="admin-qr__code"><img :src="qrImage"></div>
</template>

<script>
export default {
  props: {
    qrImage: {
      type: String,
      required: true,
    },
  },
};
</script>
