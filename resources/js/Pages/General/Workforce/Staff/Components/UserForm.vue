<template>
  <div class="superadmin__block">
    <form class="form" @submit.prevent="submitForm">
      <!--      <div class="form__item">-->
      <!--        <input id="first_name" type="text" v-model="firstName">-->
      <!--        <label for="firstName">Имя</label>-->
      <!--      </div>-->
      <div class="form__item">
        <input type="text" id="phone" v-model="phoneNumber" v-mask="'+7 (###) ###-##-##'" @input="formatPhone" @focus="onFocus">
        <label for="phone" v-if="!errorMessage">Телефон</label>
        <div class="code__error" v-if="errorMessage">{{ errorMessage }}</div>
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
    };
  },
  computed: {
    isMaskValid() {
      return this.phoneNumber.match(/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/) !== null
    },
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
    addStaffInUserList(staff) {
      this.$emit('addStaffInUserList', staff)
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

      axios.post('/storeUser', {
        first_name: this.firstName,
        phone: this.phoneNumber,
        role_slug: 'employee'
      })
        .then(response => {
          if (response && response.data.success === true) {
            this.$toast.open({
              message: response.data.message,
              type: 'success',
              position: 'top-right',
              duration: 2000,
            });

            this.addStaffInUserList(response.data.user)

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
  },
};
</script>

