<template>
    <div class="superadmin">
      <Head title="Заказы" />
      <div class="superadmin__container">
        <div class="superadmin__top">
          <Link class="back" href="/">назад</Link>
          <h2>Заказы</h2>
        </div>
        <div class="superadmin-tips">
          <v-select v-model="selectedOption"
                    :options="options"
                    label="title"
                    :searchable="false"
                    :clearable="false">
          </v-select>

          <div class="superadmin-tips__filter">
            <div class="superadmin-tips__step superadmin-tips__step_prev" @click="fetchData('prev')"></div>
            <div class="superadmin-tips__date">{{ formattedDate }}</div>
            <div class="superadmin-tips__step superadmin-tips__step_next" @click="fetchData('next')"></div>
          </div>
          <form :options="data.options" />
          <filter :date="data.date" />
          <content
            :revenue="data.revenue"
            :equalizationFee="data.equalizationFee"
            :savedEqualization="data.savedEqualization"
            :totalOrders="data.totalOrders"
          />
          <orders-list :orders="data.orders" />
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
import OrdersList from './Components/OrdersList.vue'
import Spiner from  '@/Shared/Spiner.vue'
import vSelect from 'vue-select'

export default {
  components: {
    Head,
    vSelect,
    Spiner,
    Link,
    Form,
    Filter,
    Content,
    OrdersList,
  },
  props: {
    data: Object
  },
  data() {
    return {
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
            console.log(this.startDate)
            this.startDate.setDate(this.startDate.getDate() - 7);
            this.endDate.setDate(this.endDate.getDate() - 7);
            console.log(this.startDate)
            this.newFormatDate();
          } else if (direction === 'next') {
            this.startDate.setDate(this.startDate.getDate() + 7);
            this.endDate.setDate(this.endDate.getDate() + 7);
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
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      this.formattedDate = `${this.startDate.toLocaleDateString('ru-RU', options)} - ${this.endDate.toLocaleDateString('ru-RU', options)}`;
    }
  },

    watch: {
      selectedOption(newValue, oldValue) {

        if (newValue !== oldValue) {
          this.startLoading();
          this.formatDate();
          setTimeout(() => {
            this.stopLoading()
          }, 500);
        }
      },

      // axios.post('/masters/getFilterMaster', {
      //   date: this.formattedDate
      // })
      //   .then((response) => {
      //     console.log(response.data);
      //   })
      //   .catch((error) => {
      //     console.error('Ошибка при получении данных:', error);
      //   });
    },
}
</script>

<style scoped>
.superadmin-tips__filter {
  margin-top: 16px;
}

.superadmin-tips__date {
  text-decoration: none;
}
</style>
