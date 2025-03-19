<template>
  <div class="admin-masters">
    <div class="admin-masters__top">
      <span><b>АДМИНИСТРАТОРЫ</b></span>
      <p class="admin-masters__count">в смене: <b>{{ selectedAdministrators.length }}</b></p>
    </div>

    <div class="ytips-text ytips-text_padding ytips-text_12">Часть от чаевых будет равномерно распределена на Администраторов находящихся в смене.</div>

    <div v-if="showError" class="error-message">{{ errorMessage }}</div>

    <div class="empty_masters" v-if="administrators.length === 0">
      <p class="text-red-600 text-center"><b>Сотрудники с ролью АДМИНИСТРАТОР отсутствуют!</b></p>
      <Link href="/tips/administrators">
        <div class="underline text-center">ДОБАВИТЬ АДМИНИСТРАТОРОВ</div>
      </Link>
    </div>

    <br>
    <div class="admin-masters__list" v-if="administrators.length > 0">
      <div v-for="(administrator, index) in administrators" :key="index">
        <input :checked="administrator.shift" type="checkbox" :id="index + 'admin'" :value="administrator" @change="updateSelectedMasters(index, $event)">
        <label class="admin-masters__item" :for="index + 'admin'">
          <div class="admin-masters__avatar">
            <img :src="administrator.photo_path || '/img/content/icon_avatar.svg'">
          </div>
          <div class="admin-masters__name">{{ administrator.first_name }}</div>
          <span v-if="!administrator.first_name">Без имени</span>
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
    administrators: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      selectedAdministrators: this.administrators.filter(administrator => administrator.shift),
      showError: false,
      errorMessage: 'Не более 5 человек в смене'
    };
  },
  methods: {
    updateSelectedMasters(index, event) {
      if (event.target.checked) {

        if (this.selectedAdministrators.length > 5) {
          this.showError = true;
          setTimeout(() => {
            this.showError = false;
          }, 2000);
          event.target.checked = false;
          return;
        }

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
.admin-masters__avatar img{
  border-radius: 0.7rem;
}

.error-message {
  color: red;
  margin: 10px;
  font-size: 12px;
}
</style>
