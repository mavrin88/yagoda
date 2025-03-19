<template>
  <Head title="Информация" />
  <div class="superadmin__container">
    <div class="superadmin__top">
      <Link></Link>
      <Icon @click="close" icon="system-uicons:close" width="41" height="41" class="close_icon" />
    </div>
    <div class="info_block">
      <div class="organization_full_name">
        {{ selectedOrganization.full_name }}
      </div>
      <div class="organization_f">
        {{selectedOrganization.activity_type.name}}
      </div>

      <div class="organization_name">
        {{ selectedOrganization.name }}
      </div>
      <div class="organization_phone">
        {{ selectedOrganization.phone }}
      </div>

      <div class="organization_contact_name">
        <span>Контактное лицо:</span>
        {{ selectedOrganization.contact_name }}
      </div>

      <div class="organization_contact_phone">
        {{ selectedOrganization.contact_phone }}
      </div>
    </div>

    <div class="organization_contract">
      <label>Договор N:</label>
      <input
        v-model="selectedOrganization.agency_agreement_number"
        id="agreementNumber"
        type="text"
        inputmode="numeric"
        class="selectedOrganization_agency_agreement_number"
        maxlength="6"
        :class="{'error-input': agencyAgreementError}"
      />
    <span class="mr-3">от </span> <Datepicker v-model="selectedOrganization.agency_agreement_date" locale="ru" :format="format" auto-apply :clearable="false" position="left" :enable-time-picker="false" @update:model-value="filterOrders"/>
    </div>

    <div class="organization_status">
      <label>Статус:</label>
      <custom-select
        :options="options"
        v-model="selectedOption"
        :canChange="statusCanChange"
      />
    </div>

    <div class="navigate-to-organization" @click="navigateToOrganization">
      <span class="navigate-text">Перейти к организации  <Icon icon="fluent:chevron-right-12-filled" width="12"
                                                               height="12" /></span>
    </div>

    <div class="comment_block">
      <label for="internal-comment">Внутренний комментарий:</label>
      <textarea
        id="internal-comment"
        v-model="selectedOrganization.comment"
        class="comment-input"
      ></textarea>
    </div>

    <div class="button-wrapper">
      <button @click="saveOrganization" class="save-button">Сохранить</button>
    </div>

    <iframe id="iframe" class="organization__iframe" v-if="iframeSrc" :src="iframeSrc" width="100%" height="1000"></iframe>


  </div>

</template>
<script>
import { Head, Link } from '@inertiajs/vue3'
import { Icon } from '@iconify/vue'
import CustomSelect from '../Components/CustomSelectStatus.vue'
import axios from 'axios'
import Datepicker from '@vuepic/vue-datepicker'

export default {
  components: { Datepicker, CustomSelect, Head, Link, Icon },
  props: {
    selectedOrganization: Object,
  },
  data() {
    return {
      selectedOption: { title: 'Выберите статус' },
      options: [
        // { value: 'new', title: 'Активная', color: '#38b0b0' },
        { value: 'active', title: 'Активная', color: '#38b0b0' },
        { value: 'stoped', title: 'Приостановлена', color: '#FFA500' },
        // { value: 'deleted', title: 'Удалена', color: '#FF0000' },
      ],
      isLoading : false,
      agencyAgreementError: false,
      statusCanChange: true,
      iframeSrc: null,
    }
  },
  mounted() {
    this.updateStatus();
  },
  methods: {
    close() {
      this.iframeSrc = null;
      this.$emit('showInfoBlock', false)
    },
    startLoading() {
      this.iframeSrc = null;
      this.isLoading = true;
    },

    stopLoading() {
      this.iframeSrc = null;
      this.isLoading = false;
    },
    updateStatus() {
      this.iframeSrc = null;
      if (this.selectedOrganization && this.selectedOrganization.status) {
        if (this.selectedOrganization.status === 'deleted') {
          this.selectedOption = { title: 'Удалена', value: null, color: '#FF0000' };
          this.statusCanChange = false;
        } else {
          const matchingOption = this.options.find(option => option.value === this.selectedOrganization.status);
          if (matchingOption) {
            this.selectedOption = matchingOption;
          } else {
            this.selectedOption = { title: 'Выберите статус', value: null};
          }
          this.statusCanChange = true;
        }
      } else {
        this.selectedOption = { title: 'Выберите статус', value: null };
        this.statusCanChange = true;
      }
    },

    navigateToOrganization() {
      const organizationId = this.selectedOrganization.id;
      this.iframeSrc = `/organizations/switch-organization/${organizationId}`;
    },
    saveOrganization() {
      axios.post('/dashboard/organization/save', {
        organizationId: this.selectedOrganization.id,
        status: this.selectedOption.value,
        comment: this.selectedOrganization.comment,
        agency_agreement_number: this.selectedOrganization.agency_agreement_number,
        agency_agreement_date: this.selectedOrganization.agency_agreement_date,
      })
        .then((response) => {
          this.$emit('saveOrganization', true)
          if (response.data.exists) {
            this.agencyAgreementError = true;
          } else {
            this.agencyAgreementError = false;
          }
        })
        .catch((error) => {

        })
    },
  },
  watch: {
    selectedOrganization: 'updateStatus',
    'selectedOrganization.agency_agreement_number': function(newValue) {
      this.agencyAgreementError = false;
    }
  },
}
</script>


<style lang="scss" scoped>
.close_icon {
  cursor: pointer;
}
.superadmin__container {
  display: flex;
  flex-direction: column;
  max-width: 380px;
  padding-bottom: 50px;
}
.organization_full_name {
  font-size: 25px;
  font-weight: bold;
}
.organization_name {
  padding-top: 20px;
}
.organization_phone {
  padding-top: 2px;
}
.organization_contact_name {
  padding-top: 20px;
}
.organization_contract {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding-top: 20px;
}
.selectedOrganization_agency_agreement_number {
  width: 90px;
 margin-right: 5px;
  margin-left: 5px;
  border-bottom: 1px solid #828282 !important;
  text-align: right;
  outline: none;
}
.selectedOrganization_agency_agreement_number.error-input {
color: red;
}
.dp__main {
  width: 150px;
}
.calendar_icon{
  padding-left: 10px;
}
.organization_status {
  display: flex;
  align-items: center;
  margin-top: 20px;
}
.organization_status label {
  font-size: 14px;
}
.navigate-to-organization {
  display: flex;
  justify-content: flex-end;
  font-size: 14px;
  font-weight: 400;
  color: #000000;
  cursor: pointer;
  text-decoration: underline;
  padding-top: 30px;

  svg {
    display: inline-block;
  }
}
.navigate-text {
  margin-right: 5px;
}
.arrow-right span {
  text-decoration: none;
}
.comment_block {
  margin-top: 40px;

  label {
    font-size: 14px;
    font-weight: 400;
    color: #828282;
  }
}
.comment-input {
  margin-top: 5px;
  width: 100%;
  height: 150px !important;
  padding: 10px;
  border-radius: 0px;
  border: 1px solid #ccc;
  font-size: 14px;
  resize: none;
  outline: none;
}
.button-wrapper {
  margin-top: 40px;
  margin-left: 25px;
}
.save-button {
  width: 95%;
  height: 36px;
  font-size: 16px;
  color: white;
  background-color: #404854;
  border: none;
  border-radius: 16px;
  cursor: pointer;
}
.organization {
  &__iframe {
    margin-top: 20px;
  }
}
</style>
