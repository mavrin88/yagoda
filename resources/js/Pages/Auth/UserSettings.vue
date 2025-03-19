<template>
  <div class="auth__wrapper auth__wrapper_grey">
    <Head title="Настройки" />
    <div class="auth__step">
      <div v-show="user.is_registered" @click="closePopup" class="back">назад</div>
      <div class="form">
        <div class="form__item form__item_avatar">
          <div class="superadmin-add">
            <div class="superadmin-add__top">
              <label for="avatar" @click.prevent="handleAvatarClick">
                <div class="user-top__image" >
                  <img v-if="user.photo_path" :src="user.photo_path">
                  <img v-else src="/img/content/icon_avatar.svg">
                </div>
              </label>
              <input type="file" ref="fileInput" id="avatar" @change="handleImageUpload" style="display: none;">
            </div>
          </div>
        </div>
        <div class="centered-container">
          <span class="client-name">{{ user.first_name }}</span>
          <label class="client-vision-label">Так вас увидит Клиент</label>
        </div>
        <div class="form__group">
          <div class="form__item">
            <input v-model="user.first_name" type="text" id="name" required>
            <label for="name">Имя&nbsp;<span>*</span></label>
            <span class="form__error" v-if="nameErrorMessage">{{ nameErrorMessage }}</span>
          </div>
          <!--          <div class="form__item">-->
          <!--            <input v-model="user.telegram_name" type="text" id="telegram_name">-->
          <!--            <label for="name">Имя Телеграм. Например @example (для информировании о важном и чаевых)</label>-->
          <!--            <span class="form__error" v-if="nameErrorMessage">{{ nameErrorMessage }}</span>-->
          <!--          </div>-->
          <div class="form__item">
            <input type="text" inputmode="numeric" id="number" v-model="user.card_number" v-mask="'#### #### #### ####'" ref="backup_card">
            <label for="number">Номер карты для чаевых (хранится в зашифрованном виде)</label>
            <span class="form__error" v-if="cardErrorMessage">{{ cardErrorMessage }}</span>
          </div>

          <div class="form__item">
            <input type="text" v-model="user.purpose_tips" maxlength="30" ref="purpose_tips">
            <label for="purpose_tips">Если вы Мастер - укажите, на что вы копите деньги. Плательщику будет проще вам помочь.<br>
              (Максимум 30 символов)
            </label>
            <span class="form__error" v-if="cardErrorMessage">{{ cardErrorMessage }}</span>
          </div>
          <div class="form__item">
            <input type="email" v-model="user.email" maxlength="30" ref="email" @blur="validateEmail">
            <label for="purpose_tips">E-mail для отчетов и кассовых чеков
            </label>
            <span class="form__error" v-if="emailErrorMessage">{{ emailErrorMessage }}</span>
          </div>
        </div>

        <!--        <div class="form__group tips_block">-->
        <!--          <div class="form__item">-->
        <!--            <label class="card_saved" for="number">Для получения чаевых привязана карта</label>-->
        <!--            <span v-if="!isCardBinding">{{ maskedCardNumber }}</span>-->
        <!--            <span v-else>-->
        <!--              <input-->
        <!--                v-model="card_number"-->
        <!--                type="text"-->
        <!--                id="card_number"-->
        <!--                placeholder="Номер карты"-->
        <!--                v-mask="'#### #### #### ####'"-->
        <!--                required-->
        <!--                class="input-margin"-->
        <!--              >-->
        <!--              <input-->
        <!--                v-model="expiry_date"-->
        <!--                type="text"-->
        <!--                id="expiry_date"-->
        <!--                placeholder="MM/YY"-->
        <!--                v-mask="'##/##'"-->
        <!--                required-->
        <!--                class="input-margin"-->
        <!--              >-->
        <!--              <input-->
        <!--                v-model="cvv"-->
        <!--                type="text"-->
        <!--                id="cvv"-->
        <!--                placeholder="CVV"-->
        <!--                v-mask="'###'"-->
        <!--                required-->
        <!--                class="input-margin"-->
        <!--              >-->
        <!--              <span class="form__error" v-if="cardErrorMessage">{{ cardErrorMessage }}</span>-->
        <!--            </span>-->
        <!--            <div v-if="isCardBinding" class="edit_card_buttons">-->
        <!--              <span class="save_edit_card" @click="cancelEditCard">Отменить</span>-->
        <!--              <span class="save_edit_card" @click="saveEditCard">Сохранить</span>-->
        <!--            </div>-->
        <!--            <span v-if="!isCardBinding && card_number" class="btn_edit" @click="toggleCardBinding">{{ isCardBinding ? 'Отменить' : 'Изменить' }}</span>-->
        <!--          </div>-->
        <!--        </div>-->

        <!--        <div class="superadmin-add__buttons">-->
        <!--          <button-->
        <!--            v-if="!isCardBinding"-->
        <!--            class="btn btn_red"-->
        <!--            @click="bindCard"-->
        <!--          >-->
        <!--            <b>Привязать карту</b> <span class="tips">для получения чаевых</span>-->
        <!--          </button>-->
        <!--        </div>-->
        <div class="superadmin-add__buttons">
          <button class="btn" @click="update">Сохранить</button>
        </div>
      </div>
    </div>
  </div>

  <cropper-modal-static
    type="static"
    :croppedWidth="280"
    :croppedHeight="280"
    :is-active="isModalActive"
    messageButton="ПРИМЕНИТЬ"
    ref="cropperModal"
    :imageToEdit="user.photo_path"
    @save="saveCroppedImage"
    @close="isModalActive = false"
  />

  <ModalShowTrueAvatar
    :is-active="isModalShowTrueAvatarActive"
    @close="hideModalShowTrueAvatar"
    @confirmed="openFileDialog"
    :message="modalShowTrueAvatarText"
    :messageButton="modalShowTrueAvatarButtonText"
  />

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import Modal from '../../Shared/ModalDeleteAccount.vue'
import CropperModalStatic from '../../Shared/Cropper/CropperModalStatic.vue'
import ModalShowTrueAvatar from '../../Shared/Modals/ModalShowTrueAvatar.vue'

export default {
  components: {
    CropperModalStatic,
    ModalShowTrueAvatar,
    Modal,
    Link,
    Head,
  },
  props: {
    user: Object,
    auth: Object,
  },
  data() {
    return {
      isModalShowTrueAvatarActive: false,
      modalShowTrueAvatarText: '',
      modalShowTrueAvatarButtonText: '',
      changed: false,
      nameErrorMessage: '',
      cardErrorMessage: '',
      emailErrorMessage: '',
      isCardBinding: false,
      draftCardNumber: '',
      card_number: '',
      expiry_date: '', // Добавлено поле для срока действия карты
      cvv: '', // Добавлено поле для CVV
      isModalActive: false,
    }
  },
  watch: {
    first_name: {
      handler: function(v) {
        return this.hasChanged()
      },
    },
    card_number: {
      handler: function(v) {
        return this.hasChanged()
      },
    },
  },
  mounted() {
    this.draftCardNumber = this.card_number
  },
  computed: {
    maskedCardNumber() {
      // Проверяем, есть ли номер карты и достаточно ли символов
      if (this.card_number && this.card_number.length >= 4) {
        // Замена первых 12 цифр на звездочки
        return `**** **** **** ${this.card_number.slice(-4)}`
      }
    },
  },
  methods: {
    showModalShowTrueAvatar() {
      this.isModalShowTrueAvatarActive = true;
    },
    hideModalShowTrueAvatar() {
      this.isModalShowTrueAvatarActive = false
    },
    handleAvatarClick() {
      this.modalShowTrueAvatarText = '<b>Важно</b>: если вы <b>Мастер</b>, установите такую фотографию, что бы клиент <b>смог легко вас узнать</b>!<br>' +
        'В противном случае он не сможет выбрать вас из списка Мастеров для перечисления чаевых.';
      this.modalShowTrueAvatarButtonText = 'ВЫБРАТЬ ФОТОГРАФИЮ';
      this.isModalShowTrueAvatarActive = true // Открытие модального окна
    },
    openFileDialog() {
      // Позволяет открывать диалог выбора файла только после подтверждения
      this.isModalShowTrueAvatarActive = false; // Закрываем модальное окно
      this.$nextTick(() => {
        this.$refs.fileInput.click(); // Открыть диалог выбора файла
      });
    },
    validateEmail() {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Регулярное выражение для валидации email

      // Если поле пустое, просто сбрасываем ошибку
      if (this.user.email === '') {
        this.emailErrorMessage = '';
      } else if (!emailPattern.test(this.user.email)) {
        // Если email некорректен, устанавливаем сообщение об ошибке
        this.emailErrorMessage = 'Введите корректный адрес электронной почты.';
      } else {
        // Сброс ошибки, если email валиден
        this.emailErrorMessage = '';
      }
    },
    saveCroppedImage(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.user.photo_path = compressedDataUrl
        this.user.saveImage = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error)
      })
      // this.catalogImage = dataUrl;
    },
    compressImage(dataUrl, quality = 0.3) {
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
    cancelEditCard() {
      this.isCardBinding = !this.isCardBinding
      this.card_number = this.draftCardNumber
    },
    saveEditCard() {
      this.isCardBinding = !this.isCardBinding
      // this.card_number = this.draftCardNumber
    },
    toggleCardBinding() {
      this.isCardBinding = !this.isCardBinding // Переключаем состояние
    },
    bindCard() {
      this.toggleCardBinding()
      this.card_number = ''
    },
    handleImageUpload(event) {
      this.isModalActive = true
      const file = event.target.files[0]
      const reader = new FileReader()

      reader.onload = (e) => {
        this.user.photo_path = e.target.result
      }

      reader.readAsDataURL(file)

      this.user.saveImage = event.target.files[0]
    },
    hasChanged() {
      this.changed = true
    },
    Luhn(num) {
      if (num) {
        if (/[^0-9-\s]+/.test(num)) {
          return false
        }

        let numString = num.toString()
        if (numString.length < 13 && numString.length > 18) {
          return false
        }

        // The Luhn Algorithm.
        let nCheck = 0, bEven = false
        num = num.replace(/\D/g, '')

        for (var n = num.length - 1; n >= 0; n--) {
          var cDigit = num.charAt(n),
            nDigit = parseInt(cDigit, 10)

          if (bEven && (nDigit *= 2) > 9) nDigit -= 9

          nCheck += nDigit
          bEven = !bEven
        }

        return (nCheck % 10) == 0
      }
      return false
    },
    closePopup() {
      this.$inertia.visit('/organizations/choose')
    },

    exit() {
      this.$inertia.visit('/logout')
    },

    update() {

      let isHasErrors = false

      if (!this.user.first_name) {
        this.nameErrorMessage = 'Это обязательное поле'
        isHasErrors = true
      } else {
        this.nameErrorMessage = ''
      }

      if (this.emailErrorMessage) {
        isHasErrors = true
      }

      // if (!this.card_number) {
      //   this.cardErrorMessage = 'Это обязательное поле'
      //   isHasErrors = true;
      //   console.log('er2')
      //   return
      // } else {
      //   this.cardErrorMessage = ''
      // }

      // if (this.card_number && !this.Luhn(this.card_number)) {
      //   this.cardErrorMessage = 'Проверьте номер карты'
      //   isHasErrors = true;
      //   console.log('er2')
      // } else {
      //   this.cardErrorMessage = ''
      // }

      if (!isHasErrors) {

        if (this.user.card_number === '') {

          return axios.post('/user_update_account', { user: this.user })
            .then(response => {
              if (this.auth.user.is_own_organization) {
                this.$inertia.visit('/super_admin/settings')
              } else {
                this.$inertia.visit('/organizations/choose')
              }
            })
            .catch(error => {

              return false
            })
        }

        // this.$inertia.post('/user_settingss', this.user);

        this.validateCardNumber(this.user.card_number)
          .then((isValid) => {
            if (!isValid) {
              this.$refs.backup_card.scrollIntoView({ behavior: 'smooth', block: 'center' })
              this.cardErrorMessage = 'Неверный номер карты'
              return this.$refs.backup_card
              return alert('Неверный номер карты')
            }

            return axios.post('/user_update_account', { user: this.user })
              .then(response => {
                this.$inertia.visit('/organizations/choose')
              })
              .catch(error => {

                return false
              })

            // this.$inertia.post('/user_update', {
            //   user: this.user,
            // });
            // this.$inertia.visit('/organizations/choose');
          })

        // axios.put(`/user_settings/${this.user.id}`, data)
        //   .then(response => {
        //
        //     if (response && response.data.success === true) {
        //       this.$toast.open({
        //         message: 'Данные успешно обновлены',
        //         type: 'success',
        //         position: 'top-right',
        //         duration: 2000,
        //       });
        //         this.changed = false;
        //         this.nameErrorMessage = '';
        //         this.cardErrorMessage = '';
        //     }
        //   })
        //   .catch(error => {
        //     console.log('error', error)
        //   })
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
    handleDelete() {
      axios.delete(`/user_settings/${this.user.id}`)
        .then(response => {
          if (response && response.data.success === true) {
            this.isModalActive = false
          }
        })
        .catch(error => {
          console.log('error', error)
        })
    },
  },
}
</script>

<style scoped>
.btn_delete_noborder {
  border: 0;
  margin-right: 10px;
}

.user-top {
  text-align: center;
}

.user-top__image img {
  border-radius: 30%;
}

.btn_red {
  background: red;
}

.tips {
  margin-left: 5px;
}

.tips_block {
  margin-top: 10px;
}

.card_saved {
  margin-bottom: 12px;
}

.btn_edit {
  cursor: pointer;
  padding-left: 46px;
  text-decoration: underline;
}

.input-margin {
  margin-top: 10px; /* Установите нужный отступ сверху между полями ввода */
}

.edit_card_buttons {
  margin-top: 16px;
}

.save_edit_card {
  margin-left: 24px;
  cursor: pointer;
}

.test {
  /*margin-top: 445px;*/
}

.superadmin-add__top {
  margin-top: 2.375rem;
  text-align: center;
}

.centered-container {
  display: flex; /* Используем Flexbox */
  flex-direction: column; /* Устанавливаем вертикальное выравнивание */
  align-items: center; /* Центрируем по горизонтали */
  text-align: center; /* Центрируем текст по горизонтали */
  margin-bottom: 20px;
}

.client-vision-label {
  /* Дополнительные стили для метки, если нужно */
  font-size: 12px; /* Размер шрифта */
  margin-top: 8px; /* Отступ между именем и меткой */
}

.client-name {
  /* Дополнительные стили для имени, если нужно */
  font-size: 20px; /* Размер шрифта */
  font-weight: bold; /* Полужирный шрифт */
}

.exit {
  cursor: pointer;
}
</style>

