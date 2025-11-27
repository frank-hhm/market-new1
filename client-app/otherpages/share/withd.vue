<template>
	<view class="container container-page">
		<customNav leftColor="#000" bg="#F4CF4A" title="提现">
			<template slot='right'>
				<router-link class="" to="/otherpages/member/history?type=share">提现记录</router-link>
			</template>
		</customNav>
		<view class="share-mian">
		</view>
		<view class="commission-box">
			<view class="commission-title">
				可提现金额（USD）
			</view>
			<view class="commission-number">
				{{member.commission_balance || 0}}
			</view>
		</view>


		<view class="withdraw-main">
			<view class="withdraw-form">
				<view class="title">提取金额</view>
				<view class="transfer-input-wrapper flex align-end">
					<text class="unit">$</text>
					<u-input class="input" v-model="value" dis type="digit" placeholder="请输入金额"/>
				</view>
				<view class="tip">
					满1.00可提现
				</view>
			</view>
			<view class="card-cell-main">

				<view class="card-cell-main-title">提现至</text></view>
				<view class="card-cell">
					<view class="card-cell-main-icon"></view>
					<view class="card-cell-main-desc">{{member.username || ''}}</view>
				</view>
			</view>
			<!-- <view v-if="withdrawal_tips" class="withdrawal-tips"> {{withdrawal_tips}}</view> -->
			<view class="common-btn submit-btn" :class="isYes?'':'disabled'" @tap="submit">提现</view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import diyBottomNav from '@/components/diy-bottom-nav/index.vue';
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			diyBottomNav
		},
		data() {
			return {
				isYes: false,
				member: {
					commission_balance: 0.00,
				},
				value: "",
				member_withdrawal_rate: 0,
				bank_status: 1,
				withdrawal_tips: "",
				submitLoading: false
			};
		},
		computed: {
			...mapGetters("user", ["getUserInfo", "getToken"]),
			...mapGetters("app", ["getConfig"]),
			...mapState("app", ["systemConfig"])
		},
		methods: {
			submit() {
				let t = this;
				if (t.submitLoading === true) {
					return false;
				}
				if (!t.value) {
					uni.showToast({
						icon: "none",
						title: "请输入金额"
					})
					return
				}
				let usdtType = '';
				uni.showLoading({
					title: '申请提交中...'
				})
				t.submitLoading = true
				t.$u.api.withdrawApi({
					type: 'commission_balance',
					pay_type: this.bank_status == 1 ? 'offline_bank' : 'offline_usdt',
					usdt_type: usdtType,
					money: t.value
				}).then(res => {
					uni.showToast({
						icon: "none",
						title: res.message
					})
					setTimeout(function() {
						t.value = ''
						t.toInit();
					}, 1000)
					t.submitLoading = false;
				}).catch((err) => {
					t.submitLoading = false;
					uni.showToast({
						icon: "none",
						title: "转出提交失败!" + (err.data.message || '')
					})
				})

			},
			onSelectBank(e) {
				this.bank_status = e
			},
			async toInit() {
				uni.showLoading()
				//websocket
				try {
					await this.$u.api.getCommissionDetail().then(res => {
						uni.hideLoading()
						this.member = res.data
						this.withdrawal_tips = res.data.withdrawal_tips || ''
						this.isYes = true;
					})
				} catch (e) {
					uni.hideLoading()
					//TODO handle the exception
				}
			}
		},
		async onShow() {
			this.toInit();
		},
		async onLoad() {
			try {
				this.member_withdrawal_rate = this.getConfig.member_withdrawal_rate || 0

			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>


<style lang="scss" scoped>
	.container-page {
		background-color: #f1f1f1;
		color: #000;
	}


	.share-mian {
		margin-top: -120rpx;
		width: 100%;
		position: absolute;
		z-index: -1;
		height:400rpx;
		background-color: #F4CF4A;
		.share-mian-bg {
			width: 100%;
		}
		&::after{
			position: absolute;
			content: "";
			height: 260rpx;
			width: 260rpx;
			right: 0;
			bottom: 0;
			background: url("~@/static/images-new1/share/icon-1.png");
			background-size: 100% 100%;
			z-index: 1;
		}
	}

	.commission-box {
		margin-top: 40rpx;
		text-align: left;
		padding:0 30rpx ;
		.commission-title {}
		.commission-number {
			font-weight: bold;
			font-size: 50rpx;
			margin-top:20rpx;
			margin-bottom: 60rpx;
		}
	}



	.withdraw-main {
		padding: 30rpx;
		background-color: #fff;
		border-top-right-radius: 20rpx;
		border-top-left-radius: 20rpx;
		min-height: calc(100vh - 320rpx);
	}

	.withdraw-form {

		.title {
			font-size: $baseFontSize;
			position: relative;
			padding-left: 30rpx;
			font-weight: bold;
			line-height: 30rpx;
			&::after{
				position: absolute;
				content: "";
				width: 6rpx;
				height: 30rpx;
				left: 0;
				top: 0;
				border-radius:3rpx;
				background: #F4CF4A;
			}
		}

		.transfer-input-wrapper {
			margin-top:40rpx;
			padding: 20rpx 30rpx;
			border-bottom: 2rpx solid #F0F8FF;
			border-radius:30rpx;
			background: #F5F5F5;

			.unit {
				margin-right: 10rpx;
				color: #333333;
				line-height: 70rpx;
				font-weight: bold;
				font-size: $baseFontSizeLg;
			}

			.input {
				flex: 1;
				font-size: $baseFontSizeLgXXXX;
				font-weight: 500;
				color: #333333;
			}

			.close-icon {
				width: 48rpx;
				height: 48rpx;
				background: url("~@/static/images/icon/close-icon.png");
				background-size: 100% 100%;
			}
		}

		.tip {
			margin-top: 20rpx;
			font-size: $baseFontSizeSmX;
			color: var(--base-grey);
		}
	}

	.withdraw-type {
		padding: 15rpx 30rpx;

		.label {
			font-size: $baseFontSizeSm;
			color: #333;
		}

		.icon {
			margin: 20rpx;
			width: 72rpx;
			height: 72rpx;
			background: url("~@/static/images/icon/yl-icon.png");
			background-size: 100% 100%;
		}

		.value {
			font-size: $baseFontSizeSm;
			color: #333;
		}
	}

	.submit-btn {
		margin: 60rpx 24rpx;
	}

	.deposit-wrapper-tips {
		display: flex;
		margin: 24rpx;
		height: 76rpx;
		color: var(--base-red);
		border-radius: $baseRadius;

		.deposit-wrapper-tips-item {
			display: flex;
			align-items: center;
			// padding: 0 24rpx;
			justify-content: space-between;
			max-width: 100%;
			white-space: nowrap;
			word-break: keep-all;
			overflow: hidden;
			text-overflow: ellipsis;

			.deposit-wrapper-tips-label {
				color: var(--base-red);
				margin-right: 20rpx;
			}

			.deposit-wrapper-tips-text {
				color: var(--base-red);

				.unit {}

				.number {
					margin: 0 2rpx;
					font-size: $baseFontSize;
					font-weight: 550;
				}
			}

		}
	}

	.card-cell-main {
		background-color: #F5F5F5;
		border-radius: 20rpx;
		padding: 30rpx 30rpx;
		margin-top: 30rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.card-cell {
			display: flex;
			align-items: center;

			// margin: 24rpx;
			.card-cell-main-desc {
				margin-left: 10rpx;
			}
			.card-cell-main-icon{
				width: 26rpx;
				height: 32rpx;
				background: url("~@/static/images-new1/share/icon-2.png");
				background-size: 100% 100%;
			}
		}
	}

	.common-btn {

		background:#F4CF4A;
		border-radius: 100rpx;
		text-align: center;
		line-height: 100rpx;
		height: 100rpx;
		color: #000;
		font-weight: bold;
		font-size: $baseFontSize;
		margin: 60rpx 0;
	}

	.withdrawal-tips {
		width: 100%;
		text-align: center;
		margin-top: 20rpx;
		color: var(--base-blue);
	}
</style>