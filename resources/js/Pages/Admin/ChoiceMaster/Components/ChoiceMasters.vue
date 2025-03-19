<template>
  <div class="admin-masters">
    <div class="admin-masters__total">Мастеров: {{ total }}</div>
    <div class="admin-masters__list">
      <div v-for="master in masters" :key="master.id">
        <input :id="`master${master.id}`" type="checkbox" v-model="selectedMasters">
        <label :for="`master${master.id}`" class="admin-masters__item">
          <div class="admin-masters__avatar"><img :src="master.avatar"></div>
          <div class="admin-masters__name">{{ master.name }}</div>
        </label>
      </div>
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
    total: {
      type: Number,
      required: true,
    },
  },
  data() {
    return {
      selectedMasters: [],
    };
  },
};
</script>
