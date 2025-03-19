<template>
  <div>

    <div class="mb-4">
      <Link class="group flex items-center py-3" href="">
        <div :class="isUrl('dashboard/organizations') ? 'text-black' : 'text-black group-hover:text-black'">РУТАДМИНИСТРАТОР</div>
      </Link>
    </div>

    <div class="mb-4">
      <Link class="group flex items-center py-3" href="">
        <div :class="isUrl('dashboard/categories') ? 'text-black' : 'text-black group-hover:text-black'">ДОКУМЕНТАТОР</div>
      </Link>
    </div>

    <div class="mb-4">
      <Link class="group flex items-center py-3" href="">
        <div :class="isUrl('dashboard/users') ? 'text-black' : 'text-black group-hover:text-black'">ОТДЕЛ ЗАБОТЫ</div>
      </Link>
    </div>
    <div class="mb-4">
      <Link class="group flex items-center py-3" href="">
        <div :class="isUrl('dashboard/tests') ? 'text-black' : 'text-black group-hover:text-black'">БУХГАЛТЕР</div>
      </Link>
    </div>
    <div class="add-role">
<span>+ добавить роль</span>
    </div>

  </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
import Icon from '@/Shared/Icon.vue'
import vSelect from 'vue-select'

export default {
  components: {
    vSelect,
    Icon,
    Link,
  },
  props: {
    roles: Object,
  },
  data() {
    return {
      selectingRole: null,
    };
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (urls[0] === '') {
        return currentUrl === ''
      }
      return urls.filter((url) => currentUrl.startsWith(url)).length
    },
  },
}
</script>

<style lang="scss" scoped>
.add-role {

  span:hover {
    cursor: pointer;
  }
}
</style>
