<template>
  <div class="admin">
    <div class="admin__container">
      <div class="admin-choose-masters">
        <div class="admin-choose-masters__top">
          <img class="line-icon" @click="goBack" src="/img/icon_line.svg" alt="">
          <h2>Клиент</h2>
          <img class="close-icon" @click="cancelOrder" src="/img/icon_close.svg" alt="">
        </div>
        <div class="form mt-10">
          <div class="form__item">
            <input :class="{ 'text-red-600': isPhoneNotInvalid }"
                   class="font-medium"
                   @input="handlePhoneInput"
                   @paste="handlePaste"
                   @change="handleAutoComplete"
                   v-model="form.phone"
                   type="tel"
                   id="tel"
                   ref="phoneInput"
                   v-mask="'+7 (###) ###-##-##'">
            <label for="tel">Телефон</label>
          </div>

          <div class="form__item">
            <input class="font-medium" v-model="form.name" type="text" id="name">
            <label for="tel">Имя</label>
          </div>
        </div>

      <div class="client__item" v-for="client in foundClients" :key="client.phone" v-if="foundClients.length > 0">
          <div class="client__icon__none"></div>
          <div class="client__name">
            <span class="">{{ client.phone }}</span>
            <p class="mt-2 font-normal">{{ client.name }}</p>
          </div>
          <div class="client__price">
            <button @click="selectClient(client)" class="btn btn_arrow px-4 min-h-14 bg-[#38b0b0]">ВЫБРАТЬ</button>
          </div>
        </div>
      </div>

      <div class="admin-orders__buttons clientSearchFooter">
        <div class="admin__container">
<!--          <button @click="saveOrder" class="btn">СОХРАНИТЬ<br> ЗАКАЗ</button>-->
<!--          <button @click="showQrCode" class="btn btn_arrow">К ОПЛАТЕ</button>-->
          <button @click="showMasterChoice" class="btn btn_arrow">ДАЛЕЕ</button>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import axios from 'axios'

export default {
  components: {

  },
  props: {
    editOrder: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      form: {
        phone: '+7 ',
        name: '',
      },
      visibleMasterChoice: true,
      foundClients: [],
    }
  },
  mounted() {
    this.$refs.phoneInput.focus();
    this.$nextTick(() => this.$refs.phoneInput.focus())

    // Фиксация кнопок внизу
    const clientSearchFooter = document.querySelector('.clientSearchFooter');
    if (clientSearchFooter && window.visualViewport) {
      const vv = window.visualViewport;
      function fixPosition() {
        clientSearchFooter.style.top = `${vv.height - 90}px`;
      }
      vv.addEventListener('resize', fixPosition);
      fixPosition();
    }
// Пред установка данных клиента при редактировании заказа
     this.setClientData()
  },
  computed: {
    isPhoneNotInvalid() {
      return this.form.phone.length >= 4 && this.form.phone.length < 18
    }
  },
  methods: {
    setClientData() {
      if (this.editOrder.client) {
        this.form.phone = this.editOrder.client.phone;
        this.form.name = this.editOrder.client.name;
      } else {
        this.form.phone = '+7 ';
        this.form.name = '';
      }
    },
    saveOrder() {
      this.$emit('saveOrder', this.form)
    },

    //region Форматирование телефона-
    async handlePhoneInput() {
      const self = this;

      if (this.searchTimeout) {
        clearTimeout(this.searchTimeout);
      }

      this.searchTimeout = setTimeout(async function () {
        const phoneDigits = self.form.phone;

        if (phoneDigits.length >= 5) {
          try {
            const response = await axios.get("/api/search-clients", {
              params: { phone: phoneDigits },
            });
            self.foundClients = response.data;
          } catch (error) {
            console.error("Ошибка при поиске клиентов:", error);
            self.foundClients = [];
          }
        } else {
          self.foundClients = [];
        }
      }, 300);
    },

    handlePaste(event) {
      event.preventDefault();
      let paste = (event.clipboardData || window.clipboardData).getData("text");
      this.cleanAndFormatPhone(paste);
    },

    handleAutoComplete() {
      this.cleanAndFormatPhone(this.form.phone);
    },

    cleanAndFormatPhone(value) {
      if (!value) return;

      let cleaned = value.replace(/\D/g, ""); // Оставляем только цифры

      if (cleaned.startsWith("8")) {
        cleaned = "7" + cleaned.slice(1);
      } else if (!cleaned.startsWith("7")) {
        cleaned = "7" + cleaned;
      }

      cleaned = cleaned.slice(0, 11);

      this.$nextTick(() => {
        this.form.phone = cleaned; // v-mask автоматически отформатирует
      });
    },
    //endregion

    showQrCode() {
      this.$emit('showQrCode', this.form)
    },

    showMasterChoice() {
      this.$emit('showMasterChoice', this.form)
    },

    async saveNewClient() {
      if (!this.form.phone || this.form.phone.length !== 18) {
        return;
      }

      const phoneDigits = this.form.phone;

      try {
        const response = await axios.post("/api/save-client", {
          phone: phoneDigits,
          name: this.form.name,
          organization_id: 1,
        });
        // this.foundClients = [response.data]; // Добавляем нового клиента в список
        // this.selectClient(response.data); // Автоматически выбираем нового клиента
      } catch (error) {
        console.error("Ошибка при сохранении клиента:", error);
      }
    },

    // Выбор клиента
    selectClient(client) {

      this.form.phone = client.phone;
      this.form.name = client.name;
      this.form.discount = client.discount;
      // this.foundClients = [];
      this.$emit('showMasterChoice', this.form)
    },
    goBack(){
      this.$emit('goBack')
    },

    cancelOrder() {
      this.$emit('cancelOrder')
    },
  },
};
</script>

<style scoped>
.admin-bill__price,
.admin-bill__quantity,
.admin-bill__list,
.admin-bill__total {
  text-decoration: none; /* Отменяем подчеркивание */
}

.admin-masters__item.selected {
  background-color: rgba(0, 0, 0, 0.1); /* Пример: полупрозрачный черный фон */
  /* Или другие стили для затемнения */
}

.masters {
  cursor: pointer;
}

.close-icon {
  cursor: pointer;
}

.line-icon {
  cursor: pointer;
  margin-top: 5px;
}

.admin-choose-masters__top h2 {
  font-size: 16px;
  font-weight: 300;
  margin-right: -220px;
  padding: 0;
  text-transform: uppercase;
}

.error-message {
  color: red; /* Цвет сообщения об ошибке */
  margin: 10px;
  text-decoration: none;
}

img {
  border-radius: 20%;
}
.admin-orders__buttons {
  background-color: #ffffff;
  padding: 10px;
  bottom: 0;
  max-height: 90px;
  max-width: 100%;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
}
.admin-orders__buttons .admin__container {
  display: flex;
  justify-content: space-between;
  gap: .8125rem;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
</style>
