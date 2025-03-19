<template>
  <div class="admin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="admin__container">
      <div class="admin-services">
        <div class="admin-services__top">
          <Link class="back" href="/">отменить заказ</Link>
          <h2>Выбор услуг</h2>
        </div>
        <ServicesList :services="data.services" @updateService="updateService"></ServicesList>
        <ServicesCategories :categories="data.categories" @categoryClick="categoryClick"></ServicesCategories>
        <div class="admin-services__bottom">
          <div class="admin-services__info">
            <div class="admin-services__items">Позиций: <span>{{ data.positions }}</span></div>
            <div class="admin-services__total">Позиций: <span>{{ data.total }}</span></div>
          </div>
          <div class="admin-services__next">
            <button class="btn btn_arrow" @click="goNext">далее</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ServicesList from './Components/ServicesList.vue'
import ServicesCategories from './Components/ServicesCategories.vue'
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link,
    ServicesList,
    ServicesCategories,
  },
  props: {
    data: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      meta: {
        title: "Выбор услуг"
      },
      services: [
        {
          icon: '/img/content/icon_service.svg',
          name: 'Стрижка модельная с навороченной укладкой',
          price: 3100,
          quantity: 1,
          active: true,
        },
        {
          icon: '/img/content/icon_service.svg',
          name: 'Стрижка модельная с навороченной укладкой',
          price: 3100,
          quantity: 0,
          active: false,
        },
      ],
      categories: [
        {
          image: '/img/demo/beard.png',
          title: 'Борода',
          active: true,
        },
        {
          image: '/img/demo/beard.png',
          title: 'Стрижки',
          active: false,
        },
        {
          image: '/img/demo/beard.png',
          title: 'Маникюр Маникюр Маникюр',
          active: false,
        },
      ],
      positions: 12,
      total: 12359,
    }
  },
  methods: {
    cancelOrder() {
      // Реализуйте логику отмены заказа
      console.log('Заказ отменен')
    },
    updateService(service) {
      // Реализуйте логику обновления услуги
      console.log('Обновлена услуга:', service)
    },
    categoryClick(category) {
      // Реализуйте логику выбора категории
      console.log('Выбрана категория:', category)
    },
    goNext() {
      // Реализуйте логику перехода к следующему шагу
      console.log('Переход к следующему шагу')
    },
  },
}
</script>
