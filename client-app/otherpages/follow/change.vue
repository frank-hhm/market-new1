<template>
	<view class="container container-bg container-page">
		<view class="change-header">
			<view class="change-header-item item-left">
				跟单资产
			</view>
			<view class="change-header-item item-center">
				转至=>
			</view>
			<view class="change-header-item item-right">
				账户余额
			</view>
		</view>
	</view>
</template>

<script>
</script>

<style lang="scss">
	.container {
		background: #f5f5f5;

		.change-header {
			margin: 20rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			.change-header-item {
				background: #fff;
			border-radius: 20rpx;
				padding: 30rpx;
				&.item-left{
					font-size: $baseFontSizeLgX;
					
				}
			}
		}
	}
</style>