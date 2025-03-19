<template>
  <div class="admin">
    <Head :title="meta.title" />
    <div class="admin__container">
      <div class="admin-masters">
        <div class="admin-masters__top">
          <img class="line-icon cursor-pointer font-light" @click="closeMasterShift" src="/img/icon_line.svg" alt="">
          <div></div>
          <h2 class="font-light">Сотрудники в смене</h2>
          <img class="close-icon" @click="closeMasterShift" src="/img/icon_close.svg" alt="">

<!--          <Link class="back" href="/">назад</Link>-->
        </div>

        <Masters :masters="filteredMasters" @selected-masters-count="handleSelectedMastersCount"/>
<!--        <Administrators :administrators="administrators" @selected-administrators-count="handleSelectedAdministratorsCount"/>-->
        <Employee :employees="filteredEmployees" @selected-employees-count="handleSelectedEmployeeCount"/>
      </div>
    </div>
    <div class="fixed-bottom">
<!--      <button @click="saveMasters()" class="btn btn_w100">Сохранить</button>-->
    </div>
  </div>
</template>

<script>
import Masters from './Components/Masters.vue';
import Administrators from './Components/Administrators.vue';
import Employee from './Components/Employee.vue';
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
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
    }
  },
  methods: {
    handleSelectedMastersCount(selectedMasters) {
      this.selectedMasters = selectedMasters;
      this.saveMasters()
    },

    handleSelectedAdministratorsCount(selectedAdministrators) {
      this.selectedAdministrators = selectedAdministrators;
    },

    handleSelectedEmployeeCount(selectedEmployees) {
      this.selectedEmployees = selectedEmployees;
      this.saveMasters()
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

      if (typeof ym === 'function') {
        ym(98232814, 'reachGoal', 'staff_shift');
      }
    },

    closeMasterShift() {
      this.$inertia.visit('/');
    }
  },
};
</script>
