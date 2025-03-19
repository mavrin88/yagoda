<template>
  <Head title="Мастер" />
  <main class="page-content">
    <div class="ytips">
      <TipsHeader />
      <div class="ytips-top">
        <div class="ytips__container"><a class="ytips-top__back"></a>
          <div class="ytips-top__text">
            <p>{{data.role_name }}</p>
            <p>{{data.group_name }}</p>
          </div>
          <div class="ytips-top__burger" @click="menuOpen"><span></span><span></span><span></span></div>
        </div>
      </div>
      <div class="ytips-top-stat">
        <div class="ytips__container">
          <div class="ytips-top-stat__block">
            <p>СОТРУДНИКИ В СМЕНЕ</p>
            <div class="ytips-top-stat__people"><span>
              <b>{{ data.employees_on_shift_admin }}</b>М</span>
              <span>-</span><span><b>{{ data.employees_on_shift_master }}</b>А</span>
              <span>-</span><span><b>{{ data.employees_on_shift_staff }}</b>П</span>
            </div>
          </div>
          <div class="ytips-top-stat__text">
            <p>переводов <span>{{ data.translations }}</span></p>
            <p>₽ <b>{{ data.translations_summ }}</b></p>
          </div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-filter">
          <div class="ytips-filter__step ytips-filter__step_prev"></div>
          <div class="ytips-filter__date">10 июня 2025 - 10 июля 2025 г.</div>
          <div class="ytips-filter__step ytips-filter__step_next"></div>
        </div>
      </div>
      <div class="ytips__container">
        <div class="ytips-people">
          <!--это можно плодить под все должности-->

          <div class="ytips-people__group" v-for="employee in data.employees" :key="employee.id">
            <div class="ytips-people__list">
              <div class="ytips-people__item" v-for="employee in employee.employees" :key="employee.id">
                <div class="ytips-people__avatar"><img :src="employee.avatar"></div>
                <div class="ytips-people__name">{{ employee.name }}
                  <div class="ytips-people__crown"></div>
                </div>
                <div class="ytips-people__info">
                  <p><b>{{  employee.translations_summ }}</b> ₽</p>
                  <div><b>{{ employee.translations }} </b> переводов</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Показывать/скрывать по клику на ytips-top__burger-->
      <div class="ytips-menu" :class="{'js-active':isMenuOpen}">
        <div class="ytips__container">
          <div class="ytips-top__burger js-active" @click="menuClose"><span></span><span></span><span></span></div>
          <div class="ytips-menu__links">
            <a href="#">ЧАЕВЫЕ</a>
            <a href="#">СОТРУДНИКИ В СМЕНЕ</a>
            <a href="#">QR-КОДЫ МАСТЕРА</a>
            <a href="#">СТРАНИЦА ПЕРЕВОДА ЧАЕВЫХ</a>
            <Link href="/tips/master/stands">СТОЙКИ YAGODA-TIPS</Link>
            <a href="#">ВЫЙТИ ИЗ ГРУППЫ</a>
          </div>
        </div>
      </div>
    </div>
  </main>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import TipsHeader from '../Components/TipsHeader.vue'

export default {
  name: "Index",
  components: { TipsHeader, Head, Link },
  data() {
    return {
      isMenuOpen: false
    }
  },
  props: {
    data: Object
  },
  mounted() {

    },
  methods: {
    menuOpen() {
      this.isMenuOpen = true;
    },
    menuClose() {
      this.isMenuOpen = false;
    }
  }
}
</script>
