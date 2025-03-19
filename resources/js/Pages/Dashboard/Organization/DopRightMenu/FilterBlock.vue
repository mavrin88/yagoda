<template>
  <Head title="Фильтр" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link></Link>
        <Icon @click="close" icon="system-uicons:close" width="41" height="41" class="close_icon" />
      </div>

      <div class="registration-date">
        <div class="registration-date__top">
          <div class="registration-date__title">
            Дата регистрации партнера:
          </div>
        </div>
        <div class="registration-date__range">
            <span>с
        <input type="date" v-model="startDate"
               class="date-input" />
        по
        <input type="date" v-model="endDate"
               class="date-input" />
      </span>
          <label class="switch">
            <input type="checkbox" v-model="isDateFilterEnabled" />
            <span class="slider"></span>
          </label>
        </div>
      </div>


      <div class="partner_revenue">
        <div class="partner_revenue__top">
          <div class="partner_revenue__title">
            Выручка партнера за период <span class="partner_revenue__currency">(р.):</span>
          </div>
        </div>
        <div class="partner_revenue__range">
         <span>от
        <input type="text" v-model="partner_revenue_ot" class="revenue-input" />
        до
        <input type="text" v-model="partner_revenue_do" class="revenue-input" />
      </span>
          <label class="switch">
            <input type="checkbox" v-model="isRevenueFilterEnabled" />
            <span class="slider"></span>
          </label>
        </div>
      </div>

      <div class="tipping">
        <div class="tipping__top">
          <div class="tipping__title">Шеринг чаевых:</div>
        </div>
        <div class="tipping_cheekiness">
          <div class="checkboxes">
            <input type="radio" id="yes" v-model="tippingChoice" value="yes"/>
            <label for="yes"> Да</label>

            <input type="radio" id="no"  v-model="tippingChoice" value="no"/>
            <label for="no"> Нет</label>
          </div>

          <label class="switch">
            <input type="checkbox" v-model="isTippingFilterEnabled" />
            <span class="slider"></span>
          </label>
        </div>
      </div>


      <div class="status">
        <div class="status__top">
          <div class="status__title">Статус:</div>
        </div>
        <div class="status-cheekiness">
          <div class="checkboxes">
            <input type="checkbox" id="new" v-model="selectedStatuses.new" />
            <label class="pr-8" for="new">Новая</label>

            <input type="checkbox" id="stoped" v-model="selectedStatuses.stopped" />
            <label for="stoped"> Приостановка</label>
          </div>
          <label class="switch">
            <input type="checkbox" v-model="isStatusEnabled"/>
            <span class="slider"></span>
          </label>
        </div>
        <div class="status-cheekiness">
          <div class="checkboxes">
            <input type="checkbox" id="active" v-model="selectedStatuses.active" />
            <label for="active">Действует</label>

            <input type="checkbox" id="deleted" v-model="selectedStatuses.deleted" />
            <label for="deleted">Удалена</label>
          </div>
        </div>
      </div>

      <div class="button-wrapper">
        <button @click="applyFilter" class="show-button">Показать</button>
      </div>

    </div>
</template>

<script>
import axios from 'axios'
import { Head, Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import vSelect from 'vue-select'
import Sms from '../../../Auth/Sms.vue'
import OrdersList from '../../../SuperAdmin/OrdersStatistics/Components/OrdersList.vue'
import Filter from '../../../SuperAdmin/OrdersStatistics/Components/Filter.vue'
import Form from '../../../SuperAdmin/OrdersStatistics/Components/Form.vue'
import Content from '../../../SuperAdmin/OrdersStatistics/Components/Content.vue'

export default {
  components: {
    Content, Form, Filter, OrdersList,
    Sms, Head,
    vSelect,
    Icon,
    Link,
  },
  data() {
    return {
      isDateFilterEnabled: false,
      startDate: '',
      endDate: '',
      isRevenueFilterEnabled: false,
      partner_revenue_ot: '',
      partner_revenue_do: '',
      isTippingFilterEnabled: false,
      tippingChoice: '',
      isStatusEnabled: false,
      selectedStatuses: {
        new: false,
        stopped: false,
        active: false,
        deleted: false
      },
    }
  },
  methods: {
    applyFilter() {
      const filterData = {};

      // if (this.isDateFilterEnabled && (this.startDate || this.endDate)) {
      //   filterData.date = {
      //     startDate: this.startDate,
      //     endDate: this.endDate,
      //   };
      // }
      if (this.isDateFilterEnabled) {
        if (this.startDate) {
          filterData.date = filterData.date || {};
          filterData.date.startDate = this.startDate;
        }
        if (this.endDate) {
          filterData.date = filterData.date || {};
          filterData.date.endDate = this.endDate;
        }
      }
      if (this.isRevenueFilterEnabled) {
        filterData.revenue = {
          // isRevenueFilterEnabled: this.isRevenueFilterEnabled,
          partner_revenue_ot: this.partner_revenue_ot,
          partner_revenue_do: this.partner_revenue_do,
        };
      }
      if (this.isTippingFilterEnabled && this.tippingChoice) {
        filterData.tipping = {
          enabled: this.isTippingFilterEnabled,
          choice: this.tippingChoice,
        };
      }
      // if (this.isStatusEnabled) {
      //   filterData.status = {
      //     isStatusEnabled: this.isStatusEnabled,
      //     status: this.isStatusEnabled,
      //   };
      // }
      if (this.isStatusEnabled) {
        const selectedStatuses = Object.keys(this.selectedStatuses).filter(status => this.selectedStatuses[status]);
        if (selectedStatuses.length > 0) {
          filterData.status = selectedStatuses;
        }
      }

      if (Object.keys(filterData).length > 0) {
        this.$emit('filterChanged', filterData);
      }
    },
    close() {
      this.$emit('showFilterBlock', false)
    },
  },
}
</script>

<style lang="scss" scoped>
.superadmin__container {
  display: flex;
  flex-direction: column;
  max-width: 380px;
  padding-bottom: 50px;
}
.switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 24px;
  margin-left: auto;
}
.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}
.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 34px;
}
.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  border-radius: 50%;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
}

.close_icon {
  cursor: pointer;
}

.registration-date {
  margin-top: 30px;
}

.registration-date__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.registration-date__title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.registration-date__range {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: #555;
  margin-top: 15px;
}

.date-input {
  border: none;
  border-bottom: 1px solid black;
  outline: none;
}
.partner_revenue {
  margin-top: 30px;
}

.partner_revenue__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.partner_revenue__title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.partner_revenue__currency {
  font-weight: normal;
}

.partner_revenue__range {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 14px;
  color: #555;
  margin-top: 15px;
}
.revenue-input {
  border: none;
  border-bottom: 1px solid #ccc;
  outline: none;
  width: 140px;
  text-align: right;
  direction: rtl;
  padding-right: 10px;
  font-weight: bold;
}

.tipping {
  margin-top: 30px;
}

.tipping__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.tipping__title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.tipping_cheekiness {
  margin-top: 15px;
}
.tipping_cheekiness  .checkboxes{
 gap: normal;
}
.status {
  margin-top: 30px;
}


.status__top {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
}

.status__title {
  font-size: 16px;
  font-weight: bold;
  color: #333;
}

.status-cheekiness {
  margin-top: 15px;
  margin-bottom: 12px;
}

/* Стили для переключателя */
.switch {
  position: relative;
  display: inline-block;
  width: 34px;
  height: 20px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: 0.4s;
  border-radius: 50px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 12px;
  width: 12px;
  border-radius: 50px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: 0.4s;
}

input:checked + .slider {
  background-color: #38b0b0;
}

input:checked + .slider:before {
  transform: translateX(14px);
}
.button-wrapper {
  margin-top: 40px;
 margin-left: 25px;
}
.show-button {
  width: 95%;
  height: 54px;
  padding: 18px;
  font-size: 16px;
  color: white;
  background-color: #38b0b0;
  border: none;
  border-radius: 16px;
  cursor: pointer;
}

.status-cheekiness {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 24px;
  .checkboxes {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  input[type="checkbox"] {
    opacity: 0;
    position: absolute;
  }
  label {
    position: relative;
    display: block;
    padding-left: 34px;
    padding-top: 3px;
    cursor: pointer;
    &::after,
    &::before {
      position: absolute;
      top: 0;
      left: 0;
      display: inline-block;
      content: '';
    }
    &::before {
      width: 20px !important;
      min-width: 23px !important;
      max-width: 20px !important;
      height: 25px !important;
      min-height: 0px !important;
      max-height: 23px !important;
      border-radius: 80% !important;
      background-color: #fff;
    }
  }
}


.tipping_cheekiness {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-top: 24px;
  .checkboxes {
    display: flex;
    gap: 10px;
    align-items: center;
  }
  input[type="checkbox"] {
    opacity: 0;
    position: absolute;
  }
  label {
    position: relative;
    display: block;
    padding-left: 34px;
    padding-top: 3px;
    cursor: pointer;
    &::after,
    &::before {
      position: absolute;
      top: 0;
      left: 0;
      display: inline-block;
      content: '';
    }
    &::before {
      width: 20px !important;
      min-width: 23px !important;
      max-width: 20px !important;
      height: 25px !important;
      min-height: 0 !important;
      max-height: 23px !important;
      border-radius: 80% !important;
      background-color: #fff;
    }
  }
}

.checkboxes {
  display: flex;
  align-items: center;
  margin-right: 20px;
  input {
    opacity: 0;
    width: 0;
    height: 0;
  }
  label {
    position: relative;
    display: block;
    padding-left: 30px;
    cursor: pointer;
    margin-right: 15px;
    &::before {
      position: absolute;
      top: 0;
      left: 0;
      display: inline-block;
      content: '';
      width: 20px;
      height: 20px;
      border: 1px solid #000;
      border-radius: 50%;
      background-color: #fff;
    }
    &::after {
      visibility: hidden;
      width: 20px;
      height: 20px;
      margin: 7px 0 0 5px;
      opacity: 0;
      background: url('/resources/img/icon_check_white.svg') no-repeat;

    }
  }
  input:checked + label::before {
    background-color: #38b0b0;
  }
  input:checked + label::after {
    visibility: visible;
    opacity: 1;
  }
}

</style>
