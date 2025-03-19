<template>
  <div class="superadmin">

    <TipsHeader />

    <Head title="Ссылки на карты" />
    <div class="superadmin__container">

      <div class="ytips-top">
        <div class="ytips__container">
          <Link class="ytips-top__back" href="/tips/groups_settings"></Link>
          <div class="ytips-top__title">ССЫЛКИ НА КАРТЫ</div>
        </div>
      </div>
      <div class="superadmin__note superadmin__note_dark">
        <p>После оплаты (в случае, если Гость оценил ваше заведение на 5 звезд) ему будет предложено оставить отзыв о вашей организации на Картах.
          Укажите адрес страницы Нового Отзыва вашей организации на картах, чтобы система знала, куда его направить.</p>
        <br>
        <p>Важно: На картах копируйте ссылку именно на раздел ОТЗЫВОВ о вашей организации. Гостям так будет существенно проще оставить отзыв.</p>
        <br>
        <br>
      </div>

      <div class="yandex mb-[64px]">
        <div class="map_name  mb-[12px]">Карты <b><span class="text-red-700">Я</span>ндекс</b></div>
        <div class="link_paste mb-[12px]">
          <p class="text-[14px] underline cursor-pointer" @click="pasteLink('map_link_yandex')">
            вставить скопированную ссылку
          </p>
          <p>
            <button class="icon icon_delete" @click="deleteLink('map_link_yandex')"></button>
          </p>
        </div>
        <div class="superadmin__note pl-0">{{ group.map_link_yandex }}</div>
      </div>

      <div class="2gis">
        <div class="map_name mb-[12px]">Карты <b>2GIS</b></div>
        <div class="link_paste mb-[12px]">
          <p class="text-[14px] underline cursor-pointer" @click="pasteLink('map_link_2gis')">
            вставить скопированную ссылку
          </p>
          <p>
            <button class="icon icon_delete" @click="deleteLink('map_link_2gis')"></button>
          </p>
        </div>
        <div class="superadmin__note pl-0">{{ group.map_link_2gis }}</div>
      </div>

    </div>
  </div>

</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import TipsHeader from '@/Pages/YagodaTips/Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
    Head,
    Link
  },
  props: {
    group: Object
  },
  computed: {},
  data() {
    return {
      links: {
        map_link_yandex: this.group.map_link_yandex,
        map_link_2gis: this.group.map_link_2gis,
      }
    }
  },
  methods: {
    pasteLink(service) {
      navigator.clipboard.readText()
        .then(text => {
          this.links[service] = text;
          this.group.map_link_yandex = this.links.map_link_yandex
          this.group.map_link_2gis = this.links.map_link_2gis
          this.saveLinks()
        })
        .catch(err => {
          console.error('Ошибка при вставке ссылки:', err);
        });
    },
    deleteLink(service) {
      this.links[service] = '';

      if (service === 'map_link_yandex' || service === 'map_link_2gis') {
        this.group[service] = '';
      }

      this.saveLinks();
    },
    saveLinks() {
      axios.post('/tips/save-links-settings', this.group)
        .then(response => {
          console.log('Ответ сервера:', response.data);
        })
        .catch(error => {
          console.error('Ошибка при отправке данных:', error);
        });
    }
  },
  watch: {

  },
}
</script>
<style lang="scss" scoped>
.link_paste {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.superadmin {
  padding-top: 0;
  &__top {
    margin-top: 10px;
  }
}
</style>
