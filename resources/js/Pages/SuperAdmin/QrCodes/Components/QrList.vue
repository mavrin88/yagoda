<template>
  <div class="qr-list">
    <div
      v-for="(qr_code, index) in qr_codes"
      :key="qr_code.id"
      class="qr-list__item"
      :class="{ 'qr-list__item_active': activeIndex === index }"
      @click="setActiveIndex(index)"
    >
      <div class="qr-list__count">{{ qr_code.id }}</div>
      <div class="qr-list__name">{{ qr_code.name }}</div>
      <div
        v-if="activeIndex === index"
        class="qr-list__delete"
        @click.stop="deleteQrCode(index)"
      ></div>
    </div>
  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'

export default {
  components: {
    Link,
  },
  props: {
    qr_codes: Object
  },
  data() {
    return {
      activeIndex: null
    };
  },
  methods: {
    setActiveIndex(index) {
      this.activeIndex = index;
    },
    deleteQrCode(index) {
      this.$emit('delete-qr-code', index);

      if (this.activeIndex === index) {
        this.activeIndex = null;
      }
    }
  },
}
</script>
