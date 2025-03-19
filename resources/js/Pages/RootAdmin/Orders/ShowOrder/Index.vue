<template>
  <div class="admin">
    <div class="admin__container" @click="handleOutsideClick">
      <div class="admin-bill" v-if="order">
        <div class="admin-orders__top">
          <div></div>
          <img class="close-icon" @click="back" src="/img/icon_close.svg" alt="">
        </div>
        <div class="admin-bill__block">
          <div class="admin-bill__list">
            <ul>
              <li v-if="order.order_items" v-for="orderItem in order.order_items">
                <span class="order-product-name">{{ orderItem.product_name }}</span>
                <span>{{ new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(discountedPrice(orderItem.product_price).toFixed(2)) }}</span>
                <span>{{ orderItem.quantity }}</span>
                <span>
                  <b>{{ new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(discountedPrice(orderItem.product_price * orderItem.quantity)) }}</b>
                </span>
              </li>
            </ul>
            <!--            <p>{{ totalWithoutDiscount.toFixed(2) }} <span> ₽</span></p>-->
            <div class="admin-bill__total" style="margin-top: 24px; padding-left: .125rem; padding-right: .125rem">
              <div>Заказ (₽): <span v-if="order.discount" class="admin-bill__discount">Скидка {{ order.discount }} %</span></div>
              <div>
                <b style="font-size: 20px">{{ new Intl.NumberFormat('ru-RU', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format((order.total_amount)) }}</b>
                <!--                <span> ₽</span>-->
              </div>
            </div>
          </div>
        </div>
        <!--        <div class="admin-bill__block">-->
        <!--          <div class="admin-bill__total">-->
        <!--            <div>Скидка (%): <b>{{ order.discount }}</b></div>-->
        <!--            <div>-->
        <!--              <b>{{ order.discount_summ }}</b>-->
        <!--              <span> ₽</span>-->
        <!--            </div>-->
        <!--          </div>-->
        <!--        </div>-->
        <!--        <div class="admin-bill__block">-->
        <!--          <div class="admin-bill__total">-->
        <!--            <div>Заказ (₽): <span v-if="order.discount" class="admin-bill__discount">Скидка {{ order.discount }} %</span></div>-->
        <!--            <div>-->
        <!--              <b>{{ order.total_amount }}</b>-->
        <!--              <span> ₽</span>-->
        <!--            </div>-->
        <!--          </div>-->
        <!--        </div>-->
        <div class="admin-bill__block" v-for="orderTip in order.participantsArray">
          <div class="admin-bill__master">
            <div class="admin-bill__user">
              <div class="admin-bill__avatar">
                <img :src="orderTip.photo_path || '/img/content/icon_avatar.svg'">
              </div>
              <div class="admin-bill__name">{{ orderTip.first_name }}</div>
              <!--              <div class="admin-bill__name">{{ orderTip.first_name }} {{ orderTip.last_name }}</div>-->
            </div>
            <!--            <div class="admin-bill__tips">{{ formatPrice(findParticipant(orderTip.id).tips) ?? 0 }} ₽</div>-->
            <div class="admin-bill__tips">{{ formatPrice(orderTip.distributed_amount) }} ₽</div>
          </div>
        </div>
        <div class="admin-creator" v-if="order.user">
          <div class="admin-creator-name">Создатель: {{ order.user.first_name }} {{ order.user.last_name }}</div>
        </div>
        <div class="admin-bill__bottom">
          <div class="admin-bill__date">{{ formatDate(order.created_at) }}</div>
          <div
            v-if="order.status === 'new'"
            class="admin-bill__status admin-bill__status_cancel"
          >
            не оплачен
          </div>
          <div
            v-if="order.status === 'ok'"
            class="admin-bill__status admin-bill__status_ok"
          >
            оплачено
          </div>
          <div
            v-if="order.status === 'cancel'"
            class="admin-bill__status admin-bill__status_cancel"
          >
            отменен
          </div>
        </div>


        <!--        <div v-if="order.status !== 'ok'" class="send-link-to-client">-->
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


        <div class="result__textarea">
          <h5 class="pt-2">Комментарий к заказу
            <span class="char-count">{{ charCount }}/150</span>
          </h5>
          <div class="client-result__textarea">
          <textarea class="input-field"
                    v-model="order.comment"
                    maxlength="150"
                    @blur="sendComment">
          </textarea>
          </div>
        </div>

        <div class="result__textarea">
          <div class="admin-bill__list" v-if="order.status == 'ok'" style="margin-top: 20px">
            <ul>
              <li v-for="statistic in orderStatistics">
                <span class="order-product-name" style="background: none; max-width: 265px;">{{ statistic.name }}</span>
                <span style="background: none;">
                  <b>{{ statistic.value }}</b>
                </span>
              </li>
            </ul>
          </div>
        </div>

        <div class="admin-qr" v-if="order.status !== 'ok'">
          <div class="admin-qr__code">
            <div class="qr-code-container">
              <qrcode-vue :value="order.qr_code.link" size="300" level="H"></qrcode-vue>
            </div>
          </div>
          <div class="admin-qr__link">
            <button class="btn btn_none btn_delete_noborder" @click="showModal">
              <img class="close-icon" src="/img/icon_cart.svg" alt="">
            </button>
            <button class="btn btn_none btn_delete_noborder" @click="copyLink(order.qr_code.link)" :class="{'btn-copied': isCopied}">
              <img class="close-icon" src="/img/icon_copy.svg" alt="">
            </button>
            <button ref="shareBlock" class="btn btn_none btn_delete_noborder" @click="share(order.qr_code.link)">
              <img class="share-icon" src="/img/icon_share.svg" alt="">
              <div id="share-block" :class="{'js-hidden': !isShowShareBlock}"></div>
            </button>
          </div>

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

        </div>
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

  <Spiner :isLoading="isLoading"></Spiner>
</template>

<script>

import { Link } from '@inertiajs/vue3'
import QrcodeVue from 'qrcode.vue'
import ModalDeleteOrder from '../../../../Shared/Modals/ModalDeleteOrder.vue'
import Spiner from  '@/Shared/Spiner.vue'
import axios from 'axios'
import OrdersItem from '../Components/OrdersItem.vue'

export default {
  components: {
    OrdersItem,
    ModalDeleteOrder,
    QrcodeVue,
    Link,
    Spiner
  },
  props: {
    order: {
      type: Object,
      required: true
    },
    qrCodes: {
      type: Object,
      required: true
    },
    orderStatistics: {
      type: Object,
      required: true
    }
  },
  setup() {
    let shareScript = document.createElement('script')
    shareScript.setAttribute('src', 'https://yastatic.net/share2/share.js')
    document.head.appendChild(shareScript)
  },
  data() {
    return {
      isModalActive: false,
      isLoading: false,
      isShowShareBlock: false,
      organizationName: '',
      selectQrCode: null,
      generatedQrCode: null,
      comment: '',
      phoneNumber: '',
      invalidPhoneNumber: false,
      emptyPhoneNumber: false,
      isCopied: false,
    };
  },
  computed: {
    formattedOrderTotal() {
      return this.order.total_amount; // 70.40
    },
    discountAmount() {
      return Math.ceil(this.order.total_amount * (this.order.discount / 100));
    },
    finalTotal() {
      return Math.ceil(this.order.total_amount);
    },

    totalWithoutDiscount() {
      return this.order.order_items.reduce((sum, product) => {
        return sum + (product.product_price * product.quantity);
      }, 0);
    },

    calculateDiscount() {
      const totalAmount = 80;
      const discountPercentage = 12;

      // Вычисляем сумму скидки
      const discountAmount = (discountPercentage / 100) * totalAmount;

      // Возвращаем значение скидки
      return Math.floor(discountAmount * 100) / 100; // Округляем до 2-х знаков после запятой
    },

    finalTotalAsDiscount() {
      return Math.ceil(this.order.total_amount - this.discountAmount);
    },
    charCount() {
      return this.order.comment ? this.order.comment.length : 0;
    }
  },
  methods: {
    discountedPrice(price) {
      const discountValue = parseFloat(this.order.discount) || 0;
      const priceAfterDiscount = price * ((100 - discountValue) / 100);
      const flooredPrice = Math.floor(priceAfterDiscount * 100) / 100; // Округляем в меньшую сторону до двух десятичных знаков
      return flooredPrice; // Форматируем до двух знаков после запятой
    },

    trySelectQrCode(qrCode) {
      axios.post('/checkQrCodeStatus', {
        qrCode: qrCode
      })
        .then((response) => {
          if (!response.data.status) {
            alert(response.data.error)
          }
          else {
            this.setQrCode(qrCode)
            this.saveQrCode()
          }
        })
        .catch((error) => {

        });
    },
    saveQrCode() {
      axios.post('admin/save_qr_code_in_oder', {
        order: this.order,
        qrCode: this.selectQrCode
      })
        .then((response) => {
          if (!response.data.status) {
            this.setQrCode(qrCode)
            this.saveQrCode()
          }
        })
        .catch((error) => {

        });
    },
    setQrCode(qrCode) {
      this.selectQrCode = qrCode
      this.generatedQrCode = null
      this.order.qr_code_id = qrCode.id
      this.order.qr_code.link = qrCode.link
    },
    generateQrCode() {
      this.selectQrCode = null
      this.generatedQrCode = true
      axios.get('generateHideQrCode')
        .then(response => {
          this.setQrCode(response.data)
          this.saveQrCode()

          setTimeout(() => {

          }, 1000)
        })
        .catch(error => {
          console.error(error)
        })
    },
    copyLink(link) {
      navigator.clipboard.writeText(link);
      this.isCopied = true;
      if (typeof ym === 'function') {
        ym(98232814,'reachGoal','Saved Order Link Copy')
      }
    },
    findParticipant(searchUserId) {
      return this.order.order_participants.find(
        (participant) => participant.user_id === searchUserId
      );
    },
    formatPrice(value) {
      // Преобразуем значение в число
      const parsedValue = parseFloat(value);

      // Проверяем, является ли результат числом
      if (isNaN(parsedValue)) {
        return '0'; // Или другое значение по умолчанию
      }

      // Форматируем число
      return parsedValue
        .toFixed(2) // Округляем до целого числа
        .replace(/\B(?=(\d{3})+(?!\d))/g, ' '); // Добавляем пробелы для тысяч
    },
    formatDate(dateString) {
      const months = [
        'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
        'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'
      ];

      const date = new Date(dateString);

      const day = date.getDate();
      const month = months[date.getMonth()];
      const year = date.getFullYear();

      const hours = String(date.getHours()).padStart(2, '0');
      const minutes = String(date.getMinutes()).padStart(2, '0');

      return `${day} ${month} ${year} ${hours}:${minutes}`;
    },
    getStatusClass(status) {
      switch (status) {
        case 'new':
          return 'Новый';
        case 'cancel':
          return 'Отменен';
        case 'ok':
          return 'Оплачен';
        default:
          return 'Черновик';
      }
    },
    // handleDelete() {
    //   this.$inertia.post('/super_admin/delete_category', { id: this.order.id });
    //   this.closeModal();
    // },
    handleDelete() {
      this.startLoading()
      this.$inertia.post('/admin/delete_order', {
        orderId: this.order.id
      })

      if (typeof ym === 'function') {
        ym(98232814,'reachGoal','Saved order Deleted order')
      }

      this.closeModal();

      setTimeout(() => {
        this.$inertia.visit('/orders');
        this.stopLoading()
      }, 1000);
    },
    showModal() {
      this.isModalActive = true;
    },
    closeModal() {
      this.isModalActive = false;
    },
    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },
    back() {
      // this.$inertia.get(`/orders`);
      this.$emit('back');
    },
    share(link) {
      this.isShowShareBlock = true;
      let title = this.organizationName ? this.organizationName : 'yagoda.team';
      title = title + '. Ссылка на оплату услуг';
      var share = Ya.share2('share-block', {
        content: {
          url: link,
          title: title,
          description: 'Ссылка на оплату услуг',
        },
        theme: {
          services: 'telegram,whatsapp,viber,vkontakte',
          lang: 'ru'
        }
      });
      if (typeof ym === 'function') {
        ym(98232814,'reachGoal','Saved order Sharing')
      }
    },
    handleOutsideClick(event) {
      if (!this.$refs.shareBlock || !this.$refs.shareBlock.contains(event.target)) {
        this.isShowShareBlock = false;
      }
    },
    charCount() {
      return this.comment.length;
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
    sendComment() {
      const orderId = this.order.id
      axios.post('/orders/' + orderId + '/order_comment', {
        comment: this.order.comment
      })
        .then((response) => {

        })
    },
    validatePhoneNumber(phone) {
      const phonePattern = /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/;
      const hasDigits = /\d/.test(phone);
      this.invalidPhoneNumber = hasDigits && !phonePattern.test(phone);
    },
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
  margin-top: 18px;
}

.qr-code-container {
  text-align: center;
  margin: 20px;
}

.admin-qr__share {
  width: 22px;
  height: 22px;
  margin-left: .625rem;
  background:url(../img/icon_share_order.svg) no-repeat;
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

.delete_margin {
  margin-bottom: 10px;
}

.admin-qr__copy {
  width: 22px;
  height: 22px;
  margin-left: .625rem;
  background:url(../img/icon_copy.svg) no-repeat;
}


.admin-qr__link {
  display: flex;
  min-height: 72px;
  margin-top: .5rem;
  border-radius: 1rem;
  background: none;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
}

.btn {
  flex: 1;
  margin: 0 0.5rem;
  padding: 10px;
  color: #fff;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
}

.btn:first-child {
  margin-left: 0;
}

.btn:last-child {
  margin-right: 0;
}

.btn_none {
  width: 3.375rem;
  min-width: 3.375rem;
  height: 3.375rem;
  min-height: 3.375rem;
  background: #fff;
}

.close-icon {
  cursor: pointer;
}

.share-icon {
  width: 1.663rem;
  margin-left: 30px;
}

.order-product-name {
  display: inline-block; /* Позволяет элементу занимать только необходимую ширину */
  max-width: 185px; /* Укажите максимальную ширину для заголовка, подберите по необходимости */
  overflow-wrap: break-word; /* Переносит слова длиннее имеющегося пространства */
}

.admin-bill__list ul li span:not(.admin-bill__title) {
  white-space: normal;
}

.admin-qr__list {
  margin-top: 2rem;
}

.admin-bill__date {
  text-decoration: none;
}

.admin-creator-name{
  font-size: 14px;
  font-weight: 500;
  color: #9ca4b5;
}

.admin-bill__bottom{
  margin-top: 1px;
}

.admin-creator{
  margin-top: 14px;
}
.admin-bill__avatar img{
  border-radius: 0.7rem;
}

.admin-bill__discount {
  font-size: 12px;
  font-weight: 500;
  color: #949699;
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
  margin-top: 20px;
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
  font-size: 20px;
  color: #333;
  box-sizing: border-box;
}
.btn-copied {
  background: #e5e5e5;
  box-shadow: none;
}
</style>
