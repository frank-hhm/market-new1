<template>
	<view class="order-item-market-price">
		{{ priceChange }}
	</view>
</template>

<script>
	import {
		mapGetters
	} from "vuex";

	export default {
		name: "OrderItemMarketPrice",
		props: {
			pid: {
				type: Number,
				required: true
			}
		},
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			...mapGetters("market", ["getMarketPrice"]),

			priceChange() {
				return this.getMarketPrice(this.pid);
			},
		}
	};
</script>

<style lang="scss" scoped>
	.order-item-market-price{
		font-size: $baseFontSizeSm;
		color: var(--base-red);
	}
</style>