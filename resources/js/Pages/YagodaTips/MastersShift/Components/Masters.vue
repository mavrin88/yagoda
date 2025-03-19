<template>
  <div class="admin-masters">
    <div class="admin-masters__top">
      <span><b>МАСТЕРА</b></span>
      <p class="admin-masters__count">в смене: <b>{{ selectedMasters.length }}</b></p>
    </div>

    <div class="ytips-text ytips-text_padding ytips-text_12">Мастера в смене будут отображены на <u>странице выбора Мастеров</u> для перечисления чаевых гостями.</div>


    <div class="empty_masters" v-if="masters.length === 0">
      <p class="text-red-600 text-center"><b>Сотрудники с ролью МАСТЕР отсутствуют!</b></p>
      <Link href="/tips/masters">
        <div class="underline text-center">ДОБАВИТЬ МАСТЕРОВ</div>
      </Link>
    </div>

    <br>
    <div class="admin-masters__list" v-if="masters.length > 0">
      <div v-for="(master, index) in masters" :key="index">
        <input :checked="master.shift" type="checkbox" :id="index" :value="master" @change="updateSelectedMasters(index, $event)">
        <label class="admin-masters__item" :for="index">
          <div class="admin-masters__avatar">
            <img :src="master.photo_path || '/img/content/icon_avatar.svg'">
          </div>
          <div class="admin-masters__name">{{ master.first_name }}</div>
          <span v-if="!master.first_name">Без имени</span>
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

<style scoped lang="scss">

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
</style>
