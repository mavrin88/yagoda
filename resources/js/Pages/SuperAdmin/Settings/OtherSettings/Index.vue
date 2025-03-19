<template>
  <div class="superadmin">
    <Head title="Другие настройки" />
    <div class="superadmin__container">
      <div class="superadmin__top">
        <Link class="back" href="/super_admin/settings">назад</Link>
        <h2>ПРОЧИЕ НАСТРОЙКИ</h2>
      </div>
      <div class="mt-5 text-sm font-bold">
        Источник компенсации транзакционных издержек:
      </div>
        <div class="mt-5">
          <div class="auth__rules">
            <input type="radio" id="rules" required v-model="clientOrTips" value="client">
            <label class="text-sm font-bold text-black-900" for="rules">Клиент</label>
          </div>

          <div class="mt-5 text-sm">
            В этом случае, на странице Оплаты, будет присутствовать предложение Плательщику компенсировать транзакционные издержки.
          </div>

          <div class="auth__rules">
            <input type="radio" id="tips" required v-model="clientOrTips" value="tips">
            <label class="text-sm font-bold text-black-900" for="tips">Чаевые</label>
          </div>

          <div class="mt-5 text-sm">
            В случае, если Плательщик решит оставить чаевые, компенсация транзакционных издержек будет удержана из суммы чаевых. Для распределения чаевых между сотрудниками будет принята сумма чаевых за минусом величины транзакционных издержек.
          </div>
        </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import 'vue-select/dist/vue-select.css'

export default {
  components: {
    Head,
    Link
  },
  props: {
    initialValue: {
      type: String,
      default: 'tips',
    },
    organization: {
      type: Object
    },
  },
  computed: {},
  data() {
    return {
      clientOrTips: this.organization.comp_source || 'client',
    }
  },
  methods: {
    async saveToServer(value) {
      try {
        this.$inertia.post('/super_admin/other_settings', {
          selectedOption: value
        }, {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            console.log('Данные успешно сохранены:', value);
          },
          onError: (errors) => {
            console.error('Ошибка при сохранении данных:', errors);
          }
        });
      } catch (error) {
        console.error('Ошибка:', error);
      }
    }

  },
  watch: {
    clientOrTips(newValue) {
      this.saveToServer(newValue);
    },
  },
}
</script>

<style scoped lang="scss">
.auth__rules {
  label {
    font-size: 14px;
    font-weight: 400;
    color: #000000;
    &::after {
      margin: 5px 0 0 4px;
    }
  }
}
</style>
