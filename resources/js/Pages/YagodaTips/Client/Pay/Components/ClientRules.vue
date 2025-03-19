<template>
  <div class="ytips-pay__rules">
    <div class="client__field">
      <input type="checkbox" id="rules" v-model="isRulesAccepted">
      <label for="rules">Принимаю условия <a href="#" @click.prevent="modal('user_agreement')">Пользовательского соглашения</a> и <a href="#" @click.prevent="modal('privacy')">Политики обработки персональных данных</a></label>
    </div>
    <div class="client__field">
      <input type="checkbox" id="commission" v-model="isCommissionAccepted" @change="onCommissionChange">
      <label for="commission">Я согласен компенсировать издержки на транзакции в размере {{commission}}р.</label>
    </div>
  </div>



</template>

<script>
import ModalPages from '../../../Shared/Modals/ModalPages.vue'
import axios from 'axios'
export default {
  components: {
    ModalPages,
  },
  props: {
    totalSum: {
      type: Number,
      required: true
    },
    globalCommission: {
      type: Number,
      required: true
    },
    group: {
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
      if (page === 'user_agreement' || page === 'privacy') {
        axios.get('/api/pages/' + page, {
        })
          .then((response) => {
            if (response.data.success) {
              this.isShowModal = true;

              const modalData = response.data.data;
              this.modalData = modalData;
              this.$emit('modalData', modalData);
              this.modalOpen()
            }
          })
          .catch((error, response) => {
            console.log('error', error)
          })
      }
    },
    modalOpen() {
      this.$emit('modalOpen');
    },
    modalClose() {
      // this.isShowModal = false;
      this.$emit('modalClose');
    },
  },
  computed: {
    commission() {
      // Преобразуем this.tips в число, если оно не является числом
      const tips = Number(this.tips) || 0;

      // Вычисляем комиссию с учетом глобальной комиссии
      const commission = Math.ceil(Number((tips * (this.globalCommission / 100)).toFixed(2)));

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

      return Math.ceil(discountedSum * (this.globalCommission / 100));
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
