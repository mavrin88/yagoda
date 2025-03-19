<template>
  <div class="admin-masters__top">
    <span><b>АДМИНИСТРАТОРЫ</b></span>
    <p class="admin-masters__count">в смене: <b>{{ selectedAdministrators.length }}</b></p>
  </div>

  <div class="admin-masters__list">
    <div v-for="(administrator, index) in administrators" :key="index">
      <input :checked="administrator.shift" type="checkbox" :id="index + 'admin'" :value="administrator" @change="updateSelectedMasters(index, $event)">
      <label class="admin-masters__item" :for="index + 'admin'">
        <div class="admin-masters__avatar">
          <img :src="administrator.avatar" />
        </div>
        <div class="admin-masters__name">{{ administrator.first_name }}</div>
      </label>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: { Link },
  props: {
    administrators: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedAdministrators: this.administrators.filter(administrator => administrator.shift)
    };
  },
  methods: {
    updateSelectedMasters(index, event) {
      if (event.target.checked) {
        this.selectedAdministrators.push(this.administrators[index]);
      } else {
        this.selectedAdministrators = this.selectedAdministrators.filter(administrator => administrator.id !== this.administrators[index].id);
      }

      this.$emit('selected-administrators-count', this.selectedAdministrators);
    },
  },
};
</script>

<style scoped>

.admin-masters__top {
  display: flex;
  margin-bottom: 1.25rem;
  align-items: center;
  justify-content: space-between;
}

.admin-masters__count {
  margin-left: auto;
  text-align: right;
}

.admin-masters__list {
  margin-bottom: 3rem;
}
</style>
