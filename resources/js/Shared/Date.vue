<template>
  <div class="form">
    <div class="form__item">
      <custom-select
        :options="options"
        v-model="selectedOption"
      />
    </div>
  </div>
  <div class="superadmin-tips__filter">
    <div class="superadmin-tips__step superadmin-tips__step_prev" @click="fetchData('prev')"></div>
    <div class="superadmin-tips__date">{{ formattedDate }}</div>
    <div class="superadmin-tips__step superadmin-tips__step_next" @click="fetchData('next')"></div>
  </div>

  <Spiner :isLoading="isLoading"></Spiner>
</template>

<script>
import Spiner from '@/Shared/Spiner.vue'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import CustomSelect from '@/Shared/CustomSelect.vue';
import axios from 'axios'

export default {
  components: {
    CustomSelect,
    vSelect,
    Spiner
  },

  data() {
    return {
      date: new Date(),
      formattedDate: '',
      isLoading: false,
      selectedOption: { value: 'day', title: 'за день' },
      options: [
        { value: 'day', title: 'день' },
        { value: 'week', title: 'последние 7 дней' },
        { value: 'last_month', title: 'последние 30 дней' },
        { value: 'year', title: 'последние 365 дней' },
        { value: 'week_2', title: 'неделя' },
        { value: 'month', title: 'месяц' },
        { value: 'year_2', title: 'год' },
      ],
      startDate: null,
      endDate: null,
    }
  },
  mounted() {
    this.formatDate();
  },
  methods: {

    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },

    formatDate() {
      this.updateDateRange();
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      if (this.selectedOption.value === 'day') {
        this.formattedDate = this.date.toLocaleDateString('ru-RU', options);
      } else {
        this.formattedDate = `${this.startDate.toLocaleDateString('ru-RU', options)} - ${this.endDate.toLocaleDateString('ru-RU', options)}`;
      }
      this.fetchFilteredData();
    },
    updateDateRange() {
      const today = new Date(this.date);
      switch (this.selectedOption.value) {
        case 'day':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 1);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'week':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 6);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'last_month':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 30);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'year':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 365);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'week_2':
          const dayOfWeek = today.getDay(); // Текущий день недели (0 - воскресенье, 6 - суббота)
          const mondayOffset = (dayOfWeek === 0 ? -6 : 1) - dayOfWeek; // Сколько дней до понедельника
          const startOfWeek = new Date(today.getFullYear(), today.getMonth(), today.getDate() + mondayOffset); // Начало недели (понедельник)
          this.startDate = startOfWeek;
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate()); // Конец недели (сегодня)
          break;
        case 'month':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), 1); // Первое число текущего месяца
          this.endDate = today; // Сегодняшняя дата
          break;
        case 'year_2':
          this.startDate = new Date(today.getFullYear(), 0, 1);
          this.endDate = new Date(today.getFullYear(), 11, 31);
          break;
      }
    },
    fetchData(direction) {
      this.startLoading();
      const today = new Date(this.date);
      switch (this.selectedOption.value) {
        case 'week':
          if (direction === 'prev') {
            this.startDate.setDate(this.startDate.getDate() - 7);
            this.endDate.setDate(this.endDate.getDate() - 7);
            this.newFormatDate();
          } else if (direction === 'next') {
            this.startDate.setDate(this.startDate.getDate() + 7);
            this.endDate.setDate(this.endDate.getDate() + 7);
            this.newFormatDate();
          }
          break;
        case 'last_month':
          if (direction === 'prev') {
            const startDateDay = this.startDate.getDate();
            const endDateDay = this.endDate.getDate();

            this.endDate = new Date(this.startDate);
            this.endDate.setDate(this.endDate.getDate() - 1);

            this.startDate = new Date(this.endDate);
            this.startDate.setDate(this.endDate.getDate() - 30);

            const startMonthDays = new Date(this.startDate.getFullYear(), this.startDate.getMonth() + 1, 0).getDate();
            const endMonthDays = new Date(this.endDate.getFullYear(), this.endDate.getMonth() + 1, 0).getDate();

            if (this.startDate.getDate() > startMonthDays) {
              this.startDate.setDate(startMonthDays);
            }
            if (this.endDate.getDate() > endMonthDays) {
              this.endDate.setDate(endMonthDays);
            }
            this.newFormatDate();
          } else if (direction === 'next') {
            const startDateDay = this.startDate.getDate();
            const endDateDay = this.endDate.getDate();
            this.startDate = new Date(this.endDate);
            this.startDate.setDate(this.startDate.getDate() + 1);

            this.endDate = new Date(this.startDate);
            this.endDate.setDate(this.startDate.getDate() + 30);
            const startMonthDays = new Date(this.startDate.getFullYear(), this.startDate.getMonth() + 1, 0).getDate();
            const endMonthDays = new Date(this.endDate.getFullYear(), this.endDate.getMonth() + 1, 0).getDate();
            if (this.endDate.getDate() > endMonthDays) {
              this.endDate.setDate(endMonthDays);
            }
            this.newFormatDate();
          }
          break;
        case 'month':
          if (direction === 'prev') {
            this.date.setMonth(this.date.getMonth() - 1, 1);
            this.startDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1);
            this.endDate = new Date(this.date.getFullYear(), this.date.getMonth() + 1, 0); // последний день - 0 - берет последний день предыдущего месяца

            this.newFormatDate()
          } else {
            this.date.setMonth(this.date.getMonth() + 1, 1);
            this.startDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1);
            this.endDate = new Date(this.date.getFullYear(), this.date.getMonth() + 1, 0); // последний день - 0 - берет последний день предыдущего месяца

            this.newFormatDate()
          }
          break;
        case 'year':
          if (direction === 'prev') {
            this.startDate.setDate(this.startDate.getDate() - 365);
            this.endDate.setDate(this.endDate.getDate() - 365);
            this.newFormatDate();
          } else {
            this.startDate.setDate(this.startDate.getDate() + 365);
            this.endDate.setDate(this.endDate.getDate() + 365);
            this.newFormatDate();
          }
          break;
        case 'week_2':
          const dayOfWeek = today.getDay();
          const mondayOffset = (dayOfWeek === 0 ? -6 : 1) - dayOfWeek;

          if (direction === 'prev') {
            this.date.setDate(this.date.getDate() - 7);
          } else {
            this.date.setDate(this.date.getDate() + 7);
          }
          const startOfWeek = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + mondayOffset);
          this.startDate = startOfWeek;
          this.endDate = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + mondayOffset);
          this.endDate.setDate(this.startDate.getDate() + 6);

          this.newFormatDate();
          break;
        case 'year_2':
          if (direction === 'prev') {
            const todayYear = this.date.getFullYear();
            const selectedYear = this.startDate.getFullYear();
            if(selectedYear === todayYear) {
              this.startDate = new Date(todayYear - 1, 0, 1);
              this.endDate = new Date(todayYear - 1, 11, 31);
            }else{

              this.startDate = new Date(selectedYear - 1, 0, 1);
              this.endDate = new Date(selectedYear - 1, 11, 31);
            }
            this.newFormatDate();
          }

          if (direction === 'next') {
            const todayYear = this.date.getFullYear();
            const selectedYear = this.startDate.getFullYear();
            if(selectedYear === todayYear) {
              this.startDate = new Date(todayYear + 1, 0, 1);
              this.endDate = new Date(todayYear+ 1, 11, 31);
            }else{

              this.startDate = new Date(selectedYear + 1, 0, 1);
              this.endDate = new Date(selectedYear + 1, 11, 31);
            }
            this.newFormatDate();
          }
          break;

        default:
          if (direction === 'prev') {
            this.date.setDate(this.date.getDate() - 1);
          } else {
            this.date.setDate(this.date.getDate() + 1);
          }
          this.formatDate();
      }

      setTimeout(() => {
        this.stopLoading()
      }, 500);
    },

    newFormatDate() {
      this.fetchFilteredData()
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      this.formattedDate = `${this.startDate.toLocaleDateString('ru-RU', options)} - ${this.endDate.toLocaleDateString('ru-RU', options)}`;
    },
    fetchFilteredData() {
      this.startLoading();
      axios.post('/super_admin/filter_tips_statistics', {
        type: this.selectedOption.value,
        startDate: this.startDate,
        endDate: this.endDate
      })
        .then((response) => {
          this.$emit('filteredTipsData', response.data)
        })
        .catch((error) => {
          console.error('Ошибка при получении данных:', error);
        })
        .finally(() => {
          this.stopLoading();
        });
    }
  },
  watch: {
    selectedOption(newValue, oldValue) {
      if (newValue !== oldValue) {
        this.startLoading();
        this.formatDate();
        this.fetchFilteredData()
        setTimeout(() => {
          this.stopLoading()
        }, 500);
      }
    },
  },
}
</script>

<style scoped>
.disabled {
  pointer-events: none;
}
</style>
