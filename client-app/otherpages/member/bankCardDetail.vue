<template>
	<view class="container container-page">
		<customNav title="绑定银行卡"></customNav>
		<view class="bank-card-tip flex flex-column align-center">
			<view class="bank-icon"></view>
			<text class="bank-card-bind">恭喜您!银行卡绑定成功</text>
			<text class="bank-card-desc">您提交的银行资料已通过审核</text>
			<view class="bank-info ">
				<view class="bank-info-header">
					<text class="bank-name">{{member.bank_name}}</text>
					<view class="btn" @tap="turnUpdate">修改</view>
				</view>
				<view class="flex mt-20">
					<text class="label">银行卡号:</text>
					<text class="value">{{member.bank_code}}</text>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapState,
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {
			};
		},
		computed: {
			...mapState("member", ['member']),
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
		methods: {
			turnUpdate() {
				uni.navigateTo({
					url: '/otherpages/member/bindBankCard'
				})
			}
		},
		async onShow() {}
	}
</script>

<style lang="scss">
	.container {
		position: relative;
		background-color: #fff;

		.bank-card-tip {
			padding: 200rpx 20rpx;

			.bank-icon {
				width: 200rpx;
				height: 200rpx;
				background: url("~@/static/images/icon/bank-icon.png");
				background-size: 100% auto;
				background-repeat: no-repeat;
				background-position: center;
			}

			.bank-card-bind {
				margin-top: 50rpx;
				font-size: $baseFontSize;
				color: $color-blue;
			}

			.bank-card-desc {
				margin-top: 10rpx;
				font-size: $baseFontSize;
				color: #999;
			}

			.bank-info {
				margin-top: 50rpx;
				width: 90%;
				padding: 20rpx;
				border-radius: $baseRadius;
				background-color: #f0f0f0;

				.bank-info-header {
					display: flex;
					align-items: center;
					justify-content: space-between;

					.btn {
						color: $color-blue;
					}
				}

				.bank-name {
					font-size: $baseFontSize;
					color: #000;
				}

				.label {
					margin-right: 10rpx;
					font-size: $baseFontSize;
					color: #000;
				}

				.mt-20 {
					margin-top: 20rpx;
				}

				.value {
					font-size: $baseFontSize;
					color: #000;
				}
			}
		}
	}
</style>