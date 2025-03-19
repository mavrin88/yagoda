<template>
  <div class="superadmin">
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/root_admin/">назад</Link>
        <h2>НАСТРОЙКИ</h2>
      </div>
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" id="phone" v-model="formattedSettings.order_link_life_time" v-mask="'####'">
            <label for="phone">Время жизни оплаты заказа (секунды)</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.commission_for_using_the_service" v-mask="'#.##'">
            <label for="commission">Комиссия за использование сервиса (%)</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.acquiring_fee" v-mask="'#.##'">
            <label for="acquiring_fee">Комиссия за эквайринг (%)</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.card_payment_fee" v-mask="'#.##'">
            <label for="acquiring_fee">Комиссия Точка при оплате по карте (%)</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.sbp_payment_fee" v-mask="'#.##'">
            <label for="acquiring_fee">Комиссия Точка при оплате по CБП (%)</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.maximum_tip_amount" v-mask="'##'">
            <label for="acquiring_fee">Максимальная сумма чаевых (%)</label>
          </div>
        </div>
      </div>

      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" id="phone" v-model="formattedSettings.number_company_contract" v-mask="'###############'">
            <label for="phone">Номер договора офферты</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.date_company_contract" v-mask="'####-##-##'">
            <label for="commission">Дата договора офферты (2025-01-31)</label>
          </div>
        </div>
      </div>

      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" id="phone" v-model="formattedSettings.organization_name">
            <label for="phone">Наименование организации</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.organization_phone">
            <label for="commission">Телефон организации</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.inn">
            <label for="commission">ИНН</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.legal_address">
            <label for="acquiring_fee">Юридический адрес</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.kpp">
            <label for="acquiring_fee">КПП</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.email">
            <label for="acquiring_fee">E-mail для отчетов</label>
          </div>
        </div>
      </div>
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" id="phone" v-model="formattedSettings.bill_name">
            <label for="phone">Наименование счёта</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.bill_number">
            <label for="commission">Номер счёта</label>
          </div>
          <div class="form__item">
            <input type="text" v-model="formattedSettings.bik">
            <label for="commission">Бик</label>
          </div>
        </div>
      </div>
      <div class="superadmin__block">
        <div class="superadmin__container">
          <div class="superadmin__note">Установите предлагаемые значения чаевых.</div>
          <div class="superadmin__tips">
            <div
              v-for="tip in tips"
              :key="tip.id"
              @click="selectTip(tip.id)"
              :class="['superadmin__tip', {'superadmin__tip_active': tip.isActive}]">{{ tip.value }}%</div>
          </div>
        </div>
      </div>
      <div class="fixed-bottom">
        <button class="btn btn_w100" @click="saveSettings">Сохранить</button>
      </div>
    </div>
  </div>
  <Spiner :isLoading="isLoading"></Spiner>

  <Modal
    :is-active="isModalActive"
    @close="closeModal"
    :form="true"
    messageButton="Установить"
    @confirmed="setTip"
    :value="selectedTip.value"
  />
</template>

<script>
import { Link } from '@inertiajs/vue3'
import axios from 'axios'
import Spiner from '../../../Shared/Spiner.vue'
import Modal from '../../../Shared/Modal.vue'

export default {
  components: {
    Modal,
    Spiner,
    Link
  },
  props: {
    formattedSettings: Object
  },
  data() {
    return {
      isLoading: false,
      tips: [],
      selectedTip: {},
      isModalActive: false,
    }
  },
  created() {
    this.initializeTips();
  },
  computed: {
    combinedSettings() {
      const tipsObject = this.tips.reduce((acc, tip) => {
        acc[tip.name] = tip.value;
        return acc;
      }, {});

      return { ...this.formattedSettings, ...tipsObject };
    },
  },
  methods: {
    initializeTips() {
      this.tips = [
        {
          id: 1,
          name: 'tips_1',
          value: parseInt(this.formattedSettings.tips_1, 10),
          isActive: true,
        },
        {
          id: 2,
          name: 'tips_2',
          value: parseInt(this.formattedSettings.tips_2, 10),
          isActive: true,
        },
        {
          id: 3,
          name: 'tips_3',
          value: parseInt(this.formattedSettings.tips_3, 10),
          isActive: true,
        },
        {
          id: 4,
          name: 'tips_4',
          value: parseInt(this.formattedSettings.tips_4, 10),
          isActive: true,
        },
      ];
    },
    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },
    async saveSettings() {
      this.startLoading();
      try {
        const response = await axios.post('/settings/save', {
          settings: this.combinedSettings
        });

        setTimeout(() => {
          this.stopLoading();
          this.$toast.open({
            message: 'Настройки успешно сохранены',
            type: 'success',
            position: 'top-right',
            duration: 2000,
          });
        }, 500);
      } catch (error) {
      }
    },
    selectTip(id) {
      this.tips.forEach(tip => {
        tip.isActive = tip.id === id;
      });

      this.selectedTip = this.tips.find(tip => tip.isActive);

      this.isModalActive = true
    },
    closeModal() {
      this.isModalActive = false
    },
    setTip(value) {
      this.selectedTip = this.tips.find(tip => tip.isActive);
      this.selectedTip.value = Number(value);
    },
  },
}
</script>
