<template>
  <div class="masters-full">
    <div class="masters-full__item" v-for="master in masters" :key="master.first_name">
      <div class="masters-full__image"><img :src="master.photo_path || '/img/content/icon_avatar.svg'"></div>
      <div class="masters-full__content">
        <div class="masters-full__top">{{ master.ordersWithTips }} / {{ master.percentageWithTips }}% / {{ master.averageTip }} / <b>{{ master.totalTips }}</b> <span> ₽</span></div>
        <div class="masters-full__bottom">
          <div class="masters-full__name">{{ master.first_name }} {{ master.last_name }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    masters: {
      type: Array,
      required: true
    }
  },

  data() {
    return {
      showMasters: false,
    }
  },

  methods: {
    toggleMasters() {
      this.showMasters = !this.showMasters
    }
  }
}
</script>

<style scoped>
.masters-full {
  max-height: 1000px;
  overflow: hidden;
  transition: max-height 0.5s ease-in-out;
  margin-top: 40px;
}

.masters-full--hidden {
  max-height: 0;
}
</style>
