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
          <button class="btn btn_none btn_delete_noborder" @click="copyLink()">
            <img class="close-icon" src="/img/icon_copy.svg" alt="">
          </button>
          <button ref="shareBlock" class="btn btn_none btn_delete_noborder" @click="share()">
            <img class="share-icon" src="/img/icon_share.svg" alt="">
            <div id="share-block" :class="{'js-hidden': !isShowShareBlock}"></div>
          </button>
        </div>

<!--        <div class="admin-qr__link" v-if="generatedQrCode">-->
<!--          <span>{{ generatedQrCode.link.length > 30 ? generatedQrCode.link.slice(0, 30) + '...' : generatedQrCode.link }}</span>-->
<!--          <div class="admin-qr__share" title="Копировать" @click="share(generatedQrCode.link) && copyLink(generatedQrCode.link)">-->
<!--            <div id="share-block" :class="{'js-hidden': !isShowShareBlock}"></div>-->
<!--          </div>-->
<!--        </div>-->
        <div class="admin-qr__total">
          <p>к оплате: <b>{{ $formatNumber(finalTotalAsDiscount) }}</b> ₽</p>
          <p>мастера: <b>{{ selectedMasters.map(master => master.first_name || 'Без имени').join(', ') }}</b></p>
        </div>
<!--        <div class="admin-qr__note">Или выбери статичный QR находящийся возле вас.</div>-->
        <div class="admin-qr__note">Выбери статичный QR находящийся возле вас.</div>
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
        </div>

        <div @click="generateQrCode()" :class="['admin-qr__item', generatedQrCode ? 'admin-qr__item_active' : '']">
          <div class="admin-qr__count">
            <img src="img/content/unicum.svg">
          </div>
          <div class="admin-qr__name">Уникальный</div>
        </div>
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

export default {
  components: {
    Modal,
    QrcodeVue,
    ModalDeleteOrder
  },
  props: {
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
  },
  data() {
    return {
      selectQrCode: null,
      generatedQrCode: null,
      orderIsOpen: null,
      intervalId: null,
      isShowShareBlock: false,
      isModalActive: false,
    }
  },
  setup() {
    let shareScript = document.createElement('script')
    shareScript.setAttribute('src', 'https://yastatic.net/share2/share.js')
    document.head.appendChild(shareScript)
  },
  mounted() {
    this.checkStartQrCode()
  },
  computed: {
    discountAmount() {
      return Math.ceil(this.totalPrice * (this.discount / 100));
    },

    finalTotal() {
      return Math.ceil(this.totalSumm - this.discountAmount);
    },

    finalTotalAsDiscount() {
      return Math.ceil(this.totalPrice - this.discountAmount);
    },
  },
  methods: {
    share() {
      let link = '';

      if (this.generatedQrCode && this.generatedQrCode.link) {
        link = this.generatedQrCode.link;
      }

      if (this.selectQrCode && this.selectQrCode.link) {
        this.generateQrCode()
        this.selectQrCode = null
        link = this.generatedQrCode.link;
      }

      if (link) {
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
      } else {
        console.error('Нет ссылки для копирования.');
      }
    },
    goBack() {
      this.$emit('hideQrCode')
      this.stopOrderStatusCheck()
    },
    goToOrders() {
      this.stopOrderStatusCheck()
      // this.$inertia.visit('/orders')
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
    copyLink() {
      let link = '';

      if (this.generatedQrCode && this.generatedQrCode.link) {
        link = this.generatedQrCode.link;
      }

      if (this.selectQrCode && this.selectQrCode.link) {
        link = this.selectQrCode.link;
      }

      if (link) {
        navigator.clipboard.writeText(link).then(() => {
          this.$toast.open({
            message: 'Ссылка скопирована',
            type: 'success',
            position: 'top-right',
            duration: 2000,
          });
          console.log('Ссылка скопирована в буфер обмена!');
        }).catch(err => {
          console.error('Ошибка при копировании ссылки: ', err);
        });
      } else {
        console.error('Нет ссылки для копирования.');
      }
    },
    generateQrCode() {
      this.selectQrCode = null
      axios.get('generateHideQrCode')
        .then(response => {
          this.generatedQrCode = response.data

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
      this.intervalId = setInterval(() => {
      this.checkOrderStatus();
      }, 2000);
    },
    stopOrderStatusCheck() {
      clearInterval(this.intervalId);
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
            // this.stopOrderStatusCheck();
            this.goToOrders()
          }
        })
        .catch((error) => {
          console.error('Error checking order status:', error);
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

      setTimeout(() => {
        this.goToOrders()
      }, 500);
    },
    handleOutsideClick(event) {
      if (!this.$refs.shareBlock || !this.$refs.shareBlock.contains(event.target)) {
        this.isShowShareBlock = false;
      }
    }
  },
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
  width: 1.063rem;
}

.share-icon {
  width: 1.263rem;
  margin-left: 54px;
}

.admin-qr__list {
  margin-bottom: 1.125rem;
}
</style>
