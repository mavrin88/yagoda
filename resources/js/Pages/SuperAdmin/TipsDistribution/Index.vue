<template>
  <div class="superadmin">
    <Head title="Распределение чаевых" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <div @click="goBack" class="back">назад</div>
        <h2>Чаевые распределение</h2>
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

        <p class="danger">Рекомендуем предоставить часть от чаевых всем сотрудникам, от кого зависит мнение Клиента о вашей организации.</p>

        <p>Например: Администратор с улыбкой встретил и проводил Клиента. Клинер тщательнее убрала помещение и не мешался, что повысило комфорт нахождения клиента в заведении.</p>

        <p>Мастер качественно и с вниманием обслужил клинета.</p>

        <p>Все это повлияло на впечатление и удовлетворенность клиента. Что отразилось на факте и размере Чаевых.</p>

        <p>Все это повлияло на впечатление и удовлетворенность клиента. Что отразилось на факте и размере Чаевых.</p>

        <b>Рекомендации к размерам долей:</b>

        <p>Администратору: <b>12-17%</b>. Или используя расчет: если в среднем мастеров в смене 6 и администартор седьмой - то 1/7 (14%).</p>
        <p>Цель: что бы примерный доход администратора от чаевых, от всех мастеров, был схож с чаевыми каждого мастера.</p>
        <p>Персоналу: обычно <b>2-5%</b>.</p>
        <p>*Находящимся в смене считается сотрудник, выделенный Администартором, на странице “Сотрудники в смене”.</p>
      </div>

      <button @click="saveDistribution" class="btn btn_w100 btn_primary" :disabled="totalPercentage > 100">Сохранить</button>
    </div>
  </div>

  <Spiner :isLoading="isLoading"></Spiner>

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

export default {
  components: {
    Head,
    ModalTipsDistribution,
    Spiner,
    Link
  },
  props: {
    organization: Object
  },
  data() {
    return {
      masterPercentage: this.organization.master_percentage,
      adminPercentage: this.organization.admin_percentage,
      staffPercentage: this.organization.staff_percentage,
      organizationPercentage: this.organization.organization_percentage,
      isLoading: false,
      isModalActiveTipsDistribution: false,
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
    handleDelete() {
      axios.post('/organizations/delete', {
        organizationId: this.organization.id
      })
        .then((response) => {
          this.$inertia.visit("/organizations/choose");
        })
        .catch((error) => {

        });
    },
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
        axios.post('tip_distribution', {
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
      if (!this.organization.show_message && this.adminPercentage == 0 && this.staffPercentage == 0 && this.organizationPercentage == 0) {
        this.isModalActiveTipsDistribution = true

        axios.post('tip_distribution/show_message', {
          organizationId: this.organization.id
        })
          .then((response) => {
            this.organization.show_message = true
          })
          .catch((error) => {

          });
      }else {
        this.$inertia.get(`/`);
      }
    }
  },
  watch: {
    adminPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
    },
    staffPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
    },
    organizationPercentage() {
      this.masterPercentage = 100 - this.totalPercentage;
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

.description__text {
  font-weight: 600;
  margin-bottom: 10px;
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
</style>
