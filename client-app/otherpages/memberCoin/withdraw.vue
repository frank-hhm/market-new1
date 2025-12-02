<template>
	<view class="container container-page">
		<customNav title="取款申请" leftColor="#000000">
		</customNav>
		<view class="banner">
			<view class="banner-box">
				<view class="banner-box-title">可取款资产</view>
				<view class="banner-box-count">{{memberCoin.balance || 0}}<text class="banner-box-unit">usd</text>
				</view>
				<view>≈{{((systemConfig.usdt_out_rate ||  7 ) * memberCoin.balance).toFixed(2)}}cny</view>
			</view>
		</view>
		<view class="withdraw-main">
			<view class="withdraw-form">
				<view class="title">提取金额</view>
				<view class="transfer-input-wrapper flex align-end">
					<text class="unit">$</text>
					<u-input class="input" v-model="value" dis type="digit" placeholder="请输入取款金额" :customStyle="{
						fontSize:'40rpx'
					}" />
				</view>
			</view>
			<view class="charge-money-desc">
				今天汇率<text class="charge-money-desc-text">1</text>美元=<text
					class="charge-money-desc-text">{{systemConfig.usdt_out_rate || 7}}</text>人民币
			</view>
			<view class="deposit-tips">
				<view class="deposit-tips-item">
					<view class="deposit-tips-label">预估到账</view>
					<view class="deposit-tips-text" v-if="bank_status == 1"><text
							class="number">{{value ? (value * systemConfig.usdt_out_rate).toFixed(2) : 0}}</text><text
							class="unit">人民币</text>
					</view>
					<view class="deposit-tips-text" v-if="bank_status == 2"><text
							class="number">{{value ? value : 0}}</text><text class="unit">USDT</text></view>
				</view>
			</view>
		</view>
		<view class="card-cell-main">
			<view class="card-cell-main-head">
				请选择收款方式
			</view>
			<view class="card-cell">
				<view class="card-cell-item" @tap="onSelectBank(1)">
					<view class="card-cell-item-left brank">
						<view class="icon"></view>
						<view class="icon-detail">
							<text class="value">银行卡</text>
							<view class="tip">
								手续费{{member_withdrawal_rate}}%
							</view>
						</view>
					</view>
					<view class="card-cell-item-right" :class="bank_status == 1?'active':''"></view>
				</view>

				<view class="card-cell-item" @tap="onSelectBank(2)">
					<view class="card-cell-item-left usdt">
						<view class="icon"></view>
						<view class="icon-detail">
							<text class="value">USDT - TRC20</text>
							<view class="tip">
								手续费{{member_usdt_withdrawal_rate}}%
							</view>
						</view>
					</view>
					<view class="card-cell-item-right" :class="bank_status == 2?'active':''"></view>
				</view>
				<view class="card-cell-item" v-if="member_alipay_withdrawal_status" @tap="onSelectBank(4)">
					<view class="card-cell-item-left alipay">
						<view class="icon"></view>
						<view class="icon-detail">
							<text class="value">支付宝</text>
							<view class="tip">
								手续费{{member_usdt_withdrawal_rate}}%
							</view>
						</view>
					</view>
					<view class="card-cell-item-right" :class="bank_status == 4?'active':''"></view>
				</view>
			</view>
		</view>
		<view class="common-btn submit-btn" :class="isYes?'':'disabled'" @tap="onSubmit">提交申请</view>
		<view class="tip-wrapper">
			<view class="tip-head">注意事项</view>
			<view class="tip-item">1.非港币美元币种是商家合作取款，商家收到由本平台结算的港币美元资金，兑换相应币种转账至您的收款账户。</view>
			<view class="tip-item">2.取款到账时间1个工作日，约24小时内到达您的收款账户。</view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {
				isYes: false,
				value: "",
				member_withdrawal_rate: 0,
				member_usdt_withdrawal_rate: 0,
				member_alipay_withdrawal_status: 0,
				bank_status: 1,
				submitLoading: false
			};
		},
		computed: {
			...mapGetters("member", ["getToken"]),
			...mapGetters("app", ["getConfig"]),
			...mapState("app", ["systemConfig"]),
			...mapState("member", ["memberCoin", "member"])
		},
		methods: {
			onSubmit() {
				let t = this;
				if (t.submitLoading === true) {
					return false;
				}
				if (t.bank_status == 1 && !t.isYes) {
					uni.showToast({
						icon: "none",
						title: "请先绑定银行卡"
					})
					return false;
				}
				if (t.bank_status == 2 && !t.member.usdt_card) {
					uni.showToast({
						icon: "none",
						title: "请先绑定USDT-TRC20地址"
					})
					return false;
				}
				if (t.bank_status == 3 && !t.member.usdt_bep_card) {
					uni.showToast({
						icon: "none",
						title: "请先绑定USDT-BRP20地址"
					})
					return false;
				}
				if (t.bank_status == 4 && !t.member.alipay_card) {
					uni.showModal({
						title: '提示',
						content: '请先绑定支付宝',
						cancelText: '取消',
						confirmText: '去绑定',
						success: function(res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/otherpages/member/bindAlipayCard'
								})
							}
						}
					})
					return false;
				}
				if (!t.value) {
					uni.showToast({
						icon: "none",
						title: "请输入金额"
					})
					return
				}
				try {


					let usdtType = '',
						payType = "";
					if (t.bank_status == 2) {
						payType = 'offline_usdt'
						usdtType = 'usdt'
					} else if (t.bank_status == 3) {
						payType = 'offline_usdt'
						usdtType = 'usdt_bep'
					} else if (t.bank_status == 4) {
						payType = 'offline_alipay'
					} else {
						payType = 'offline_bank'
					}
					t.submitLoading = true
					uni.showLoading({
						title: '正在提交中...'
					})
					t.$u.api.withdrawApi({
						pay_type: payType,
						usdt_type: usdtType,
						money: t.value
					}).then(res => {
						setTimeout(() => {
							uni.showToast({
								icon: "none",
								title: res.message
							})
							t.value = ''
							t.toInit();
							t.submitLoading = false;
						}, 1000)
					}).catch((err) => {
						setTimeout(() => {
							t.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: "出金申请提交失败!" + (err.data.message || '')
							})
						}, 1000)
					})
				} catch (e) {
					//TODO handle the exception
				}

			},
			onSelectBank(e) {
				this.bank_status = e
			},
			toInit() {
				if (this.member.bank_code == '' && this.member.usdt_card == '' && this.member
					.usdt_bep_card == '') {
					uni.showModal({
						title: '提示',
						content: '请先绑定银行卡或绑定USDT地址',
						cancelText: '银行卡',
						confirmText: 'USDT',
						success: function(res) {
							if (res.confirm) {
								uni.navigateTo({
									url: '/pages/member/usdtCard'
								})
							} else if (res.cancel) {
								uni.navigateTo({
									url: '/pages/member/bankCard'
								})
							}
						}
					})
					return false;
				}
				this.isYes = true;
			}
		},
		onShow() {
			this.toInit();
		},
		onLoad() {
			try {
				this.member_withdrawal_rate = this.getConfig.member_withdrawal_rate || 0
				this.member_usdt_withdrawal_rate = this.getConfig.member_usdt_withdrawal_rate || 0

			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>

<style lang="scss" scoped>
	.container {
		background: #f5f5f5;

		.banner {
			width: calc(100% - 40rpx);
			margin: 20rpx;
			padding: 30rpx;
			background-color: #F4CF4A;
			display: flex;
			align-items: center;
			border-radius: 20rpx;
			overflow: hidden;

			.banner-box {

				.banner-box-title {
					font-size: $baseFontSizeDefault;
					color: #000;
				}

				.banner-box-unit {
					margin-left: 10rpx;
					font-size: $baseFontSizeLg;
				}

				.banner-box-count {
					margin-top: 10rpx;
					color: #000;
					font-size: $baseFontSizeLgXXXX;
					font-weight: 550;
					display: flex;
					align-items: baseline;
				}
			}
		}

		.withdraw-main {
			background-color: #fff;
			border-radius: 20rpx;
			margin: 20rpx;
			padding: 30rpx 0;


		}

		.withdraw-form {
			padding: 0 30rpx;
			border-bottom: 1rpx solid #F5F6F8;

			.title {
				color: #000;
			}

			.transfer-input-wrapper {

				padding: 30rpx 0 18rpx 0;

				.unit {
					margin-right: 10rpx;
					line-height: 70rpx;
					font-weight: bold;
				}

				.input {
					flex: 1;
					font-size: $baseFontSizeLgXXXX;
					font-weight: 500;
					color: var(--base-grey);
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
				font-size: $baseFontSizeSm;
				color: var(--base-grey);
			}
		}

		.withdraw-type {
			padding: 15rpx 30rpx;
			background-color: #fff;
			border-radius: 20rpx;

			.label {
				font-size: $baseFontSizeSm;
				color: var(--base-grey);
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
				color: var(--base-grey);
			}
		}

		.charge-money-desc {
			padding: 30rpx;

			.charge-money-desc-text {
				color: var(--base-red);
			}

			border-bottom: 1rpx solid #F5F6F8;
		}

		.deposit-tips {
			display: flex;
			padding: 0 30rpx;
			height: 76rpx;
			border-radius: $baseRadius;

			.deposit-tips-item {
				display: flex;
				align-items: center;
				// padding: 0 24rpx;
				justify-content: space-between;
				max-width: 100%;
				white-space: nowrap;
				word-break: keep-all;
				overflow: hidden;
				text-overflow: ellipsis;

				.deposit-tips-label {
					color: #000;
					margin-right: 20rpx;
				}

				.deposit-tips-text {

					.unit {}

					.number {
						color: var(--base-red);
						margin: 0 2rpx;
						font-size: $baseFontSizeLg;
						font-weight: 550;
					}
				}

			}
		}
	}

	.card-cell-main {
		padding: 30rpx;
		background-color: #fff;
		border-radius: 20rpx;
		margin: 20rpx;

		.card-cell-main-head {
			color: #000;
		}

		.card-cell {

			margin-top: 40rpx;

			.card-cell-item {
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 20rpx;
				margin-bottom: 20rpx;
				height: 120rpx;
				line-height: 120rpx;
				border-bottom: 1rpx solid #F5F6F8;

				&:last-child {
					border-bottom: none;
				}

				.card-cell-item-left {
					display: flex;
					align-items: center;
					color: #000;

					.icon-detail {
						line-height: 30rpx;
						font-size: $baseFontSizeDefault;

						.tip {
							font-size: $baseFontSizeSm;
							color: var(--base-grey);
							margin-top: 10rpx;
						}
					}

					&.brank .icon {
						height: 80rpx;
						width: 80rpx;
						margin-right: 20rpx;
						background-image: url('~@/static/images-new1/common/icon-bank2.png');
						background-size: 100% 100%;
					}

					&.usdt .icon {
						height: 80rpx;
						width: 80rpx;
						margin-right: 20rpx;
						background-image: url('~@/static/images/icon/usdt-icon.png');
						background-size: 100% 100%;
					}
				}

				.card-cell-item-right {
					width: 60rpx;
					height: 60rpx;

					&.active {
						border: none;
						background: url("~@/static/images-new1/common/icon-select-yes.png");
						background-size: 100% 100%;
					}
				}
			}
		}
	}

	.submit-btn {
		margin: 40rpx 20rpx;
		border-radius: 96rpx;
	}

	.tip-wrapper {
		margin: 20rpx;
		background-color: #fff;
		padding: 30rpx;
		border-radius: 20rpx;
		color: #000;

		.tip-head {
			font-size: $baseFontSize;
		}

		.tip-item {
			margin-top: 20rpx;
			font-size: $baseFontSizeSm;
		}

		.tip-text {
			font-size: $baseFontSizeSm;
			color: #999999;
		}
	}
</style>