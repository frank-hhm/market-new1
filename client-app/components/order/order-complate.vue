<template>
	<view class="complate-list">
		<view class="empty-wrapper flex flex-column align-center" v-if="!list.length">
			<view class="empty-icon"></view>
			<text class="desc">暂无数据</text>
			<!-- <router-link to="/pages/market/market" navType="pushTab" class="turn-other">去看看</router-link> -->
		</view>
		<view class="complate-list" v-else>
			<view class="complate-item" v-for="(item,index) in list" :key="item.id" @tap="toComplateDetail(item)">
				<view class="">
					<!-- <text class="name mr-40">{{item.ptitle}}</text> -->
					<view class="complate-item-top">
						<view class="name">{{item.ptitle}}</view>
					</view>
					<view class="right-text ">
						<view class="">
							{{item.ostyle.name}}
						</view>
						<view>
							{{item.onumber}}手
						</view>
					</view>
					<view class="time">{{item.selltime}}</view>
				</view>

				<view class="right-wrapper">
					<view class="ying-kui" :style="{
						color:parseFloat(item.ploss)>0?getUpColor:getDownColor
					}">
						{{item.ploss > 0 ?"+":''}}{{item.ploss}}
					</view>
					<view class="order-price">{{item.buyprice}} → {{item.sellprice}}</view>
				</view>

			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapGetters
	} from "vuex"
	export default {
		props: {
			list: {
				type: Array,
				required: true
			}
		},
		computed: {
			...mapGetters("member", ["getToken", "getDownColor", "getUpColor"]),
		},
		methods: {
			toComplateDetail(item) {
				this.$Router.push({
					path: "/otherpages/order/complateDetail",
					query: {
						id: item.id
					}
				})
			},
		},
	}
</script>

<style lang="scss" scoped>
	.complate-list {
		.desc {
			margin-top: 10rpx;
			font-size: $baseFontSizeSm;
			color: var(--base-grey);
		}

		.turn-other {
			margin-top: 20rpx;
			width: 194rpx;
			height: 68rpx;
			background: linear-gradient(90deg, #4586FF 0%, #6EC0FF 100%);
			border-radius: 34rpx;
			font-size: $baseFontSize;
			text-align: center;
			color: #FFFFFF;
			line-height: 68rpx;
			text-align: center;
		}

		.complate-list {
			// margin: 24rpx;

			.complate-item {
				padding: 20rpx;
				background: #fff;
				// margin-bottom: 20rpx;
				// border-radius: 24rpx;
				padding: 20rpx 20rpx;
				border-bottom: 1rpx solid #f3f3f3;
				display: flex;
				justify-content: space-between;
				align-items: center;
				.complate-item-top{
					.name{
						font-weight: bold;
					}
				}
				.right-text {
					margin-top: 10rpx;
					display: flex;
					font-size: $baseFontSizeSm;
				}

				.order-price {
					font-size: $baseFontSizeSm;
					margin-top: 10rpx;
					color: var(--base-grey);
				}

				.close-price {
					margin-top: 10rpx;
				}

				.ying-kui {
					text-align: right;
					font-weight: bold;
				}

				.time {
					margin-top: 10rpx;
					font-size: $baseFontSizeSmX;
					color: var(--base-grey);
					text-align: right;
				}

				.bottom-box {
					margin-top: 20rpx;
					display: flex;
					justify-content: space-between;

					.bottom-btn {
						width: 26%;
						background: var(--base-default);
						font-size: $baseFontSizeSm;
						text-align: center;
						color: #fff;
						line-height: 54rpx;
						border-radius: 10rpx;
					}
				}
			}
		}
	}
</style>