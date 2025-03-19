<template>
  <div class="admin-bill__bottom">
    <div class="admin-bill__date">{{ date }}</div>
    <div :class="['admin-bill__status', `admin-bill__status_${status.toLowerCase()}`]">{{ statusName }}</div>
  </div>
</template>

<script>
export default {
  name: 'AdminBillFooter',
  props: {
    date: {
      type: String,
      required: true,
    },
    statusName: {
      type: String,
      required: true,
    },
    status: {
      type: String,
      required: true,
    },
  },
};
</script>
