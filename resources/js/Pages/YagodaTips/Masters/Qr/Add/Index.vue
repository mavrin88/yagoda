<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back"></a>
          <div class="ytips-top__title">Привязать стойку ЯГОДА с QR</div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-text ytips-text_12">
          <p>Укажите название QR кода для простоты идентификации. Например: Стойка ресепшен, Маникюр, у администратора</p>
        </div>
        <div class="form">
          <div class="form__item">
            <input class="bg-transparent" type="text" id="name" v-model="title">
            <label for="name" style="color: rgba(0, 0, 0, 1)">Наименование QR-кода</label>
          </div><br>
          <div class="btn btn_low">Сохранить</div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TipsHeader from '../../../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { TipsHeader, Head },
  data() {
    return {
      title: ''
    }
  },
  props: {
    data: Object
  },
  mounted() {

  },
  methods: {
  }
}
</script>
