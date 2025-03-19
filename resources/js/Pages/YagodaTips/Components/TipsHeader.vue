<template>
  <div class="ytips__cover">
    <img v-if="data && data.photo_path" :src="data.photo_path" alt="" />
    <img v-else src="/img/demo/persona.png" alt="" />
    <div class="ytips__logo-y"><img src="/img/content/logo_yagoda.svg"></div>
    <div class="ytips__logo" v-if="data && data.logo_path">
      <img :src="data.logo_path">
    </div>
  </div>
</template>
<script>
import axios from 'axios'

export default {
  name: "Tips header",
  data() {
    return {
      data: null
    }
  },
  mounted() {
     this.getData();
  },
  methods: {
    async getData() {
      const cachedData = localStorage.getItem('tipsGroupData');
      if (cachedData) {
        this.data = JSON.parse(cachedData);
      }

      await axios.get('/tips/group-json')
        .then(response => {
          if (response && response.data && response.data.message) {
            if (typeof response.data.message == 'object') {
              this.data = response.data.message;
            } else {
              this.data = JSON.parse(response.data.message);
            }
            localStorage.setItem('tipsGroupData', JSON.stringify(this.data));
          }
        })
        .catch(error => {
          console.error(error);
        });
    }
  }
}
</script>
