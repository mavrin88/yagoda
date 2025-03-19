<template>
  <div class="ytips">
    <TipsHeader />
    <Head :title="meta.title" />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="back" href="/tips/coord">назад</Link>
        <div class="ytips-top__title">УЧАСТНИКИ ГРУППЫ</div>
      </div>
    </div>

    <div class="ytips__container">
      <div class="admin__container">
        <div class="admin__links">
          <Link href="/tips/administrators">
            <div>АДМИНИСТРАТОРЫ <span v-if="workforceData && workforceData.administrators">({{ workforceData.administrators }})</span></div>
          </Link>
          <Link href="/tips/masters">
            <div>МАСТЕРА <span v-if="workforceData && workforceData.masters">({{ workforceData.masters }})</span></div>
          </Link>
          <Link href="/tips/staff">
            <div>ПЕРСОНАЛ <span v-if="workforceData && workforceData.staff">({{ workforceData.staff }})</span></div>
          </Link>
        </div>

        <div class="fs12">
          Для возможности распределять чаевые на сотрудников, добавьте их в подгруппы Мастера, Администраторы, Персонал.
          После чего установите пропорции в которых будут распределяться чаевые. Перейдите в раздел ЧАЕВЫЕ РАСПРЕДЕЛЕНИЕ.
        </div>

        <br>
        <br>

        <Link href="/tips/tip_distribution">ЧАЕВЫЕ РАСПРЕДЕЛЕНИЕ</Link>
      </div>
    </div>
  </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import 'vue-select/dist/vue-select.css'
import TipsHeader from '../Components/TipsHeader.vue'
import axios from 'axios';

export default {
  components: {
    TipsHeader,
    Head,
    Link
  },
  data() {
    return {
      meta: {
        title: 'Участники группы',
      },
      workforceData: null,
      isLoading: false,
      error: null,
    }
  },
  mounted() {
    this.getWorkforce();
  },
  computed: {

  },
  methods: {
    goBack() {
      this.$emit('goBack')
    },
    async getWorkforce() {
      this.isLoading = true;
      this.error = null;

      try {
        const response = await axios.get('/tips/get_workforce');

        this.workforceData = response.data;
      } catch (error) {
        this.error = error.response?.data?.message || 'Произошла ошибка при загрузке данных';
        console.error('Ошибка при загрузке статистики:', error);
      } finally {
        this.isLoading = false;
      }
    },
  },
  watch: {

  },
}
</script>
