<template>
  <div class="admin">
    <div class="admin__container">
      <div class="admin-masters">
        <div class="admin-masters__top">
          <Link class="back" href="/">назад</Link>
        </div>

        <Masters :masters="masters" @selected-masters-count="handleSelectedMastersCount"/>
        <Administrators :administrators="administrators" @selected-administrators-count="handleSelectedAdministratorsCount"/>
        <Employee :employees="employees" @selected-employees-count="handleSelectedEmployeeCount"/>
      </div>
    </div>
    <div class="fixed-bottom">
      <button @click="saveMasters()" class="btn btn_w100">Сохранить</button>
    </div>
  </div>
</template>

<script>
import Masters from './Components/Masters.vue';
import Administrators from './Components/Administrators.vue';
import Employee from './Components/Employee.vue';
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
    Masters,
    Administrators,
    Employee
  },
  props: {
    masters: Array,
    administrators: Array,
    employees: Array,
  },
  data() {
    return {
      selectedMasters: this.masters.filter(master => master.shift),
      selectedAdministrators: this.administrators.filter(master => master.shift),
      selectedEmployees: this.employees.filter(master => master.shift),
    };
  },
  methods: {
    handleSelectedMastersCount(selectedMasters) {
      this.selectedMasters = selectedMasters;
    },

    handleSelectedAdministratorsCount(selectedAdministrators) {
      this.selectedAdministrators = selectedAdministrators;
    },

    handleSelectedEmployeeCount(selectedEmployees) {
      this.selectedEmployees = selectedEmployees;
    },

    saveMasters() {
      this.$inertia.post('/admin/save_masters_shift', {
        masters: this.selectedMasters,
        administrators: this.selectedAdministrators,
        employees: this.selectedEmployees
      });
      this.$toast.open({
        message: 'Смена успешно сохранена',
        type: 'success',
        position: 'top-right',
        duration: 2000,
      });
    }
  },
};
</script>
