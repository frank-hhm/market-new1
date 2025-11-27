<template>
	<view class="water-histroy">
		<view class="water-type">
			<view class="water-type-item">
				<view class="title">跟单收益累计</view>
				<view class="count">{{totalPrice || 0.00}}</view>
			</view>
		</view>
		<view class="water-list">
			<view v-if="!isLoading && list.length == 0" class="empty-list">暂无</view>
			<view class="list-item" v-for="item in list" :key="item.id">
				<view class="list-item-left">
					<view class="list-item-left-head">
						<view class="name">{{item.source.name2}}</view>
						<view class="label" :class="item.type.color || ''" v-if="item.source.value == 'follow_revenue'">
							{{item.type.name2 || item.type.name}}
						</view>
					</view>
					<view class="time">{{item.create_time}}</view>
				</view>
				<view class="value" :class="`text-${item.type.color}`">
					<text>{{item.type.value == 0?'-':'+'}}</text>
					<text class="unit">$</text>
					{{parseFloat(item.money).toFixed(2) }}
				</view>
			</view>
			<view class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
		</view>
	</view>
</template>

<script>
	export default {
		components: {},
		props: {
			startTime: {
				type: String,
				required: true
			},
			endTime: {
				type: String,
				required: true
			}
		},
		data() {
			return {
				list: [],
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false,
				totalPrice: 0.00
			};
		},
		methods: {
			requestList(isInit = false) {
				console.log(1)
				let t = this,
					obj = {};
				if (isInit) {
					t.currentPage = 0;
					t.isFinish = false
				} else if (t.isFinish) {
					return false
				}
				obj = {
					start_date: t.startTime,
					end_date: t.endTime,
					coin: "usdt",
					source: "commission_fee",
					page: this.currentPage + 1,
					limit: this.pageSize
				}
				this.isLoading = true;
				try {
					this.$u.api.getFollowWaterList(obj).then((res) => {
						t.currentPage = parseInt(res.data.current_page)
						if (isInit) {
							t.list = res.data.data
						} else {
							t.list = [...t.list, ...res.data.data]
						}
						t.totalPrice = res.data.follow_revenue || 0.00
						t.isLoading = false;
						if (res.data.data.length < t.pageSize) {
							t.isFinish = true
						}
					}).catch((err) => {
						t.isLoading = false
						uni.showToast({
							icon: "none",
							title: "获取失败!" + (err.data.message || '')
						})
					})
				} catch (e) {
					t.isLoading = false;
				console.log(e)
					//TODO handle the exception
				}
			}
		}
	}
</script>

<style lang="scss">
	.water-histroy {
		margin: 20rpx;
		border-radius: 20rpx;
		background: #ffffff;


		.water-list {
			// margin: 20rpx;

			.list-item {
				padding: 20rpx;
				// margin-bottom: 20rpx;
				border-bottom: 1rpx solid #f3f3f3;
				// border-radius: $baseRadius;
				display: flex;
				align-items: center;
				justify-content: space-between;

				.list-item-left {
					.list-item-left-head {
						display: flex;
						align-items: center;

						.label {
							margin-left: 10rpx;
							padding: 0 10rpx;
							height: 40rpx;
							line-height: 40rpx;
							border-radius: 40rpx;
							font-size: $baseFontSizeSm;

							&.green {
								color: #00BAAD;
								background: #B9EDEA;
							}

							&.red {
								color: #FF5733;
								background: #FCD7D7;
							}
						}
					}
				}

				.name {
					font-size: $baseFontSize;
					color: #000;
				}

				.time {
					margin-top: 20rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.value {
					font-size: $baseFontSize;

					.unit {}
				}

				.type {
					margin-top: 10rpx;
				}

				.status {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.tag {
					margin-left: 20rpx;
					font-size: $baseFontSizeSm;
					color: $baseColor;
				}
			}
		}

	}

	.water-type {
		padding: 20rpx 30rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;

		.water-type-item {
			border-radius: 20rpx;
			background: #F5F5F5;
			width: 100%;
			text-align: center;
			padding: 20rpx 0;
			color: #000000;

			.count {
				margin-top: 10rpx;
				color: #FF5733;
			}

			&.item2 {
				.count {
					color: #00BAAD;
				}
			}
		}
	}

	.empty-list {
		text-align: center;
		color: var(--base-grey);
		padding-top: 100rpx;
		padding-bottom: 100rpx;
	}

	.page-bottom {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		color: var(--base-grey);
	}
</style>