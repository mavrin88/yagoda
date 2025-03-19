<template>
  <div class="admin-masters__list">
    <div v-for="(master, index) in masters" :key="index">
      <input type="checkbox" :id="index">
      <label class="admin-masters__item" :for="index">
        <div class="admin-masters__avatar">
          <img :src="master.avatar" :alt="master.name" />
        </div>
        <div class="admin-masters__name">{{ master.name }}</div>
      </label>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AdminMasters',
  props: {
    masters: {
      type: Array,
      required: true,
    },
  },
};
</script>
