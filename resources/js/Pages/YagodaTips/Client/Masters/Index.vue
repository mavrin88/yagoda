<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-masters" v-if="visibleMasterList">
        <div class="ytips__cover-title">Выберите мастера</div>
        <div class="ytips__container">
          <div class="ytips-masters-list">
            <div class="ytips__container">
              <div class="ytips-masters-list">
                <div
                  @click="selectMaster(master)"
                  class="ytips-masters-list__item cursor-pointer transition-all duration-100 hover:shadow-[0_0_0_1px_#38b0b0]"
                  v-for="master in data.masters"
                  :key="master.id"
                  v-if="data.masters"
                >
                  <div class="ytips-masters-list__image">
                    <img :src="master.photo_path" width="72" height="72" alt="">
                  </div>
                  <div class="ytips-masters-list__info">
                    <div class="ytips-masters-list__name">{{ master.first_name }}</div>
                    <div class="ytips-masters-list__text">{{ master.purpose_tips }}</div>
                  </div>
                </div>
                <div v-else><br>Мастера ушли работать...</div>
              </div>
            </div>
          </div>
        </div>
      </div>



      <div class="ytips-pay" v-else>
        <Pay :settings="settings" :group="group" :master="selectedMaster" :orderId="orderId"></Pay>
      </div>
    </div>
  </main>
</template>

<script>
import { Head } from '@inertiajs/vue3'
import TipsHeader from '../../Components/TipsHeader.vue'
import Pay from '../Pay/Index.vue'
import axios from 'axios'

export default {
  name: "Index",
  components: {
    TipsHeader,
    Head,
    Pay
  },
  data() {
    return {
      visibleMasterList: true,
      selectedMaster: null,
      orderId: null
    }
  },
  props: {
    data: Object,
    group: Object,
    settings: Object
  },
  methods: {
    selectMaster(master) {
      this.selectedMaster = master;
      this.visibleMasterList = false;

      this.createOrder()
    },
    createOrder() {
      axios.post('/tips/create-order', {
        master_id: this.selectedMaster.id,
        groupId: this.group.id,
      }).then(response => {
        this.orderId = response.data.orderId
      })
    }
  }
}
</script>
