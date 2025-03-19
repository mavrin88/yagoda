<template>
  <div class="client__list">
    <div
      v-for="(tip, index) in tips"
      :key="tip.percent"
      class="client__tip"
      :class="{ 'client__tip_active': tip.active }"
      @click="setActiveTip(index)"
    >
      <b>{{ tip.percent }}%</b>
      <span>{{ tip.amount }} ₽</span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      activeTipIndex: 1,
      tips: [
        { percent: this.organization.tips_1, amount: this.getTotalPercent(this.organization.tips_1), active: false },
        { percent: this.organization.tips_2, amount: this.getTotalPercent(this.organization.tips_2), active: false },
        { percent: this.organization.tips_3, amount: this.getTotalPercent(this.organization.tips_3), active: false },
        { percent: this.organization.tips_4, amount: this.getTotalPercent(this.organization.tips_4), active: false }
      ]
    }
  },
  props: {
    organization: Object,
    // todo: проверить, по факту приходит String
    totalPrice: [Number, String],
    discount: {
      // Проверить, в одном из сценариев пришло Number
      type: [Number, String],
      default: '0'
    }
  },
  created() {
    this.$nextTick(() => {
      this.setActiveTip(2, true);
    });
  },
  methods: {
    uncheckedAll() {
      this.activeTipIndex = 99;
      this.tips.forEach((tip) => {
        tip.active = false;
      });
      this.$emit('tipSelected', -1);
    },
    setActiveTip(index, isOnLoad = false) {
      if (!isOnLoad && (this.activeTipIndex == index)) {
        this.uncheckedAll();
      } else {
        this.activeTipIndex = index;
        this.$emit('tipSelected', this.tips[index]);
      }

    },
    getTotalPercent(tipPercent) {
      // const discountValue = parseFloat(this.discount) || 0;
      // const adjustedPrice = this.totalPrice * ((100 - discountValue) / 100);
      return Math.ceil(this.totalPrice * (tipPercent / 100));
    }
  },
  watch: {
    activeTipIndex(newIndex) {
      this.tips.forEach((tip, index) => {
        tip.active = index === newIndex;
      });
      this.$emit('tipSelected', this.tips[newIndex]);
    }
  },
}
</script>

<style scoped>
.client__tip {
  width: 5rem;
  height: 4rem;
  padding: .8125rem .5rem;
  cursor: pointer;
  -webkit-transition: all 400ms cubic-bezier(.5,0,.3,1);
  -moz-transition: all 400ms cubic-bezier(.5,0,.3,1);
  -o-transition: all 400ms cubic-bezier(.5,0,.3,1);
  border: 1px solid #e5e5e5;
  border-radius: .5rem;
  align-items: center;
  flex-wrap: wrap;
}

.client__tip:hover {
  border-color: #38b0b0;
}

.client__tip_active {
  border-color: #38b0b0;
}

</style>
