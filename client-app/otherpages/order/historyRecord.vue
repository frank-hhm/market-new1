<template>
	<view class="container container-page container-bg">
		<customNav title="历史盈亏">
		</customNav>
		<view class="date flex justify-between">
			<text class="date-item" :class="dateIndex==index?'date-item-active':''" v-for="(item,index) in dateList"
				@tap="dateChange(index)">{{item}}</text>
		</view>

		<view class="record-picker">
			<view class="start-time" @tap="startShow=true">{{startTime}}</view>
			<view class="splite"></view>
			<view class="end-time" @tap="endShow=true">{{endTime}}</view>
		</view>
		<view class="search-btn" @tap="requestApi">查询</view>
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
						<text
							class="right-text">{{item.ostyle.name}}
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
		<u-picker mode="time" v-model="startShow" :default-time="defaultTime" @confirm="startCallback"></u-picker>
		<u-picker mode="time" v-model="endShow" :default-time="defaultTime" @confirm="endCallback"></u-picker>
	</view>
</template>

<script>
	import {
		mapGetters,
	} from "vuex"
	import customNav from '@/components/custom-nav/index.vue';
	const day = 1000 * 60 * 60 * 24
	const week = day * 7
	const month = day * 30
	const three_month = month * 3
	export default {
		components: {
			customNav
		},
		data() {
			return {
				dateList: ["当日", "最近一周", "最近一月", "最近三月"],
				dateIndex: 0,
				startShow: false,
				endShow: false,
				startTime: "",
				endTime: "",
				defaultTime: "",
				recordList: [],
				totalCount: 0,
				totalPluss: 0
			};
		},
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor"]),
		},
		methods: {
			startCallback({
				year,
				month,
				day
			}) {
				this.startTime = `${year}-${month}-${day}`
			},
			endCallback({
				year,
				month,
				day
			}) {
				this.endTime = `${year}-${month}-${day}`
			},
			requestApi() {
				let t = this
				uni.showLoading({
					title: '正在获取中...'
				})
				t.$u.api.historyRecordApi({
					start_date: t.startTime,
					end_date: t.endTime
				}).then((res) => {
					t.recordList = res.data.list
					t.totalCount = res.data.onumber_sum
					t.totalPluss = res.data.ploss_sum
					uni.hideLoading()
					// uni.showToast({
					// 	icon: "none",
					// 	title: "获取完成" 
					// })
				}).catch((err) => {
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			},
			dateChange(index) {
				if (this.dateIndex == index) {
					return false
				}
				this.dateIndex = index
				let date
				let current_data
				switch (index) {
					case 0:
						date = this.getCurrentTime()
						this.startTime = date
						this.endTime = date
						break
					case 1:
						date = this.calc_time(week)
						current_data = this.getCurrentTime()
						this.startTime = date
						this.endTime = current_data
						break
					case 2:
						date = this.calc_time(month)
						current_data = this.getCurrentTime()
						this.startTime = date
						this.endTime = current_data
						break
					case 3:
						date = this.calc_time(three_month)
						current_data = this.getCurrentTime()
						this.startTime = date
						this.endTime = current_data
						break
				}
				this.requestApi()
			},
			getCurrentTime() {
				let date = new Date()
				let year = date.getFullYear()
				let month = (date.getMonth() + 1 + "").padStart(2, '0')
				let day = (date.getDate() + "").padStart(2, '0')
				return `${year}-${month}-${day}`
			},
			calc_time(value) {
				let time = new Date().getTime()
				let date = new Date(time - value)
				let year = date.getFullYear()
				let month = (date.getMonth() + 1 + "").padStart(2, '0')
				let day = (date.getDate() + "").padStart(2, '0')
				return `${year}-${month}-${day}`
			},
			toComplateDetail(item) {
				this.$Router.push({
					path: "/otherpages/order/complateDetail",
					query: {
						id: item.id
					}
				})
			},
		},
		onLoad() {
			this.defaultTime = this.getCurrentTime()
			this.startTime = this.defaultTime
			this.endTime = this.defaultTime
			this.requestApi()
		}
	}
</script>

<style lang="scss">
	.container {
		.date {
			padding: 30rpx;
			background-color: none;

			.date-item {
				width: calc(25% - 20rpx);
				text-align: center;
				height: 58rpx;
				line-height: 58rpx;
				font-size: $baseFontSizeSm;
				color: #000;
				background-color: #fff;
				border: 1rpx solid #fff;
				border-radius: $baseRadius;
				margin-right: 20rpx;

				&:last-child {
					margin-right: 0;
				}
			}

			.date-item-active {
				background-color: #CCD6FF;
				color: var(--base-blue);
				border: 1rpx solid var(--base-blue);
			}
		}

		.record-picker {
			margin: 0 24rpx;
			background-color: #fff;
			border-radius: 16rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;
			height: 96rpx;
			padding: 0 50rpx;

			.start-time {
				width: calc(50% - 70rpx);
				font-size: $baseFontSize;
				font-weight: 500;
				padding-left: 20rpx;
				position: relative;

				&::after {
					position: absolute;
					content: "";
					height: 20rpx;
					width: 20rpx;
					background-image: url('~@/static/images/icon/icon-up-10.png');
					background-size: 100% 100%;
					right: 20rpx;
					top: calc(50% - 10rpx);
				}
			}

			.splite {
				width: 40rpx;
				height: 40rpx;
				background-image: url('~@/static/images/icon/icon-right-10.png');
				background-size: 100% 100%;
			}

			.end-time {
				width: calc(50% - 70rpx);
				font-size: $baseFontSize;
				font-weight: 500;
				padding-left: 20rpx;
				position: relative;

				&::after {
					position: absolute;
					content: "";
					height: 20rpx;
					width: 20rpx;
					background-image: url('~@/static/images/icon/icon-up-10.png');
					background-size: 100% 100%;
					right: 20rpx;
					top: calc(50% - 10rpx);
				}
			}
		}

		.search-btn {
			margin: 20rpx;
			height: 86rpx;
			width: calc(100% - 40rpx);
			line-height: 86rpx;
			text-align: center;
			background: $baseColor;
			font-size: $baseFontSize;
			color: #fff;
			border-radius: $baseRadius;
		}

		.tab-info {
			padding: 20rpx;
			background-color: #fff;
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
				background-color: #fff;

				display: flex;
				justify-content: space-between;
				border-bottom: 1rpx solid #f3f3f3;

				.right-text {
					font-size: $baseFontSizeSm;
				}

				.left-wrapper {
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
	}
</style>