<template>
	<view class="container container-page">
		<customNav title="出入金" isBgImage  leftColor="#fff"></customNav>
		<view class="content">
			<view class="topView">
				出金（取款）注意事项
			</view>
			<view class="botView lettSpace">
				一般情况下取款手续费用全免，公司只对以下情况的取款收取费用：本公司一般不收取款手续费，但以下特殊情况除外：<br />
				（1）如果存款金额的50%不曾用于建仓交易，取款时将收取6%的手续费用。<br />
				（2）如客户单次取款少于50美元将统一收取3美元的手续费用。<br />
				（3）每月取款超过4笔，从第5笔扣取款金额的5%作为手续费。
			</view>
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
		background-color: var(--base-default);
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
			font-size: $baseFontSizeSm;

			.content-explain {
				margin-bottom: 20rpx;
			}
		}
	}
	
	.lettSpace{
		letter-spacing: .2em;
		line-height: 3em;
	}
</style>
