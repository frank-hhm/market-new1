<template>
	<view class="container container-page">
		<customNav title="礼品清单" leftColor="#000" bg="#FFDE73">
		</customNav>

		<view class="product-main">
			<view class="product-title">
				<image class="product-title-bg" src="@/static/images-new1/activity/deliver-title2.png" mode="widthFix">
				</image>
			</view>
			<view class="product-list">
				
				<view class="product-item" v-for="(item,index) in proList" :key="index">
					<image class="product-cover" :src="require('@/static/images/activity/' + item.cover)"></image>
					<view class="product-info">
						<view class="product-name">{{item.title}}</view>
						<view class="product-desc">
							<text>价值</text>
							<text>￥{{item.price}}</text>
						</view>
						<view class="product-tips">{{item.desc}}</view>
					</view>
				</view>
			</view>
		</view>
			<view class="flex justify-center">
				<view class="beizhu">活动详细：</view>
				<router-link style="color: #4165e0;" to="/pages/other/kefu">联系在线客服</router-link>
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
				proList:[
					{
						title:'苹果 iPad Air5代 256G',
						cover:'deliver-pro-1.png',
						price:5999,
						desc:'下单送666手送'
					},{
						title:'苹果 iPad Pro5代 256G',
						cover:'deliver-pro-2.png',
						price:8999,
						desc:'下单送888手送'
					},{
						title:'HUAWEI Mate x6',
						cover:'deliver-pro-3.png',
						price:15999,
						desc:'下单送1288手送'
					},{
						title:'HUAWEI Mate x6',
						cover:'deliver-pro-4.png',
						price:23999,
						desc:'下单送1588手送'
					}
				]
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
		background: linear-gradient(180deg, #FFDE73 0%,#FFDE73 100%);
		padding-bottom: 100rpx;
	}

	
	.product-main {
		width: calc(100% - 48rpx);
		margin: 60rpx 24rpx;
		border: 4rpx solid #fff;
		background: linear-gradient(135deg, #FCD09D 0%, #FCDB6D 0%, #FFD4C9 69.21%, #FFCEA6 100%);
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
		.product-list{
			background: #fff;
			padding:24rpx;
		}
		.product-item{
			display: flex;
			background:linear-gradient(135deg, #FCD09D 0%, #FFEAA3 0%, #FAEAE6 69.21%, #FDF8F4 100%);
			border-radius:24rpx;
			margin-bottom:24rpx;
			width:100%;
			padding:24rpx;
			.product-cover{
				height: 150rpx;
				width: 150rpx;
				margin-right: 20rpx;
			}
			.product-name{
				font-weight: bold;
				font-size: 32rpx;
			}
			.product-desc{
				color:#6A6B6D;
				margin-top:10rpx;
			}
			.product-tips{
				margin-top:10rpx;
				background: #FFDE73;
				border-radius: 250px;
				display: inline-block;
				padding:6rpx 24rpx;
				font-size: 20rpx;
				font-weight:bold;
				
			}
		}
	}
	.beizhu{
		color: #000;
	}
</style>