<template>
  <div class="admin-masters__top">
    <span><b>ПЕРСОНАЛ</b></span>
    <p class="admin-masters__count">в смене: <b>{{ selectedEmployees.length }}</b></p>
  </div>

  <div class="admin-masters__list">
    <div v-for="(employee, index) in employees" :key="index">
      <input :checked="employee.shift" type="checkbox" :id="index + 'employee'" :value="employee" @change="updateSelectedEmployees(index, $event)">
      <label class="admin-masters__item" :for="index + 'employee'">
        <div class="admin-masters__avatar">
          <img :src="employee.avatar" />
        </div>
        <div class="admin-masters__name">{{ employee.first_name }}</div>
      </label>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: { Link },
  props: {
    employees: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedEmployees: this.employees.filter(master => master.shift)
    };
  },
  methods: {
    updateSelectedEmployees(index, event) {
      if (event.target.checked) {
        this.selectedEmployees.push(this.employees[index]);
      } else {
        this.selectedEmployees = this.selectedEmployees.filter(master => master.id !== this.employees[index].id);
      }

      this.$emit('selected-employees-count', this.selectedEmployees);
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
</style>
