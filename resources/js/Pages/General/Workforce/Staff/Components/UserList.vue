<template>
  <div class="superadmin__list list-standard">
    <div v-for="(user, index) in users"
         :key="user.id"
         class="list-standard__item flex flex-col p-3 border-b border-gray-200"
         @click="setActiveIndex(index)"
         :class="{ 'list-standard__item_active': activeIndex === index }"
    >

      <div class="flex items-center w-full">
        <div class="list-standard__image mr-3">
          <img :src="user.photo_path || '/img/content/icon_avatar.svg'" class="w-12 h-12 rounded-full">
        </div>
        <div class="list-standard__title flex-grow" v-if="user.first_name">
          {{ user.first_name }} {{ user.last_name }}
        </div>
        <div class="list-standard__right ml-auto">
          <div class="list-standard__phone">{{ user.phone }}</div>
        </div>
      </div>

      <div v-if="checkMessageSms(user)" class="message text-red-500 text-[10px] mt-2">
        <p>Пользователю отправлена СМС со ссылкой на регистрацию</p>
      </div>

      <div class="list-standard__action flex items-center justify-end mt-2"
           v-if="activeIndex === index"
           :class="{ 'list-standard__action_active': activeIndex === index }"
      >
        <div class="list-standard__action-icon list-standard__stats mr-3" @click="statsUser(user.id)"></div>
        <div class="list-standard__action-icon list-standard__delete" @click="deleteUser(user.id)"></div>
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios'

export default {
  props: {
    users: {
      type: Array,
      required: true,
    },
  },
  data() {
    return {
      activeIndex: null
    };
  },
  methods: {
    checkMessageSms(user) {
      return user.is_registered === 0 || (user.is_registered == null);
    },
    setActiveIndex(index) {
      this.activeIndex = index;
    },
    async deleteUser(userId) {
      try {
        await axios.delete(`/staff/${userId}`);
        this.users.splice(this.activeIndex, 1);
        this.activeIndex = null;
        this.$toast.open({
          message: 'Сотрудник удален',
          type: 'success',
          position: 'top-right',
          duration: 2000,
        });
      } catch (error) {
        console.error('Ошибка при удалении пользователя:', error);
      }
    },
    async statsUser(userId) {
      this.$inertia.post('/master/statistics', {
        'master_id': userId
      })
    },
  },
};
</script>
