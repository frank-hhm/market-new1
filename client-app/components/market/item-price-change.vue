<template>
	<view class="item-price-change" :class="[
		isBg?'is-bg':'',
		item.is_open ? '' : 'market-none-open'
	]" :style="{ color: isBg ?'#fff':color ,background:background }">
		<text v-if="item.is_open" :class="item.is_open ? '' : 'market-none-open'">{{ sign }}{{ changeValue }}%</text>
		<text v-else :class="'market-none-open'">休市</text>
	</view>
</template>

<script>
	import {
		mapGetters
	} from "vuex";

	export default {
		name: "ItemPriceChange",
		props: {
			item: {
				type: Object,
				required: true
			},
			isBg: {
				type: Boolean,
				required: false
			}
		},
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			...mapGetters("market", ["getPriceChange"]),

			priceChange() {
				return this.getPriceChange(this.item.id);
			},

			changeValue() {
				return this.priceChange.changeValue;
			},

			color() {
				const val = this.priceChange.changeValue;
				if (val === 0) return "#000";
				return val > 0 ? this.getUpColor : this.getDownColor;
			},
			background() {
				const val = this.priceChange.changeValue;
				if (val === 0) return "#A8A8A8";
				return val > 0 ? this.getUpColor : this.getDownColor;
			},

			sign() {
				return this.priceChange.changeValue > 0 ? "+" : "";
			}
		}
	};
</script>

<style lang="scss" scoped>
	.item-price-change {
		font-size: $baseFontSizeSm;
		width: 100%;
		&.is-bg{
			text-align: center;
			display: inline-block;
			padding: 18rpx 30rpx;
			min-width:60rpx;
			border-radius: 4rpx;
			border: 0;
		}
	}
</style>