<template>
	<view class="container">
		<view class="order-success-wrapper">
			<view class="success-icon">
				<u-icon name="checkbox-mark" size="50" color="#fff" ></u-icon>
			</view>
			<view class="order-success-text">{{$Route.query.title}}</view>
			<view class="info-line" v-if="$Route.query.ptitle">
				<text class="label">名称</text>
				<text class="value">{{$Route.query.ptitle}}</text>
			</view>
			<view class="info-line" v-if="$Route.query.type">
				<text class="label">方向</text>
				<view class="value"><text :class="$Route.query.type == 1?'text-red':'text-green'">{{$Route.query.type == 1?'买跌':'买涨'}}</text></view>
			</view>
			<view class="info-line" v-if="$Route.query.onumber">
				<text class="label">手数</text>
				<view class="value">{{$Route.query.onumber}}手</view>
			</view>
			<view class="info-line" v-if="$Route.query.order_id">
				<text class="label">订单ID</text>
				<text class="value">#{{$Route.query.order_id}}</text>
			</view>
			<view class="info-line" v-if="$Route.query.buyprice">
				<text class="label">{{ Number($Route.query.robot) === 1 ?'标记价格':'开仓价格'}}</text>
				<view class="value">{{$Route.query.buyprice}}</view>
			</view>
			<view class="info-line" v-if="$Route.query.buytime">
				<text class="label">开仓时间</text>
				<text class="value">{{$Route.query.buytime|time}}</text>
			</view>
			<view class="info-line"  v-if="$Route.query.create_time">
				<text class="label">{{ Number($Route.query.robot) === 1 ?'提交时间':'成交时间'}}</text>
				<text class="value">{{$Route.query.create_time|time}}</text>
			</view>
		</view>
		<!--  -->
		<view class="common-btn submit-btn" @tap="onRouteChange">{{ Number($Route.query.robot) === 1?'查看当前挂单':'查看持仓'}}</view>
		<view class="common-btn submit-btn submit-btn-1" @tap="back">继续交易</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
			};
		},
		methods: {
			back() {
				this.$Router.back()
			},
			onRouteChange() {
				let t = this
				//#ifdef APP-PLUS
				uni.switchTab({
					url: "/pages/charge/index",
					fail: function(e) {}
				})
				//#endif
				//#ifdef H5
				this.$Router.push({
					path: "/pages/charge/index",
					query:{
						current:this.$Route.query.robot == 1?1:0
					}
				})
				//#endif
			}
		},
		onLoad() {
			uni.setNavigationBarTitle({
				// title: this.$Route.query.title
				title: ""
			})
		}

	}
</script>

<style lang="scss">
	.container {
		background-color: #fff;

		.order-success-wrapper {
			padding: 24rpx 0 64rpx;
			background-color: #fff;

			.success-icon {
				margin:20rpx auto;
				width: 120rpx;
				height: 120rpx;
				background: var(--base-green);
				border-radius: 120rpx;
				display: flex;
				justify-content: center;
				align-items: center;
			}

			.order-success-text {
				margin-bottom:50rpx;
				color: #000000;
				font-size: $baseFontSizeLg;
				text-align: center;
				width: 100%;
			}

			.tip {
				margin-top: 16rpx;
				font-size: $baseFontSize;
				color: #BCBCBC;
			}

		}

		.info-line {
			display: flex;
			justify-content: space-between;
			margin: 30rpx 30rpx 0;
			.label {
				font-size: $baseFontSize;
				color: var(--base-grey);
			}

			.value {
				font-size: $baseFontSize;
			}
		}

		.submit-btn {
			margin: 40rpx;
		}

		.submit-btn-1 {
			background-color:#EEEEEE;
			color:#000;
		}
	}

	// 
</style>