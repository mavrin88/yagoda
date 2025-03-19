<template>
  <div class="superadmin">
    <Head title="Настройки" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link v-if="organization.status === 'new_without_save'" @click="deleteNewOrganization"
              class="back" href="/organizations/choose">назад
        </Link>
        <Link v-else class="back" href="/">назад</Link>
        <h2>НАСТРОЙКИ</h2>
      </div>
      <div class="superadmin__block">
        <div class="form">
          <!--          <div class="form__item">-->
          <!--            <input type="text" id="phone" v-model="user.phone" v-mask="'+7 (###) ###-##-##'">-->
          <!--            <label for="phone">Телефон</label>-->
          <!--          </div>-->
          <!--          <div class="form__item">-->
          <!--            <input type="password" id="password" v-model="user.password">-->
          <!--            <label for="password">Пароль</label>-->
          <!--          </div>-->
          <div class="form__item">
            <input type="text" id="name" v-model="organization.full_name" ref="full_name">
            <label for="name">Брендовое наименование</label>
            <div class="form__error" v-if="submitted && !organization.full_name">Поле не может быть пустым</div>
          </div>

          <div class="form__item">
            <v-select :options="['Выбрать', ...Object.values(formattedArray)]" v-model="selectActivityTypeData"
                      :clearable="false"></v-select>
            <label>Основное направление деятельности</label>
          </div>
        </div>
      </div>
      <div class="form__item form__item_logo">
        <div class="form__wrapper">
          <label for="avatar">
            <img v-if="organization.logo_path" :src="organization.logo_path">
          </label>
          <input type="file" id="avatar" @change="handleImageUploadLogo">
        </div>
        <div class="form__note">Логотип</div>
      </div>
    </div>
    <div class="superadmin__container superadmin__container_nopadding">
      <div class="form__item form__item_cover">
        <label for="cover">
          <img v-if="organization.photo_path" :src="organization.photo_path">
          <img v-else src="/img/demo/persona.png">
        </label>
        <div class="form__note">Панорама заведения для шапки страницы оплаты</div>
        <input type="file" id="cover" @change="handleImageUpload">
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__block">
        <div class="form">
          <!--          <div class="form__item">-->
          <!--&lt;!&ndash;            <pre><code>{{selectOrganizationFormData}}</code></pre>&ndash;&gt;-->
          <!--            <v-select :options="['Выбрать', ...Object.values(formattedArrayOrganizationForms)]" v-model="selectOrganizationFormData" :clearable="false"></v-select>-->
          <!--            <label>Форма собственности</label>-->
          <!--            <div class="form__error" v-if="submitted && !selectOrganizationFormData.id">Необходимо выбрать</div>-->
          <!--          </div>-->

          <div class="form__item">
            <input type="text" inputmode="numeric" id="inn" v-model="organization.inn" ref="inn" @blur="submitINN" @input="clearError">
            <div class="form__error" v-if="daDataErrors">По введенному ИНН -организаций не найдено</div>
            <div class="form__error" v-if="submitted && !organization.inn">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && innExists">Организация с таким ИНН уже имеется</div>
            <label for="inn">ИНН</label>
          </div>

          <div class="form__item">
            <!--            <input type="text" id="name2" v-model="organization.name" ref="name" readonly>-->
            <div class="organization_name" ref="name">{{ organization.name }}</div>
            <label for="name">Наименование организации</label>
            <div class="form__error" v-if="submitted && !organization.name">По введенному ИНН - организаций не найдено</div>
            <!--            <div class="form__error" v-if="submitted && !organization.name" ref="selectOrganizationFormData">Поле не может быть пустым</div>-->
            <!--            <div class="form__error" v-if="submitted && checkOrganizationNameIp" ref="selectOrganizationFormData">Поле должно начинаться с ИП или Индивидуальный</div>-->
            <!--            <div class="form__error" v-if="submitted && checkOrganizationNameOOO" ref="selectOrganizationFormData">Поле должно начинаться с ООО или Общество</div>-->
          </div>

          <div class="form__item" v-if="kpp">
            <input type="text" inputmode="numeric" id="kpp" v-model="organization.kpp" ref="kpp">
            <label for="kpp">КПП</label>
            <div class="form__error" v-if="submitted && !organization.kpp && daData.opf.short !== 'ИП'">Поле не может
              быть пустым
            </div>
          </div>

          <div class="form__item">
            <!-- Подключить https://github.com/vueuse/vueuse/blob/main/packages/core/useTextareaAutosize/demo.vue-->
            <textarea id="legal_address" v-model="organization.legal_address" ref="legal_address"></textarea>
            <label for="legal_address">Юридический адрес</label>
            <div class="form__error" v-if="submitted && !organization.legal_address">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidLegalAddress">Поле не может содержать менее 20 символов</div>
          </div>

          <div class="form__item">
            <input type="text" inputmode="numeric" id="organization_phone" v-model="organization.phone" v-mask="'+7 (###) ###-##-##'" ref="organization_phone">
            <label for="organization_phone">Телефон организации</label>
            <div class="form__error" v-if="submitted && !organization.phone">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidOrganizationPhone">Некорректный номер телефона</div>
          </div>

          <!--          <div class="form__item">-->
          <!--            <input type="text" id="ogrn" v-model="organization.registration_number" ref="registration_number">-->
          <!--            <label for="ogrn">ОГРН / ОГРНИП</label>-->
          <!--            <div class="form__error" v-if="submitted && !organization.registration_number">Поле не может быть пустым</div>-->
          <!--          </div>-->
          <div class="form__item">
            <input type="email" inputmode="email" id="email" v-model="organization.email" ref="email">
            <label for="email">E-mail для отчетов</label>
            <div class="form__error" v-if="submitted && !organization.email">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidEmail">Некорректный email</div>
          </div>
        </div>
      </div>
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" inputmode="numeric" id="contact_phone" v-model="organization.contact_phone" v-mask="'+7 (###) ###-##-##'"
                   ref="contact_phone">
            <label for="contact_phone">Телефон контактного лица</label>
            <div class="form__error" v-if="submitted && !organization.contact_phone">Поле не может быть пустым</div>
            <div class="form__error" v-if="submitted && invalidContactPhone">Некорректный номер телефона</div>
          </div>
          <div class="form__item">
            <!-- Подключить https://github.com/vueuse/vueuse/blob/main/packages/core/useTextareaAutosize/demo.vue-->
            <textarea id="contact_name" v-model="organization.contact_name" ref="contact_name"></textarea>
            <label for="contact_name">ФИО контактного лица</label>
            <div class="form__error" v-if="submitted && !organization.contact_name">Поле не может быть пустым</div>
          </div>
        </div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__block">
        <div class="form">
          <div class="form__item">
            <input type="text" inputmode="numeric" id="backup_card" v-model="organization.backup_card"
                   ref="backup_card" maxlength="18" @input="handleCardInput" >
            <label for="backup_card">Номер банковской карты. На этот номер будут переводиться чаевые в случае
              отстутствия карты у Сотрудника.</label>
<!--            <div class="form__error" v-if="submitted && !organization.backup_card">Поле не может быть пустым</div>-->
<!--            <div class="form__error" v-if="submitted && errorCard">Неверный номер карты</div>-->
<!--            <div class="form__error" v-if="submitted && invalidCard">Поле не может содержать меньше 16 символов</div>-->
          </div>
        </div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="superadmin__note">Установите предлагаемые значения чаевых.</div>

      <div class="superadmin__tips">
        <div
          v-for="tip in tips"
          :key="tip.id"
          @click="selectTip(tip.id)"
          :class="['superadmin__tip', {'superadmin__tip_active': tip.isActive}]">{{ tip.value }}%
        </div>
      </div>
      <span class="errorMessage" v-if="errorMessage">{{ errorMessage }}</span>
      <div class="superadmin__block">
        <div class="superadmin__comission">
          <p>Комиссия за эквайринг:</p>
          <p><b>{{ organization.acquiring_fee }}</b> <span>%</span></p>
        </div>
      </div>

      <div class="superadmin__block">
        <div class="superadmin__comission">
          <p>Агентский договор N:</p>
          <p><b>{{ organization.agency_agreement_number }}</b></p>
        </div>
        <div class="superadmin__comission">
          <p>от:</p>
          <p><b>{{ organization.agency_agreement_date }}</b></p>
        </div>
      </div>

      <button @click="submitForm" class="btn btn_w100">Сохранить</button>
      <div class="superadmin__note"></div>
      <span @click="deleteOrganization" class="delete-button">Удалить организацию</span>
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
    :imageToEdit="organization.logo_path"
    @save="saveCroppedImageLogo"
    @close="isCropperModalLogoActive = false"
  />

  <cropper-modal-stencil
    type="stencil"
    :is-active="isCropperModalActive"
    messageButton="ПРИМЕНИТЬ"
    ref="cropperModal"
    :imageToEdit="organization.photo_path"
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
import Modal from '../../../Shared/Modal.vue'
import CropperModal from '../../../Shared/Cropper/CropperModal.vue'
import CropperModalStencil from '../../../Shared/Cropper/CropperModalStencil.vue'
import ModalDeleteOrganization from '../../../Shared/Modals/ModalDeleteOrganization.vue'

export default {
  components: {
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
    organization: Object,
    user: Object,
    formattedArray: Object,
    selectActivityType: Object,
    selectTypeOrganizarion: Object,
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
      typeOrganization: [
        {
          'id': 1,
          'label': 'ООО, АО, ЗАО, ПАО, НКО, ОП',
          'value': 1,
        },
        {
          'id': 2,
          'label': 'ИП',
          'value': 2,
        },
        {
          'id': 3,
          'label': 'Самозанятый',
          'value': 3,
        },
      ],
      tips: [
        { id: 1, name: 'tips_1', value: this.organization.tips_1, isActive: true },
        { id: 2, name: 'tips_2', value: this.organization.tips_2, isActive: false },
        { id: 3, name: 'tips_3', value: this.organization.tips_3, isActive: false },
        { id: 4, name: 'tips_4', value: this.organization.tips_4, isActive: false },
      ],
      selectedTip: {},
      submitted: false,
      errorCard: false,
      checkOrganizationNameIp: false,
      checkOrganizationNameOOO: false,
      kpp: false,
      daData: {
        opf: {},
      },
      daDataErrors: false,
    }
  },
  methods: {
    handleDelete() {
      axios.post('/organizations/delete', {
        organizationId: this.organization.id,
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
        this.organization.logo_path = e.target.result
      }

      reader.readAsDataURL(file)

      this.organization.saveImageLogo = file
    },
    handleImageUpload(event) {
      this.isCropperModalActive = true
      const file = event.target.files[0]

      const reader = new FileReader()

      reader.onload = (e) => {
        this.organization.photo_path = e.target.result
      }

      reader.readAsDataURL(file)

      this.organization.saveImage = file
    },
    changeLogo({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.organization.saveImageLogo = blob
      })
      console.log(coordinates, canvas)
    },
    saveCroppedImageLogo(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.organization.logo_path = compressedDataUrl
        this.organization.saveImageLogo = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error)
      })
    },
    changeImage({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.organization.saveImage = blob
      })
      console.log(coordinates, canvas)
    },
    saveCroppedImage(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.organization.photo_path = compressedDataUrl
        this.organization.saveImage = compressedDataUrl
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

      axios.post('/super_admin/settings/tips', {
        organization: this.organization,
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
    handleCardInput(event) {
      // this.organization.backup_card = this.organization.backup_card.replace(/\D/g, '');
      // if (this.organization.backup_card.length < 16) {
      //   this.invalidCard = true;
      // } else {
      //   this.invalidCard = false;
      // }
    },
    submitINN() {
      if (this.organization.inn === '') {
        return
      }

      this.innExists = false

      axios.post('/api/daData', {
        type: 'party',
        number: this.organization.inn,
      })
        .then((response) => {
          if (response.data && response.data[0] && response.data[0].data && response.data[0].data.name && response.data[0].data.name.full) {
            this.daDataErrors = false
            this.organization.name = response.data[0].data.name.short_with_opf
            if (response.data[0].data.opf.short == 'ИП' && response.data[0].data.state.status == 'LIQUIDATED'){
              this.organization.name = response.data[0].data.name.full
            }
            if (response.data[0].data.opf.short == 'ООО' && response.data[0].data.state.status == 'LIQUIDATED'){
              this.organization.name = ''
              this.daDataErrors = true
            }
            this.daData = response.data[0].data
          } else {
            this.daDataErrors = true
            this.organization.name = ''
          }

        })
        .catch((error) => {

        })
    },
    clearError() {
      if (this.organization.inn === '') {
        this.daDataErrors = false
      }
    },
    async submitForm() {
      this.submitted = true;

      // Проверка валидации полей
      const firstInvalidField = this.validateFields();
      this.incorrectFields();

      if (firstInvalidField || this.invalidCard || this.invalidEmail || this.invalidOrganizationPhone || this.invalidContactPhone || this.invalidLegalAddress) {
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return;
      }

      const inn = this.organization.inn;
      const organizationId = this.organization.id;

      try {
        const response = await fetch(`/check-inn?inn=${inn}&organizationId=${organizationId || ''}`);
        const data = await response.json();

        if (data.exists) {
          this.innExists = true;
          this.$refs.inn.scrollIntoView({ behavior: 'smooth', block: 'center' });
          return;
        }

        this.organization.activity_type_id = this.selectActivityTypeData.id;

        this.$inertia.post('/super_admin/settings/', {
          organization: this.organization,
          user: this.user,
        });
      } catch (error) {
        console.error('Ошибка при проверке ИНН:', error);
        alert('Произошла ошибка при проверке ИНН. Попробуйте ещё раз.');
      }
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
      this.$inertia.post('/super_admin/settings/organization', this.organization)
    },
    submitFormOrganizationContact() {
      this.$inertia.post('/super_admin/settings/organization_contact', this.organization)
    },
    incorrectFields(){
      if (this.organization.phone){
        this.checkPhoneValid(this.organization.phone, 'phone')
        if (this.invalidOrganizationPhone){
          this.$refs.organization_phone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
      if (this.organization.contact_phone){
        this.checkPhoneValid(this.organization.contact_phone, 'contact_phone')
        if (this.invalidContactPhone){
          this.$refs.contact_phone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
      if (this.organization.email){
        this.checkEmailValid(this.organization.email)
        if (this.invalidEmail){
          this.$refs.email.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
      if (this.organization.legal_address){
        this.checkLenghtString(this.organization.legal_address)
        if (this.invalidLegalAddress){
          this.$refs.legal_address.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    },
    validateFields() {

      if (!this.selectOrganizationFormData.id) {
        return this.$refs.name
      }

      if (this.selectOrganizationFormData.id === 1) {
        if (!this.organization.full_name) {
          return this.$refs.full_name
        }
        if (!this.organization.inn) {
          return this.$refs.inn;
        }

        if (!this.organization.name && Object.keys(this.daData.opf).length === 0) {
          return this.$refs.name;
        }
        // if (!this.organization.name || Object.keys(this.daData.opf).length === 0 ||
        //   (!this.organization.name.startsWith('ООО') && !this.organization.name.startsWith('Общество'))) {
        //   this.checkOrganizationNameOOO = true;
        //   return this.$refs.name;
        // }
        if (!this.organization.legal_address) {
          return this.$refs.legal_address;
        }
        if (this.kpp) {
          if (!this.organization.kpp) {
            return this.$refs.kpp
          }
        }
        if (!this.organization.email) {
          return this.$refs.email;
        }
        if (!this.organization.phone) {
          return this.$refs.organization_phone;
        }
        if (!this.organization.contact_phone ) {
          return this.$refs.contact_phone;
        }

        if (!this.organization.contact_name) {
          return this.$refs.contact_name;
        }
        if (!this.organization.backup_card) {
          // return this.$refs.backup_card
        }
      }

      if (this.selectOrganizationFormData.id === 2) {
        if (!this.organization.full_name) {
          return this.$refs.full_name
        }
        if (!this.organization.inn) {
          return this.$refs.inn;
        }
        if (!this.organization.name && Object.keys(this.daData.opf).length === 0) {
          return this.$refs.name;
        }
        // if (!this.organization.name ||
        //   (!this.organization.name.startsWith('ИП') && !this.organization.name.startsWith('Индивидуальный'))) {
        //   this.checkOrganizationNameIp = true;
        //   return this.$refs.name;
        // }
        if (!this.organization.legal_address) {
          return this.$refs.legal_address;
        }
        if (this.kpp) {
          if (!this.organization.kpp) {
            return this.$refs.kpp
          }
        }
        if (!this.organization.email) {
          return this.$refs.email;
        }
        if (!this.organization.phone) {
          return this.$refs.organization_phone;
        }
        if (this.invalidEmail) {
          return this.$refs.email
        }
        if (!this.organization.contact_phone) {
          return this.$refs.contact_phone;
        }

        if (!this.organization.contact_name) {
          return this.$refs.contact_name;
        }
        if (!this.organization.backup_card) {
          // return this.$refs.backup_card
        }
      }

      if (this.selectOrganizationFormData.id === 3) {
        if (!this.organization.full_name) {
          return this.$refs.full_name
        }
        if (!this.organization.inn) {
          return this.$refs.inn;
        }
        if (!this.organization.name && Object.keys(this.daData.opf).length === 0) {
          return this.$refs.name;
        }
        if (!this.organization.legal_address) {
          return this.$refs.legal_address;
        }
        if (this.kpp) {
          if (!this.organization.kpp) {
            return this.$refs.kpp
          }
        }
        if (!this.organization.phone) {
          return this.$refs.organization_phone;
        }
        if (!this.organization.contact_phone) {
          return this.$refs.contact_phone;
        }
        if (!this.organization.contact_name) {
          return this.$refs.contact_name;
        }
        if (!this.organization.backup_card) {
          // return this.$refs.backup_card
        }
      }


      if (!this.organization.full_name) {
        return this.$refs.full_name
      }
      if (!this.organization.inn) {
        return this.$refs.inn;
      }
      if (!this.organization.name && Object.keys(this.daData.opf).length === 0) {
        return this.$refs.name;
      }
      if (!this.organization.legal_address) {
        return this.$refs.legal_address;
      }
      if (this.kpp) {
        if (!this.organization.kpp) {
          return this.$refs.kpp
        }
      }
      if (!this.organization.phone) {
        return this.$refs.organization_phone;
      }
      if (!this.organization.contact_phone) {
        return this.$refs.contact_phone;
      }
      if (!this.organization.contact_name) {
        return this.$refs.contact_name;
      }
      if (!this.organization.backup_card) {
        // return this.$refs.backup_card
      }
      if (this.daData.opf.short == 'ООО' && this.daData.state.status == 'LIQUIDATED') {
        return this.$refs.name
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
    'organization.backup_card': function(newValue) {
      this.errorCard = false
    },
    'organization.phone': function(newPhone) {
      if (this.invalidOrganizationPhone) {
        this.invalidOrganizationPhone = false;
      }
    },
    'organization.contact_phone': function(newValue) {
      if (this.invalidContactPhone) {
        this.invalidContactPhone = false;
      }
    },
    'organization.email': function(newValue) {
      if (this.invalidEmail) {
        this.invalidEmail = false;
      }
    },
    'organization.legal_address': function(newValue) {
      if (this.invalidLegalAddress) {
        this.invalidLegalAddress = false;
      }
    },
    'daData.opf.short': function(newValue) {
      if (newValue === 'ИП') {
        this.organization.form_id = 2;
        this.kpp = false;
      } else if (
        ['ООО', 'АО', 'ЗАО', 'ПАО', 'НКО', 'ОП'].includes(newValue)
      ) {
        this.organization.form_id = 1;
        this.kpp = true;
      } else {
        this.organization.form_id = 3;
        this.kpp = true;
      }
    }
  },
}
</script>

<style scoped>
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
</style>
