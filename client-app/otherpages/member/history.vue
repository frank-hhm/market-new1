<template>
	<view class="custom-container">
		<customNav title="历史资金明细" bg="#fff" leftColor="#000">
		</customNav>
		<u-tabs class="tabs" height="84" :list="tabList" :current="current" @change="change" bgColor="#fff"
			:bold="false" activeColor="#FFC400" itemWidth="25%" inactiveColor="#A6A6A6">
		</u-tabs>

		<view class="date flex justify-between">
			<text class="date-item" :class="dateIndex==index?'date-item-active':''" v-for="(item,index) in dateList"
				@tap="dateChange(index)">{{item}}</text>
		</view>
		<view class="record-picker">
			<view class="start-time" @tap="startShow=true">{{startTime}}</view>
			<view class="splite"></view>
			<view class="end-time" @tap="endShow=true">{{endTime}}</view>
		</view>
		<view class="search-btn" @tap="requestList(true)">查询</view>

		<histroy-order v-if="current === 0" ref="orderRef" :startTime="startTime" :endTime="endTime"></histroy-order>
		<histroy-water v-if="current === 1" ref="waterRef" :startTime="startTime" :endTime="endTime"></histroy-water>
		<histroy-commission-water v-if="current === 3" ref="histroyCommissionWaterRef" :startTime="startTime" :endTime="endTime"></histroy-commission-water>
		<followWater v-if="current === 2" ref="followRef" :startTime="startTime" :endTime="endTime"></followWater>
		
		<u-picker mode="time" v-model="startShow" :default-time="defaultTime" @confirm="startCallback"></u-picker>
		<u-picker mode="time" v-model="endShow" :default-time="defaultTime" @confirm="endCallback"></u-picker>
	</view>
</template>

<script>
	import {
		nextTick
	} from "vue";
	import customNav from '@/components/custom-nav/index.vue';
	import histroyWater from '@/components/histroy/water.vue';
	import histroyOrder from '@/components/histroy/order.vue';
	import histroyCommissionWater from '@/components/histroy/commission-water.vue';
	import followWater from '@/components/histroy/follow.vue';
	const day = 1000 * 60 * 60 * 24
	const week = day * 7
	const month = day * 30
	const three_month = month * 3
	export default {
		components: {
			customNav,
			histroyWater,
			histroyOrder,
			histroyCommissionWater,
			followWater
		},
		data() {
			return {
				tabList: [{
					name: "合约记录"
				}, {
					name: "存取记录"
				}, {
					name: "跟单记录"
				}, {
					name: "佣金记录"
				}],
				current: 1,
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
		methods: {
			change(index) {
				let t = this;
				if (t.current == index) {
					return false
				}
				t.current = index
				t.currentBak = index
				// initDetail()
				this.defaultTime = this.getCurrentTime()
				this.startTime = this.defaultTime
				this.endTime = this.defaultTime
				this.dateIndex = 0
				this.$nextTick(()=>{
					this.requestList(true)
				})
			},
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
				this.$nextTick(()=>{
					this.requestList(true)
				})
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
			requestList(isInit = false, isBottom = false) {
				switch (this.current) {
					case 0:
						if (isBottom === false) {
							this.$refs.orderRef.requestList(isInit)
						}
						break;
					case 1:
						this.$refs.waterRef.requestList(isInit)
						break;
					case 2:
						this.$refs.followRef.requestList(isInit)
						break;
					case 3:
						this.$refs.histroyCommissionWaterRef.requestList(isInit)
						break;

				}
			}
		},
		onReachBottom() {
			this.requestList(false, true)
		},
		onLoad() {
			this.defaultTime = this.getCurrentTime()
			this.startTime = this.defaultTime
			this.endTime = this.defaultTime
			this.$nextTick(() => {
				this.requestList(true)
			})
			switch (this.$Route.query.type) {
				case "order":
					this.current = 0;
					break;
				case "water":
					this.current = 1;
					break;
				case "follow":
					this.current = 2;
					break;
				case "share":
					this.current = 3;
					break;
				default:
					this.current = 0;
					break;
			}
		}
	}
</script>

<style lang="scss">
	.custom-container {
		background-color: #f5f5f5;
		padding-bottom: 100rpx;
		min-height: 100vh;

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
				background-color: #FFF3C9;
				color: #FFC400;
				border: 1rpx solid #F4CF4A;
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
			background: #F4CF4A;
			font-size: $baseFontSize;
			color: #000;
			border-radius: 86rpx;
		}


	}
</style>