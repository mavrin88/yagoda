<template>
  <div class="superadmin">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/">назад</Link>
        <h2>НАСТРОЙКИ</h2>
      </div>
      <div class="admin__container">
        <div class="admin__links">
          <Link href="/super_admin/map_links">
            <div>ССЫЛКИ НА КАРТЫ</div>
          </Link>
          <Link href="/super_admin/other_settings">
            <div>ПРОЧИЕ НАСТРОЙКИ</div>
          </Link>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'
import axios from 'axios'
import Modal from '../../../Shared/Modal.vue'
import CropperModal from '../../../Shared/Cropper/CropperModal.vue'
import CropperModalStencil from '../../../Shared/Cropper/CropperModalStencil.vue'
import ModalDeleteOrganization from '../../../Shared/Modals/ModalDeleteOrganization.vue'

export default {
  components: {
    Link,
    Head
  },
  props: {

  },
  computed: {},
  data() {
    return {
      meta: {
        title: "Настройки"
      }
    }
  },
  methods: {

  },
  watch: {

  },
}
</script>
