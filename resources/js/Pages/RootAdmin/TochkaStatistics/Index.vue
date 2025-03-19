<template>
  <div class="deals-container">
    <Head title="Список платежей ТОЧКИ" />
    <h1>Список платежей ТОЧКИ</h1>
    <div v-if="loading">Загрузка...</div>
    <div v-else>
      <div v-for="unidentifiedPayment in unidentifiedPayments" :key="unidentifiedPayment.id" class="payment-card">
        <div class="payment-header" @click="toggleDeals(unidentifiedPayment.id)">
          <p>
            <b :class="getStatusClass(unidentifiedPayment.status)"><span class="text-red-600" v-if="unidentifiedPayment.type === 'return'">Возврат - </span>{{ unidentifiedPayment.payment_id }}</b>
          </p>
          <p><strong>Дата создания:</strong> {{ formatDate(unidentifiedPayment.created_at) }}</p>
<!--          <button class="toggle-button">-->
<!--            {{ isDealsVisible(unidentifiedPayment.id) ? 'Скрыть сделки' : 'Показать сделки' }}-->
<!--          </button>-->
        </div>

        <!-- Раскрываемый список сделок -->
        <div v-if="isDealsVisible(unidentifiedPayment.id)" class="deals-list">
          <div v-if="unidentifiedPayment.details">
            <h3><strong>Детали платежа:</strong></h3>
            <pre>{{ JSON.stringify(unidentifiedPayment.details, null, 2) }}</pre>
          </div>

          <div v-if="unidentifiedPayment.unidentified_payments_prepare_data">
            <h3><strong>Отправляемые данные:</strong></h3>
            <pre>{{ JSON.stringify(unidentifiedPayment.unidentified_payments_prepare_data, null, 2) }}</pre>
          </div>

          <div v-if="unidentifiedPayment.identification_payment">
            <h3><strong>Ответ от идентификации денег:</strong></h3>
            <pre>{{ JSON.stringify(unidentifiedPayment.identification_payment, null, 2) }}</pre>
          </div>

          <div v-if="unidentifiedPayment.other_data">
            <h3><strong>Прочие данные:</strong></h3>
            <pre>{{ JSON.stringify(unidentifiedPayment.other_data, null, 2) }}</pre>
          </div>

          <div v-if="unidentifiedPayment.payments_aproved_list">
            <h3><strong>Оплаченные заказы:</strong></h3>
            <pre>{{ JSON.stringify(unidentifiedPayment.payments_aproved_list, null, 2) }}</pre>
          </div>

          <div v-for="deal in unidentifiedPayment.deals" :key="deal.id" class="deal-card">
            <p><strong>Сделка #{{ deal.id }}</strong></p>
            <p><strong>Дата создания:</strong> {{ formatDate(deal.created_at) }}</p>
            <div v-if="deal.deal_prepare_data">
              <h3><strong>Отправляемые данные:</strong></h3>
              <pre>{{ JSON.stringify(deal.deal_prepare_data, null, 2) }}</pre>
            </div>
            <div v-if="deal.deal_data">
              <h3><strong>Ответ от точки:</strong></h3>
              <pre>{{ JSON.stringify(deal.deal_data, null, 2) }}</pre>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import { Head } from '@inertiajs/vue3'

export default {
  components: { Head },
  data() {
    return {
      loading: true,
      visibleDeals: new Set(),
    };
  },
  props: {
    unidentifiedPayments: Object,
  },
  mounted() {
    this.fetchUnidentifiedPayments();
  },
  methods: {
    getStatusClass(status) {
      const normalized = status.toLowerCase();
      return {
        'success': normalized === 'identification_payment_success',
        'new': normalized === 'new',
        'error': normalized === 'error'
      };
    },
    getStatusTitle(status) {
      switch(status.toLowerCase()) {
        case 'identification_payment_success': return 'Успешно';
        case 'new': return 'Новый';
        case 'error': return 'Ошибка';
        default: return 'Неизвестный статус';
      }
    },
    // Метод для получения данных о платежах
    async fetchUnidentifiedPayments() {
      try {
        const response = await axios.get('/api/unidentified-payments'); // Запрос к API
        this.unidentifiedPayments = response.data;
      } catch (error) {
        console.error('Ошибка при загрузке платежей:', error);
      } finally {
        this.loading = false;
      }
    },
    // Метод для форматирования даты
    formatDate(date) {
      return new Date(date).toLocaleString();
    },
    // Метод для переключения видимости сделок
    toggleDeals(paymentId) {
      if (this.visibleDeals.has(paymentId)) {
        this.visibleDeals.delete(paymentId);
      } else {
        this.visibleDeals.add(paymentId);
      }
    },
    // Метод для проверки видимости сделок
    isDealsVisible(paymentId) {
      return this.visibleDeals.has(paymentId);
    },
  },
};
</script>

<style scoped>
.deals-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
}

.payment-card {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 16px;
  margin-bottom: 16px;
  background-color: #f9f9f9;
}

.payment-header {
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.toggle-button {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
}

.toggle-button:hover {
  background-color: #0056b3;
}

.deals-list {
  margin-top: 16px;
  padding-left: 16px;
  border-left: 2px solid #007bff;
}

.deal-card {
  margin-top: 16px;
  padding: 12px;
  background-color: #fff;
  border: 1px solid #eee;
  border-radius: 4px;
}

pre {
  background-color: #eee;
  padding: 10px;
  border-radius: 4px;
  overflow-x: auto;
}

.status-container {
  /*display: flex;*/
  align-items: center;
  gap: 8px;
}

.status-icon_unidentifiedPayment {
  font-size: 12px;
  line-height: 1;
  transition: opacity 0.3s;
}

.success {
  color: #4CAF50; /* Зеленый */
}

.new {
  color: #FFC107; /* Желтый */
}

.error {
  color: #F44336; /* Красный */
}
</style>
