<template>
	<view class="container container-page">
		<customNav leftColor="#000" bg="none" title="佣金中心">
		</customNav>
		<view class="share-mian">
			<image class="share-mian-bg" src="@/static/images/20250227/share-detail-bg.png" mode="widthFix" />

			<view class="share-member">
				<u-image width="120" height="120" border-radius="120" class="avatar" :src="info.avatar">
					<view slot="error">
						<u-image width="120" height="120" border-radius="120" class="avatar"
							src="/static/images/icon/member-default-icon.png"></u-image>
					</view>
				</u-image>
				<view class="share-member-right">
					<view class="right-top">
						<view class="right-name">{{info.real_name || info.nickname}}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="share-box">
			<view class="share-box-head"  @click="toLevel">
					<view class="head-title">{{info.fee_cash && info.fee_cash > 0?'高级会员':'普通会员'}}</view>
					<view class="head-right">
						<view class="share-level">Lv:{{info.people_level || 1}}</view>
						<view class="head-fee" v-if="info.fee_cash && info.fee_cash > 0">当前返佣:{{info.fee_cash}}%</view>
					</view>
			</view>
			<image class="share-level-icon" @click="toLevel" src="@/static/images/20250227/share-level.png" mode="widthFix"></image>
			<view class="share-box-detail">
				<view class="list-item">
					<view class="item">
						<view class="title">累计佣金(USD)</view>
						<view class="count">{{info.commission_total || 0}}</view>
					</view>
					<view class="item">
						<view class="title">可提现</view>
						<view class="count">{{info.commission_balance || 0}}</view>
					</view>
					<view class="item">
						<router-link class="item-btn" to="/otherpages/share/withdRecord">提现记录</router-link>
					</view>
				</view>
				<view class="list-item">
					<view class="item">
						<view class="title">待入账</view>
						<view class="count">{{info.commission_settlement_price || 0}}</view>
					</view>
					<view class="item">
						<view class="title">提现中(USD)</view>
						<view class="count">{{info.commission_withdrawal || 0}}</view>
					</view>
					<view class="item">
						<view class="title">已提现(USD)</view>
						<view class="count">{{info.commission_withdrawal_complete || 0}}</view>
					</view>
				</view>
				<view class="list-item"  v-if="info.fee_cash && info.fee_cash > 0">
					<view class="item">
						<view class="title">累计返佣(USD)</view>
						<view class="count">{{info.fee_cash_commission_total || 0}}</view>
					</view>
				</view>
				<router-link class="withdrawal-btn" to="/otherpages/share/withd">
					去提现
				</router-link>
			</view>

		</view>

			<view v-if="info && info.fee_cash_tips" class="fee-tips"> {{ info.fee_cash_tips}}</view>

		<diyBottomNav :curIndex="2"></diyBottomNav>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import diyBottomNav from '@/components/diy-bottom-nav/index.vue';
	import {
		mapGetters
	} from "vuex"
	export default {
		components: {
			customNav,
			diyBottomNav
		},
		data() {
			return {
				info: {}
			}
		},
		methods:{
			toLevel(){
				this.$Router.push("/pages/member/shareDetailLevel")
			}
		},

		async onShow() {
			try {
				await this.$u.api.getCommissionDetail().then(res => {
					this.info = res.data || {}
				})
			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>


<style scoped lang="scss">
	.container {
		background-color: #f1f1f1;

	}

	.share-mian {
		margin-top: -120rpx;
		width: 100%;
		position: relative;
		z-index: 1;
		min-height: 600rpx;

		.share-mian-bg {
			width: 100%;
			min-height: 600rpx;
		}

		.share-member {
			position: absolute;
			width: calc(100% - 48rpx);
			top: 170rpx;
			left: 24rpx;
			right: 24rpx;
			display: flex;
			align-items: center;

			.avatar {
				border: 4rpx solid #fff;
			}

			.share-member-right {
				margin-left: 20rpx;

				.right-top {
					display: flex;
					height: 60rpx;
					align-items: center;
				}

				.right-name {
					font-size: $baseFontSize;
					font-weight: bold;
					margin-right: 10rpx;
				}
			}
		}
	}

	.share-box {
		position: fixed;
		z-index: 2;
		top: 420rpx;
		width: calc(100% - 48rpx);
		left: 24rpx;
		border-radius: 24rpx;
		background: linear-gradient(to right, #3E4867 0%, #252C49 100%);
		// overflow: hidden;
		max-height: 800rpx;
		.share-level-icon {
			position: absolute;
			right: 0rpx;
			width:200rpx;
			top: -100rpx;
		}

		.share-box-head {
			display: flex;
			padding: 20rpx 20rpx;
			height:120rpx;
			align-items: center;

			.head-title {
				// width: 200rpx;
				margin-right: 20rpx;
				color:#FEF4CA;
				font-weight: bold;
				font-size: $baseFontSizeLg;
				display: flex;
				align-items: center;
			}
			.head-right{
				// display: flex;
				// align-items: center;
				color: #fff;
			}
			.head-fee{
				margin-top: 6rpx;
				font-size: $baseFontSizeSm;
			}

		}

		.share-box-detail {
			width: 100%;
			padding: 40rpx 20rpx;
			background: #fff;
			
			border-bottom-left-radius: 24rpx;
			border-bottom-right-radius: 24rpx;

			.list-item {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-bottom: 40rpx;

				.item {
					width: 33.33%;

					.title {
						color: #6A6B6D;
						font-size: $baseFontSizeSm;
					}

					.count {
						margin-top: 10rpx;
						font-size: $baseFontSizeLg;
						font-weight: bold;
						color: #000;
					}

					.item-btn {
						padding: 6rpx 20rpx;
						background: linear-gradient(93deg, #56D5FF 0%, #7BA2FF 100%);
						display: inline-block;
						font-size: $baseFontSizeSm;
						border-radius: 60rpx;
						color: #fff;
					}
				}
			}
		}

		.withdrawal-btn {
			background: linear-gradient(93deg, #56D5FF 0%, #7BA2FF 100%);
			box-shadow: inset -3px -2px 4px 0px rgba(58, 3, 208, 0.12), inset 0px 4px 4px 0px rgba(255, 250, 250, 0.25);
			border-radius: 100rpx;
			text-align: center;
			line-height: 100rpx;
			height: 100rpx;
			color: #fff;
			font-size: $baseFontSizeLg;
		}
	}

	.share-level {
		padding: 0 12rpx;
		height: 40rpx;
		line-height: 40rpx;
		border-radius: 24rpx;
		font-size: $baseFontSizeSm;
		display: inline-block;
		color: #fff;
		background: linear-gradient(142deg, #94C9FF 0%, #006EDC 100%);
	}

	.fee-tips {
		width: calc(100% - 40rpx);
		text-align: center;
		margin-top: 520rpx;
		margin-left: 20rpx;
		margin-right: 20rpx;
		color: var(--base-blue);
	}
</style>