<template>
  <div class="auth">
    <Head :title="meta?.title ? `${meta.title}` : 'Yagoda.team'" />
    <div class="auth__wrapper auth__wrapper_grey">
      <div class="auth__step">
        <!--      <div class="auth__back">назад</div>-->
        <!--      <Link style="color: #000" href="/logout" method="delete">Выход</Link>-->
        <div class="user-top">
          <div class="user-top__image">
            <img v-if="auth.user.photo_path" :src="auth.user.photo_path">
            <img v-else src="/img/content/icon_avatar.svg">
          </div>
          <div class="user-top__name">{{ auth.user.first_name }} {{ auth.user.last_name }}</div>
        </div>
        <div @click="menu" class="auth__burger">
          <span></span>
          <span></span>
          <span></span>
        </div>
        <div class="auth-roles">
          <div v-if="organizations.length > 0">
            <div class="auth-roles__title">Продолжить в роли</div>
            <div v-for="organization in visibleOrganizations" @click="selectOrganization(organization)" class="auth-roles__item">
              <div class="auth-roles__type">
                <span v-if="organization.tips">Чаевые / </span>{{ organization.role_name }}</div>
              <div class="auth-roles__name">
                <span v-if="organization.team">Команда / </span>{{ organization.organization_name }}</div>
            </div>
          </div>
          <div v-else>
            <p class="no__roles__title">Пока Вам не присвоено ни одной роли</p>
            <p class="no__roles__description">Обратитесь к руководителю чтоб Вас добавили в роль</p>
          </div>
        </div>

        <div class="auth-archive" v-if="archiveOrganizations.length > 0">
          <div class="auth-archive__button cursor-pointer flex justify-end items-center text-gray-500" @click="toggleArchive">
            <span class="text-lg">Архив</span>
            <svg
              class="ml-2 w-4 h-4 transform transition-transform"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
              :class="{ 'rotate-0': !showArchive, 'rotate-180': showArchive }">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 10l6 6 6-6"></path>
            </svg>
          </div>
          <transition name="slide-fade">
            <div v-if="showArchive" class="auth-archive__list">
              <div v-for="organization in archiveOrganizations" @click="selectOrganization(organization)" class="auth-roles__item">
                <div class="auth-roles__type">{{ organization.role_name }}</div>
                <div class="auth-roles__name">{{ organization.organization_name }}</div>
              </div>
            </div>
          </transition>
        </div>
      </div>
    </div>

   <div class="auth-burger-block" v-if="isShowMenu">
     <div class="auth-burger-block__overlay" @click="closeBurger()"></div>
     <div class="auth-burger-block__content">
       <div class="auth-burger-block__close" @click="closeBurger()"></div>
       <div class="auth-burger-block__menu">
         <ul>
           <li><Link href="/user_account">Аккаунт</Link></li>
           <li><Link href="/user_settings">Настройки</Link></li>
           <li><Link href="/logout">Выход</Link></li>

           <li class="cursor-pointer front-mt60" @click="createNewOrganization()">Зарегистрировать новую организацию для проведения платежей через Yagoda</li>

         </ul>
       </div>

       <div class="auth-burger-block__contacts">
         <p><a href="mailto:welcome@yagoda.team">welcome@yagoda.team</a></p>
         <div class="auth-burger-block__socials">
           <a href="https://t.me/+79153690251 ">
             <img src="/img/content/i_tg.svg" alt="">
           </a>
           <a href="https://wa.me/+79153690251 ">
             <img src="/img/content/i_wa.svg" alt="">
           </a>
         </div>
         <div class="politic_block">
         <label><a href="https://yagoda.team/pages/user_agreement">пользовательское соглашение</a></label>
         <label><a href="https://yagoda.team/pages/privacy">политика конфиденциальности</a></label>
         </div>
       </div>


     </div>
   </div>



  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import Logo from '@/Shared/Logo.vue'
import TextInput from '@/Shared/TextInput.vue'
import LoadingButton from '@/Shared/LoadingButton.vue'
import axios from 'axios';

export default {
  components: {
    Link,
    Head,
    LoadingButton,
    Logo,
    TextInput
  },
  props: {
    organizations: {
      type: Array,
      required: true
    },
    selected_organization_id: {

    },
    auth: Object,
  },
  data() {
    return {
      showArchive: false,
      meta: {
        title: 'Выбор роли'
      },
      isShowMenu: false
    }
  },
  computed: {
    visibleOrganizations() {
      return this.organizations.filter(organization => organization.deleted_at == null && organization.status !== 'new_without_save' && organization.role_status !== 'archived' && organization.role_status !== 'deleted');
    },
    archiveOrganizations() {
      return this.organizations.filter(organization => organization.role_status == 'archived');
    }
  },
  methods: {
    toggleArchive() {
      this.showArchive = !this.showArchive;
    },
    selectArchiveItem(item) {
      console.log('Выбран архивный элемент:', item);
      // Дополнительные действия при выборе элемента
    },
    createNewOrganization() {
      axios.get('/organizations/createNewOrganization')
        .then(response => {
          let organization = {
            organization_id: response.data.organizationId,
            role_id: response.data.roleId,
          }
          let from_menu = true
          this.selectOrganization(organization, from_menu)
        })
        .catch(error => {
          // Обработка ошибки
          console.error(error);
        });

    },
    selectOrganization(organization, from_menu) {
      axios.post('/organizations/selectOrganization', {
        organization_id: organization.organization_id,
        role_id: organization.role_id,
        entity_type: organization.tips ? 'group' : 'organization',
      })
        .then(response => {
          if (from_menu){
            this.$inertia.visit('/super_admin/organization');
          }else {
         this.$inertia.visit('/');
          }
          // console.log(response.data);
        })
        .catch(error => {
          // Обработка ошибки
          console.error(error);
        });
    },

    settings() {
      this.$inertia.visit('/user_settings');
    },
    menu() {
      this.isShowMenu = true
    },
    closeBurger() {
      this.isShowMenu = false
    }
  },
}
</script>

<style>
.user-top {
  text-align: center;
}

.auth .user-top__image img {
  border-radius: 30%;
}

.no__roles__title {
    font-size: 15px;
    font-weight: 500;
    margin-bottom: 0.125rem;
    text-align: center;
    color: #000;
}

.no__roles__description {
    font-size: 15px;
    margin-bottom: 1.125rem;
    text-align: center;
    color: #000;
}
.politic_block a {
  font-size: 11px !important;
  display: block;
  margin-bottom: 10px;
}
.user-top__image img{
display: inline-block;
}
.front-mt60 {
  margin-top: 3.75rem;
}

.rotate-0 {
  transition: transform 0.3s;
}

.rotate-180 {
  transition: transform 0.3s;
  transform: rotate(180deg);
}

</style>
