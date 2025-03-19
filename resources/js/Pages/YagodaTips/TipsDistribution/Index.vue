<template>
  <div class="superadmin">
    <Head title="Распределение чаевых" />

    <TipsHeader />

    <div class="ytips-top">
      <div class="ytips__container">
        <Link class="ytips-top__back" href="/tips/coord"></Link>
        <div class="ytips-top__title">Чаевые распределение</div>
      </div>
    </div>

    <div class="superadmin__container">
      <div class="description">
        <p>Часть от переведенных гостем чаевых, могут получать и другие сотрудники. Установите доли от чаевых, справедливые для каждой подгруппы (Мастер, Администраторы, Персонал, Общие расходы).
          Чаевые будут разделены и разосланы всем получателям максимально оперативно. При оплате СБП - в течение 1-5 минут, при оплате Картой - на утро следующего рабочего дня.</p>
        <p class="danger">Рекомендуем предоставить часть от чаевых всем сотрудникам, от кого зависит впечатление Клиента.</p>
      </div>
      <div class="tips_block">
        <span>Мастерам</span>
        <div>
          <span>{{ totalMasterPercentage }} %</span>
          <img class="icon_block" src="/img/icon_block.svg">
        </div>
      </div>
      <span class="description_name">Эта сумма будет разделена между  Мастерами, добавленными в заказ.</span>
      <div class="tips_block">
        <span>Администратору</span>
        <div class="form">
          <div class="form__item">
            <input type="text" inputmode="numeric" v-model="adminPercentage">
            <span>%</span>
          </div>
        </div>
      </div>
      <span class="description_name">Получит тот, кто оформил этот заказ.</span>
      <div class="tips_block">
        <span>Персоналу</span>
        <div class="form">
          <div class="form__item__personal">
            <input type="text" inputmode="numeric" v-model="staffPercentage">
            <span>%</span>
          </div>
        </div>
      </div>
      <span class="description_name">Эта сумма будет разделена между всеми  сотрудниками в смене* с ролью “Персонал”.</span>
      <div class="tips_block">
        <span>Организация</span>
        <div class="form">
          <div class="form__item__organization">
            <input type="text" inputmode="numeric" v-model="organizationPercentage">
            <span>%</span>
          </div>
        </div>
      </div>
      <span class="description_name">Эта сумма поступит на привязанную к организации карту.</span>

      <div v-if="totalPercentage > 100" class="error-message">
        Сумма чаевых к распределению, не может быть больше 100%
      </div>

      <div class="description">
        <p>Например: Администратор с улыбкой встретил и проводил Клиента. Уборщик тщательнее убрала помещение и не мешался, что повысило комфорт нахождения клиента в заведении.</p>

        <p>Мастер качественно и с вниманием обслужил клиента.</p>

        <p>Все это повлияло на впечатление и удовлетворенность клиента. Что отразилось на факте и размере Чаевых.</p>

        <p>Каждый принял участие в результате, за что и получает часть от чаевых.</p>

        <b>Рекомендации к размерам долей:</b>

        <p>Администраторам: <b>10-20%</b>.</p>
        <p>Персоналу: <b>3-8%</b>.</p>
        <p>*Находящимся в смене считается сотрудник, отмеченный Руководителем группы, на странице “Сотрудники в смене”.</p>
      </div>

<!--      <button @click="saveDistribution" class="btn btn_w100 btn_primary" :disabled="totalPercentage > 100">Сохранить</button>-->
    </div>
  </div>

<!--  <Spiner :isLoading="isLoading"></Spiner>-->

  <ModalTipsDistribution
    :is-active="isModalActiveTipsDistribution"
    @close="closeModalTipsDistribution"
  />
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import axios from 'axios'
import Spiner from '../../../Shared/Spiner.vue'
import ModalTipsDistribution from '../../../Shared/Modals/ModalTipsDistribution.vue'
import TipsHeader from '@/Pages/YagodaTips/Components/TipsHeader.vue'

export default {
  components: {
    TipsHeader,
    Head,
    ModalTipsDistribution,
    Spiner,
    Link
  },
  props: {
    group: Object
  },
  data() {
    return {
      masterPercentage: this.group?.master_percentage || 0,
      adminPercentage: this.group?.admin_percentage || 0,
      staffPercentage: this.group?.staff_percentage || 0,
      organizationPercentage: this.group?.organization_percentage || 0,
      isModalActiveTipsDistribution: false,
      isLoading: false,
      error: null,
      debounceTimeout: null
    };
  },
  computed: {
    totalPercentage() {
      return (
        +this.adminPercentage +
        +this.staffPercentage +
        +this.organizationPercentage
      );
    },
    totalMasterPercentage() {
      if (this.totalPercentage > 100) {
        return 100;
      } else {
        return this.masterPercentage;
      }
    },
  },
  methods: {
    closeModalTipsDistribution() {
      this.isModalActiveTipsDistribution = false
    },
    startLoading() {
      this.isLoading = true;
    },
    stopLoading() {
      this.isLoading = false;
    },
    saveDistribution() {
      this.startLoading();

      setTimeout(() => {
        this.stopLoading();
        axios.post('/tips/tip_distribution', {
          masterPercentage: this.masterPercentage || 0,
          adminPercentage: this.adminPercentage || 0,
          staffPercentage: this.staffPercentage || 0,
          organizationPercentage: this.organizationPercentage || 0,
        })
          .then((response) => {

            this.$toast.open({
              message: 'Чаевые распределены',
              type: 'success',
              position: 'top-right',
              duration: 2000,
            });

          })
          .catch((error) => {

          });
      }, 500);
    },
    goBack() {
      if (!this.group.show_distributions_message && this.adminPercentage == 0 && this.staffPercentage == 0 && this.organizationPercentage == 0) {
        this.isModalActiveTipsDistribution = true

        axios.post('tip_distribution/show_distributions_message', {
          groupId: this.group.id
        })
          .then((response) => {
            this.group.show_distributions_message = true
          })
          .catch((error) => {

          });
      }else {
        this.$emit('goBack');
      }
    },
    debounceSave() {
      clearTimeout(this.debounceTimeout);
      this.debounceTimeout = setTimeout(() => {
        this.saveDistribution();
      }, 200);
    }
  },
  watch: {
    adminPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
      this.debounceSave()
    },
    staffPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
      this.debounceSave()
    },
    organizationPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
      this.debounceSave()
    },
  },
}
</script>

<style scoped>

.description_name {
  font-size: 13px;
  color: #9CA4B5;
  padding-left: 20px; /* Отступ слева */
  padding-right: 0; /* Можно убрать, если не нужен отступ справа */
  display: block; /* Становится блочным элементом */
}

.tips_block {
  margin-bottom: 1.875rem;
  align-items: center;
  display: flex;
  justify-content: space-between;
  margin: 14px 20px 4px 20px;
  font-weight: 500;
  font-size: 20px;
}

.description {
  margin: 20px 20px 0 20px;
  font-size: 14px;
}

.description p {
  margin-bottom: 10px;
  font-size: 12px;
}

.danger {
  color: red;
}

.form__item {
  margin-left: 64px;
  display: flex;
}

 .form__item__personal {
  margin-left: 140px;
   display: flex;
}

.form__item__organization {
  margin-left: 105px;
  display: flex;
}

.icon_block {
  margin-left: 10px;
}

.form input {
  background-color: #f6f6f6;
}

.btn_primary {
  margin-top: 30px;
}

.error-message {
  color: red;
  margin-bottom: 12px;
  margin: 0 20px 20px 20px;
}

.superadmin {
  padding-top: 0;
}
</style>
