<template>
  <div class="ytips-pay__prices">
    <div v-for="(tip, index) in tips"
         @click="setActiveTip(index)"
         :key="index"
         :class="{ 'js-active': tip.active }"
         class="ytips-pay__price">{{ tip.amount }} <span>₽</span>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      activeTipIndex: 2,
      tips: [
        { amount: this.getTotalPercent(this.group.tips_1), active: false },
        { amount: this.getTotalPercent(this.group.tips_2), active: false },
        { amount: this.getTotalPercent(this.group.tips_3), active: true },
        { amount: this.getTotalPercent(this.group.tips_4), active: false }
      ]
    }
  },
  props: {
    group: Object,
  },
  created() {
    this.setActiveTip(2, true);
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
      return tipPercent;
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
