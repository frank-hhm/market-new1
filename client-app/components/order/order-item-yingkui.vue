<template>
  <view class="order-item">
    <text :style="{
		color:yingkuiClass
	}">
      {{ yingkuiSign }}{{ yingkuiAmount || 0.00}}
    </text>
  </view>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  name: 'OrderItem',
  props: {
    order: {
      type: Object,
      required: true
    }
  },
  computed: {
	...mapGetters("wallet", ["getYingKui"]),
    yingkuiData() {
      return this.getYingKui(this.order);
    },
    yingkuiAmount() {
      return ( this.yingkuiData.amount == NaN? 0.00 : this.yingkuiData.amount);
    },
    yingkuiSign() {
      return this.yingkuiData.sign || '';
    },
    yingkuiClass() {
      return this.yingkuiData.className;
    }
  }
};
</script>

<style scoped lang="scss">
	
	.order-item {
		font-size: $baseFontSizeLg;
		font-weight: bold;
		text-align: right;
	}
</style>