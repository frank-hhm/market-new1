<template>
	<view class="container container-bg">
		<customNav isCustomTitle hideLeft bg="#f5f5f5">
			<template #left>
				<view class="header-top-left">
					<view class="header-top-left-title">
						Hi,{{member.username!=""?(member.bind_status.value == 1?member.real_name:member.username):"***"}}
					</view>
					<view class="header-top-left-desc">
						加入亮点第 {{member.create_day || 0}} 天
					</view>
				</view>
			</template>
			<template #right>
				<!-- <view class="header-top-right">
					<view class="header-top-right-qrcode"></view>
					<router-link class="header-top-right-setting" to="/otherpages/member/setting"></router-link>
					<view class="header-top-right-message"></view>
				</view> -->
			</template>
		</customNav>
		<view class="header-bg">
			<view v-if="member.id" class="member-info ">
				<view class="member-info-wrapper">
					<view class="member-info-wrapper-title">
						总资产（USD）
					</view>
					<view class="member-info-balance">
						{{(parseFloat(balance) + parseFloat(memberCoin.commission_balance)+ parseFloat(member.follow_balance)+ parseFloat(totalProfit)).toFixed(2) || 0.00}}
					</view>
					<view class="member-info-balance-cny">
						≈{{(parseFloat((balance || 0.00)) * (getConfig.usdt_rate || 7)).toFixed(2)}}cny
					</view>
					<view class="member-level-icon" @tap="switchAccount">{{isMoni ? "模拟账户":"实盘账户"}}</view>
				</view>
			</view>
			<view v-else class="login-main">
				<view class="login-bottom">
					<router-link to="/pages/login/index" class="login-btn">
						去登录
					</router-link>
					<router-link to="/pages/login/register" class="register-btn">
						去开户
					</router-link>
				</view>
			</view>

			<view class="member-coin-main">
				<view class="member-coin">
					<view class="member-coin-item">
						<view class="label">
							<view class="text">合约账户(USD)</view>
						</view>
						<view class="number">
							<view class="text">{{balance || 0.00}}</view>
						</view>
					</view>
					<view class="member-coin-item">
						<view class="label">
							<view class="text">佣金账号(USD)</view>
						</view>
						<view class="number">
							<view class="text">{{memberCoin.commission_balance || 0.00}}</view>
						</view>
					</view>
					<view class="member-coin-item">
						<view class="label">
							<view class="text">跟单账户(USD)</view>
						</view>
						<view class="number">
							<view class="text">{{member.follow_balance || 0.00}}</view>
						</view>
					</view>
					<view class="member-coin-item">
						<view class="label">
							<view class="text">浮动盈亏(USD)</view>
						</view>
						<view class="number">
							<view class="text" :style="{
								color:totalProfitClass
							}">{{totalProfit || 0.00}}</view>
						</view>
					</view>
				</view>

				<view class="member-coin2">
					<view class="member-coin2-item" @tap="toRecharge">
						<view class="member-coin2-item-icon icon-1"></view>
						<view class="member-coin2-item-title">在线存款</view>
					</view>
					<router-link class="member-coin2-item" to="/otherpages/memberCoin/withdraw">
						<view class="member-coin2-item-icon icon-2"></view>
						<view class="member-coin2-item-title">取款申请</view>
					</router-link>
					<router-link class="member-coin2-item" to="/otherpages/member/history?type=order">
						<view class="member-coin2-item-icon icon-3"></view>
						<view class="member-coin2-item-title">交易明细</view>
					</router-link>
					<view class="member-coin2-item" @tap="switchAccount">
						<view class="member-coin2-item-icon icon-4"></view>
						<view class="member-coin2-item-title">{{isMoni?"切换实盘":"切换体验"}}</view>
					</view>
				</view>
			</view>
		</view>


		<view class="bind-card-wrapper "
			v-if="member.bind_status && (member.bind_status.value == 0  || member.bind_status.value == 3) &&!isMoni">
			<view class="bind-card-left">
				<view class="bind-card-icon icon icon-shenhe"></view>
				<text class="bind-card-text">完成实名认证，领取更多豪礼</text>
			</view>
			<router-link class="bind-card-btn" to="/otherpages/member/bindCard">去认证<span
					class="icon icon-yao-1"></span></router-link>
		</view>


		<view class="menu-list-wrapper">
			<view class="menu-list-wrapper-title">
				常用功能
			</view>
			<view class="menu-list">
				<router-link class="menu-item" to="/otherpages/member/info">
					<view class="menu-icon menu-icon-1"></view>
					<view class="menu-tag"
						v-if="member.bind_status && (member.bind_status.value == 0  || member.bind_status.value == 3) &&!isMoni">
						未认证</view>
					<text class="menu-text">个人信息</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/member/harvest">
					<view class="menu-icon menu-icon-2"></view>
					<view class="menu-tag" v-if="!member.usdt_card || !member.bank_code">未绑定</view>
					<text class="menu-text">收款账户</text>
				</router-link>
				<router-link class="menu-item"  to="/otherpages/about/role">
					<view class="menu-icon menu-icon-3"></view>
					<text class="menu-text">交易细则</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/know/index">
					<view class="menu-icon menu-icon-4"></view>
					<text class="menu-text">用户需知</text>
				</router-link>
				<view class="menu-item" @tap="onChangeKefu">
					<view class="menu-icon menu-icon-5"></view>
					<text class="menu-text">专属客服</text>
				</view>
				<router-link class="menu-item" to="/pages/other/kefu">
					<view class="menu-icon menu-icon-6"></view>
					<text class="menu-text">平台客服 </text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/about/index">
					<view class="menu-icon menu-icon-7"></view>
					<text class="menu-text">关于我们</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/member/setting">
					<view class="menu-icon menu-icon-8"></view>
					<text class="menu-text">系统设置</text>
				</router-link>
			</view>
		</view>

		<router-link class="modal-main1" to="/otherpages/share/index">
			<view class="modal-main1-left">
				<view class="modal-main1-left-top">
					<text>加入</text>
					<text class="modal-main1-left-tip">亮点推广</text>
					<text>创建金融脉络</text>
				</view>
				<view class="modal-main1-left-bottom">
					<view class="modal-main1-left-bottom-text">无风险 随时提现</view>
					<view class="modal-main1-btn">
						<text>点击了解详情</text>
						<text class="modal-main1-btn-icon"></text>
					</view>
				</view>
			</view>
			<view class="modal-main1-bg"></view>
		</router-link>

		<view class="menu-list-wrapper">
			<view class="menu-list-wrapper-header">
				<view class="menu-list-wrapper-title">
					亮点推广
				</view>
				<router-link class="menu-list-wrapper-more" to="/otherpages/share/index">
					更多
					<view class="menu-list-wrapper-more-icon"></view>
				</router-link>
			</view>
			<view class="share-main">
				<view class="share-main-item">
					<view class="title">待提现(USD)</view>
					<view class="number number-tip">{{commissionInfo.commission_withdrawal_complete || 0.00}}</view>
				</view>
				<view class="share-main-item">
					<view class="title">累计收入(USD)</view>
					<view class="number">{{commissionInfo.fee_cash_commission_total || 0.00}}</view>
				</view>
				<view class="share-main-item">
					<view class="title">推荐用户(人)</view>
					<view class="number">{{commissionInfo.people || 0}}</view>
				</view>
			</view>
			<view class="menu-list">
				<router-link class="menu-item" to="/otherpages/share/team">
					<view class="menu-icon menu-share-icon-1"></view>
					<text class="menu-text">我的客户</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/member/history?type=share">
					<view class="menu-icon menu-share-icon-2"></view>
					<text class="menu-text">收益明细</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/share/index">
					<view class="menu-icon menu-share-icon-3"></view>
					<text class="menu-text">我要推荐</text>
				</router-link>
				<router-link class="menu-item" to="/otherpages/share/withd">
					<view class="menu-icon menu-share-icon-4"></view>
					<text class="menu-text">我要提现</text>
				</router-link>
			</view>
		</view>


		<u-popup v-model="kefuStatus" mode="center" border-radius="20">
			<view class="kefu-popup-wrapper">
				<view class="kefu-popup-wrapper-title">
					专属务服
				</view>
				<view class="kefu-popup-wrapper-desc">
					您可将专属客服YM号/QQ号复制后并添加好友尊享专属客服1对1的优质服务。
				</view>
				<view class="kefu-list">
					<view class="kefu-item">
						<view class="kefu-icon kefu-wechat"></view>
						<view class="kefu-name">蝙蝠号：{{member.kefu_wechat ||""}}</view>
						<view class="kefu-copy-icon" @tap="copy(member.kefu_wechat || '')"></view>
					</view>
					<view class="kefu-item">
						<view class="kefu-icon kefu-qq"></view>
						<view class="kefu-name">QQ号：{{member.kefu_qq ||""}}</view>
						<view class="kefu-copy-icon" @tap="copy(member.kefu_qq || '')"></view>
					</view>
				</view>
			</view>
		</u-popup>

		<view class="member-logout" @tap="exit">
			<view class="member-logout-icon"></view>
			<view class="member-logout-title">退出登录</view>
		</view>


		<u-modal v-model="tipsShow" :title="tipsTitle" :content='tipsContent' :closeOnClickOverlay="true"
			confirmText="关闭"></u-modal>

		<copyright></copyright>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import copyright from '@/components/common/copyright.vue';
	import {
		mapMutations,
		mapGetters,
		mapState
	} from "vuex"
	import {
		loginqie
	} from "@/utils/initData.js"
	import {
		initDetail
	} from "@/utils/initData.js"
	export default {
		components: {
			customNav,
			copyright
		},
		data() {
			return {
				header_bgPaddingTop: 50,
				header_bgHeight: 420,
				iStatusBarHeight: 0,
				tipsShow: false,
				tipsTitle: "",
				tipsContent: "",
				commissionInfo: {},
				kefuStatus: false
			}
		},
		computed: {
			...mapGetters("member", ["getMemberInfo", "getToken", "getBalance", "getUpColor", "getDownColor"]),
			...mapState("member", ["member", "memberCoin", "isMoni"]),
			...mapGetters("app", ["getConfig"]),
			...mapGetters('wallet', [
				'getTotalMarginUsed',
				"getTotalProfit",
				'getAvailableBalance',
				'getNetValue',
				'getMarginRatio'
			]),
			totalProfitClass() {
				if (this.getTotalProfit > 0) return this.getUpColor;
				if (this.getTotalProfit < 0) return this.getDownColor;
				return '';
			},
			balance() {
				return (parseFloat(this.getBalance) + parseFloat(this.totalProfit)).toFixed(2);
			},
			availableBalance() {
				return this.getAvailableBalance;
			},
			netValue() {
				return this.getNetValue;
			},
			marginRatio() {
				return this.getMarginRatio;
			},
			marginUsed() {
				return this.getTotalMarginUsed;
			},
			totalProfit() {
				const profit = this.getTotalProfit || 0;
				return `${profit >= 0 ? '+' : ''}${parseFloat(profit).toFixed(2)}`;
			},

		},

		async onLoad() {
			// 获取高度
			this.iStatusBarHeight = uni.getSystemInfoSync().statusBarHeight
			/*#ifdef APP-PLUS*/
			this.header_bgHeight = this.header_bgHeight + this.iStatusBarHeight;
			this.header_bgPaddingTop = this.header_bgPaddingTop + this.iStatusBarHeight;
			/*#endif*/
		},
		onShow() {
			initDetail()
			this.initCommission()
		},
		methods: {
			...mapMutations("member", ["logout"]),
			onShowTips(title, content) {
				this.tipsTitle = title;
				this.tipsContent = content;
				this.tipsShow = true;
			},
			switchAccount() {
				loginqie()
			},
			onChangeKefu() {
				this.kefuStatus = !this.kefuStatus
			},

			toRecharge() {
				if (this.isMoni) {
					uni.showToast({
						title: "模拟账号禁用",
						icon: "none"
					})
					return false;
				} else if (this.member.bind_status && (this.member.bind_status.value == 0 || this.member.bind_status
						.value == 3)) {
					this.$Router.push("/otherpages/member/bindCard")
				} else if (this.member.bind_status && this.member.bind_status.value == 2) {
					uni.showToast({
						title: "实名认证正在审核中...请稍后再试！",
						icon: "none"
					})
					return false;
				} else {
					this.$Router.push("/otherpages/memberCoin/recharge")
				}
			},
			exit() {
				this.logout()
				this.$Router.replace("/pages/login/index")
			},
			initCommission() {
				this.$u.api.getCommissionDetail().then(res => {
					this.commissionInfo = res.data || {}
				})
			},
			copy(value) {
				uni.setClipboardData({
					data: value,
					success: function() {
						uni.showToast({
							icon: "success",
							title: "复制成功"
						})
					}
				});
			},
		}
	}
</script>

<style lang="scss" scoped>
	.container {
		background: #f5f5f5;

		.header-top-left {
			display: flex;
			align-items: center;

			.header-top-left-title {
				font-size: $baseFontSizeLg;
				font-weight: bold;
			}

			.header-top-left-desc {
				color: var(--base-grey);
				font-size: $baseFontSizeSm;
				margin-left: 10rpx;
			}

			// qrcode-icon.png
		}

		.header-top-right {
			display: flex;
			align-items: center;

			.header-top-right-qrcode {
				height: 36rpx;
				margin-left: 20rpx;
				width: 36rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/member/qrcode-icon.png")
			}

			.header-top-right-setting {
				height: 36rpx;
				margin-left: 20rpx;
				width: 36rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/member/setting-icon.png")
			}

			.header-top-right-message {
				height: 36rpx;
				margin-left: 20rpx;
				width: 36rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/member/message-icon.png")
			}
		}

		.header-bg {
			position: relative;
			background-image: linear-gradient(to bottom, #3E3331 230rpx, #fff 230rpx);
			margin: 0 20rpx 20rpx;
			border-radius: 20rpx;
			display: block;

			.member-info {
				display: flex;
				justify-content: space-between;
				position: relative;
				height: 230rpx;
				padding: 20rpx;

				.member-info-wrapper {
					color: #fff;
				}

				.member-info-balance {
					margin-top: 10rpx;
					font-size: 40rpx;
				}

				.member-info-balance-cny {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
				}

				.member-level-icon {
					position: absolute;
					right: 20rpx;
					bottom: 0;
					height: 202rpx;
					margin-left: 20rpx;
					width: 202rpx;
					text-align: center;
					line-height: 162rpx;
					background-size: 100% auto;
					background-image: url("~@/static/images-new1/common/test-2.png");
					background-position: center;
					background-repeat: no-repeat;

				}
			}

			.member-coin-main {
				background-color: #fff;
				margin-top: -40rpx;
				z-index: 9;
				padding: 30rpx;
				border-top-left-radius: 30rpx;
				border-top-right-radius: 30rpx;
				border-bottom-left-radius: 20rpx;
				border-bottom-right-radius: 20rpx;
				color: #000;

				.member-coin {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;

					.member-coin-item {
						width: 50%;
						margin-top: 20rpx;
						text-align: left;
						position: relative;
						z-index: 999;

						.number {
							margin-top: 10rpx;
							font-weight: 550;
						}

						.label {
							font-size: $baseFontSizeSm;
						}
					}
				}

				.member-coin2 {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-top: 40rpx;

					.member-coin2-item-icon {
						margin: 0 auto;
						width: 80rpx;
						height: 80rpx;
						background-size: 100% 100%;

						&.icon-1 {
							background-image: url("~@/static/images-new1/member/coin2-icon-1.png");
						}

						&.icon-2 {
							background-image: url("~@/static/images-new1/member/coin2-icon-2.png");
						}

						&.icon-3 {
							background-image: url("~@/static/images-new1/member/coin2-icon-3.png");
						}

						&.icon-4 {
							background-image: url("~@/static/images-new1/member/coin2-icon-4.png");
						}
					}

					.member-coin2-item-title {
						margin-top: 10rpx;
						font-size: $baseFontSize;
					}

				}

			}
		}



		.login-main {
			padding-top: 20rpx;
			text-align: center;
			height: 210rpx;

			.login-bottom {
				display: flex;
				margin-top: 30rpx;
				justify-content: center;

				.login-btn {
					border: 1rpx solid #fff;
					width: 198rpx;
					color: #fff;
					height: 68rpx;
					border-radius: 68rpx;
					line-height: 68rpx;
					margin-right: 40rpx;
				}

				.register-btn {
					width: 200rpx;
					background: #fff;
					color: #000;
					height: 70rpx;
					border-radius: 70rpx;
					line-height: 70rpx;

				}
			}
		}



		.bind-card-wrapper {
			margin: 20rpx 24rpx 0;
			padding: 3rpx 20rpx;
			height: 66rpx;
			border-radius: 16rpx;
			background: #FEDEDE;
			display: flex;
			align-items: center;
			justify-content: space-between;

			.bind-card-left {
				display: flex;
				align-items: center;
			}

			.bind-card-icon {
				width: 36rpx;
				height: 36rpx;
				font-size: $baseFontSizeLgXX;
				margin-right: 14rpx;
				color: $baseRedColor;
			}

			.bind-card-text {
				font-size: $baseFontSize;
				color: $baseRedColor;
			}

			.bind-card-btn {
				font-size: $baseFontSize;
				display: flex;
				align-items: center;
				color: $baseRedColor;
				position: relative;
			}
		}


		.coin-cell-main {
			display: flex;
			justify-content: space-around;
			margin: 20rpx 0;
			background: #fff;
			padding: 40rpx 20rpx;

			.coin-cell-item {
				width: 25%;
				text-align: center;

				.coin-cell-item-icon {
					margin: 0 auto;
					height: 60rpx;
					width: 60rpx;
					background-size: 100% 100%;

					&.icon-1 {
						background-image: url("~@/static/images/icon/coin-1.png");
					}

					&.icon-2 {
						background-image: url("~@/static/images/icon/coin-2.png");
					}

					&.icon-3 {
						background-image: url("~@/static/images/icon/coin-3.png");
					}

					&.icon-4 {
						background-image: url("~@/static/images/icon/coin-4.png");
					}
				}
			}
		}


		.menu-list-wrapper {
			background-color: #fff;
			margin: 20rpx;
			border-radius: 20rpx;
			padding: 30rpx;

			.menu-list-wrapper-header {
				display: flex;
				justify-content: space-between;
				align-items: center;

				.menu-list-wrapper-more {
					display: flex;
					align-items: center;
					font-size: $baseFontSizeSm;
				}

				.menu-list-wrapper-more-icon {
					height: 16rpx;
					margin-left: 10rpx;
					width: 16rpx;
					background-size: auto 100%;
					background-image: url("~@/static/images-new1/member/main1-icon-right.png");
					background-repeat: no-repeat;
				}
			}

			.menu-list-wrapper-title {
				color: #000;
			}

			.menu-title {
				font-size: $baseFontSize;
				font-weight: 500;
				color: var(--base-grey);
			}

			.menu-list {

				display: flex;
				flex-wrap: wrap;
				align-items: center;
				margin-top: 20rpx;

				.menu-item {
					width: 25%;
					position: relative;
					padding: 0 20rpx;
					text-align: center;
					padding: 20rpx 0;

					.menu-tag {
						position: absolute;
						background: red;
						color: #fff;
						font-size: $baseFontSizeSmX;
						border-radius: $baseRadius;
						padding: 0 5rpx;
						top: 0;
						right: 0;
					}
				}

				.menu-icon {
					height: 48rpx;
					width: 48rpx;
					margin: 0 auto 10rpx;
					background-size: 100% 100%;

					&.menu-icon-1 {
						background-image: url("~@/static/images-new1/member/menu-icon-1.png");
					}

					&.menu-icon-2 {
						background-image: url("~@/static/images-new1/member/menu-icon-2.png");
					}

					&.menu-icon-3 {
						background-image: url("~@/static/images-new1/member/menu-icon-3.png");
					}

					&.menu-icon-4 {
						background-image: url("~@/static/images-new1/member/menu-icon-4.png");
					}

					&.menu-icon-5 {
						background-image: url("~@/static/images-new1/member/menu-icon-5.png");
					}

					&.menu-icon-6 {
						background-image: url("~@/static/images-new1/member/menu-icon-6.png");
					}

					&.menu-icon-7 {
						background-image: url("~@/static/images-new1/member/menu-icon-7.png");
					}

					&.menu-icon-8 {
						background-image: url("~@/static/images-new1/member/menu-icon-8.png");
					}

					&.menu-share-icon-1 {
						background-image: url("~@/static/images-new1/member/menu-share-icon-1.png");
					}

					&.menu-share-icon-2 {
						background-image: url("~@/static/images-new1/member/menu-share-icon-2.png");
					}

					&.menu-share-icon-3 {
						background-image: url("~@/static/images-new1/member/menu-share-icon-3.png");

					}

					&.menu-share-icon-4 {
						background-image: url("~@/static/images-new1/member/menu-share-icon-4.png");

					}

					&.menu-icon-help {

						background-image: url("~@/static/images/icon/menu-icon-help.png");
					}
				}

				.menu-text {
					font-size: $baseFontSize;
					color: #000;
					font-weight: 500;
				}
			}

			.share-main {
				background: #F8F8F8;
				margin: 20rpx 0;
				border-radius: 10rpx;
				padding: 30rpx;
				display: flex;
				justify-content: space-between;

				.share-main-item {
					width: 33.3333%;
					color: #000;

					.title {
						font-size: $baseFontSizeSm;
					}

					.number {
						font-size: $baseFontSizeLg;
						margin-top: 10rpx;
					}

					.number-tip {
						color: #FF5733;
					}
				}
			}
		}

		.modal-main1 {
			padding: 30rpx;
			margin: 20rpx;
			background: #fff;
			border-radius: 20rpx;
			height: 160rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.modal-main1-left {
				width: calc(100% - 160rpx);

				.modal-main1-left-top {
					font-weight: 550;
				}

				.modal-main1-left-tip {
					color: #F26444;
					margin: 0 10rpx;
				}

				.modal-main1-left-bottom {
					display: flex;
					align-items: center;
					margin-top: 20rpx;

					.modal-main1-left-bottom-text {
						color: var(--base-grey);
					}

					.modal-main1-btn {
						margin-left: 20rpx;
						color: #000;
						height: 40rpx;
						line-height: 40rpx;
						border-radius: 20rpx;
						padding: 0 20rpx;
						background: #F4CF4A;
						font-size: $baseFontSizeSmX;
					}
				}
			}

			.modal-main1-bg {
				width: 140rpx;
				height: 160rpx;
				background-size: 100% auto;
				background-image: url("~@/static/images-new1/member/main1-bg.png");
			}
		}

		.member-logout {
			padding: 30rpx;
			margin: 20rpx;
			background: #fff;
			border-radius: 20rpx;
			display: flex;
			align-items: center;
			justify-content: center;

			.member-logout-icon {
				width: 32rpx;
				height: 32rpx;
				background-size: 100% auto;
				background-image: url("~@/static/images-new1/member/logout.png");
				margin-right: 10rpx;
			}
		}
	}

	.kefu-popup-wrapper {
		width: 560rpx;
		padding: 30rpx 30rpx 50rpx;
		text-align: center;

		.kefu-popup-wrapper-title {
			font-size: $baseFontSizeLg;
			font-weight: bold;
		}

		.kefu-popup-wrapper-desc {
			margin-top: 20rpx;
			color: var(--base-grey);
			font-size: $baseFontSizeSm;
		}

		.kefu-item {
			align-items: center;
			display: flex;
			justify-content: space-between;
			margin-top: 40rpx;
			text-align: left;

			.kefu-name {
				width: calc(100% - 80rpx - 80rpx);
				white-space: nowrap;
				/* 不换行 */
				overflow: hidden;
				/* 超出隐藏 */
				text-overflow: ellipsis;
				/* 显示省略号 */
			}

			.kefu-copy-icon {
				width: 60rpx;
			}
		}

		.kefu-wechat {
			width: 60rpx;
			height: 60rpx;
			background-size: 100% 100%;
			background-image: url("~@/static/images-new1/common/kefu.jpg");
			border-radius: 10rpx;
		}

		.kefu-qq {
			width: 60rpx;
			height: 60rpx;
			background-size: 100% 100%;
			background-image: url("~@/static/images-new1/common/kefu-qq.png");
		}

		.kefu-copy-icon {
			width: 40rpx;
			height: 40rpx;
			background-size: auto 100%;
			background-image: url("~@/static/images-new1/common/copy.png");
			background-repeat: no-repeat;
			background-position: center;
		}
	}
</style>