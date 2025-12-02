<template>
	<view class="container">
		<view class="usdt-card-tip flex flex-column align-center">
			<view class="usdt-icon"></view>
			<text class="usdt-card-bind">恭喜您!支付宝绑定成功</text>
			<text class="usdt-card-desc">您提交的支付宝账号已通过审核</text>
			<view class="usdt-info ">
				<view class="label">
					<view>支付宝账号:</view>
					<view class="btn" @tap="turnUsdt">修改</view>
				</view>
				<view class="value">{{member.alipay_card||'--'}}</view>
			</view>
			<view class="usdt-info ">
				<view class="label">
					<view>支付宝姓名:</view>
				</view>
				<view class="value">{{member.alipay_name||'--'}}</view>
			</view>
			<view class="usdt-info ">
				<view class="label">
					<view>支付宝收款码:</view>
				</view>
				<image :src="member.alipay_img" mode="widthFix" style="width: 160rpx;height: 160rpx;"></image>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
	} from "vuex"
	export default {
		data() {
			return {
			};
		},
		filters: {
			cardNumber(value) {

				if (value) {
					let header = value.slice(0, 2)
					let tail = value.slice(value.length - 2, value.length)
					return `${header}****************${tail}`
				} else {
					return ""
				}
			}
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			turnUsdt() {
				uni.navigateTo({
					url: '/otherpages/member/bindAlipayCard'
				})
			}
		},
		async onShow() {
		}
	}
</script>

<style lang="scss">
	.container {
		position: relative;
		background-color: #fff;

		.usdt-card-tip {
			padding: 200rpx 30rpx;

			.usdt-icon {
				width: 120rpx;
				height: 120rpx;
				background: url("~@/static/images/icon/icon-alipay.png");
				background-size: 100% 100%;
			}

			.usdt-card-bind {
				margin-top: 50rpx;
				font-size: 30rpx;
				color: $color-blue;
			}

			.usdt-card-desc {
				margin-top: 10rpx;
				font-size: 24rpx;
				color: #999;
			}

			.usdt-info {
				margin-top: 50rpx;
				width: calc(100% - 60rpx);
				padding: 30rpx;
				line-height: 40rpx;
				border-radius: 10rpx;
				background-color: #f0f0f0;

				.usdt-name {
					font-size: 30rpx;
				}

				.label {
					margin-right: 10rpx;
					font-size: 24rpx;
					color: #999;
					display: flex;
					justify-content: space-between;
					align-items: center;

					.btn {
						color: $color-blue;
					}
				}

				.value {
					margin-top: 20rpx;
					font-size: 32rpx;
				}
			}
		}
	}
</style>