<template>
	<view class="robot-wrapper">
		<view class="empty-wrapper flex flex-column align-center" v-if="!order_robot.length">
			<view class="empty-icon"></view>
			<text class="desc">暂无挂单</text>
			<!-- <router-link to="/pages/market/market" navType="pushTab" class="turn-other">去看看</router-link> -->
		</view>
		<view class="robot-list" v-else>
			<view class="robot-item" v-for="(item,index) in order_robot" :key="item.id" @tap="robotItemTap(index)">
				<view class="flex justify-between robot-item-content">
					<view class="left-wrapper flex flex-column justify-between">
						<view class="robot-item-top">
							<text class="name">{{item.ptitle}}</text>
							<text class="right-text">{{item.ostyle.name}}/{{item.onumber}}手</text>
						</view>
						<view class="price flex align-center">
							<text class="price-title">挂单价格:</text>
							<text class="price-text">{{item.buyprice}}</text>
						</view>
						<view class="flex">
							<view class="flex mr-10">
								<text class="label mr-10">止损 :</text>
								<text class="value">{{item.loss || '--'}}</text>
							</view>
							<view class="flex">
								<text class="label mr-10">止赢 :</text>
								<text class="value">{{item.surplus || '--'}}</text>
							</view>
						</view>
					</view>
					<view class="right-wrapper flex flex-column justify-between">
						<view class="flex justify-end">
							<text class="right-text mr-10">#{{item.id}}</text>
						</view>
						<view class="ying-kui">
							<orderItemMarkePrice :pid="item.pid"></orderItemMarkePrice>
						</view>
						<view class="time">{{item.create_time}}</view>
					</view>
				</view>

				<view class="bottom-box" v-if="item.expend">
					<router-link class="bottom-btn"
						:to="{path:'/pages/market/detail',query:{name:item.ptitle,id:item.pid}}">图表</router-link>
					<view class="bottom-btn" @tap="cancelOrder(item)">撤单</view>
					<router-link class="bottom-btn"
						:to="{path:'/otherpages/order/set-robot',query:{id:item.id,robot:2}}">改单</router-link>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import orderItemMarkePrice from '@/components/order/order-item-market-price.vue';
	import {
		mapGetters,
		mapState,
		mapMutations
	} from "vuex"
	export default {
		props: {},
		components: {
			orderItemMarkePrice,
		},
		computed: {
			...mapState("charge", ["order_robot"]),
			...mapGetters("member", ["getUpColor", "getDownColor"]),
		},
		methods: {
			cancelOrder(item) {
				let t = this;
				uni.showLoading({
					mask: true,
					title: '正在撤单中...'
				})
				try {
					t.$u.api.cancelOrderRobotApi({
						id: item.id
					}).then((res) => {
						uni.showToast({
							icon: "none",
							title: "撤单成功"
						})
						// initDetail()
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: "撤单失败！" + (err.data.message || '')
						})
						// initDetail()
					})
				} catch (e) {
					uni.showToast({
						icon: "none",
						title: "撤单失败！" + (e || '')
					})
					//TODO handle the exception
				}
			},
			robotItemTap(index) {
				this.$set(this.order_robot[index], "expend", !this.order_robot[index]
					.expend)
			},
		}
	}
</script>

<style lang="scss" scoped>
	.robot-wrapper {
		.desc {
			margin-top: 10rpx;
			font-weight: 500;
			font-size: 26rpx;
			color: #A8A8A8;
		}

		.turn-other {
			margin-top: 20rpx;
			width: 194rpx;
			height: 68rpx;
			background: linear-gradient(90deg, #4586FF 0%, #6EC0FF 100%);
			border-radius: 34rpx;
			font-size: 28rpx;
			text-align: center;
			color: #FFFFFF;
			line-height: 68rpx;
			text-align: center;
		}

		.robot-list {
			.robot-item {
				padding: 20rpx;
				box-sizing: border-box;
				background: #fff;
				border-bottom: 1rpx solid #f3f3f3;

				// border-radius: $baseRadius;
				.robot-item-top {
					display: flex;
					align-items: center;

					.name{
						font-weight: bold;
						margin-right: 20rpx;
					}
					.tag {
						margin-left: 20rpx;
						color: $baseColor;
					}
					.right-text{
						font-size: $baseFontSizeSm;
					}
				}

				.robot-item-content {
					min-height: 120rpx;
				}

				.left-wrapper {
					.name {}

					.price-title {
						margin-right: 10rpx;
						font-size: $baseFontSizeSm;
						color: var(--base-grey)
					}

					.price-text {
						color: var(--base-red);
						font-size: $baseFontSizeSm;
					}

					.now-price {
						font-size: $baseFontSizeSm;
						font-weight: bold;
					}

					.label {
						font-size: $baseFontSizeSmX;
						color: var(--base-grey)
					}

					.value {
						font-size: $baseFontSizeSmX;
						color: var(--base-grey)
					}

				}

				.right-wrapper {
					.right-text {
						font-size: $baseFontSizeSmX;
					}

					.ying-kui {
						font-size: $baseFontSize;
						text-align: right;
					}

					.time {
						font-size: $baseFontSizeSmX;
						color: #999;
						text-align: right;
					}
				}

				.mr-10 {
					margin-right: 10rpx;
				}

				.bottom-box {
					margin-top: 20rpx;
					display: flex;
					justify-content: space-between;

					.bottom-btn {
						width: 26%;
						background: #f0f0f0;
						font-size: $baseFontSizeSm;
						text-align: center;
						color: #000;
						line-height: 54rpx;
						border-radius: $baseRadius;
					}
				}
			}
		}
	}
</style>