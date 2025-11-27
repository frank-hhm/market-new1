<template>
	<view class="load-box">
		<view class="load-box-main">
			<view class="load-icon"><u-icon name="reload" color="#999" size="28"></u-icon></view>
			<view class="load-text">正在连接中...请稍等</view>
		</view>
	</view>
</template>

<script>
</script>

<style lang="scss" scoped>
	.load-box {
		position: fixed;
		display: flex;
		width: 100%;
		height: 100%;
		left: 0;
		top: 0;
		justify-content: center;
		align-items: center;
		overflow: hidden;
		z-index: 99999;
	}

	.load-box-main {
		width: 100%;
		height: 80px;
		// background: rgba(0, 0, 0, 0.6);
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.load-text {
		margin-left: 10rpx;
		color: #999;
	}

	.load-icon {
		animation: rotate 0.7s linear infinite;

	}

	@-webkit-keyframes rotate {
		0% {
			transform: rotate(0);
		}

		100% {
			transform: rotate(360deg);
		}
	}

	@keyframes rotate {
		0% {
			transform: rotate(0);
		}

		100% {
			transform: rotate(360deg);
		}
	}
</style>