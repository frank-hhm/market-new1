<template>
	<view class="container container-page">
		<customNav title="风控" isBgImage leftColor="#fff"></customNav>
		<view class="content">
			<view class="topView">
				建仓手数限制
			</view>
			<view class="botView lettSpace">
				APP内单笔建仓上限为10手。下限由账户级别确定，最低为1手。
			</view>
			<view class="topView">
				强制平仓
			</view>
			<view class="botView lettSpace">
				交易时段内当保证金比例≦40%时，以当前平仓价、当日收盘价按照最大亏损优先原则部分或全部强制平仓。
			</view>
			<view class="topView">
				保证金比例
			</view>
			<view class="botView lettSpace">
				保证金比例=账户净值/已用保证金。<br/>
				保证金催缴：保证金比例≦100%时，客户应尽快入金或减少持仓，以避免触发强制平仓。<br/>
				强制平仓：当保证金比例≦40%时，系统将对客户的持仓进行部分或全部强制平仓。
			</view>
		</view>
		<!-- <view class="btnStyle">
			入金领赠金
		</view> -->
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
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		position: relative;
		padding-bottom: 100rpx;
	}

	.navSty {
		position: fixed;
		width: 100%;
		height: 100rpx;
		background-image: linear-gradient(to bottom, #2343b0, #3157c6, #4c71e4);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.navText {
		width: 100%;
		height: 60rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0 20rpx 0 10rpx;
	}

	.titleSty {
		color: #fff;
		font-size: $baseFontSize;
	}

	.btnStyle {
		position: fixed;
		bottom: 0%;
		width: 100%;
		height: 100rpx;
		background-color: #5176e9;
		color: #fff;
		text-align: center;
		line-height: 100rpx;
	}

	.content {
		padding-top: 20rpx;

		.topView {
			padding: 25rpx 25rpx;
			color: var(--base-default);
			font-weight: 600;
			font-size: $baseFontSize;
		}

		.botView {
			padding: 15rpx 25rpx 35rpx;
			background-color: #f2f2f2;
			color: #939393;
			font-size:24rpx;

			.content-explain {
				margin-bottom: 20rpx;
			}
		}
	}

	.lettSpace {
		letter-spacing: .2em;
		line-height: 3em;
	}
</style>
