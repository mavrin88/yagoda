<template>
  <div class="client__rules">
    <div class="client__field">
      <input type="checkbox" id="rules" v-model="isRulesAccepted">
      <label for="rules">Принимаю условия <a href="#" @click.prevent="modal('user_agreement')">Пользовательского соглашения</a> и <a href="#" @click.prevent="modal('privacy')">Политики обработки персональных данных</a></label>
    </div>
    <div class="client__field" v-if="checkVisibleRules">
      <input type="checkbox" id="commission" v-model="isCommissionAccepted" @change="onCommissionChange">
      <label for="commission">Я согласен компенсировать издержки на транзакции в размере {{commission}}р.</label>
    </div>
  </div>

  <ModalPages :modalData="modalData" v-if="isShowModal" @close="closeModal" />

</template>

<script>
import ModalPages from './../../../Shared/Modals/ModalPages.vue'
import axios from 'axios'
export default {
  components: {
    ModalPages,
  },
  props: {
    totalSum: {
      // todo: Проверить, в одном из сценариев пришло String
      type: [Number, String],
      required: true
    },
    discount: {
      type: [Number, String],
      default: '0'
    },
    globalCommission: {
      // todo: Проверить, в одном из сценариев пришло String
      type: [Number, String],
      required: true
    },
    organization: {
      type: Object,
      required: true
    },
    tips: {
      type: Number
    }
  },
  data() {
    return {
      isRulesAccepted: true,
      isCommissionAccepted: true,
      isShowModal: false,
      modalData: null,
      setChecked: true
    }
  },
  mounted() {
    this.$nextTick(() => this.$emit('setCommission', this.commission))
  },
  methods: {
    onCommissionChange(event) {
      if (event.target.checked) {
        this.setChecked = true
        this.$emit('setCommission', this.commission)
      } else {
        this.setChecked = false
        this.$emit('setCommission', 0);
      }
    },
    modal(page) {
      axios.get('/api/pages/' + page, {
      })
        .then((response) => {
          if (response.data.success) {
            this.isShowModal = true;
            this.modalData = response.data.data;
          }
        })
        .catch((error, response) => {
          console.log('error', error)
        })
    },
    closeModal() {
      this.isShowModal = false;
    },
    stringCommaToNumber(value) {
      return parseFloat(value.replace(",","."))
    }
  },
  computed: {
    checkVisibleRules() {
      const tips = Number(this.tips) || 0;
      // Если источник компенсации - клиент или не указан, все работает как сейчас
      if (this.organization.comp_source === 'client' || this.organization.comp_source === '') {
        return true;
      }

      // Если источник компенсации - чаевые
      if (this.organization.comp_source === 'tips') {
        // Если размер чаевых меньше или равен размеру надбавки, все как сейчас
        if (tips < this.globalFixCcommission) {
          return true;
        }

        // Тут мы сбрасываем значение возможной комиссии так как галка в любом случае скрывается
        this.$emit('setFeeConsent', 0)

        // Если размер чаевых больше размера надбавки, скрываем строку с галкой
        return false;
      }

      // По умолчанию возвращаем true (если другие случаи)
      return true;
    },
    commission() {
      // Преобразуем this.tips в число, если оно не является числом
      const tips = Number(this.tips) || 0;

      // Вычисляем discountedSum с учетом tips
      const discountedSum = Number(this.totalSum) + tips;

      // Вычисляем комиссию с учетом глобальной комиссии
      const commission = Math.ceil(Number((discountedSum * (this.stringCommaToNumber(this.globalCommission) / 100)).toFixed(2)));

      // Если комиссия больше tips, возвращаем разницу
      if (commission > tips) {
        return commission - tips;
      }

      // Иначе возвращаем commission
      return commission;
    },
    globalFixCcommission() {
      const tips = Number(this.tips) || 0;
      const discountedSum = Number(this.totalSum) + tips;

      return Math.ceil(discountedSum * (this.stringCommaToNumber(this.globalCommission) / 100));
    }
  },
  watch: {
    totalSum() {
      if (this.setChecked) {
        this.$emit('setCommission', this.commission)
      }
    },
    commission() {
      this.$emit('setCommission', this.commission)
    },
    tips() {
      if (this.isCommissionAccepted) {
        this.$emit('setCommission', this.commission)
      }else {
        this.$emit('setCommission', 0);
        this.$emit('setFeeConsent', this.commission)
      }
    },
    isCommissionAccepted() {
      if (!this.isCommissionAccepted) {
        this.$emit('setFeeConsent', this.commission)
      }

      if (this.isCommissionAccepted) {
        this.$emit('setFeeConsent', 0)
      }
    }
  },
}
</script>
