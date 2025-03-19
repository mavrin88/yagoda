<template>
  <footer class="footer" id="link5">
    <div class="footer-wrapper">
      <div class="footer-content">
        <div class="footer-form"  v-if="!isFormModalSent">
          <div class="footer-form-title">Отправить заявку на подключение</div>
          <div class="footer-form-input-container">
            <div class="footer-form-input-wrapper">
              <input
                type="tel"
                placeholder="ваш телефон"
                class="footer-form-input"
                id="phoneInput"
                v-mask="'+7 (###) ###-##-##'"
                v-model="formModal.phone"
                :class="{error: isFormModalSubmitted && !formModal.phone}"
              />
              <button class="footer-form-button" id="submitButton" @click="submitFormFooter">Отправить</button>
            </div>
            <div class="form__item">
              <div class="form__error" v-if="isFormModalSubmitted && !formModal.phone">{{formModalPhoneError1}}</div>
              <div class="form__error" v-if="isFormModalSubmitted && (formModal.phone && formModal.phone.length < 18)">{{formModalPhoneError2}}</div>
            </div>
            <div class="form__item form__item_checkbox">
              <input type="checkbox" required id="privacy" v-model="formModal.privacy" :class="{error: isFormModalSubmitted && !formModal.privacy}">
              <label for="privacy">Согласен с&nbsp; <a href="/pages/privacy" target="_blank">политикой конфиденциальности</a></label>
              <div class="form__error" v-if="isFormModalSubmitted && !formModal.privacy">{{ formModalPrivacyError }}</div>
            </div>
            <div class="form__error" v-if="formModalError">{{formModalError}}</div>
          </div>

        </div>
        <div class="footer-form" v-if="isFormModalSent">
          <div class="footer-form-title">{{formModalMessage}}</div>
        </div>


        <div class="footer-contacts">
          <div class="footer-contacts-info">
            <p>ООО “Ягода”</p>
            <p>ИНН 0800024353, ОГРН 12408000010604</p>
            <p>
              Юр. адрес: 358000, Республика Калмыкия, г. Элиста, ул. В.И. Ленина,
              дом 268, помещение 3, рабочее место 1
            </p>
            <p>Контакты: mastera@yagoda.team</p>
          </div>
          <div class="footer-contacts-links">
            <a href="https://t.me/+79153690251" target="_blank" class="footer-contacts-link">
              <img src="img/landing/tg.svg" alt="Telegram" class="footer-contacts-icon" />
            </a>
            <a href="https://wa.me/79153690251" target="_blank" class="footer-contacts-link">
              <img src="img/landing/ws.svg" alt="WhatsApp" class="footer-contacts-icon" />
            </a>
            <a href="tel:+74992132233" class="footer-contacts-link">
              +7 (499) 213-22-33
            </a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>
<script>
import axios from 'axios'

export default {
  name: "FrontFooterComponent",
  computed: {
    isCanSendFormModal() {
      // При изменении поменять валидацию и в FormController.php
      return this.formModal.phone && this.formModal.phone.length === 18 && this.formModal.privacy;
    },
  },
  data() {
    return {
      // Форма
      isFormModalOpened: false,
      isFormModalSubmitted: false,
      formModal: {
        phone: null,
        privacy: null
      },
      isFormModalSent: false,
      isFormModalSubmitting: false,
      formModalError: '',
      formModalMessage: '',
      formModalPhoneError1: 'Поле не может быть пустым',
      formModalPhoneError2: 'Пожалуйста, проверьте номер телефона',
      formModalPrivacyError: 'Обязательное поле',
    }
  },
  methods: {
    submitFormFooter() {
      // Если запрос уже выполняется, блокируем
      if (this.isFormModalSubmitting) return;

      this.isFormModalSubmitted = true;

      if (this.isCanSendFormModal && !this.isFormModalSent) {

        this.isFormModalSubmitting = true;

        axios.post('/form/front-footer', this.formModal)
          .then((response) => {
            if (!response.data.success) {
              this.formModalError = response.data.message || 'Произошла ошибка. Повторите попытку позже.'
              this.isFormModalSent = false
            }

            if (response.data.success) {
              this.formModalPhoneError1 = '';
              this.formModalPhoneError2 = '';
              this.isFormModalSent = true;
              this.formModalMessage = response.data.message || 'Форма успешно отправлена'
              if (this.isFormModalSent && this.formModalMessage) {
                this.formModal.phone = '';
              }
              if (typeof ym === 'function') {
                ym(98232814, 'reachGoal', 'form_modal_submit');
              }
            }
          })
          .catch((error, response) => {
            console.log('response', response)
            this.isFormModalSent = false
            if (response && response.data && response.data.message) {
              this.formModalError = response.data.message
            } else {
              this.formModalError = 'Произошла ошибка. Повторите попытку позже.'

            }
          })
          .finally(() => {
            this.isFormModalSubmitting = false;
          })
      }

    },
  }
}
</script>
<style lang="scss" scoped>
@import "@scss/Landing/_mixins.scss";
@import "@scss/Landing/_variables.scss";
@import "@scss/Landing/buttons.scss";

//region Не из вёрстки
.footer-form-input-container {
  background-color: transparent !important;
  flex-wrap: wrap;
  padding: 0 !important;
  display: block !important;
  .footer-form-input-wrapper {
    background: #fff;
    padding: 0 20px 0 0;
    border-radius: 100px;
    min-width: 100%;
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
  }
  .footer-form-input {
    color: #000;
    height: 100%;
    padding: 20px 24px;
    max-width: 100% !important;
    border-radius: 100px;
  }
  .form__item_checkbox {
    font-size: 12px;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 5px;
    cursor: pointer;
    min-width: 100%;
    a {
      text-decoration: underline;
    }
  }
  .footer-form-button {
    @media only screen and (max-width: 420px) {
      flex: 1;
      margin-left: -30px;
      margin-right: 30px;
      z-index: 2;
    }
  }
  .form__error {
    min-width: 100%;
  }

}

//endregion

.footer {
  background-color: $darkBlue;
  padding: 40px 20px;
  color: $textInverted;
  position: relative;
  display: flex;
  flex-direction: column;
  gap: 40px;

  @media (min-width: 1024px) {
    flex-direction: row;
    column-gap: 80px;
  }

  .footer-wrapper {
    width: 100%;
  }

  .footer-content {
    display: flex;
    flex-direction: column;
    gap: 30px;

    @media (min-width: 1024px) {
      flex-direction: row;
      width: 100%;
      align-items: center;
    }
  }

  .footer-form {
    display: flex;
    flex-direction: column;
    width: 100%;
    gap: 20px;

    @media (min-width: 1110px) {
      flex-direction: row;
      gap: 30px;
      align-items: center;
      flex-basis: 200%;
    }
  }

  .footer-form-title {
    font-size: 16px;
    width: 70%;
    @media (min-width: 1110px) {
      font-size: 20px;
      width: unset;
      text-wrap: nowrap;
    }
  }

  .footer-form-input-container {
    padding: 20px 24px;
    background-color: $backgroundWhite;
    border-radius: 100px;
    display: flex;
    justify-content: space-between;
    max-width: 100%;

    @media (min-width: 600px) {
      max-width: 80%;
    }

    @media (min-width: 1024px) {
      max-width: none;
      width: 90%;
      flex-basis: 100%;
    }
  }

  .footer-form-input {
    border: none;
    outline: none;
    flex-grow: 1;
    font-size: 16px;
    font-family: "Montserrat", sans-serif;
    max-width: 160px;

    &::placeholder {
      color: $textPrimary;
    }
  }

  .footer-form-button {
    background: none;
    border: none;
    color: $textPrimary;
    font-size: 16px;
    cursor: pointer;
    font-family: "Montserrat", sans-serif;
    text-transform: uppercase;
  }

  .footer-contacts {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 310px;
  }

  .footer-contacts-info {
    font-size: 12px;
    line-height: 1.3;
    p {
      margin: 6px 0;
    }
  }

  .footer-contacts-links {
    display: flex;
    gap: 16px;
    align-items: center;
  }

  .footer-contacts-link {
    color: $textInverted;
    text-decoration: none;
    display: flex;
    align-items: center;
  }

  .footer-contacts-icon {
    width: 24px;
    height: 24px;
    @media (min-width: 1024px) {
      width: 30px;
      height: 30px;
    }
  }

  .footer-input {
    border: none;
    outline: none;
    font-size: 16px;
    font-family: "Montserrat", sans-serif;
    max-width: 160px;
  }
}
</style>
