<template>
	<view class="container container-page">
		<customNav leftColor="#000" bg="#D7EEFF" title="提现">
			<template slot='right'>
				<router-link class="" to="/otherpages/member/history?type=share">提现记录</router-link>
			</template>
		</customNav>
		<view class="share-mian">
			<image class="share-mian-bg" src="@/static/images/20250227/share-detail-bg.png" mode="widthFix" />

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
					<u-input class="input" v-model="value" dis type="digit" />
				</view>
				<view class="tip">
					满1.00可提现
				</view>
			</view>
			<view class="card-cell-main">

				<view class="card-cell-main-title">提现至</text></view>
				<view class="card-cell">
					<u-image width="60" height="60" border-radius="60" class="avatar" :src="member.avatar">
						<view slot="error">
							<u-image width="60" height="60" border-radius="60" class="avatar"
								src="/static/images/member-default-icon.png"></u-image>
						</view>
					</u-image>
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
	page {
		background-color: #f1f1f1;

	}


	.share-mian {
		margin-top: -120rpx;
		width: 100%;
		position: absolute;
		z-index: -1;
		min-height: 600rpx;

		.share-mian-bg {
			width: 100%;
		}
	}

	.commission-box {
		margin-top: 40rpx;
		text-align: center;

		.commission-title {}

		.commission-number {
			font-weight: bold;
			font-size: 50rpx;
			margin-top: 60rpx;
			margin-bottom: 60rpx;
		}
	}



	.withdraw-main {
		margin: 30rpx 24rpx;
	}

	.withdraw-form {
		padding: 30rpx;
		background-color: #fff;
		border-radius: 20rpx;

		.title {
			font-size: $baseFontSizeSm;
		}

		.transfer-input-wrapper {

			padding: 30rpx 0 18rpx 0;
			border-bottom: 2rpx solid #F0F8FF;

			.unit {
				margin-right: 10rpx;
				color: #333333;
				line-height: 70rpx;
				font-weight: bold;
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
		background-color: #fff;
		border-radius: 20rpx;
		padding: 20rpx 30rpx;
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
		}
	}

	.common-btn {

		background: linear-gradient(93deg, #56D5FF 0%, #7BA2FF 100%);
		box-shadow: inset -3px -2px 4px 0px rgba(58, 3, 208, 0.12), inset 0px 4px 4px 0px rgba(255, 250, 250, 0.25);
		border-radius: 100rpx;
		text-align: center;
		line-height: 100rpx;
		height: 100rpx;
		color: #fff;
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