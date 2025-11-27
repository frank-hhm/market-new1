<template>
	<view class="recharge-box">
		<view class="recharge-box-head">
			<view class="recharge-box-head-left">
				<view class="recharge-box-head-left-title">待付款</view>
				<view class="recharge-box-head-left-desc">请尽快支付完毕</view>
			</view>
			<view class="recharge-box-head-icon"></view>
		</view>
		<view class="recharge-box-main" v-if="!hideInfo">
			<view class="recharge-box-main-head">
				存款信息确认
			</view>
			<view class="recharge-box-main-list">
				<view class="recharge-box-main-item">
					<view class="recharge-box-main-label">存款金额</view>
					<view class="recharge-box-main-content">{{query.type != 'offline_usdt' ?query.usdt:query.money}}美元
					</view>
				</view>
				<view class="recharge-box-main-item">
					<view class="recharge-box-main-label">当前汇率</view>
					<view class="recharge-box-main-content"><text
							class="content-red">{{systemConfig.usdt_rate || 7}}</text></view>
				</view>
				<view class="recharge-box-main-item">
					<view class="recharge-box-main-label">兑换后约支付</view>
					<view class="recharge-box-main-content" v-if="query.type != 'offline_usdt'"><text
							class="content-red">{{query.usdt && query.type != 'offline_usdt' ? (query.usdt * systemConfig.usdt_rate).toFixed(2):''}}</text>人民币
					</view>
					<view class="recharge-box-main-content" v-else><text
							class="content-red">{{query.money && query.type == 'offline_usdt' ?  query.money:''}}</text>USDT
					</view>
				</view>
			</view>
		</view>
		<view class="recharge-box-channel">
			<view class="title">
				支付通道
			</view>
			<view class="desc">
				{{channel}}
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters
	} from 'vuex';

	export default {
		props: {
			query: {
				type: Object,
				required: true
			},
			channel: {
				type: String,
				required: true
			},
			hideInfo: {
				type: Boolean,
				required: false
			},
		},
		computed: {

			...mapState("app", ["systemConfig"])
		},
		methods: {}
	};
</script>


<style scoped lang="scss">
	.recharge-box {
		margin: 20rpx;
		position: relative;
		z-index: 1;

		.recharge-box-head {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 30rpx 0;

			.recharge-box-head-left-title {
				font-size: $baseFontSizeLgXX;
				font-weight: bold;
			}

			.recharge-box-head-left-desc {
				margin-top: 10rpx;
				color: var(--base-grey);
			}

			.recharge-box-head-icon {
				position: absolute;
				z-index: -1;
				top: 20rpx;
				right: 0;
				width: 180rpx;
				height: 180rpx;
				background: url("~@/static/images-new1/recharge/icon-time.png");
				background-size: 100% 100%;
			}
		}

		.recharge-box-main {
			background: #fff;
			border-radius: 20rpx;
			padding: 30rpx;
			z-index: 1;

			.recharge-box-main-head {
				color: #000;
			}

			.recharge-box-main-list {
				margin-top: 20rpx;

				.recharge-box-main-item {
					margin: 20rpx 0;
					display: flex;
					justify-content: space-between;

					.recharge-box-main-label {
						color: var(--base-grey);
					}
				}
			}
		}

		.recharge-box-channel {
			display: flex;
			align-items: center;
			justify-content: space-between;
			background: #fff;
			border-radius: 20rpx;
			padding: 30rpx;
			margin: 20rpx 0;

			.title {
				color: var(--base-grey);
			}

			.desc {
				color: #000;
				font-size: $baseFontSizeDefault;
			}
		}

		.content-red {
			color: #FF5733;
		}
	}
</style>