<template>
  <Head title="Статистика" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/organizations/choose"></Link>
          <div class="ytips-top__text">
            <p>{{localData.role_name }}</p>
            <p>{{localData.group_name }}</p>
          </div>
          <div class="ytips-top__burger" @click="menuOpen"><span></span><span></span><span></span></div>
        </div>
      </div>
      <div class="ytips-top-stat">
        <div class="ytips__container">
          <div class="ytips-top-stat__block" @click="go('/tips/staff_shift')">
            <p>СОТРУДНИКИ В СМЕНЕ</p>
            <div class="ytips-top-stat__people"><span title="Мастера">
              <b>{{ localData.people_count.masters }}</b>М</span>
              <span>-</span><span title="Администраторы"><b>{{ localData.people_count.administrators }}</b>А</span>
              <span>-</span><span title="Персонал"><b>{{ localData.people_count.employees }}</b>П</span>
            </div>
          </div>
          <div class="ytips-top-stat__text">
            <p>переводов <span>{{ localData.translations }}</span></p>
            <p>₽ <b>{{ localData.translations_sum }}</b></p>
          </div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-filter">
          <div class="ytips-filter__step ytips-filter__step_prev" @click="fetchData('prev')"></div>
          <div class="ytips-filter__date" @click="periods.isShow = !periods.isShow">{{ formattedDate }}</div>
          <div class="ytips-filter__step ytips-filter__step_next" @click="fetchData('next')"></div>
          <div class="ytips-filter-periods" v-if="periods.isShow">
            <div class="ytips-filter-periods__item"
                 v-if="periods && periods.list"
                 v-for="(item, index) in periods.list"
                 :key="index"
                 @click="periodsChange(item.value)"
            >{{item.name}}</div>
          </div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-people">
          <!--это можно плодить под все должности-->

          <div class="ytips-people__group" v-for="employee in localData.employees" :key="employee.id" v-if="localData && localData.employees">
            <div class="ytips-people__top">
              <div class="ytips-people__title">{{ employee.name }}</div>
              <div class="ytips-people__sum">₽ <b>{{ employee.total_translations_sum }}</b></div>
            </div>
            <div class="ytips-people__list">
              <div @click="handleEmployeeClick(employee)" class="ytips-people__item" v-for="employee in employee.employees" :key="employee.id">
                <div class="ytips-people__avatar"><img :src="employee.avatar" alt=""></div>
                <div class="ytips-people__name">{{ employee.name }}
                  <div class="ytips-people__crown"></div>
                </div>
                <div class="ytips-people__info">
                  <p><b>{{  employee.tips ?? 0 }}</b> ₽</p>
                  <div><b>{{ employee.orders_count ?? 0 }} </b> переводов</div>
                </div>
              </div>
            </div>
          </div>

          <div class="ytips-people__group">
            <div class="ytips-people__top">
              <div class="ytips-people__title">ОБЩИЕ РАСХОДЫ</div>
              <div class="ytips-people__sum">₽ <b>{{ localData.total_translations_sum ?? 0 }}</b></div>
            </div>
          </div>
        </div>
      </div>

      <div class="ytips-menu" :class="{'js-active':isMenuOpen}">
        <div class="ytips__container">
          <div class="ytips-top__burger js-active" @click="menuClose"><span></span><span></span><span></span></div>
          <div class="ytips-menu__links">
            <Link href="/tips/coord/tips">ЧАЕВЫЕ</Link>
            <Link href="/tips/staff_shift">СОТРУДНИКИ В СМЕНЕ</Link>
            <Link href="/tips/workforce">УЧАСТНИКИ ГРУППЫ</Link>
            <Link href="/tips/qr">QR КОДЫ</Link>
            <Link href="/tips/coord/stands">СТОЙКИ YAGODA-TIPS</Link>
            <Link href="/tips/tip_distribution">ЧАЕВЫЕ РАСПРЕДЕЛЕНИЕ</Link>
            <Link href="/tips/groups_settings">НАСТРОЙКИ</Link>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import TipsHeader from '../Components/TipsHeader.vue'
import TipsStatistic from './Tips/Index.vue'
import axios from 'axios'
import CustomSelect from '@/Shared/CustomSelect.vue'

export default {
  name: "Index",
  components: {
    CustomSelect,
    TipsHeader,
    Head,
    Link,
    TipsStatistic
  },
  data() {
    return {
      isMenuOpen: false,
      groupSettings: false,
      selectEmployee: {},
      formattedDate: null,
      selectedOption: { value: 'day'},
      date: new Date(),
      startDate: null,
      endDate: null,
      dateToday: new Date(),
      isLoading: false,
      localData: this.data,
      periods: {
        list: [
          {
            name: 'сегодня',
            value: 'day'
          },
          {
            name: '7 дней',
            value: 'week'
          },
          {
            name: 'месяц',
            value: 'month'
          },
          {
            name: 'год',
            value: 'year'
          },
          // {
          //   name: 'свой период',
          //   value: 'custom'
          // },
        ],
        isShow: false
      },
    }
  },
  props: {
    data: Object
  },
  mounted() {
    // Дата по умолчанию
    this.formatDate();
  },
  methods: {
    periodsChange(type) {
      this.periods.isShow = false
      this.selectedOption = { value: type};
      this.formatDate();
    },
    go(path) {
      if (path) {
        router.visit(path)
      }
    },
    formatDate() {
      this.updateDateRange();
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      if (this.selectedOption.value === 'day') {
        this.formattedDate = this.date.toLocaleDateString('ru-RU', options);
      } else {
        this.formattedDate = `${this.startDate.toLocaleDateString('ru-RU', options)} - ${this.endDate.toLocaleDateString('ru-RU', options)}`;
      }
      if (this.startDate && this.endDate) {
        this.fetchFilteredData();
      }
    },
    newFormatDate() {
      const options = { year: 'numeric', month: 'long', day: 'numeric' };
      if (this.startDate && this.endDate) {
        this.formattedDate = `${this.startDate.toLocaleDateString('ru-RU', options)} - ${this.endDate.toLocaleDateString('ru-RU', options)}`;
        this.fetchFilteredData()
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
    fetchFilteredData() {

      const startDate = new Date(this.startDate);
      const endDate = new Date(this.endDate);
      // Преобразуем в UTC перед отправкой
      const formattedStartDate = new Date(startDate.setHours(0, 0, 0, 0)).toISOString();
      const formattedEndDate = new Date(endDate.setHours(23, 59, 59, 999)).toISOString();

      this.isLoading = true

      axios.get('/tips/coord/get-tips', {
        params: {  // Передаём даты через params
          type: this.selectedOption.value,
          startDate: formattedStartDate,
          endDate: formattedEndDate
        }
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


    },
    fetchData(direction) {
      switch (this.selectedOption.value) {
        case 'week':
          if (direction === 'prev') {
            this.startDate.setDate(this.startDate.getDate() - 7);
            this.endDate.setDate(this.endDate.getDate() - 7);
            this.newFormatDate();
          } else if (direction === 'next') {
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Обнуляем время

            const selectedDate = new Date(this.endDate);
            selectedDate.setHours(0, 0, 0, 0); // Обнуляем время

            // Не даём смотреть в будущее
            if (selectedDate.getTime() === today.getTime() || selectedDate > today) {
              return;
            }

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
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Обнуляем время

            const selectedStartDate = new Date(this.endDate);
            selectedStartDate.setHours(0, 0, 0, 0); // Обнуляем время

            // Если startDate уже сегодня или в будущем — не изменяем
            if (selectedStartDate.getTime() === today.getTime() || selectedStartDate > today) {
              return;
            }

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

            const today = new Date();
            today.setHours(0, 0, 0, 0);

            const selectedDate = new Date(this.date);
            selectedDate.setHours(0, 0, 0, 0);

            // Не даём смотреть будущее
            if (selectedDate.getTime() !== today.getTime()) {
              this.date.setDate(this.date.getDate() + 1);
            }


          }
          this.formatDate();
      }
    },
    stopLoading() {
      this.isLoading = false
    },
    goBack() {
      this.activeComponent = 'Main'
    },
    menuOpen() {
      this.isMenuOpen = true
    },
    menuClose() {
      this.isMenuOpen = false
    },
    handleEmployeeClick(employee) {
      this.selectEmployee = employee;
      this.$inertia.post(`/tips/coord/tips/show/`, {employee: employee.id})
    },
  },
}
</script>
<style lang="scss" scoped>
.ytips-top-stat__block {
  cursor: pointer;
}
.ytips-filter {
  position: relative;
}
.ytips-filter__date {
  cursor: pointer;
}
.ytips-filter-periods {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  margin-left: auto;
  margin-right: auto;
  background: #fff;
  z-index: 2;
  border-radius: 8px;
  padding: 10px;
  max-width: 200px;
  margin-top: 20px;
  &__item {
    cursor: pointer;
    margin-bottom: 7px;
    &:hover {
      text-decoration: underline;
    }
  }
}
</style>
