<template>
  <div class="admin-masters">
    <div class="admin-masters__top">
      <span><b>ПЕРСОНАЛ</b></span>
      <p class="admin-masters__count">в смене: <b>{{ selectedEmployees.length }}</b></p>
    </div>

    <div class="ytips-text ytips-text_padding ytips-text_12">Часть от чаевых будет равномерно распределена на Персонал находящийся в смене.</div>

    <div v-if="showError" class="error-message">{{ errorMessage }}</div>

    <div class="empty_masters" v-if="employees.length === 0">
      <p class="text-red-600 text-center"><b>Сотрудники с ролью ПЕРСОНАЛ отсутствуют!</b></p>
      <Link href="/tips/staff">
        <div class="underline text-center">ДОБАВИТЬ ПЕРСОНАЛ</div>
      </Link>
    </div>

    <br>
    <div class="admin-masters__list" v-if="employees.length > 0">
      <div v-for="(employee, index) in employees" :key="index">
        <input :checked="employee.shift" type="checkbox" :id="index + 'employee'" :value="employee" @change="updateSelectedEmployees(index, $event)">
        <label class="admin-masters__item" :for="index + 'employee'">
          <div class="admin-masters__avatar">
            <img :src="employee.photo_path || '/img/content/icon_avatar.svg'">
          </div>
          <div class="admin-masters__name">{{ employee.first_name }}</div>
          <span v-if="!employee.first_name">Без имени</span>
        </label>
      </div>
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
      selectedEmployees: this.employees.filter(master => master.shift),
      showError: false,
      errorMessage: 'Не более 5 человек в смене'
    };
  },
  methods: {
    updateSelectedEmployees(index, event) {
      if (event.target.checked) {
        if (this.selectedEmployees.length > 5) {
          this.showError = true;
          setTimeout(() => {
            this.showError = false;
          }, 2000);
          event.target.checked = false;
          return;
        }
        this.selectedEmployees.push(this.employees[index]);
      } else {
        this.selectedEmployees = this.selectedEmployees.filter(master => master.id !== this.employees[index].id);
      }

      this.$emit('selected-employees-count', this.selectedEmployees);
    },
  },
};
</script>

<style scoped lang="scss">
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

.admin-masters__avatar img{
  border-radius: 0.7rem;
}

.error-message {
  color: red;
  margin: 10px;
  font-size: 12px;
}
</style>
