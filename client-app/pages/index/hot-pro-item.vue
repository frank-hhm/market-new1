<template>
	<view class="pro-list ">
		<view class="pro-item" v-for="(item,index) in markets" :key="index" @tap.shop="toStockDetail(item)" v-if="item.is_hot == 1">
			<view class="pro-item-name">{{item.name}}</view>
			<view class="pro-item-price" :style="{color:item.up_status?getUpColor:getDownColor}">
				<text :class="item.is_open?'':'market-none-open-color'">{{getMarketPrice(item.id)}}</text>
			</view>
			<view class="pro-item-change">
				<itemPriceChange class="pro-item-change-tag" :item="item" :isBg="true"></itemPriceChange>
			</view>
		</view>
	</view>
</template>
<script>
	import {
		mapGetters,
		mapState
	} from "vuex"
	import detailPriceChange from '@/components/market/detail-price-change.vue';
	import itemPriceChange from '@/components/market/item-price-change.vue';
	export default {
		components: {
			detailPriceChange,
			itemPriceChange
		},
		props: {
		},
		data() {
			return {
				mainData: [{
					k: 0,
				}, {
					k: 1,
				}],
			}
		},
		watch: {
			marketList(newValue, oldValue) {
				this.$nextTick(() => {
					newValue.forEach((item, index) => {
						if (this.mainData[index]) {
							this.mainData[index] = JSON.parse(JSON.stringify(Object.assign(item, this
								.mainData[index])))
						}
					})
				})
			},
		},
		computed: {
			...mapGetters("member", ["getDownColor", "getUpColor"]),
			...mapGetters("market", ["getMarketById", "getMarketPrice"]),
			...mapState("market", ["markets", "sector", "marketStatus"]),
		},
		methods: {
			toStockDetail(item) {
				this.$Router.push({
					path: '/pages/market/detail',
					query: {
						id: item.id
					}
				})
			},
		}
	}
</script>

<style lang="scss">
	.pro-list {
		width: 100%;
		.pro-item {
			width:100%;
			display: flex;
			padding:24rpx 20rpx;
			justify-content: space-between;
			min-height:40rpx;
			align-items: center;
			.pro-item-name{
				width: 200rpx;
				text-align: left;
			}
			.pro-item-price{
				width: calc(100% - 340rpx);
				text-align: left;
				
			}
			.pro-item-change{
				width: 140rpx;
				text-align: right;
			}
		}
	}
</style>