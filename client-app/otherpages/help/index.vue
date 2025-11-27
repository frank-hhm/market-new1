<template>
	<view class="page container-page">
		<customNav title="帮助中心"  leftColor="#000">
		</customNav>
		<image class="head-main-bg" :src="require('@/static/images/bg/help-banner.jpg')" mode="aspectFill"></image>
		<view class="cell-main">
			<view class="cell-label">快速指引</view>
			<view class="cell-list">
				<view class="cell-item common-btn" @click="shiming">
					<view class="cell-item-icon1"></view>
					<view class="title">实名认证</view>
					<view class="btn">
						前往认证
					</view>
				</view>
				<view class="cell-item common-btn" @click="bangka">
					<view class="cell-item-icon2"></view>
					<view class="title">绑定/修改银行卡</view>

					<view class="btn">
						前往绑定
					</view>
				</view>
				<view class="cell-item common-btn" @click="rujin">
					<view class="cell-item-icon3"></view>
					<view class="title">入金交易</view>
					<view class="btn">
						前往入金
					</view>
				</view>
			</view>
			<view class="cell-label cell-label-2">问题导航</view>
			<view class="cell-list2">
				<view class="cell-item common-btn" @click="peoplee">
					<image class="cell-icon" :src="require('@/static/images/icon/help-icon-a1.png')"></image>
					<view class="label">
						<view class="title">新人必看</view>
						<view class="desc">开户投资指南针</view>
					</view>
					<u-icon class="cell-icon-right" style="font-size: 20rpx;" name="arrow-right"></u-icon>
				</view>
				<view class="cell-item common-btn" @click="skyblue">
					<image class="cell-icon" :src="require('@/static/images/icon/help-icon-a2.png')"></image>
					<view class="label">
						<view class="title">账户类型</view>
						<view class="desc">先择更佳点差优惠</view>
					</view>
					<u-icon class="cell-icon-right" style="font-size: 20rpx;" name="arrow-right"></u-icon>
				</view>
				<view class="cell-item common-btn" @click="gold">
					<image class="cell-icon" :src="require('@/static/images/icon/help-icon-a3.png')"></image>
					<view class="label">
						<view class="title">出入金指南</view>
						<view class="desc">资金存取便捷安全</view>
					</view>
					<u-icon class="cell-icon-right" style="font-size: 20rpx;" name="arrow-right"></u-icon>
				</view>
				<view class="cell-item common-btn" @click="purple">
					<image class="cell-icon" :src="require('@/static/images/icon/help-icon-a4.png')"></image>
					<view class="label">
						<view class="title">0基础学投资</view>
						<view class="desc">从入门到精通</view>
					</view>
					<u-icon class="cell-icon-right" style="font-size: 20rpx;" name="arrow-right"></u-icon>
				</view>
				<view class="cell-item common-btn" @click="green">
					<image class="cell-icon" :src="require('@/static/images/icon/help-icon-a5.png')"></image>
					<view class="label">
						<view class="title">风险管理</view>
						<view class="desc">保障资金稳健增长</view>
					</view>
					<u-icon class="cell-icon-right" style="font-size: 20rpx;" name="arrow-right"></u-icon>
				</view>
			</view>
			<router-link class="cell-btn common-btn" to="/pages/other/kefu">
				没找到答案？联系在线客服
			</router-link>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {

			};
		},
		methods: {
			clickLeft() {
				uni.navigateBack()
			},
			back() {
				uni.navigateBack({
					delta: 1
				})
			},
			rujin() {
				uni.navigateTo({
					url: '/otherpages/memberCoin/recharge'
				})
			},
			bangka() {
				uni.navigateTo({
					url: '/otherpages/member/bindBankCard'
				})
			},
			shiming() {
				uni.navigateTo({
					url: '/otherpages/member/bindCard'
				})
			},
			peoplee() {
				uni.navigateTo({
					url: '/otherpages/help/open'
				})
			},
			gold() {
				uni.navigateTo({
					url: '/otherpages/help/coin'
				})
			},
			green() {
				uni.navigateTo({
					url: '/otherpages/help/rick'
				})
			},
			skyblue() {
				uni.navigateTo({
					url: '/otherpages/help/type'
				})
			},
			purple() {
				uni.navigateTo({
					url: '/otherpages/help/transaction'
				})
			}
		},
	}
</script>

<style lang="scss" scoped>
	.page {
		background: #f5f5f5;
	}


	.head-main-bg {
		height: 240rpx;
		width: 100%;
	}

	.cell-main {
		background: #fff;
		padding: 20rpx;
		margin-bottom: 20rpx;

		.cell-label {
			color: #000;
			height: 40rpx;
			line-height: 40rpx;
			padding-left: 20rpx;
			font-weight: bold;
			font-size: $baseFontSize;
			position: relative;

			&::after {
				position: absolute;
				content: "";
				width: 12rpx;
				height: 40rpx;
				background: $baseColor;
				border-radius: 10rpx;
				left: -10rpx;
			}
		}

		.cell-label-2 {
			margin-top: 60rpx;
		}

		.cell-list {
			margin-top: 20rpx;
			display: flex;
			justify-content: space-between;

			.cell-item {
				text-align: center;
				margin-top: 30rpx;
				width: 33.34%;

				.title {
					margin-top: 12rpx;
					color: #000;
					font-size: $baseFontSize;
					font-weight: 500;
				}

				.cell-item-icon1 {
					margin: 0 auto;
					height: 76rpx;
					width: 76rpx;
					background-image: url('~@/static/images/icon/help-icon-1.png');
					background-size: 100% 100%;
				}

				.cell-item-icon2 {
					margin: 0 auto;
					height: 76rpx;
					width: 76rpx;
					background-image: url('~@/static/images/icon/help-icon-2.png');
					background-size: 100% 100%;
				}

				.cell-item-icon3 {
					margin: 0 auto;
					height: 76rpx;
					width: 76rpx;
					background-image: url('~@/static/images/icon/help-icon-3.png');
					background-size: 100% 100%;
				}

				.btn {
					margin: 20rpx auto;
					height: 46rpx;
					line-height: 46rpx;
					border-radius: 46rpx;
					font-size: $baseFontSizeSm;
					width: 160rpx;
					color: #fff;
					background: $baseColor;
				}
			}
		}

		.cell-list2 {
			margin-top: 30rpx;
			display: flex;
			flex-wrap: wrap;
			justify-content: space-between;

			.cell-item {
				display: flex;
				width: calc(50% - 20rpx);
				height: 120rpx;
				margin-bottom: 20rpx;
				border-top-left-radius: $baseRadius;
				border-top-right-radius: $baseRadius;
				padding: 10rpx;
				font-size: 28rpx;
				font-weight: 500;
				justify-content: space-between;
				align-items: center;

				.label {
					line-height: 36rpx;
					width: calc(100% - 64rpx - 60rpx);

					.title {
						color: #fff;
					}

					.desc {
						font-size: $baseFontSizeSm;
					}
				}

				&:nth-child(1) {
					background-image: linear-gradient(to right, #685BEF 0%, #899FF0 100%);

					.desc {
						color: #3935D3;
					}
				}

				&:nth-child(2) {
					background-image: linear-gradient(to right, #23A1F5 0%, #86CDF7 100%);

					.desc {
						color: #0E77B9;
					}
				}

				&:nth-child(3) {
					background-image: linear-gradient(to right, #FDA336 0%, #F7BF74 100%);

					.desc {
						color: #D8520E;
					}
				}

				&:nth-child(4) {
					background-image: linear-gradient(to right, #9D48E0 0%, #C287F3 100%);

					.desc {
						color: #8233C2;
					}
				}

				&:nth-child(5) {
					background-image: linear-gradient(to right, #16B357 0%, #78DDA3 100%);

					.desc {
						color: #037B32;
					}
				}

				.cell-icon {
					background-color: #fff;
					border-radius: 40rpx;
					padding: 12px;
					height: 40rpx;
					width: 40rpx;
				}

				.cell-icon-right {
					color: #fff;
				}
			}
		}

		.cell-btn {
			margin-top: 78rpx;
			height: 76rpx;
			line-height: 76rpx;
			width: 100%;
			border-radius: $baseRadius;
			background: $baseColor ;
			color: #fff;
			font-size: $baseFontSize;
			font-weight: 500;
			text-align: center;
		}
	}
</style>