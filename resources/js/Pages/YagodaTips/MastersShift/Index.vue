<template>
  <div class="admin">
    <Head :title="meta.title" />
    <TipsHeader />

    <div class="ytips__container">

      <div class="admin-masters__top">
        <img class="line-icon cursor-pointer font-light" @click="closeMasterShift" src="/img/icon_line.svg" alt="">
        <div></div>
        <h2 class="font-light">Сотрудники в смене</h2>
      </div>

      <Masters :masters="filteredMasters" @selected-masters-count="handleSelectedMastersCount"/>
      <Administrators :administrators="filteredAdministrators" @selected-administrators-count="handleSelectedAdministratorsCount"/>
      <Employee :employees="filteredEmployees" @selected-employees-count="handleSelectedEmployeeCount"/>

    </div>
  </div>
</template>

<script>
import Masters from './Components/Masters.vue';
import Administrators from './Components/Administrators.vue';
import Employee from './Components/Employee.vue';
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
    Head,
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
      meta: {
        title: 'Сотрудники в смене'
      },
      selectedMasters: this.masters.filter(master => master.shift),
      selectedAdministrators: this.administrators.filter(master => master.shift),
      selectedEmployees: this.employees.filter(master => master.shift),
    };
  },
  computed: {
    filteredMasters() {
      return this.masters.filter(master => master.status !== 'deleted');
    },
    filteredEmployees() {
      return this.employees.filter(employee => employee.status !== 'deleted');
    },
    filteredAdministrators() {
      return this.administrators.filter(administrator => administrator.status !== 'deleted');
    }
  },
  methods: {
    handleSelectedMastersCount(selectedMasters) {
      this.selectedMasters = selectedMasters;
      this.saveMasters()
    },

    handleSelectedAdministratorsCount(selectedAdministrators) {
      this.selectedAdministrators = selectedAdministrators;
      this.saveMasters()
    },

    handleSelectedEmployeeCount(selectedEmployees) {
      this.selectedEmployees = selectedEmployees;
      this.saveMasters()
    },

    saveMasters() {
      this.$inertia.post('/tips/save_masters_shift', {
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
    },

    closeMasterShift() {
      this.$inertia.visit('/tips/coord');
    }
  },
};
</script>
<style lang="scss" scoped>
.admin {
  padding-top: 0;
}
.admin-masters {
  padding-bottom: 50px;
  background: none;
  &__top {
    margin-top: 20px;
  }
}
</style>
