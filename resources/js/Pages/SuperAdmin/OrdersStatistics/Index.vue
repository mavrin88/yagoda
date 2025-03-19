<template>
    <div class="superadmin">
      <Head title="Продажи" />
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" @click="goBack">назад</Link>
          <h2>Продажи</h2>
        </div>
        <div class="superadmin-tips">
          <custom-select
            :options="options"
            v-model="selectedOption"
          />

          <div class="superadmin-tips__filter">
            <div class="superadmin-tips__step superadmin-tips__step_prev" @click="fetchData('prev')"></div>
            <div class="superadmin-tips__date">{{ formattedDate }}</div>
            <div class="superadmin-tips__step superadmin-tips__step_next" @click="fetchData('next')"></div>
          </div>
          <form :options="localData.options" />
          <filter :date="localData.date" />

          <content
            :revenue="localData.revenue"
            :equalizationFee="localData.equalizationFee"
            :savedEqualization="localData.savedEqualization"
            :totalOrders="localData.totalOrders"
            :todayOrdersWithTipsCount="localData.todayOrdersWithTipsCount"
            :todayOrdersWithTipsPercentage="localData.todayOrdersWithTipsPercentage"
            :medium_tips="localData.medium_tips"
            :totalTips="localData.totalTips"
          />

          <div class="xls-container cursor-pointer" @click="exportToExcel" v-if="localData.orders.length > 0">
            <Icon icon="bi:filetype-xls" width="26" height="34" />
          </div>

          <reports-list :orders="localData.orders" />
        </div>
      </div>
    </div>

  <Spiner :isLoading="isLoading"></Spiner>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

import Form from './Components/Form.vue'
import Filter from './Components/Filter.vue'
import Content from './Components/Content.vue'
import ReportsList from './Components/ReportsList.vue'
import Spiner from  '@/Shared/Spiner.vue'
import CustomSelect from '@/Shared/CustomSelect.vue';
import { Icon } from "@iconify/vue";
import axios from 'axios'

export default {
  components: {
    Head,
    CustomSelect,
    Spiner,
    Link,
    Form,
    Filter,
    Content,
    ReportsList,
    Icon
  },
  props: {
    data: Object,
  },
  data() {
    return {
      localData: { ...this.data },
      date: new Date(),
      formattedDate: '',
      isLoading: false,
      periodButtonDisabled: false,
      selectedOption: { value: 'day', title: 'за день' },
      options: [
        { value: 'day', title: 'за день' },
        { value: 'week', title: 'за последние 7 дней' },
        { value: 'month', title: 'за последние 30 дней' },
        { value: 'year', title: 'за год' },
      ],
      startDate: null,
      endDate: null,
    };
  },
  mounted() {
    this.formatDate();
  },
  methods: {
    async exportToExcel() {
      const name = 'Период продаж : ' + this.formattedDate + ' ' + this.localData.brandName
      try {
        const response = await axios.post('/export-products', {
          products: this.localData.orders,
          name: name
        }, {
          responseType: 'blob'
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', name + '.xlsx');
        document.body.appendChild(link);
        link.click();

        link.remove();
      } catch (error) {
        console.error('Ошибка при экспорте:', error);
      }
    },
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
          this.newFormatDate();
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 6);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'month':
          this.startDate = new Date(today.getFullYear(), today.getMonth(), today.getDate() - 30);
          this.endDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
          break;
        case 'year':
          this.startDate = new Date(today.getFullYear(), 0, 1);
          this.endDate = new Date(today.getFullYear(), 11, 31);
          break;
      }
    },

    fetchData(direction) {
      this.startLoading();
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
        case 'month':
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
        case 'year':
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
    goBack() {
        this.$inertia.get(`/super_admin/reports`);
    },
    // todo: разобраться, зачем тут несколько загрузок сразу
    fetchFilteredData() {
      this.startLoading();

      const startDate = new Date(this.startDate);
      const endDate = new Date(this.endDate);
      // Преобразуем в UTC перед отправкой
      const formattedStartDate = new Date(startDate.setHours(0, 0, 0, 0)).toISOString();
      const formattedEndDate = new Date(endDate.setHours(23, 59, 59, 999)).toISOString();

      axios.post('/super_admin/filter_orders_statistics', {
            type: this.selectedOption.value,
            startDate: formattedStartDate,
            endDate: formattedEndDate
        })
          .then((response) => {
            this.localData = response.data;
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

<style>
.superadmin-tips__filter {
  margin-top: 16px;
}

.superadmin-tips__date {
  text-decoration: none;
}
.xls-container {
  display: flex;
  justify-content: flex-end; /* Выравнивание значка по правому краю */
  margin-left: auto; /* Это перемещает элемент в конец контейнера */
  width: 100%; /* Для контроля ширины */
  margin-top: 10px; /* Отступ сверху */
}
</style>
