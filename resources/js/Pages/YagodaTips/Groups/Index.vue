<template>
  <div class="superadmin">
    <Head title="Настройки" />

    <TipsHeader />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" href="/tips/groups_settings"></Link>
        <div class="ytips-top__title">НАСТРОЙКИ ГРУППЫ</div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" id="name" v-model="group.full_name" ref="full_name">
            <label for="name">Брендовое наименование</label>
            <div class="form__error" v-if="submitted && !group.full_name">Поле не может быть пустым</div>
          </div>

          <div class="form__item">
            <v-select :options="['Выбрать', ...Object.values(formattedArray)]"
                      v-model="selectActivityTypeData"
                      :clearable="false"
                      @option:selected="coverByWork(selectActivityTypeData)"
            ></v-select>
            <label>Основное направление деятельности</label>
          </div>
        </div>
      </div>
      <div class="form__item form__item_logo">
        <div class="form__wrapper">
          <label for="avatar">
            <img v-if="group.logo_path" :src="group.logo_path">
          </label>
          <input type="file" id="avatar" @change="handleImageUploadLogo">
        </div>
        <div class="form__note">Логотип</div>
      </div>
    </div>
    <div class="superadmin__container superadmin__container_nopadding">
      <div class="form__item form__item_cover">
        <label for="cover">
          <img v-if="group.photo_path" :src="group.photo_path">
          <img v-else :src="coverCurrent" alt="Обложка по умолчанию">
        </label>
        <div class="form__note">Панорама заведения для шапки страницы оплаты</div>
        <input type="file" id="cover" @change="handleImageUpload">
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" inputmode="numeric" id="contact_phone" v-model="group.contact_phone" v-mask="'+7 (###) ###-##-##'"
                   ref="contact_phone">
            <label for="contact_phone">Телефон контактного лица</label>
            <div class="form__error" v-if="submitted && !group.contact_phone">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidContactPhone">Некорректный номер телефона</div>
          </div>
          <div class="form__item">
            <textarea id="contact_name" v-model="group.contact_name" ref="contact_name"></textarea>
            <label for="contact_name">ФИО контактного лица</label>
            <div class="form__error" v-if="submitted && !group.contact_name">Поле не может быть пустым</div>
          </div>
          <div class="form__item">
            <input type="email" inputmode="email" id="email" v-model="group.email" ref="email">
            <label for="email">E-mail для отчетов</label>
            <div class="form__error" v-if="submitted && !group.email">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidEmail">Некорректный email</div>
          </div>
        </div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" inputmode="numeric" id="backup_card" v-model="group.backup_card"
                   ref="backup_card" maxlength="18" @input="handleCardInput" >
            <label for="backup_card">Номер банковской карты. На этот номер будут переводиться чаевые в случае
              отстутствия карты у Сотрудника.</label>
            <div class="form__error" v-if="submitted && !group.backup_card">Поле не может быть пустым</div>
          </div>
        </div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__note"><span class="font-bold">Предустановленные чаевые.</span> Эти значения чаевых будут отображаться на странице оплаты чаевых по умолчанию.</div>
      <div class="superadmin__tips mb-7">
        <div
          v-for="tip in tips"
          :key="tip.id"
          @click="selectTip(tip.id)"
          :class="['superadmin__tip', {'superadmin__tip_active': tip.isActive}]">{{ tip.value }}₽
        </div>
      </div>
      <span class="errorMessage" v-if="errorMessage">{{ errorMessage }}</span>
      <button @click="submitForm" class="btn btn_w100">Сохранить</button>
      <div class="superadmin__note"></div>
      <span @click="deleteOrganization" class="delete-button">Удалить группу</span>
    </div>
  </div>

  <Modal
    :is-active="isModalActive"
    @close="closeModal"
    :form="true"
    messageButton="Установить"
    @confirmed="setTip"
    :value="selectedTip.value"
  />

  <cropper-modal
    :is-active="isCropperModalLogoActive"
    messageButton="ПРИМЕНИТЬ"
    ref="cropperModal"
    :imageToEdit="group.logo_path"
    @save="saveCroppedImageLogo"
    @close="isCropperModalLogoActive = false"
  />

  <cropper-modal-stencil
    type="stencil"
    :is-active="isCropperModalActive"
    messageButton="ПРИМЕНИТЬ"
    ref="cropperModal"
    :imageToEdit="group.photo_path"
    :croppedWidth="750"
    :croppedHeight="308"
    @save="saveCroppedImage"
    @close="isCropperModalActive = false"
  />

  <ModalDeleteOrganization
    :is-active="isModalActiveDeleteOrganization"
    @confirmed="handleDelete"
    @close="closeModalDeleteOrganization"
    messageButton="Удалить организацию"
  />

  <Spiner :isLoading="isLoading"></Spiner>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import Spiner from '@/Shared/Spiner.vue'
import axios from 'axios'
import Modal from '../Shared/Modal.vue'
import CropperModal from '../../../Shared/Cropper/CropperModal.vue'
import CropperModalStencil from '../../../Shared/Cropper/CropperModalStencil.vue'
import ModalDeleteOrganization from '../../../Shared/Modals/ModalDeleteOrganization.vue'
import TipsHeader from '@/Pages/YagodaTips/Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
    Head,
    Spiner,
    ModalDeleteOrganization,
    Link,
    vSelect,
    Modal,
    CropperModalStencil,
    CropperModal,
  },
  props: {
    group: Object,
    user: Object,
    formattedArray: Object,
    selectActivityType: Object,
    selectTypeOrganization: Object,
    formattedArrayOrganizationForms: Object,
    selectOrganizationForm: Object,
  },
  computed: {},
  data() {
    return {
      errorMessage: '',
      isLoading : false,
      visibleSmsConfirm: false,
      selectActivityTypeData: this.selectActivityType,
      selectOrganizationFormData: this.selectOrganizationForm,
      isModalActive: false,
      isCropperModalActive: false,
      isCropperModalLogoActive: false,
      isModalActiveDeleteOrganization: false,
      organizationTypeId: '',
      invalidEmail: false,
      invalidOrganizationPhone: false,
      invalidContactPhone: false,
      invalidLegalAddress: false,
      invalidCard: false,
      innExists: false,
      tips: [
        { id: 1, name: 'tips_1', value: this.group.tips_1, isActive: true },
        { id: 2, name: 'tips_2', value: this.group.tips_2, isActive: false },
        { id: 3, name: 'tips_3', value: this.group.tips_3, isActive: false },
        { id: 4, name: 'tips_4', value: this.group.tips_4, isActive: false },
      ],
      selectedTip: {},
      submitted: false,
      errorCard: false,
      checkOrganizationNameIp: false,
      checkOrganizationNameOOO: false,
      daDataErrors: false,
      covers: [
        '/img/content/covers/1.jpg',
        '/img/content/covers/2.jpg',
        '/img/content/covers/3.jpg',
        '/img/content/covers/4.jpg',
        '/img/content/covers/5.jpg',
        '/img/content/covers/6.jpg',
        '/img/content/covers/7.jpg',
      ],
      coverDefault: '/img/demo/persona.png',
      coverCurrent: '/img/demo/persona.png'
    }
  },
  mounted() {
    // Установка обоев
    this.coverByWork()
  },
  methods: {
    coverByWork(work = null) {
      console.log('selectActivityTypeData', this.selectActivityTypeData)
      if (work && work.id) {
        let id = parseInt(work.id - 1);
        this.coverCurrent = this.covers[id];
      } else {
        if (this.selectActivityTypeData && this.selectActivityTypeData.id) {
          let id = parseInt(this.selectActivityTypeData.id - 1);
          this.coverCurrent = this.covers[id];
        } else {
          this.coverCurrent = this.coverDefault
        }
      }
    },
    handleDelete() {
      axios.post('/organizations/delete', {
        groupId: this.group.id,
      })
        .then((response) => {
          this.$inertia.visit('/organizations/choose')
        })
        .catch((error) => {

        })
    },
    compressImage(dataUrl, quality = 0.7) {
      return new Promise((resolve, reject) => {
        const img = new Image()
        img.src = dataUrl

        img.onload = () => {
          const canvas = document.createElement('canvas')
          const ctx = canvas.getContext('2d')

          // Устанавливаем размеры канваса
          canvas.width = img.width
          canvas.height = img.height

          // Рисуем изображение на канвасе
          ctx.drawImage(img, 0, 0)

          // Сжимаем изображение, используя метод toDataURL
          const compressedDataUrl = canvas.toDataURL('image/jpeg', quality)
          resolve(compressedDataUrl)
        }

        img.onerror = (error) => {
          reject(error)
        }
      })
    },
    handleImageUploadLogo(event) {
      this.isCropperModalLogoActive = true
      const file = event.target.files[0]

      const reader = new FileReader()

      reader.onload = (e) => {
        this.group.logo_path = e.target.result
      }

      reader.readAsDataURL(file)

      this.group.saveImageLogo = file
    },
    handleImageUpload(event) {
      this.isCropperModalActive = true
      const file = event.target.files[0]

      const reader = new FileReader()

      reader.onload = (e) => {
        this.group.photo_path = e.target.result
      }

      reader.readAsDataURL(file)

      this.group.saveImage = file
    },
    changeLogo({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.group.saveImageLogo = blob
      })
      console.log(coordinates, canvas)
    },
    saveCroppedImageLogo(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.group.logo_path = compressedDataUrl
        this.group.saveImageLogo = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error)
      })
    },
    changeImage({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.group.saveImage = blob
      })
      console.log(coordinates, canvas)
    },
    saveCroppedImage(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.group.photo_path = compressedDataUrl
        this.group.saveImage = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error)
      })
      // this.catalogImage = dataUrl;
    },
    selectTip(id) {
      this.tips.forEach(tip => {
        tip.isActive = tip.id === id
      })

      this.selectedTip = this.tips.find(tip => tip.isActive)

      this.isModalActive = true
    },
    setTip(value) {
      this.selectedTip = this.tips.find(tip => tip.isActive)
      this.selectedTip.value = value

      axios.post('/tips/setTips', {
        group: this.group,
        tips: this.tips,
      })
        .then((response) => {
          if (response.data.error) {
            this.errorMessage = response.data.message
            this.selectedTip.value = response.data.value
          }
        })
        .catch((error) => {

        })
    },
    closeModal() {
      this.isModalActive = false
    },
    async submitForm() {
      this.submitted = true;

      // Проверка валидации полей

      const firstInvalidField = this.validateFields();
      this.incorrectFields();
      if (firstInvalidField || this.invalidCard || this.invalidEmail || this.invalidOrganizationPhone || this.invalidContactPhone) {
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      this.startLoading()

      try {
        this.group.activity_type_id = this.selectActivityTypeData.id;

        this.$inertia.post('/tips/group', {
          group: this.group,
          user: this.user,
        });
      } catch (error) {
        this.stopLoading()
      }

      setTimeout(() => {
        this.stopLoading()
        this.$inertia.visit('/organizations/choose')
      }, 1000);
    },

    validateCardNumber(cardNumber) {
      return axios.post('/super_admin/settings/validateCardNumber', { card_number: cardNumber })
        .then(response => {
          return response.data.status
        })
        .catch(error => {
          console.error('Ошибка при валидации карты:', error)
          return false
        })
    },

    submitFormOrganization() {
      this.$inertia.post('/super_admin/settings/organization', this.group)
    },
    submitFormOrganizationContact() {
      this.$inertia.post('/super_admin/settings/organization_contact', this.group)
    },
    incorrectFields(){
      if (this.group.contact_phone){
        this.checkPhoneValid(this.group.contact_phone, 'contact_phone')
        if (this.invalidContactPhone){
          this.$refs.contact_phone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
      if (this.group.email){
        this.checkEmailValid(this.group.email)
        if (this.invalidEmail){
          this.$refs.email.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    },
    validateFields() {
      if (!this.group.full_name) {
        return this.$refs.full_name
      }
      if (!this.group.name && Object.keys(this.daData.opf).length === 0) {
        return this.$refs.name;
      }
      if (!this.group.contact_phone) {
        return this.$refs.contact_phone;
      }
      if (!this.group.contact_name) {
        return this.$refs.contact_name;
      }
      if (!this.group.backup_card) {
        return this.$refs.backup_card
      }
    },
    checkPhoneValid(phone, type) {
      const phonePattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
      const hasDigits = /\d/.test(phone);
      if (type === 'phone'){
        this.invalidOrganizationPhone = hasDigits && !phonePattern.test(phone);
      }else {
        this.invalidContactPhone = hasDigits && !phonePattern.test(phone);
      }

    },
    checkEmailValid(email) {
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/
      this.invalidEmail = !emailRegex.test(email)
    },
    checkLenghtString(string) {
      if (string.length < 20) {
        this.invalidLegalAddress = true;
      }
    },

    deleteOrganization() {
      this.isModalActiveDeleteOrganization = true
    },
    deleteNewOrganization() {
      this.startLoading()
      axios.post('/super_admin/deleteNewOrganization', {
        organizationId: this.organization.id
      })
        .then(response => {
          if (response.data.success) {
            this.stopLoading();
            this.$inertia.visit('/organizations/choose');
          }
        })
        .catch(error => {
          console.error(error);
        });
    },
    closeModalDeleteOrganization() {
      this.isModalActiveDeleteOrganization = false
    },
    startLoading() {
      this.isLoading = true;
    },

    stopLoading() {
      this.isLoading = false;
    },
  },
  watch: {
    // Отслеживание изменений в номере карты
    'group.backup_card': function(newValue) {
      this.errorCard = false
    },
    'group.phone': function(newPhone) {
      if (this.invalidOrganizationPhone) {
        this.invalidOrganizationPhone = false;
      }
    },
    'group.contact_phone': function(newValue) {
      if (this.invalidContactPhone) {
        this.invalidContactPhone = false;
      }
    },
    'group.email': function(newValue) {
      if (this.invalidEmail) {
        this.invalidEmail = false;
      }
    },
    'group.legal_address': function(newValue) {
      if (this.invalidLegalAddress) {
        this.invalidLegalAddress = false;
      }
    },
  },
}
</script>

<style scoped lang="scss">
.form__item_logo label img {
  max-width: 100%;
  height: auto;
  max-height: 44px;
}

.form__item_logo .form__wrapper {
  width: 139px;
  height: 53px;
  margin-right: auto;
  margin-left: auto;
  border: 1px solid #e5e5e5;
  border-radius: .5625rem;
  background: #fff;
}

label[for="avatar"] {
  padding: 12px;
}


.delete-button {
  display: block;
  text-align: center;
  font-size: 14px;
  text-transform: uppercase;
  cursor: pointer;
  color: #9ca4b5;
  font-weight: 500;
  margin-top: 32px;
}

input[readonly] {
  color: black !important;
}

input[readonly]:hover {
  cursor: not-allowed;
}

.organization_name {
  font-size: 24px;
  border-bottom: 1px solid #d5d5d5;
  letter-spacing: 1px;
  min-height: 30px;
  display: flex;
  align-items: center;
}

.organization_name:hover {
  cursor: not-allowed;
}

.errorMessage {
  color: red;
  font-size: 11px;
}

.superadmin {
  padding-top: 0;
}
</style>
