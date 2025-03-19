<template>
  <section class="block-11" id="link4">
    <h1 class="block-11-title">Частые вопросы</h1>

    <div class="block-11-faq">
      <div
        class="block-11-faq-unit"
        v-for="(faq, index) in faqs"
        :key="index"
      >
        <div
          class="block-11-faq-question"
          :class="{ active: faq.active }"
          @click="toggleFaq(index)"
        >
          {{ faq.question }}
        </div>
        <div
          class="block-11-faq-answer"
          :ref="'faqAnswer' + index"
          style="height: 0; overflow: hidden;"
        >
          {{ faq.answer }}
        </div>
      </div>
    </div>

    <div class="block-11-connect-button">
      <a href="/connect">
        <button class="connect-button">подключить организацию</button>
      </a>
    </div>
  </section>
</template>

<script>
export default {
  name: "FaqComponent",
  data() {
    return {
      faqs: [
        {
          question: "Зачем это нужно моим сотрудникам?",
          answer:
            "Во многих сферах, где оказываются услуги, оклады подавляющего большинства не отличаются высокими размерами. Мы подумали, как увеличить доход и мотивировать персонал на 100% отдачу в вашем бизнесе. Как правило, любые услуги должны быть вознаграждены, и традиционно чаевые в сфере услуг являются одной из ключевых статей дохода. С сервисом Yagoda вы напрямую влияете на доходы ваших сотрудников!",
          active: false
        },
        {
          question: "В чем мой интерес как владельца бизнеса?",
          answer:
            "С 1 млн выручки сейчас вы платите 22 000р. (2,2%). С нами вы будете платить всего 6 900р. (0,69%). Экономия - более 180 000 рублей в год! Уникальная функция распределения чаевых позволяет замотивировать не только сотрудников, но и линейный персонал. Общие чаевые можно разделить на всех сотрудников.",
          active: false
        },
        {
          question: "Сколько стоит ваш сервис?",
          answer:
            "Подключение и дальнейшее сопровождение - бесплатно. Почему предлагаете пониженный процент, из чего он складывается? Все платежные системы и мы включаем в платеж надбавку в качестве компенсаций за транзакции, и этой надбавкой делимся мы с вами.",
          active: false
        },
        {
          question: "В чем моя выгода как администратора?",
          answer:
            "Рекомендуя оплачивать услуги через Yagoda, вы получаете дополнительно 10 000 - 50 000 руб. к основной заработной плате. В среднем дополнительный доход составляет от 1,5 до 2% от всей выручки в смену администратора. (Расчет-формула). При условии использования функционала распределения чаевых.",
          active: false
        },
        {
          question: "В чем моя выгода как мастера?",
          answer:
            "Более 80% клиентов оставляют чаевые при оплате через систему Yagoda. Ориентируйте клиента оплатить услугу через QR-код - это позволит увеличить объем ваших чаевых от 2 до 5 раз! Ведь каждый оплаченный счет принесет вам чаевые!",
          active: false
        },
        {
          question: "Как получить вознаграждение 3000 руб?",
          answer:
            "За каждое успешное подключение организации у Yagoda по вашей рекомендации – вы получите вознаграждение 3000 руб.! Сообщите руководителю о Yagoda, получите заслуженное вознаграждение! Выплата вознаграждения поступит на реквизиты, указанные в ЛК компании, на 7-й день с момента поступления первого платежа по QR-коду.",
          active: false
        },
        {
          question: "Если нет администратора, тогда как?",
          answer:
            "Заказ может оформить любой ваш сотрудник. Мы приняли решение с вами сотрудничать. Что дальше? Заполните заявку на сайте https://yagoda.team или позвоните по номеру +7 (499) 213-22-33. Договор подпишем удаленно.",
          active: false
        },
        {
          question: "Как быстро происходит подключение к сервису?",
          answer: "С момента получения заявки постараемся уложиться в 1 час.",
          active: false
        },
        {
          question: "Как получить от вас стойку с QR-кодом?",
          answer:
            "Доставим курьером или отправим на ближайший пункт выдачи WB или СДЭК.",
          active: false
        },
        {
          question: "Где будет размещаться отзыв?",
          answer:
            "На выбор клиента, отзыв можно оставить на Яндекс Картах или 2ГИС.",
          active: false
        },
        {
          question: "Как мне узнать, сколько чаевых мне отправил клиент?",
          answer:
            "Сразу после начисления чаевых вам придет сообщение в Telegram.",
          active: false
        },
        {
          question: "Когда будут зачислены чаевые на мою карту?",
          answer:
            "Чаевые будут зачислены или сразу, или на следующий рабочий день, в зависимости от способа оплаты услуг клиентом.",
          active: false
        },
        {
          question: "Если мы подключились, но через время решили, что услуга не подходит?",
          answer: "Просто перестаете пользоваться.",
          active: false
        }
      ]
    };
  },
  methods: {
    toggleFaq(index) {
      this.faqs[index].active = !this.faqs[index].active;
      this.$nextTick(() => {
        const answerEl = this.$refs['faqAnswer' + index];
        if (answerEl) {
          // Если ref возвращается как массив (в случае v-for)
          const el = Array.isArray(answerEl) ? answerEl[0] : answerEl;
          if (this.faqs[index].active) {
            el.style.height = el.scrollHeight + 16 + "px";
          } else {
            el.style.height = "0";
          }
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@scss/Landing/_mixins.scss";
@import "@scss/Landing/_variables.scss";
@import "@scss/Landing/buttons.scss";
// Блок 11

.block-11 {
  @include container;
  padding: 64px 20px 40px;

  @media (min-width: 768px) {
    padding-left: 40px;
    padding-right: 40px;
  }

  .block-11-title {
    @include container;
    font-size: 30px;
    margin-bottom: 32px;
    text-align: left;
    color: $greenLight;
    @media (min-width: 768px) {
      font-size: 40px;
    }
  }

  .block-11-faq {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .block-11-faq-question {font-weight: 600;
    font-size: 16px;
    color: $textPrimary;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 600;
  }

  .block-11-faq-answer {
    font-size: 16px;
    color: $totalBlack;
    overflow: hidden;
    height: 0;
    padding-top: 8px;
    transition: height 0.3s ease;

    &.open {
      max-height: 500px;
      padding: 4px 0 30px;
    }
  }

  .block-11-connect-button {
    width: 100%;
    margin-top: 40px;
    text-align: left;
    .connect-button {
      margin: 0;
      width: 100%;
    }
    @media (min-width: 768px) {
      .connect-button {
        width: unset;
      }
    }
  }

  .block-11-connect-button-link {
    display: inline-block;
    background-color: #1E2530;
    color: $textInverted;
    border-radius: 20px;
    padding: 16px 32px;
    text-decoration: none;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    width: 100%;

    @media (min-width: 600px) {
      width: auto;
    }
  }
}
</style>
