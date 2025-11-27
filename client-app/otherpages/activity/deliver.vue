<template>
	<view class="container container-page">
		<customNav leftColor="#000" title="下单送好礼" bg="none"></customNav>
		<view class="deliver-mian">
			<view class="deliver-title1"></view>
			<view class="deliver-title2"></view>
			<view class="deliver-item"></view>
		</view>
		<view class="deliver-btn-main">
			<router-link class="deliver-btn1"  to="/pages/other/kefu"></router-link>
			<router-link class="deliver-btn2"   to="/otherpages/activity/deliver-list"></router-link>
		</view>

		<view class="deliver-bottom-bg">

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
				proList: [{
					title: '苹果 iPad Air5代 256G',
					cover: 'deliver-pro-1.png',
					price: 5999,
					desc: '下单送666手送'
				}, {
					title: '苹果 iPad Pro5代 256G',
					cover: 'deliver-pro-2.png',
					price: 8999,
					desc: '下单送888手送'
				}, {
					title: 'HUAWEI Mate x6',
					cover: 'deliver-pro-3.png',
					price: 15999,
					desc: '下单送1288手送'
				}, {
					title: 'HUAWEI Mate x6',
					cover: 'deliver-pro-4.png',
					price: 23999,
					desc: '下单送1588手送'
				}]
			}
		},
		methods: {
			onDeliver() {
				uni.switchTab({
					url: '/pages/market/index'
				})
				// this.$Router.push({
				// 	path: '/pages/market/list'
				// })
			}
		}
	}
</script>

<style lang="scss">
	.container {
		padding-bottom: 100rpx;
		background-image: url('@/static/images-new1/activity/deliver-bg.png');
		background-size: 100% 100%;
		background-repeat: no-repeat;
		background-position: center;
	}

	.deliver-mian {
		margin-top: 60rpx;
		width: 100%;
		// position: fixed;
		z-index: 1;
		height: auto;
		position: relative;
		overflow: hidden;

		.deliver-title1 {
			width: 90%;
			margin: 0 auto;
			height: 240rpx;
			background-image: url('@/static/images-new1/activity/deliver-title-1.png');
			background-size: 100% auto;
			background-repeat: no-repeat;
			background-position: center;
		}

		.deliver-title2 {
			width: 90%;
			margin: -70rpx auto 0;
			height: 90rpx;
			background-image: url('@/static/images-new1/activity/deliver-title-2.png');
			background-size: auto 100%;
			background-repeat: no-repeat;
			background-position: center;
		}

		.deliver-item {
			width: 90%;
			margin: 30rpx auto 0;
			height: 660rpx;
			background-image: url('@/static/images-new1/activity/deliver-item.png');
			background-size: 100%;
			background-repeat: no-repeat;
			background-position: center;
		}

	}


	.deliver-btn-main {
		margin: 30rpx auto 30rpx;
		width: 80%;
		position: relative;
		z-index: 99;
		display: flex;
		align-items: center;
		justify-content: space-between;

		.deliver-btn1 {
			width: 45%;
			margin: 30rpx auto 0;
			height: 120rpx;
			background-image: url('@/static/images-new1/activity/deliver-btn-bg-1.png');
			background-size: 100%;
			background-repeat: no-repeat;
			background-position: center;
			position: relative;
			&::after{
				position: absolute;
				content: "";
			height: 110rpx;
			width: 110rpx;
			right: 0;
			bottom: -50rpx;
			background-image: url('@/static/images-new1/activity/deliver-btn-bg-1s.png');
			background-size: 100%;
			background-repeat: no-repeat;
			background-position: center;
			}
		}

		.deliver-btn2 {
			width: 45%;
			margin: 30rpx auto 0;
			height: 120rpx;
			background-image: url('@/static/images-new1/activity/deliver-btn-bg-2.png');
			background-size: 100%;
			background-repeat: no-repeat;
			background-position: center;
		}
	}


	.deliver-btn {
		box-shadow: inset 0px 4px 4px 0px rgba(255, 250, 250, 0.25);
		background-image: linear-gradient(93deg,
				rgba(86, 213, 255, 1) 0,
				rgba(123, 162, 255, 1) 100%);
		border-radius: 100rpx;
		height: 120rpx;
		width: 100%;
		text-align: center;
		line-height: 120rpx;
		color: #fff;
		font-weight: bold;
		font-size: 40rpx;
		letter-spacing: 6rpx;

	}

	.product-main {
		width: calc(100% - 48rpx);
		margin: 60rpx 24rpx;
		border: 4rpx solid #fff;
		background: linear-gradient(102deg, #B0B7F4 0%, #B9BBE6 37%, #B5D1EA 57%, #CAE2F9 100%);
		border-radius: 24rpx;

		.product-title {
			height: 106rpx;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.product-title-bg {
			width: 392rpx;
		}

		.product-list {
			background: #fff;
			padding: 24rpx;
		}

		.product-item {
			display: flex;
			background: linear-gradient(96deg, #E5F1FD 0%, #F6F4FC 57%, #FFF9F2 100%);
			border-radius: 24rpx;
			margin-bottom: 24rpx;
			width: 100%;
			padding: 24rpx;

			.product-cover {
				height: 150rpx;
				width: 150rpx;
				margin-right: 20rpx;
			}

			.product-name {
				font-weight: bold;
				font-size: 32rpx;
			}

			.product-desc {
				color: #6A6B6D;
				margin-top: 10rpx;
			}

			.product-tips {
				margin-top: 10rpx;
				background: #D1D3F9;
				border-radius: 250px;
				display: inline-block;
				padding: 6rpx 24rpx;
				font-size: 20rpx;
				font-weight: bold;

			}
		}
	}

	.deliver-bottom-bg {
		width: 100%;
		height:660rpx;
		background-image: url('@/static/images-new1/activity/deliver-bg-1.png');
		background-size:auto  100% ;
		background-repeat: no-repeat;
		background-position: center;
	}
</style>