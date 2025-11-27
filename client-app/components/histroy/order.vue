<template>
	<view class="order-histroy">
		<view class="tab-info">
			<text class="tab-item">
				<text class="label">平仓总手数:</text>
				<text class="value" :style="{
								color:totalCount > 0?getUpColor:getDownColor
							}">{{totalCount}}</text>
			</text>
			<text class="tab-item">
				<text class="label">总盈亏:</text>
				<text class="value" :style="{
								color:totalPluss>0?getUpColor:getDownColor
							}">{{totalPluss>0 ?'+':''}}{{totalPluss.toFixed(2)}}</text>
			</text>
		</view>
		<scroll-view scroll-y="true" class="record-list">
			<view class="record-item" v-for="(item,index) in recordList" :key="index" @tap="toComplateDetail(item)">
				<view class="left-wrapper ">
					<view class="name">
						{{item.ptitle}}
					</view>
					<view class="price">
						<text class="order-price mr-40">{{item.buyprice}}</text>
						<text class="close-price"> → {{item.sellprice}}</text>
					</view>
					<view class="time">{{item.selltime}}</view>
				</view>
				<view class="right-wrapper">
					<view>
						<text class="right-text">{{item.ostyle.name}}
						</text>
						<text class="right-text">{{item.onumber}}手</text>
					</view>
					<view class="ying-kui" :style="{
								color:parseFloat(item.ploss)>0?getUpColor:getDownColor
							}">{{parseFloat(item.ploss)>0?'+':''}}{{item.ploss}}
					</view>
				</view>
			</view>
		</scroll-view>
	</view>
</template>

<script>
	import {
		mapGetters
	} from "vuex"
	export default {
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
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false,
				recordList: [],
				totalCount: 0,
				totalPluss: 0
			}
		},
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor"]),
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
			requestList(isInit = false) {
				let t = this,
					obj = {};
				if (isInit) {
					t.currentPage = 0;
				} else if (t.isFinish) {
					return false
				}
				uni.showLoading({
					title: '正在获取中...'
				})
				obj = {
					start_date: t.startTime,
					end_date: t.endTime
				}
				t.isLoading = true;

				t.$u.api.historyRecordApi(obj).then((res) => {
					t.recordList = res.data.list
					t.totalCount = res.data.onumber_sum
					t.totalPluss = res.data.ploss_sum
					uni.hideLoading()
					t.isLoading = false
					// uni.showToast({
					// 	icon: "none",
					// 	title: "获取完成" 
					// })
				}).catch((err) => {
					t.isLoading = false
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			}
		}
	}
</script>

<style lang="scss" scoped>
	.order-histroy{
		background: #fff;
		margin: 20rpx ;
		border-radius: 20rpx;
	}
	.tab-info {
		padding: 20rpx;
		border-bottom: 1rpx solid #f3f3f3;
		display: flex;
		justify-content: space-between;

		.tab-item {}

		.value {
			font-size: $baseFontSizeLg;
		}
	}

	.record-list {
		background: none;
		height: calc(100% - 312rpx);
		.record-item {
			padding: 20rpx 20rpx;
			display: flex;
			justify-content: space-between;
			border-bottom: 1rpx solid #f3f3f3;

			.right-text {
				font-size: $baseFontSizeSm;
			}

			.left-wrapper {
				.name{
					font-weight: bold;
				}
				.price {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
				}

				.time {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}
			}

			.right-wrapper {
				text-align: right;

				.ying-kui {
					margin-top: 20rpx;
				}
			}

		}
	}

	.empty-list {
		text-align: center;
		color: var(--base-grey);
	}

	.page-bottom {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		color: var(--base-grey);
	}
</style>