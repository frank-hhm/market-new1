<template>
	<view class="header-main">
		<view class="header-main-top">
			<view class="header-main-top1">
				<view class="title">理财跟单资产</view>
				<view class="">
					<text class="header-main-top1-number">{{member.follow_balance || 0.00}}</text>
					<text class="header-main-top1-unit">usd</text>
				</view>
				<view>≈{{((member.follow_balance || 0.00) * systemConfig.usdt_rate).toFixed(2)}} cny</view>
			</view>
			
			<router-link to="/pages/other/kefu" class="apply-person">
				<text>成为交易员</text>
				<text class="apply-person-icon"></text>
			</router-link>
	
			<view class="header-main-top2">
				<view class="header-main-top2-item">
					<view class="title">
						今日预估收益(usd)
					</view>
					<view class="header-main-top2-number">
						{{member.follow_revenue || 0.00}}
					</view>
				</view>
				<view class="header-main-top2-item header-main-top2-item2">
					<view class="title">
						累计收益(usd)
					</view>
					<view class="header-main-top2-number">
						{{memberCoin.follow_total_revenue ||0.00}}
					</view>
				</view>
			</view>
		</view>
	</view>
	
</template>

<script>
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
		},
		data() {
			return {
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
			...mapGetters("member", ["getBalance"]),
			...mapState("app", ["systemConfig"]),
			...mapState("member", ["member","memberCoin"]),
			balance() {
				return this.getBalance;
			},
		},
	}
</script>

<style lang="scss">
	
	.header-main {
		margin: 20rpx;
		background: #fff;
		border-radius: 20rpx;
		overflow: hidden;
	
		.header-main-top {
			background: #F4CF4A;
			overflow: hidden;
			border-radius: 20rpx;
			position: relative;
			.title {
				color: #000;
			}
		}
		.apply-person{
			position: absolute;
			background: linear-gradient(108.65deg, rgba(222, 199, 149, 1) 0%, rgba(255, 247, 210, 1) 100%);
			top: 40rpx;
			right: 0;
			height: 64rpx;
			line-height: 64rpx;
			padding: 0 30rpx;
			border-top-left-radius: 64rpx;
			border-bottom-left-radius: 64rpx;
			display: flex;
			align-items: center;
			color: #7B643A;
			.apply-person-icon{
					width: 20rpx;
					height: 16rpx;
					background-size: auto 100%;
					background-image: url("~@/static/images-new1/follow/icon-right.png");
					background-repeat: no-repeat;
					background-position: center;
					margin-left: 10rpx;
			}
		}
	
		.header-main-top1 {
			padding: 30rpx;
			color: #000;
	
			.header-main-top1-number {
				font-size: $baseFontSizeLgXXX;
				margin-right: 10rpx;
				font-weight: bold;
			}
		}
	
		.header-main-top2 {
			display: flex;
			justify-content: space-between;
				text-align: center;
			background: #EAC538;
			padding: 30rpx;
	
			.header-main-top2-item {
				width: 50%;
			}
	
			.header-main-top2-item2 {
			}
	
			.header-main-top2-number {
				color: #000;
				font-weight: bold;
				font-size: $baseFontSizeLg;
			}
		}
	
		.header-main-bottom {
			display: flex;
			color: #000;
			padding: 20rpx 30rpx;
			justify-content: space-between;
	
			.transfer-btn {
				width: calc(50% - 20rpx);
				height: 80rpx;
				line-height: 80rpx;
				background: #F4CF4A;
				text-align: center;
				border-radius: 80rpx;
			}
	
			.transfer-out-btn {
				width: calc(50% - 20rpx);
				height: 80rpx;
				line-height: 80rpx;
				background: #EEEEEE;
				text-align: center;
				border-radius: 80rpx;
			}
		}
	
	}
</style>