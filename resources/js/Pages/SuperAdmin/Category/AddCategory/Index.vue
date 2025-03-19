<template>
  <div class="superadmin superadmin_white">
    <Head title="Добавить раздел" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/super_admin/categories">назад</Link>
        <h2>ДОБАВИТЬ РАЗДЕЛ</h2>
      </div>
      <div class="superadmin-add">
        <div class="superadmin-add__top">
          <div class="img-w-text">
            <img v-if="catalog.image" :src="catalog.image" alt="">
<!--            <img v-else src="/img/demo/beard.png" alt="">-->
            <span>{{ catalog.name }}</span>
          </div>
        </div>

        <div class="form">
          <div class="form__item">
            <div class="superadmin-add__image">
              <input type="file" id="cover" @change="handleImageUpload">
              <label for="cover"><img :src="catalog.image"></label>
            </div>
          </div>
          <div class="form__item">
            <input type="text" id="name" v-model="catalog.name">
            <label for="name">Наименование раздела каталога</label>
            <div class="form__error" v-if="submitted && !catalog.name">Поле не может быть пустым</div>
          </div>
          <div class="form__item">
            <v-select :options="bills" v-model="catalog.bill_id" :clearable="false"></v-select>
            <label for="bill">Счет получения денег</label>
            <div class="form__error" v-if="submitted && !catalog.bill_id">Поле не может быть пустым</div>
          </div>
          <div class="form__item">
            <div class="form__copy"></div>
            <input type="text" id="id" readonly v-model="catalog.parent_catalog_id">
            <label for="id">ID каталога для его привязки</label>
          </div>
        </div>
        <buton type="button" class="btn btn_save btn_w100" @click="saveCatalog">Сохранить</buton>
      </div>
    </div>
  </div>

  <cropper-modal
    type="static"
    :croppedWidth="280"
    :croppedHeight="280"
    :is-active="isModalActive"
    messageButton="ПРИМЕНИТЬ"
    ref="cropperModal"
    :imageToEdit="catalog.image"
    @change="change"
    @save="saveCroppedImage"
    @close="isModalActive = false"
  />

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import FileInput from '@/Shared/FileInput.vue'

import { Cropper } from 'vue-advanced-cropper';
import 'vue-advanced-cropper/dist/style.css';

import CropperModal from '../../../../Shared/CropperModal.vue'

export default {
  components: {
    Head,
    FileInput,
    Link,
    vSelect,
    Cropper,
    CropperModal
  },
  data() {
    return {
      accept: String,
      catalog: {
        id: null,
        name: '',
        image: null,
        bill_id: null,
        parent_catalog_id: null,
        saveImage: null,
      },
      submitted: false,
      croppedImage: null,
      isModalActive: false,
      catalogImage: false,
    };
  },
  methods: {
    change({ coordinates, canvas, url }) {
      canvas.toBlob((blob) => {
        this.catalog.saveImage = blob;
      });
      console.log(coordinates, canvas);
    },
    handleImageUpload(event) {
      this.isModalActive = true
      const file = event.target.files[0];

      // this.catalog.image = event.target.files[0]


      const reader = new FileReader();

      reader.onload = (e) => {
        this.catalog.image = e.target.result;
      }

      reader.readAsDataURL(file);

      this.catalog.saveImage = file;
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
        this.catalog.image = compressedDataUrl;
        this.catalog.saveImage = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error);
      });
      // this.catalogImage = dataUrl;
    },
    saveCatalog() {
      this.submitted = true;

      if (this.canSaveCatalog) {
        this.$inertia.post('/super_admin/category/store', this.catalog);
      }
    },
  },
  computed: {
    canSaveCatalog() {
      return this.catalog.name && this.catalog.bill_id;
    }
  },
  props: {
    bills: Object,
  },
}
</script>
