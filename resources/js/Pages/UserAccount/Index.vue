<template>
  <div class="superadmin" v-if="mainModal">
    <Head title="Аккаунт" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>АККАУНТ</h2>
      </div>
      <div class="form">
        <div class="form__group">
          <div class="form__item">
            <span v-if="!editNumber" class="form__label">{{ user.phone }}</span>
            <input v-else v-model="user.phone" type="tel" id="tel" v-mask="'+7 (###) ###-##-##'" @input="formatPhone">
            <label for="name">Телефон</label>
          </div>
          <div v-if="!editNumber" class="form__actions form__actions_columns">
            <button class="btn btn_text-right" @click="startEditNumber">Изменить номер телефона</button>
          </div>
          <div v-if="editNumber" class="form__actions form__actions_columns">
            <button class="btn btn_text-right" @click="endEditNumber">Отменить</button>
            <button class="btn btn_text-right" @click="sendSms">Подтвердить</button>
          </div>
        </div>
        <div class="form__actions form__actions_columns">
          <button class="btn btn_text-right" @click="showModal">Удалить аккаунт</button>
        </div>
      </div>
    </div>
  </div>

    <Modal
      :is-active="isModalDelete"
      @confirmed="handleDelete"
      @close="closeModal"
      message="Удалить каталог?"
      messageButton="Удалить аккаунт"
    />

  <ModalDeleteAccountConfirm
      :is-active="isModalDeleteAccountConfirm"
      @close="closeModalDeleteAccountConfirm"
      :message="isModalDeleteAccountConfirmMessage"
    />

<Sms v-if="smsView" :phone="user.phone" @closeSms="closeSms"/>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import Modal from '../../Shared/ModalDeleteAccount.vue'
import ModalDeleteAccountConfirm from '../../Shared/Modals/ModalDeleteAccountConfirm.vue'
import Sms from './Components/Sms.vue'

export default {
  components: {
    Modal,
    Link,
    Head,
    ModalDeleteAccountConfirm,
    Sms,
  },
  props: {
    user: Object,
  },
  data() {
    return {
      isModalDelete: false,
      isModalDeleteAccountConfirm: false,
      isModalDeleteAccountConfirmMessage: '',
      editNumber: false,
      smsView: false,
      mainModal: true,
    }
  },
  methods: {
    formatPhone() {
      if (!this.user.phone.startsWith('+7 ')) {
        this.user.phone = '+7 ' + this.user.phone.replace(/^\+7\s?/, '');
      }
    },
    showModal() {
      this.isModalDelete = true;
    },
    closeModal() {
      this.isModalDelete = false;
    },
    showModalDeleteAccountConfirm() {
      this.isModalDeleteAccountConfirm = true;
    },
    closeModalDeleteAccountConfirm() {
      this.isModalDeleteAccountConfirm = false;
    },
    handleDelete() {
      axios.delete(`/user_account/${this.user.id}`)
        .then(response => {
          if (response && response.data.success === false) {
            this.isModalDeleteAccountConfirmMessage = response.data.message;
            this.closeModal();
            this.showModalDeleteAccountConfirm();
          }
          this.$inertia.visit("/");
        })
        .catch(error => {
          console.log('error', error)
        })
    },
    updatePhone() {
      axios.put(`/user_account/${this.user.id}`, {
        phone: this.user.phone
      })
        .then(response => {
          if (response && response.data.success === true) {
            this.$toast.open({
              message: 'Номер телефона успешно изменен',
              type: 'success',
              position: 'top-right',
              duration: 2000,
            });
          }
        })
        .catch(error => {
          console.log('error', error)
        })
    },
    startEditNumber() {
      this.editNumber = true
    },
    endEditNumber() {
      this.editNumber = false
    },
    sendSms() {
      this.editNumber = false
      this.smsView = true
      this.mainModal = false
    },
    closeSms() {
      this.editNumber = false
      this.smsView = false
      this.mainModal = true
    }
  },
}
</script>

<style scoped>
.form__label {
  font-size: 24px;
}
</style>

