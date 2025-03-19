<template>
  <div class="superadmin superadmin_white">
    <Head title="Редактировать раздел" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/super_admin/categories">назад</Link>
        <h2>РЕДАКТИРОВАТЬ РАЗДЕЛ</h2>
      </div>
      <div class="superadmin-add">
        <div class="superadmin-add__top">
          <div class="img-w-text">
            <img :src="category.image">
            <span>{{ category.name }}</span>
          </div>
        </div>
        <div class="form">
          <div class="form__item">
            <div class="superadmin-add__image">
              <input type="file" id="cover" @change="handleImageUpload">
              <label for="cover">
                <img :src="category.image">
              </label>
            </div>
          </div>
          <div class="form__item">
            <input type="text" id="name" v-model="category.name">
            <label for="name">Наименование раздела каталога</label>
          </div>
          <div class="form__item">
            <v-select :options="bills" v-model="selected" :clearable="false"></v-select>
            <label for="bill">Счет получения денег</label>
          </div>
<!--          <div class="form__item">-->
<!--            <div class="form__copy"></div>-->
<!--            <input type="text" id="id" readonly value="gDSFGG5U%hDFGн5">-->
<!--            <label for="id">ID каталога для его привязки</label>-->
<!--          </div>-->
        </div>
        <div class="superadmin-add__buttons">
          <button class="btn btn_delete btn_delete_noborder" @click="showModal"></button>
          <button class="btn" @click="saveCategory">Сохранить</button>
        </div>
      </div>

      <cropper-modal
        type="static"
        :croppedWidth="280"
        :croppedHeight="280"
        :is-active="isModalActiveImage"
        messageButton="ПРИМЕНИТЬ"
        ref="cropperModal"
        :imageToEdit="category.image"
        @change="change"
        @save="saveCroppedImage"
        @close="isModalActive = false"
      />

      <Modal
        :action-type="'delete'"
        :is-active="isModalActive"
        @confirmed="handleDelete"
        @close="closeModal"
        message="Удалить каталог?"
      />

    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import Modal from '../../../../Shared/Modal.vue'
import CropperModal from '../../../../Shared/CropperModal.vue'

export default {
  components: {
    Head,
    CropperModal,
    Link,
    vSelect,
    Modal
  },
  data() {
    return {
      selected: null,
      isModalActive: false,
      isModalActiveImage: false,
    };
  },
  props: {
    category: Object,
    bills: Object,
  },
  mounted() {
    this.initSelectedBill();
  },
  computed: {
    canSaveCategory() {
      return this.category.name;
    },
  },
  methods: {
    change({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.catalog.saveImage = blob;
      });
      console.log(coordinates, canvas);
    },
    compressImage(dataUrl, quality = 0.7) {
      return new Promise((resolve, reject) => {
        const img = new Image();
        img.src = dataUrl;

        img.onload = () => {
          const canvas = document.createElement('canvas');
          const ctx = canvas.getContext('2d');

          // Устанавливаем размеры канваса
          canvas.width = img.width;
          canvas.height = img.height;

          // Рисуем изображение на канвасе
          ctx.drawImage(img, 0, 0);

          // Сжимаем изображение, используя метод toDataURL
          const compressedDataUrl = canvas.toDataURL('image/jpeg', quality);
          resolve(compressedDataUrl);
        };

        img.onerror = (error) => {
          reject(error);
        };
      });
    },
    saveCroppedImage(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.catalogImage = dataUrl;
        this.category.image = compressedDataUrl;
        this.category.saveImage = compressedDataUrl
        this.isModalActiveImage = false
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error);
      });
      // this.catalogImage = dataUrl;
    },
    initSelectedBill() {
      const selectedBill = this.bills.find(bill => bill.id === this.category.bill_id)
      if (selectedBill) {
        this.selected = {
          id: selectedBill.id,
          label: selectedBill.label,
          value: selectedBill.id,
        }
      }
    },

    handleImageUpload(event) {
      this.isModalActiveImage = true
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.category.image = e.target.result;
      }

      reader.readAsDataURL(file);

      this.category.saveImage = event.target.files[0];
    },
    saveCategory() {
      this.submitted = true;

      if (this.canSaveCategory) {
        this.category.bill_id = this.selected.id
        this.$inertia.post('/super_admin/save_category', this.category);
      }
    },

    showModal() {
      this.isModalActive = true;
    },
    closeModal() {
      this.isModalActive = false;
    },
    handleDelete() {
      this.$inertia.post('/super_admin/delete_category', { id: this.category.id });
      this.closeModal();
    }
  },
}
</script>

<style>
.v-select .vs__selected {
  font-size: 24px !important;
}
</style>
