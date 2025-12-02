<template>
	<view class="holder">
		<view class="empty-wrapper flex flex-column align-center" v-if="!order_hold.length">
			<view class="empty-icon"></view>
			<text class="desc">当前无持仓订单</text>
			<router-link to="/pages/market/index" navType="pushTab" class="turn-other">去交易</router-link>
		</view>
		<view class="holder-list" v-else>
			<view class="holder-item" v-for="(item,index) in order_hold" :key="item.id"
				@click.stop="holdItemTap(index)">
				<view class="flex justify-between holder-item-content">
					<view class="left-wrapper flex flex-column justify-between">
						<view class="holder-item-top">
							<text class="name">{{item.ptitle}}</text>
							<!-- <view class="tag" v-if="item.pro_type_id == 1">期货</view>
							<view class="tag" v-if="item.pro_type_id == 2">股票</view> -->
							<text class="right-text">{{item.ostyle.name}}</text>
							<text class="right-text">/{{item.onumber}}手</text>
						</view>
						<view class="price flex align-center">
							<text class="order-price">{{item.buyprice}}</text>
							<text class="arrow-right" style="padding: 0 15rpx;"> → </text>
							<!-- <orderItemMarkePrice :pid="item.pid"></orderItemMarkePrice> -->
							<view class="order-item-market-price">
								{{ getMarketPrice(item.pid) }}
							</view>
						</view>
						<!-- <view class="flex">
							<text class="label mr-10">手续费 :</text>
							<text class="value">{{item.sx_fee}}</text>
						</view> -->
						<view class="flex">
							<view class="flex mr-10">
								<text class="label mr-10">止损 :</text>
								<text class="value">{{item.loss?item.loss:'--'}}</text>
							</view>
							<view class="flex">
								<text class="label mr-10">止赢 :</text>
								<text class="value">{{item.surplus?item.surplus:'--'}}</text>
							</view>
						</view>
						<view class="flex" v-if="item.mark_price && item.mark_price != '' && item.mark_price != 0">
							<view class="flex mr-10">
								<text class="label mr-10">触发：</text>
								<text
									class="value">{{item.mark_price > item.trigger_price ? '>=':'<='}}{{item.mark_price}}</text>
							</view>
						</view>
					</view>
					<view v-if="isMoni" class="items-moni">
						<view class="items-moni-text">模拟</view>
					</view>
					<view class="right-wrapper flex flex-column justify-between">
						<view class="flex justify-end">
							<text class="right-text mr-10">#{{item.orderno}}</text>
						</view>
						<!-- 	<view class="ying-kui" :style="{color:item.yingkui_t>=0?getUpColor:getDownColor}">
							{{item.yingkui_t > 0?'+':''}}{{item.yingkui_t}}
						</view> -->
						<orderItemYingKui :order="item"></orderItemYingKui>
						<view class="time">{{item.buytime}}</view>
					</view>
				</view>
				<view class="bottom-box" v-if="item.expend">
					<router-link class="bottom-btn common-btn"
						:to="{path:'/pages/market/detail',query:{name:item.ptitle,id:item.pid}}">图表</router-link>
					<router-link class="bottom-btn common-btn"
						:to="{path:'/otherpages/order/set-robot',query:{id:item.id,robot:1}}">止盈止损</router-link>
					<!-- <view class="bottom-btn" @tap="$emit('closeOrder',item)">减仓</view> -->
					<view class="common-btn bottom-btn" @click.stop="closeOrder(item)">平仓</view>
				</view>

			</view>
		</view>
	</view>
</template>

<script>
	import orderItemMarkePrice from '@/components/order/order-item-market-price.vue';
	import orderItemYingKui from '@/components/order/order-item-yingkui.vue';
	import {
		mapState,
		mapGetters
	} from "vuex"
	import {
		initDetail
	} from "@/utils/initData.js"
	export default {
		name: "OrderHold",
		props: {},
		components: {
			orderItemMarkePrice,
			orderItemYingKui
		},
		computed: {
			...mapState("member", ["isMoni"]),
			...mapState("charge", ["order_hold"]),
			...mapState("market", ["getMarketPrice"]),
			...mapGetters("member", ["getDownColor", "getUpColor"]),
			...mapGetters("market", ["getMarketPrice"])
		},
		methods: {
			holdItemTap(index) {
				this.$set(this.order_hold[index], "expend", !this.order_hold[index]
					.expend)
			},
			closeOrder(item) {
				let t = this
				try {
					uni.showLoading({
						mask: true,
						title: '正在平仓中...'
					})
					t.$u.api.sellApi({
						oid: item.id
					}).then((res) => {
						uni.showToast({
							icon: "none",
							title: "平仓成功"
						})
						// initDetail()
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: "平仓失败！" + (err.data.message || '')
						})
						if (err.data.code === 2) {
							// initDetail()
							// t.$u.api.getMemberDetailApi().then((res) => {
							// 	t.$store.commit('member/setIsMoni', res.data.member.moni == 0 ? false : true)
							// 	t.$store.commit('member/setMember', res.data.member)
							// 	t.$store.commit('member/setMemberCoin', res.data.member_coin)
							// 	t.$store.commit('charge/setOrderHold', res.data.order_hold)
							// })
						}
					})

				} catch (e) {
					//TODO handle the exception
				}
			},
		}
	}
</script>

<style lang="scss" scoped>
	.holder {
		.desc {
			margin-top: 10rpx;
			font-weight: 500;
			font-size: $baseFontSizeSm;
			color: #A8A8A8;
		}

		.turn-other {
			margin-top: 20rpx;
			width: 194rpx;
			height: 68rpx;
			background: $baseColor;
			border-radius: 68rpx;
			text-align: center;
			color: #000000;
			line-height: 68rpx;
			text-align: center;
		}

		.holder-list {
			.holder-item {
				padding: 20rpx;
				box-sizing: border-box;
				background: #fff;
				border-bottom: 1rpx solid #f3f3f3;

				.holder-item-top {
					display: flex;
					align-items: center;
					.tag {
						margin-left: 20rpx;
						font-size: $baseFontSizeSm;
						color: $baseColor;
					}
					.name{
						margin-right: 20rpx;
						font-weight: bold;
					}
					.right-text {
						font-size: $baseFontSizeSm;
					}

				}

				.order-item-market-price {
					font-size: $baseFontSizeSm;
					color: var(--base-red);
					
				}

				.holder-item-content {
					min-height: 120rpx;
				}

				.left-wrapper {
					.name {
						// font-size: 28rpx;
						// font-weight: 550;
					}

					.order-price {
						font-size: $baseFontSizeSm;
						// font-weight: bold;
					}

					.now-price {
						font-size: $baseFontSizeSm;
						// font-weight: bold;
					}

					.label {
						font-size: $baseFontSizeSmX;
						;
						color: #999;
					}

					.value {
						font-size: $baseFontSizeSmX;
						;
						color: #999;
					}

				}

				.right-wrapper {
					.ying-kui {
						font-size: $baseFontSize;
						text-align: right;
					}

					.time {
						font-size: $baseFontSizeSmX;
						color: var(--base-grey);
						text-align: right;
					}
					.right-text {
						font-size: $baseFontSizeSmX;
					}
				}

				.mr-10 {
					margin-right: 10rpx;
				}

				.bottom-box {
					margin-top: 40rpx;
					margin-bottom: 20rpx;
					display: flex;
					justify-content: space-between;

					.bottom-btn {
						padding: 0 30rpx;
						background: #f0f0f0;
						font-size: $baseFontSizeSm;
						text-align: center;
						color: #000;
						line-height: 48rpx;
						border-radius: $baseRadius;
					}
				}
			}
		}
		.items-moni{
			display: flex;
			align-items: center;
			color: var(--base-grey);
			.items-moni-text{
				line-height: 26rpx;
			}
		}
	}
</style>