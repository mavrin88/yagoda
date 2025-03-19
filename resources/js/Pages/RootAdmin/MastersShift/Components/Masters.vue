<template>
  <div class="admin-masters__top">
    <span><b>МАСТЕРА</b></span>
    <p class="admin-masters__count">в смене: <b>{{ selectedMasters.length }}</b></p>
  </div>

  <div class="admin-masters__list">
    <div v-for="(master, index) in masters" :key="index">
      <input :checked="master.shift" type="checkbox" :id="index" :value="master" @change="updateSelectedMasters(index, $event)">
      <label class="admin-masters__item" :for="index">
        <div class="admin-masters__avatar">
          <img :src="master.avatar" />
        </div>
        <div class="admin-masters__name">{{ master.first_name }}</div>
      </label>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: { Link },
  props: {
    masters: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedMasters: this.masters.filter(master => master.shift)
    };
  },
  methods: {
    updateSelectedMasters(index, event) {
      if (event.target.checked) {
        this.selectedMasters.push(this.masters[index]);
      } else {
        this.selectedMasters = this.selectedMasters.filter(master => master.id !== this.masters[index].id);
      }

      this.$emit('selected-masters-count', this.selectedMasters);
    },
  },
};
</script>

<style scoped>

.admin-masters__count {
  margin-left: auto;
  text-align: right;
}

.admin-masters__list {
  margin-bottom: 3rem;
}
</style>
