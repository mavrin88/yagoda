<template>
  <div class="admin">
    <Head :title="meta.title" />
    <div class="admin__container">
      <div class="admin-masters">
        <div class="admin-masters__top">
<!--          <span class="back" @click="closeMasterShift()">назад</span>-->
          <img class="line-icon" @click="closeMasterShift" src="/img/icon_line.svg" alt="">
          <div></div>
          <h2>Сотрудники в смене</h2>
          <img class="close-icon" @click="closeMasterShift" src="/img/icon_close.svg" alt="">
        </div>

        <Masters :masters="masters" @selected-masters-count="handleSelectedMastersCount"/>
<!--        <Administrators :administrators="administrators" @selected-administrators-count="handleSelectedAdministratorsCount"/>-->
        <Employee :employees="employees" @selected-employees-count="handleSelectedEmployeeCount"/>
      </div>
    </div>
    <div class="fixed-bottom">
<!--      <button @click="saveMasters()" class="btn btn_w100">Сохранить</button>-->
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Masters from '../../MastersShift/Components/Masters.vue';
import Administrators from '../../MastersShift/Components/Administrators.vue'
import Employee from '../../MastersShift/Components/Employee.vue'

export default {
  components: {
    Head,
    Employee,
    Administrators,
    Masters,
    Link
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
      // this.$emit('closeMasterShift')
    },
    closeMasterShift() {
      this.$emit('closeMasterShift')
    }
  },
};
</script>

<style scoped>

.admin-masters__top h2 {
  font-size: 16px;
  font-weight: 300;
  margin-right: -22px;
  padding: 0;
  text-transform: uppercase;
}

.close-icon {
  cursor: pointer;
}

.line-icon {
  cursor: pointer;
}
</style>
