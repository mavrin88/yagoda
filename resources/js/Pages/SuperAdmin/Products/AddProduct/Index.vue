<template>
  <div class="superadmin superadmin_white">
    <Head title="Добавление" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" :href="`/super_admin/category_products/${category.id}`">назад</Link>
        <h2>ДОБАВИТЬ</h2>
      </div>
      <div class="superadmin-add">
        <div class="superadmin-add__top">
          <div class="img-w-text">
            <img :src="product.image">
            <span>{{ product.name }}</span>
          </div>
        </div>

        <form class="form">
          <div class="form__item">
            <div class="superadmin-add__image">
              <input type="file" id="cover" @change="handleImageUpload">
              <label for="cover"><img :src="product.image"></label>
            </div>
          </div>
          <div class="form__item">
            <v-select :options="categoriesFormated" v-model="selected" :clearable="false"></v-select>
            <label for="name">Каталог</label>
          </div>
          <div class="form__item">
            <input type="text" id="name" v-model="product.name">
            <label for="name">Наименование услуги или товара</label>
            <div class="form__error" v-if="submitted && !product.name">Поле не может быть пустым</div>
          </div>
          <div class="form__item">
            <input type="text" id="price" v-model="product.price" v-mask="'########'">
            <label for="price">Цена</label>
            <div class="form__error" v-if="submitted && !product.price">Поле не может быть пустым</div>
          </div>
          <button type="button" class="btn btn_save btn_w100" @click="saveProduct">Сохранить</button>
        </form>
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
    :imageToEdit="product.image"
    @save="saveCroppedImage"
    @close="isModalActive = false"
  />
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css';
import CropperModal from '../../../../Shared/CropperModal.vue'

export default {
  components: {
    Head,
    CropperModal,
    Link,
    vSelect
  },
  data() {
    return {
      product: {
        id: null,
        name: '',
        image: null,
        category_id: null,
        saveImage: null,
      },
      submitted: false,
      selected: {
        id: this.category.id,
        label: this.category.name,
        value: this.category.id,
      },
      isModalActive: false,
    };
  },
  methods: {
    saveCroppedImage(dataUrl) {
      this.compressImage(dataUrl, 0.7).then(compressedDataUrl => {
        this.product.image = compressedDataUrl;
        this.product.saveImage = compressedDataUrl
      }).catch(error => {
        console.error('Ошибка сжатия изображения:', error);
      });
      // this.catalogImage = dataUrl;
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
    handleImageUpload(event) {
      this.isModalActive = true
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = (e) => {
        this.product.image = e.target.result;
      }

      reader.readAsDataURL(file);

      this.product.saveImage = event.target.files[0];
    },
    saveProduct() {
      this.submitted = true;

      if (this.canSaveProduct) {
        this.product.category_id = this.selected.id
        this.$inertia.post('/super_admin/product/store', this.product);
      }
    },
    setCategory(val) {
      alert(val)
      this.product.category = val;
    }
  },
  computed: {
    canSaveProduct() {
      return this.product.name && this.product.price;
    },
  },
  props: {
    category: Object,
    categoriesFormated: Object,
  },
}
</script>
