<template>
  <div class="admin-bill__block">
    <div class="admin-bill__master">
      <div class="admin-bill__user">
        <div class="admin-bill__avatar"><img :src="user.avatar"></div>
        <div class="admin-bill__name">{{ user.name }}</div>
      </div>
      <div class="admin-bill__tips">{{ tips }} ₽</div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminBillMaster',
  props: {
    user: {
      type: Object,
      required: true,
    },
    tips: {
      type: Number,
      required: true,
    },
  },
};
</script>
