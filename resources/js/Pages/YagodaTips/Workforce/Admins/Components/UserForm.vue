<template>
  <div class="superadmin__block">
    <form class="form" @submit.prevent="submitForm">
      <div class="form__item">
        <input type="text" id="phone" v-model="phoneNumber" v-mask="'+7 (###) ###-##-##'" @input="formatPhone" @focus="onFocus">
        <label for="phone" v-if="!errorMessage">Телефон</label>
        <div class="code__error" v-if="errorMessage">{{ errorMessage }}</div>
        <div class="contacts-icon"
             @click="selectContact"
             v-if="isAndroidChrome"
        ></div>
      </div>
      <div class="form__actions">
        <button v-if="!errorMessage" type="submit" class="btn btn_text">ДОБАВИТЬ</button>
      </div>
    </form>
  </div>

  <Modal :is-active="isModal"
         @close="closeModal"
         @confirm="closeModal"
         message="Пользователь с таким номером телефона уже присутствует этой роли"
         messageButton="ОК"/>
</template>

<script>
import axios from 'axios'
import Modal from '../../../../../Shared/Modal.vue'

export default {
  components: {
    Modal
  },
  data() {
    return {
      firstName: '',
      phoneNumber: '+7 ',
      isModal: false,
      errorMessage: '',
      isAndroidChrome: false
    };
  },
  computed: {
    isMaskValid() {
      return this.phoneNumber.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/) !== null
    },
  },
  mounted() {
    this.checkUserAgent()
  },
  methods: {
    openModal() {
      this.isModal = true
    },

    closeModal() {
      this.isModal = false
    },
    clearErrors() {
      this.errorMessage = ''
    },
    onFocus() {
      this.errorMessage = ''
    },
    formatPhone() {
      if (!this.phoneNumber.startsWith('+7 ')) {
        this.phoneNumber = '+7 ' + this.phoneNumber.replace(/^\+7\s?/, '');
      }
    },
    addAdminInUserList(admin) {
      this.$emit('addAdminInUserList', admin)
    },
    submitForm() {
      this.clearErrors()
      if (!this.isMaskValid) {
        this.$toast.open({
          message: 'Некорректный формат номера телефона',
          type: 'error',
          position: 'top-right',
          duration: 2000,
        });
        return;
      }

      axios.post('/tips/storeUser', {
        first_name: this.firstName,
        phone: this.phoneNumber,
        role_slug: 'admin'
      })
        .then(response => {
          if (response && response.data.success === true) {
            this.$toast.open({
              message: response.data.message,
              type: 'success',
              position: 'top-right',
              duration: 2000,
            });

            this.addAdminInUserList(response.data.user)

            // this.$inertia.reload();
          }

          if (response && response.data.success === false) {
            //this.openModal();
            this.errorMessage = response.data.message
          }else{
            this.phoneNumber = '+7 ';
          }
        })
        .catch(error => {
          console.error(error);
        });


    },

    async selectContact() {
      if (!("contacts" in navigator && "select" in navigator.contacts)) {
        alert("Ваш браузер не поддерживает выбор контактов.");
        return;
      }

      try {
        const contacts = await navigator.contacts.select(["tel"], { multiple: false });
        if (contacts.length > 0 && contacts[0].tel.length > 0) {
          phone.value = contacts[0].tel[0]; // Берем первый номер из контакта
        }
      } catch (error) {
        console.error("Ошибка при получении контакта:", error);
      }
    },
    checkUserAgent() {
      const userAgent = navigator.userAgent.toLowerCase();
      this.isAndroidChrome =
        "contacts" in navigator &&
        "select" in navigator.contacts &&
        userAgent.includes("android") &&
        userAgent.includes("chrome");
    }
  },
};
</script>
<style lang="scss" scoped>
.form__item {
  position: relative;
  .contacts-icon {
    position: absolute;
    right: 0;
    top: 0;
    height: 15px;
    width: 18px;
    background-image: url("data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTciIGhlaWdodD0iMjAiIHZpZXdCb3g9IjAgMCAxNyAyMCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHBhdGggZD0iTTEgMTguNzVDMSAxNC42MDc5IDQuMzU3ODYgMTEuMjUgOC41IDExLjI1QzEyLjY0MjEgMTEuMjUgMTYgMTQuNjA3OSAxNiAxOC43NU0xMiA0Ljc1QzEyIDYuNjgzIDEwLjQzMyA4LjI1IDguNSA4LjI1QzYuNTY3IDguMjUgNSA2LjY4MyA1IDQuNzVDNSAyLjgxNyA2LjU2NyAxLjI1IDguNSAxLjI1QzEwLjQzMyAxLjI1IDEyIDIuODE3IDEyIDQuNzVaIiBzdHJva2U9ImJsYWNrIiBzdHJva2Utd2lkdGg9IjEuMjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPgo8L3N2Zz4K");
  }
}
</style>

