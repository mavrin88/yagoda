<template>
  <Head title="Статистика" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />

      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/tips/coord"></Link>
          <div class="ytips-top__title">ЧАЕВЫЕ</div>
        </div>
      </div>
      <div class="ytips-h ead">
        <div class="ytips__container">
          <Datepicker class="ytips-head__date" v-model="date" locale="ru" :format="format" auto-apply :clearable="false" position="left"
                      :enable-time-picker="false" @update:model-value="getStatistic" />

          <div class="ytips-head__total">{{ data.data.translations }}  <span>/</span>  <b>{{ data.data.translations_sum }}</b> <span>₽</span></div>
        </div>
      </div>
      <div class="ytips-report">
        <div class="ytips__container">
          <div v-if="data" class="ytips-report__group" v-for="statistic in data.employee" :key="statistic.id">
            <div class="ytips-report__left">
              <div class="ytips-report__item">
                <div class="ytips-report__title">{{ statistic.name }}</div>
                <div class="ytips-report__dots"></div>
                <div class="ytips-report__price">{{ statistic.totalTips }} ₽</div>
              </div>
              <div class="ytips-report__item">
                <div class="ytips-report__title">Общие расходы</div>
                <div class="ytips-report__dots"></div>
                <div class="ytips-report__price">{{ statistic.totalTraffic }} ₽</div>
              </div>
            </div>
            <div class="ytips-report__right">
              <p>{{ statistic.date }}</p>
              <p>{{ statistic.time }}</p>
              <p>
                <img v-if="statistic.payType === 'spb'" src="/img/content/icon_spb.svg">
                <img v-if="statistic.payType === 'card'" src="/img/content/icon_card.svg">
              </p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../../Components/TipsHeader.vue'
import axios from 'axios';
import Datepicker from '@vuepic/vue-datepicker'

export default {
  name: "Index",
  components: { Datepicker, Link, TipsHeader, Head },
  data() {
    return {
      isOpen: false,
      statisticData: null,
      isLoading: false,
      error: null,
      date: new Date(),
      format: this.formatDate,

    }
  },
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
  mounted() {
    // this.getStatistic();
  },
  methods: {
    async getStatistic() {
      this.isLoading = true;
      this.error = null;
      if (this.selectEmployee && this.selectEmployee.id) {
        try {
          const response = await axios.post('/tips/tip_statistic', {
            employeeId: this.selectEmployee.id,
            date: this.date
          });

          this.statisticData = response.data;
        } catch (error) {
          this.error = error.response?.data?.message || 'Произошла ошибка при загрузке данных';
          console.error('Ошибка при загрузке статистики:', error);
        } finally {
          this.isLoading = false;
        }
      }
    },
    formatDate(date) {
      const day = String(date.getDate()).padStart(2, '0')
      const monthNames = ['янв', 'фев', 'мар', 'апр', 'май', 'июн', 'июл', 'авг', 'сен', 'окт', 'ноя', 'дек']
      const month = monthNames[date.getMonth()]
      const year = date.getFullYear()

      return `${day} ${month}.${year}г`
    },

  }
}
</script>
<style lang="scss" scoped>
:deep(.ytips-head__date) {
  flex: 1;
  font-weight: 400;
  .dp__input_wrap {
    div {
      display: none;
    }
    input {
      cursor: pointer;
      border: 0;
      outline: 0;
      background: 0 0;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      padding: 0;
      color: #000;
      font-weight: 500;
      text-decoration: underline;
      font-family: 'Montserrat';
      font-size: 14px;
    }
  }
}

</style>
