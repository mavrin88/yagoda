<template>
  <div class="admin" @click="handleOutsideClick">
    <div class="admin__container">
      <div class="admin-qr">
        <div class="admin-qr__top">
          <img v-if="!orderIsOpen" class="line-icon" @click="goBack" src="/img/icon_line.svg" alt=""></img>
          <!--          <div v-else class="back">назад</div>-->
          <h2>QR ДЛЯ ОПЛАТЫ</h2>
        </div>
        <!-- если выбран статичный код, то admin-qr__note скрываем, показываем admin-qr__static. И наоборот-->
        <!--        <div class="admin-qr__note">Отсканируйте QR для оплаты.</div>-->
        <!--        <div class="admin-qr__static">QR <b>6 Ресепшен</b></div>-->
        <div class="admin-qr__code" v-if="selectQrCode">
          <div class="qr-code-container">
            <qrcode-vue :value="selectQrCode.link" size="300" level="H"></qrcode-vue>
          </div>
        </div>
        <div class="admin-qr__code" v-if="generatedQrCode">
          <div class="qr-code-container">
            <qrcode-vue :value="generatedQrCode.link" size="300" level="H"></qrcode-vue>
          </div>
        </div>
        <!--        <div class="admin-qr__link" v-if="selectQrCode">-->
        <!--          <span>{{ selectQrCode.link }}</span>-->
        <!--          <div class="admin-qr__share" title="Копировать" @click="share(selectQrCode.link) && copyLink(selectQrCode.link)"><div id="share-block" :class="{'js-hidden': !isShowShareBlock}"></div></div>-->

        <!--        </div>-->
        <div class="admin-qr__link">
          <div class="admin-qr__total">
            <div class="fg">
              <p class="totalSumm"><b>{{ totalSumm.toFixed(2) }}</b> ₽</p>
              <p class="selectedMasters"><b>{{ selectedMasters.map(master => master.first_name || 'Без имени').join(', ') }}</b></p>
            </div>
          </div>

          <button ref="copyButton" class="btn btn_none btn_delete_noborder" @click="copyToClipboard" :class="{'btn-copied': isCopied}">
            <img class="copyLink" src="/img/icon_copy.svg" alt="">
          </button>


          <div
            v-if="showPopup"
            ref="popperElement"
            class="popup-message rounded-lg shadow-lg"
            :style="popperStyles"
            :class="popperClasses"
          >
            ссылка на страницу оплаты скопирована
          </div>

          <div class="share-container" style="position: relative;">
            <button ref="shareBlock" class="btn btn_none btn_delete_noborder" @click="share()" :class="{'btn-copied': isShared}">
              <img class="share-icon" src="/img/icon_share.svg" alt="">
            </button>

            <div id="share-block" :class="{'js-hidden': !isShowShareBlock}" class="share-block">
            </div>
          </div>

        </div>

        <!--        <div class="admin-qr__link" v-if="generatedQrCode">-->
        <!--          <span>{{ generatedQrCode.link.length > 30 ? generatedQrCode.link.slice(0, 30) + '...' : generatedQrCode.link }}</span>-->
        <!--          <div class="admin-qr__share" title="Копировать" @click="share(generatedQrCode.link) && copyLink(generatedQrCode.link)">-->
        <!--            <div id="share-block" :class="{'js-hidden': !isShowShareBlock}"></div>-->
        <!--          </div>-->
        <!--        </div>-->

        <div class="admin-qr__list">

          <div v-for="qrCode in qrCodes"
               :key="qrCode.id"
               @click="trySelectQrCode(qrCode)"
               :class="['admin-qr__item', qrCode === selectQrCode ? 'admin-qr__item_active' : '']">
            <div class="admin-qr__count">
              <img src="img/content/qr.svg">
            </div>
            <div class="admin-qr__name">{{ qrCode.name }}</div>
          </div>
          <div @click="generateQrCode()" :class="['admin-qr__item', generatedQrCode ? 'admin-qr__item_active' : '']">
            <div class="admin-qr__count">
              <img src="img/content/unicum.svg">
            </div>
            <div class="admin-qr__name">Уникальный</div>
          </div>
        </div>

<!--        <div class="send-link-to-client">-->
<!--          <h5>Отправить клиенту ссылку на страницу оплаты в мессенджер:</h5>-->
<!--          <div class="phone-and-messengers">-->
<!--            <div class="phone-input">-->
<!--              <div class="form__item">-->
<!--                <input type="text" inputmode="numeric" id="contact_phone" v-model="phoneNumber" v-mask="'+7 (###) ###-##-##'"-->
<!--                       ref="contact_phone" placeholder="+7">-->
<!--                <label v-if="!invalidPhoneNumber && !emptyPhoneNumber" for="contact_phone">Телефон</label>-->
<!--                <div class="form__error" v-if="invalidPhoneNumber">Некорректный номер телефона</div>-->
<!--                <div class="form__error" v-if="emptyPhoneNumber">Введите номер телефона</div>-->
<!--              </div>-->
<!--            </div>-->
<!--            <div class="messenger-icons-wrapper">-->
<!--              <div class="messenger-icons">-->
<!--                <svg @click="sendLink('telegram')" xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 256 256">-->
<!--                  <defs>-->
<!--                    <linearGradient id="logosTelegram0" x1="50%" x2="50%" y1="0%" y2="100%">-->
<!--                      <stop offset="0%" stop-color="#2aabee"/>-->
<!--                      <stop offset="100%" stop-color="#229ed9"/>-->
<!--                    </linearGradient>-->
<!--                  </defs>-->
<!--                  <path fill="url(#logosTelegram0)" d="M128 0C94.06 0 61.48 13.494 37.5 37.49A128.04 128.04 0 0 0 0 128c0 33.934 13.5 66.514 37.5 90.51C61.48 242.506 94.06 256 128 256s66.52-13.494 90.5-37.49c24-23.996 37.5-56.576 37.5-90.51s-13.5-66.514-37.5-90.51C194.52 13.494 161.94 0 128 0"/><path fill="#fff" d="M57.94 126.648q55.98-24.384 74.64-32.152c35.56-14.786 42.94-17.354 47.76-17.441c1.06-.017 3.42.245 4.96 1.49c1.28 1.05 1.64 2.47 1.82 3.467c.16.996.38 3.266.2 5.038c-1.92 20.24-10.26 69.356-14.5 92.026c-1.78 9.592-5.32 12.808-8.74 13.122c-7.44.684-13.08-4.912-20.28-9.63c-11.26-7.386-17.62-11.982-28.56-19.188c-12.64-8.328-4.44-12.906 2.76-20.386c1.88-1.958 34.64-31.748 35.26-34.45c.08-.338.16-1.598-.6-2.262c-.74-.666-1.84-.438-2.64-.258c-1.14.256-19.12 12.152-54 35.686c-5.1 3.508-9.72 5.218-13.88 5.128c-4.56-.098-13.36-2.584-19.9-4.708c-8-2.606-14.38-3.984-13.82-8.41c.28-2.304 3.46-4.662 9.52-7.072"/>-->
<!--                </svg>-->
<!--                <svg @click="sendLink('whatsapp')" xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 256 258"><defs><linearGradient id="logosWhatsappIcon0" x1="50%" x2="50%" y1="100%" y2="0%"><stop offset="0%" stop-color="#1faf38"/><stop offset="100%" stop-color="#60d669"/></linearGradient><linearGradient id="logosWhatsappIcon1" x1="50%" x2="50%" y1="100%" y2="0%"><stop offset="0%" stop-color="#f9f9f9"/><stop offset="100%" stop-color="#fff"/></linearGradient></defs><path fill="url(#logosWhatsappIcon0)" d="M5.463 127.456c-.006 21.677 5.658 42.843 16.428 61.499L4.433 252.697l65.232-17.104a123 123 0 0 0 58.8 14.97h.054c67.815 0 123.018-55.183 123.047-123.01c.013-32.867-12.775-63.773-36.009-87.025c-23.23-23.25-54.125-36.061-87.043-36.076c-67.823 0-123.022 55.18-123.05 123.004"/><path fill="url(#logosWhatsappIcon1)" d="M1.07 127.416c-.007 22.457 5.86 44.38 17.014 63.704L0 257.147l67.571-17.717c18.618 10.151 39.58 15.503 60.91 15.511h.055c70.248 0 127.434-57.168 127.464-127.423c.012-34.048-13.236-66.065-37.3-90.15C194.633 13.286 162.633.014 128.536 0C58.276 0 1.099 57.16 1.071 127.416m40.24 60.376l-2.523-4.005c-10.606-16.864-16.204-36.352-16.196-56.363C22.614 69.029 70.138 21.52 128.576 21.52c28.3.012 54.896 11.044 74.9 31.06c20.003 20.018 31.01 46.628 31.003 74.93c-.026 58.395-47.551 105.91-105.943 105.91h-.042c-19.013-.01-37.66-5.116-53.922-14.765l-3.87-2.295l-40.098 10.513z"/><path fill="#fff" d="M96.678 74.148c-2.386-5.303-4.897-5.41-7.166-5.503c-1.858-.08-3.982-.074-6.104-.074c-2.124 0-5.575.799-8.492 3.984c-2.92 3.188-11.148 10.892-11.148 26.561s11.413 30.813 13.004 32.94c1.593 2.123 22.033 35.307 54.405 48.073c26.904 10.609 32.379 8.499 38.218 7.967c5.84-.53 18.844-7.702 21.497-15.139c2.655-7.436 2.655-13.81 1.859-15.142c-.796-1.327-2.92-2.124-6.105-3.716s-18.844-9.298-21.763-10.361c-2.92-1.062-5.043-1.592-7.167 1.597c-2.124 3.184-8.223 10.356-10.082 12.48c-1.857 2.129-3.716 2.394-6.9.801c-3.187-1.598-13.444-4.957-25.613-15.806c-9.468-8.442-15.86-18.867-17.718-22.056c-1.858-3.184-.199-4.91 1.398-6.497c1.431-1.427 3.186-3.719 4.78-5.578c1.588-1.86 2.118-3.187 3.18-5.311c1.063-2.126.531-3.986-.264-5.579c-.798-1.593-6.987-17.343-9.819-23.64"/></svg>-->
<!--              </div>-->
<!--            </div>-->
<!--          </div>-->
<!--        </div>-->

        <div v-if="showClient" class="result__textarea">
          <div class="client-result__textarea">
            <p class="font-bold mb-1">{{ dopOrderData.newClientData.name }}</p>
            <span>{{ dopOrderData.newClientData.phone }}</span>
          </div>
        </div>

        <div class="result__textarea">
          <h5 class="pt-2">Комментарий к заказу
            <span class="char-count">{{ charCount }}/150</span>
          </h5>
          <div class="client-result__textarea">
    <textarea class="input-field"
              v-model="comment"
              @input="debouncedSendComment"
              maxlength="150"
              @blur="sendComment">
    </textarea>
          </div>
        </div>


<!--        <div class="admin-qr__note">Или выбери статичный QR находящийся возле вас.</div>-->

        <!--        <div class="fixed-bottom">-->
        <!--          <button @click="saveOrder" v-if="!orderIsOpen" class="btn btn_w100">Сохранить</button>-->
        <!--          <button @click="goToOrders" v-if="orderIsOpen" class="btn btn_w100">К заказам</button>-->
        <!--        </div>-->
        <div class="fixed-bottom-white" v-if="generatedQrCode || selectQrCode">
          <div class="fixed-bottom">
            <div class="admin-qr__buttons">
              <div @click="showModal" class="btn btn_delete btn_delete_noborder btn_delete_nobg"></div>
              <button @click="goToOrders" class="btn btn_w100 btn_back">К заказам</button>
            </div>
          </div>
        </div>
        <!--        <button @click="saveOrder" v-if="selectQrCode" class="btn btn_w100">Сохранить</button>-->
      </div>
    </div>
  </div>

  <ModalDeleteOrder
    :is-active="isModalActive"
    @confirmed="handleDelete"
    @close="closeModal"
    message="Удалить каталог?"
    messageButton="Удалить заказ"
  />
</template>

<script>

import QrcodeVue from 'qrcode.vue'
import axios from 'axios'
import ModalDeleteOrder from '../../../../../Shared/Modals/ModalDeleteOrder.vue'
import Modal from '../../../../../Shared/ModalDeleteAccount.vue'
import { createPopper } from '@popperjs/core';
import { debounce } from 'lodash'


export default {
  components: {
    Modal,
    QrcodeVue,
    ModalDeleteOrder,
    createPopper
  },
  props: {
    selectedProducts: Object,
    qrCodes: Object,
    totalPrice: Number,
    selectedMasters: Object,
    organizationName: {
      type: String,
      required: false,
      default: 'yagoda.team'
    },
    draftOrder: Object,
    discount: Number,
    dopOrderData: Object,
    editedOrderId: Number,
    isEditingOrder: Boolean,
  },
  data() {
    return {
      selectQrCode: null,
      generatedQrCode: null,
      orderIsOpen: null,
      intervalId: null,
      isShowShareBlock: false,
      isModalActive: false,
      isCopied: false,
      isShared: false,
      showPopup: false,
      ok: true,
      popperInstance: null,
      popperStyles: {},
      popperClasses: '',
      comment: '',
      phoneNumber: '',
      invalidPhoneNumber: false,
      emptyPhoneNumber: false,
      textToCopy: '',

    }
  },
  created() {
    // Сохраняем коммент в любом случае
    this.debouncedSendComment = debounce(this.sendComment, 1000);
    window.addEventListener('beforeunload', () => this.sendComment());
  },
  beforeUnmount() {
    // Очишаем слушатель
    window.removeEventListener('beforeunload', () => this.sendComment());
  },
  setup() {
    let shareScript = document.createElement('script')
    shareScript.setAttribute('src', 'https://yastatic.net/share2/share.js')
    document.head.appendChild(shareScript)
  },
  mounted() {
    this.checkStartQrCode()
    this.getCurrentComment()
  },
  computed: {
    showClient() {
      return this.dopOrderData.newClientData.phone.length >= 18
    },
    totalSumm() {
      // Суммируем итоговые цены с учетом скидки для всех продуктов
      return this.selectedProducts.reduce((acc, product) => {
        // return acc + this.getDiscountedTotal(product);
        console.log(this.getDiscountedTotal(product))
        return acc + this.getDiscountedTotal(product);
      }, 0);
    },
    discountAmount() {
      return Math.ceil(this.totalPrice * (this.discount / 100));
    },

    finalTotal() {
      return Math.ceil(this.totalSumm - this.discountAmount);
    },

    finalTotalAsDiscount() {
      return Math.ceil(this.totalPrice - this.discountAmount);
    },
    charCount() {
      return this.comment.length;
    },
  },
  methods: {
    showPopupMessage() {
      this.showPopup = true;
      this.createPopper();
      setTimeout(() => {
        this.showPopup = false;
        // this.popperInstance.destroy();
      }, 2000);
    },
    createPopper() {
      const copyButton = this.$refs.copyButton;
      const popperElement = this.$refs.popperElement;

      this.popperInstance = createPopper(copyButton, popperElement, {
        // placement: 'top',
        // modifiers: [
        //   {
        //     name: 'offset',
        //     options: {
        //       offset: [40, 70],
        //     },
        //   },
        // ],
      });

      // this.popperStyles = {
      //   position: 'absolute',
      //   zIndex: 9999,
      // };
      this.popperClasses = 'rounded-lg shadow-lg';
    },
    getDiscountedTotal(product) {
      const discountValue = (product.price * this.discount) / 100;
      const discountedPrice = product.price - discountValue;

      // Округляем до двух знаков после запятой
      const roundedDiscountedPrice = Math.round(discountedPrice * 100) / 100;

      // Вычисляем общую цену с учетом количества товаров
      const total = roundedDiscountedPrice * product.quantity;

      // Округляем итоговую стоимость до двух знаков после запятой и обеспечиваем минимальную цену в 1 рубль
      const finalTotal = Math.max(Math.round(total * 100) / 100, 1); // Минимальная цена 1 рубль

      return finalTotal;
    },
    share() {
      let link = '';

      if (this.generatedQrCode && this.generatedQrCode.link) {
        link = this.generatedQrCode.link;
      }

      if (this.selectQrCode && this.selectQrCode.link) {
        link = this.selectQrCode.link;
      }

      if (link) {
        this.isShowShareBlock = true;
        let brend_name = this.organizationName;
        let hareSrting = brend_name + '.' + '\u000A' + 'Ссылка на оплату услуг' + '.' + '\u000A' + link;
        var share = Ya.share2('share-block', {
          content: {
            url: hareSrting,
            title: '',
          },
          theme: {
            services: 'telegram,whatsapp,viber,vkontakte',
            lang: 'ru'
          }
        });
        this.isShared = true;
        console.log(hareSrting)
        if (typeof ym === 'function') {
          ym(98232814,'reachGoal','QR_code_page_thanks_sharing')
        }
      } else {
        console.error('Нет ссылки для копирования.');
      }
    },
    goBack() {
      if (typeof ym === 'function') {
        ym(98232814,'reachGoal','QR Code Page Thanks Back to Editing')
      }
      this.$emit('hideQrCode')
      this.stopOrderStatusCheck()
    },
    goToOrders() {
      this.$inertia.visit('/orders')
      this.stopOrderStatusCheck()
    },
    trySelectQrCode(qrCode) {
      if (qrCode === this.selectQrCode) {
        return
      }
      axios.post('/checkQrCodeStatus', {
        qrCode: qrCode
      })
        .then((response) => {
          if (!response.data.status) {
            alert(response.data.error)
          }
          else {
            this.setQrCode(qrCode)
            this.isCopied = false
          }
        })
        .catch((error) => {

        });
    },
    setQrCode(qrCode) {
      this.$emit('setQrCode', qrCode)
      this.selectQrCode = qrCode
      this.generatedQrCode = null
      this.$emit('saveOrder', this.draftOrder)
    },
    saveOrder() {
      this.$emit('saveOrder', this.draftOrder)
      this.stopOrderStatusCheck()
    },
    copyToClipboard() {
      const text = this.generatedQrCode?.link || this.selectQrCode?.link;
      if (!text) return;

      const textarea = document.createElement('textarea');
      textarea.value = text;
      textarea.style.position = 'fixed'; // Убираем с экрана
      textarea.style.opacity = 0;
      document.body.appendChild(textarea);
      textarea.focus();
      textarea.select();

      try {
        const successful = document.execCommand('copy');
        if (successful) {
          this.showPopupMessage();
          this.isCopied = true;
        } else {
          console.error('Не удалось скопировать текст');
        }
      } catch (error) {
        console.error('Ошибка копирования:', error);
      }

      document.body.removeChild(textarea);
    },



    // async copyToClipboard() {
    //   try {
    //     if (this.generatedQrCode && this.generatedQrCode.link) {
    //       console.log('tut1');
    //       await navigator.clipboard.writeText(this.generatedQrCode.link);
    //       if (!this.isCopied) {
    //         this.showPopupMessage();
    //         this.isCopied = true;
    //       }
    //     } else if (this.selectQrCode && this.selectQrCode.link) {
    //       this.generateQrCode();  // Генерация нового QR кода, если нужно
    //
    //       await navigator.clipboard.writeText(this.generatedQrCode.link);  // Пишем в буфер обмена
    //
    //       if (!this.isCopied) {
    //         this.showPopupMessage();
    //         this.isCopied = true;
    //       }
    //
    //       this.selectQrCode = null; // Сбрасываем выбранный QR код, если нужно
    //     }
    //   } catch (error) {
    //     console.error('Ошибка при копировании в буфер обмена:', error);
    //   }
    // },
    // linkSelect(link){
    //   this.showPopupMessage();
    //   this.isCopied = true
    //   navigator.clipboard.writeText(link).then(() => {
    //     console.log('Ссылка скопирована в буфер обмена!!');
    //     if (typeof ym === 'function') {
    //       ym(98232814,'reachGoal','QR_code_page_thanks_for_copying_the_link')
    //     }
    //   })
    // },
    generateQrCode() {
      this.isCopied = false
      axios.get('generateHideQrCode')
        .then(response => {
          this.generatedQrCode = response.data
          this.selectQrCode = null
          setTimeout(() => {
            this.$emit('setQrCode', this.generatedQrCode)
            this.$emit('saveOrderGenerateHideQrCode', this.draftOrder)
            this.startOrderStatusCheck()
          }, 1000)
        })
        .catch(error => {
          console.error(error)
        })
    },
    checkStartQrCode() {
      if (this.qrCodes.length) {
        this.checkQrCodeStatus(this.qrCodes[0]);
      } else {
        this.generateQrCode()
      }
    },
    startOrderStatusCheck() {
      if (!this.intervalId) {
        this.intervalId = setInterval(() => this.checkOrderStatus(), 2000);
      }
    },
    stopOrderStatusCheck() {
      if (this.intervalId) {
        clearInterval(this.intervalId);
        this.intervalId = null;
      }
    },
    checkQrCodeStatus(qrCode) {
      axios.post('/checkQrCodeStatus', {
        qrCode: qrCode
      })
        .then((response) => {
          this.startOrderStatusCheck()
          if (!response.data.status) {
            this.generateQrCode()
          }
          else {
            this.setQrCode(qrCode)
          }
        })
        .catch((error) => {

        });
    },
    checkOrderStatus() {
      axios.post('/api/checkIsOpenOrder', {
        generatedQrCode: this.generatedQrCode || this.selectQrCode
      })
        .then((response) => {
          this.orderIsOpen = response.data.status;

          if (this.orderIsOpen) {
            this.stopOrderStatusCheck();
            setTimeout(() => {
              this.goToOrders();
            }, 500);
          }
        })
        .catch((error) => {
          console.error('Error checking order status:', error);
          this.stopOrderStatusCheck();
        })
        .finally(() => {
          // this.stopOrderStatusCheck();
        });
    },
    showModal() {
      this.isModalActive = true;
    },
    closeModal() {
      this.isModalActive = false;
    },
    handleDelete() {
      this.$inertia.post('/admin/delete_order/', {
        orderId: this.draftOrder.order.id
      });

      if (typeof ym === 'function') {
        ym(98232814,'reachGoal','Saved order Deleted order')
      }

      setTimeout(() => {
        this.goToOrders()
      }, 500);
    },
    handleOutsideClick(event) {
      if (!this.$refs.shareBlock || !this.$refs.shareBlock.contains(event.target)) {
        this.isShowShareBlock = false;
      }
    },
    sendLink(messenger) {
      if (this.phoneNumber) {
        this.validatePhoneNumber(this.phoneNumber);
      }else {
        this.emptyPhoneNumber = true
      }

      if (this.phoneNumber && !this.invalidPhoneNumber) {
        const formattedPhone = this.phoneNumber.replace(/[^\d+]/g, '');

        let link = '';

        if (messenger === 'telegram') {
          link = `https://t.me/${formattedPhone}`; // Ссылка для Telegram
        } else {
          link = `https://wa.me/${formattedPhone}`; // Ссылка для WhatsApp
        }

        window.open(link, '_blank');
      }
    },
    async sendComment() {
      try {
        const orderId = this.isEditingOrder ? this.editedOrderId : this.draftOrder.order.id
        if (orderId) {
          await axios.post('/orders/' + orderId + '/order_comment', {
            comment: this.comment,
          })
        }
      }
      catch (error) {
        console.error('Ошибка сохранения комментария');
      }
    },
    validatePhoneNumber(phone) {
      const phonePattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
      const hasDigits = /\d/.test(phone);
      this.invalidPhoneNumber = hasDigits && !phonePattern.test(phone);
    },

    getCurrentComment() {
      const orderId = this.isEditingOrder ? this.editedOrderId : this.draftOrder.order.id
      if (orderId) {
        console.log('ok2')

        axios.get('/orders/' + orderId + '/order_comment')
          .then(response => {
            if (response && response.data && response.data.message) {
              this.comment = response.data.message;
            }

          })
          .catch(error => {
            console.error(error)
          })

      }
    }
  },
  watch: {
    phoneNumber(newValue) {
      if (newValue) {
        this.invalidPhoneNumber = false;
        this.emptyPhoneNumber = false;
      }
    },
  }
};
</script>

<style scoped>
.admin-qr__code {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
}

.qr-code-container {
  text-align: center;
  margin: 20px;
}

.admin-qr__share {
  position: relative;
}

#share-block {
  width: 122px;
  margin-top: -36px;
  margin-left: -50px;
  background: #fff;
  padding: 5px;
  border-radius: 6px;
  border: 1px solid #ccc;
}

#share-block.js-hidden {
  opacity: 0;
  visibility: hidden;
}

.admin-qr__top h2 {
  font-size: 16px;
  font-weight: 300;
  padding: 0;
  text-transform: uppercase;
}

.line-icon {
  cursor: pointer;
  margin-top: 5px;
}

.admin-qr__link {
  display: flex;
  min-height: 72px;
  margin-top: .5rem;
  border-radius: 1rem;
  background: none;
  align-items: center;
  justify-content: space-between;
  padding: 0;
}

.btn {
  flex: 1;
  margin: 0 0.5rem;
  padding: 10px;
  color: #fff;
  border: none;
  cursor: pointer;
}

.btn:first-child {
  margin-left: 0;
}

.btn:last-child {
  margin-right: 0;
}

.btn_none {
  width: 72px;
  min-width: 3rem;
  height: 3.375rem;
  background: #fff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}

.close-icon {
  width: 1.063rem;
}

.share-icon {
  width: 32px;
  height: 32px;

}
#share-block {
  width: 95px !important; /* Фиксированная ширина */
  height: 100px; /* Фиксированная высота */
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #f5f5f5;
  border-radius: 8px; /* Если нужно, скругленные углы */
  transition: opacity 0.3s ease; /* Плавное появление блока */
  overflow: hidden; /* Чтобы элементы внутри не выходили за пределы блока */
}

.admin-qr__list {
  margin-bottom: 1.125rem;
}

.totalSumm {
  font-size: 24px;
}
.selectedMasters {
  font-size: 16px;
}
.copyLink {
  width: 32px;
  height: 32px;
}
.admin-qr__note {
  padding-left: 13px !important;
}
.admin-qr__total {
  margin-right: 34px;
}
.fg {
  padding-top: 20px;
}

.popup-message {
  display: inline-block;
  position: absolute;
  background-color: #e5e5e5;
  padding: 6px;
  font-size: 14px;
  border-radius: 10px;
  white-space: nowrap;
  z-index: 9999;
  top: 50%;
  left: 50%;
  transform: translateX(-50%);
}
.popup-message::after {
  content: '';
  position: absolute;
  top: 100%;
  left: 64%;
  border-width: 6px;
  border-style: solid;
  border-color: #e5e5e5 transparent transparent transparent;
}
.btn-copied {
  background: #e5e5e5;
  box-shadow: none;
}
button#shareBlock {
  display: inline-block;
  padding: 0;
  width: auto;
  height: auto;
  box-sizing: border-box;
}

.share-block {
  position: absolute;
  top: 35%;
  left: 10%;
  z-index: 10;
}
.result__textarea {
  background-color: #FFFFFF;
  padding: 10px 20px;
  border-radius: 16px;
  max-width: 400px;
  margin: 20px auto;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

.client-result__textarea {
  width: 100%;
  margin-top: 10px;
}

.input-field {
  width: 100%;
  padding: 15px;
  border: 1px solid #E5E5E5;
  border-radius: 16px;
  font-size: 14px;
  color: #333;
  box-sizing: border-box;
  resize: none;
  position: relative;
}

.input-field:focus {
  outline: none;
}

.input-field::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 10%;
  transform: translateX(-50%);
  width: 10%;
}

.char-count {
  font-size: 14px;
  color: #777;
  margin-left: 60px;
  align-self: flex-end;
}
.send-link-to-client {
  background-color: #FFFFFF;
  padding: 20px;
  max-width: 400px;
  border-radius: 16px;
}

.send-link-to-client h5 {
  font-size: 16px;
  margin-bottom: 10px;
  color: #333;
}

.form__item {
  position: relative;
  width: 100%;
}
.form__item label {
  position: absolute;
  top: 50px;
  left: 10px;
  font-size: 14px;
  color: #9ca4b5;
  transition: all 0.2s ease;
}
.form__error {
  position: absolute;
  top: 50px;
  left: 10px;
  transition: all 0.2s ease;
}
.phone-and-messengers {
  display: flex;
  align-items: center;
  gap: 10px;
}

.phone-input {
  flex: 1;
}
#contact_phone:focus {
  outline: none;
}

.messenger-icons {
  cursor: pointer;
  display: flex;
  align-items: center;
  padding-bottom: 15px;
  gap: 20px;
}

input[type="text"] {
  width: 90%;
  padding: 10px 0 10px 10px;
  border-bottom: 1px solid #D5D5D5;
  font-size: 17px;
  color: #333;
  box-sizing: border-box;
}
</style>
