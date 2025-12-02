<template>
	<view class="container-page">
		<view class="custom-nav">
			<view class="custom-navbar-s" v-if="iStatusBarHeight > 0" :style="{
				height:'calc('+iStatusBarHeight + 'px'+')'
			}">
			</view>
			<view class="custom-nav-main">
				<view class="custom-left" @tap="onChangeAccount">
					<view class="custom-name">{{getConfig.system_name ||""}}{{isLogin || !isMoni ? "真实账户":'模拟账户'　}}</view>
					<view class="custom-name-icon"></view>
				</view>
				<router-link class="custom-kefu" to="/pages/other/kefu"></router-link>
			</view>
		</view>
		<u-popup v-model="accountStatus" mode="top" border-radius="20">
			<view class="account-popup-wrapper">
				<view class="account-list">
					<view class="account-item" @tap="onSelectMoni">
						<view class="account-item-icon"></view>
						<view class="account-item-name">{{getConfig.system_name ||""}}模拟账户</view>
						<view class="account-item-btn" v-if="!isMoni">切换</view>
						<view class="charge-select-icon" v-else></view>
					</view>
					<view class="account-item" @tap="onSelectMoni">
						<view class="account-item-icon"></view>
						<view class="account-item-name">{{getConfig.system_name ||""}}真实账户</view>
						<view class="account-item-btn"  v-if="isMoni">切换</view>
						<view class="charge-select-icon" v-else></view>
					</view>
				</view>
			</view>
		</u-popup>
		<view class="charge-system-main" v-if="!isLogin">
			<view class="charge-system-top">
				<view class="charge-system-top-header">
					<image class="charge-system-top-logo" :src="getConfig.system_logo ||''"></image>
					<view class="charge-system-top-name">{{getConfig.system_name ||""}}</view>
				</view>
				<view class="charge-system-top-desc">值得信赖的期货合约交易平台</view>
			</view>
			<view class="charge-system-box">
				<view class="charge-system-box-item">
					<view class="icon box-item-icon-1"></view>
					<view class="right">
						<view class="title">10年品牌</view>
						<view class="desc">监管牌照许可证号BJE645</view>
					</view>
				</view>
				<view class="charge-system-box-item">
					<view class="icon box-item-icon-2"></view>
					<view class="right">
						<view class="title">品种丰富</view>
						<view class="desc">100+热门交易品种</view>
					</view>
				</view>
				<view class="charge-system-box-item">
					<view class="icon box-item-icon-3"></view>
					<view class="right">
						<view class="title">快捷存取</view>
						<view class="desc">存款15分钟，取款1个工作日</view>
					</view>
				</view>
				<view class="charge-system-box-item">
					<view class="icon box-item-icon-3"></view>
					<view class="right">
						<view class="title">尊贵服务</view>
						<view class="desc">7*24h在线服务，社群交流。</view>
					</view>
				</view>
			</view>

			<router-link class="charge-system-open-member" to="/pages/login/register">极速开户</router-link>
			<router-link class="charge-system-login" to="/pages/login/index">已有账户?马上登录</router-link>
		</view>

		<view v-if="isLogin" class="charge-container">
			<u-tabs class="tabs" height="84" :list="getTabList" :current="current" @change="change" bgColor="#f5f5f5" bold
				activeColor="#000000" inactiveColor="#000000" :showBar="false" :offset="[20,0]">
			</u-tabs>

			<view class="member-charge-main">
				<view class="member-charge-main-box">
					<view class="member-charge-main-top">
						<view class="member-charge-main-top-left">
							<view class="member-charge-main-top-title">账户余额（USD）</view>
							<view class="member-charge-main-top-number">{{getBalance || 0.00}}</view>
						</view>
						<view class="member-charge-main-top-right">
							<view class="member-charge-main-top-title">实时盈亏（USD）</view>
							<view class="member-charge-main-top-number">{{totalProfit || 0.00}}</view>
						</view>
					</view>
					<view class="member-charge-main-bottom">
						<view class="member-charge-main-bottom-item">
							<view>可用</view>
							<view>{{availableBalance || 0.00}}</view>
						</view>
						<view class="member-charge-main-bottom-item">
							<view>保证金比例<text></text></view>
							<view>{{marginRatio || 0.00}}</view>
						</view>
						<view class="member-charge-main-bottom-item">
							<view>占用</view>
							<view>{{marginUsed || 0.00}}</view>
						</view>
						<view class="member-charge-main-bottom-item">
							<view>账户净值</view>
							<view>{{netValue || 0.00}}</view>
						</view>
					</view>
				</view>


				<view class="member-charge-btn-box">
					<view class="member-charge-btn-box-item" @tap="toRecharge"><text class="item-icon icon-1"></text>存款
					</view>
					<router-link class="member-charge-btn-box-item" to="/otherpages/member/history?type=water"><text
							class="item-icon icon-2"></text>钱包明细</router-link>
					<router-link class="member-charge-btn-box-item" to="/otherpages/member/history?type=order"><text
							class="item-icon icon-3"></text>历史平仓</router-link>
				</view>
			</view>


			<scroll-view scroll-y="true" class="scroll-view">
				<template v-if="current==0">
					<orderHold></orderHold>
				</template>
				<template v-if="current==1">
					<orderRobot></orderRobot>
				</template>
				<template v-if="current==2">
					<orderComplate :list="complateList"></orderComplate>
				</template>
			</scroll-view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import orderHold from '@/components/order/order-hold.vue';
	import orderRobot from '@/components/order/order-robot.vue';
	import orderComplate from '@/components/order/order-complate.vue';
	import walletCharge from '@/components/wallet/charge.vue';
	import {
		createWebsocket,
		sendSocketMessage
	} from '@/websocket/index.js'
	import {
		initDetail,
		loginqie
		
	} from "@/utils/initData.js"
	import {
		mapGetters,
		mapState,
		mapMutations
	} from "vuex"
	export default {
		components: {
			customNav,
			orderHold,
			walletCharge,
			orderRobot,
			orderComplate
		},
		data() {
			return {
				timer: null,
				tabList: [{
					name: "持仓",
					count:0
				}, {
					name: "挂单",
					count:0
				}, {
					name: "今日盈亏"
				}],
				current: 0,
				currentBak: 0,
				complateList: [],
				requestTimer: null,
				iStatusBarHeight: 0,
				accountStatus: false
			};
		},
		computed: {
			...mapState("member", ["memberCoin", "member", "isLogin", "isMoni"]),
			...mapState("charge", ["order_hold","order_robot"]),
			...mapGetters("member", ["getToken", "getUserInfo", "getUpColor", "getDownColor", "getBalance"]),
			...mapGetters("app", ["getConfig"]),
			...mapGetters('wallet', [
				'getTotalMarginUsed',
				"getTotalProfit",
				'getAvailableBalance',
				'getNetValue',
				'getMarginRatio'
			]),

			getTabList() {
				this.tabList[0].count = this.order_hold.length || 0
				this.tabList[1].count = this.order_robot.length || 0
				return this.tabList;
			},

			balance() {
				return this.getBalance;
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

			totalProfitClass() {
				if (this.getTotalProfit > 0) return this.getUpColor;
				if (this.getTotalProfit < 0) return this.getDownColor;
				return '';
			}
		},
		methods: {
			onChangeAccount() {
				if(this.isLogin){
					this.accountStatus = !this.accountStatus
				}
			},
			onSelectMoni(){
				loginqie()
				this.onChangeAccount();
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
			toComplateDetail(item) {
				this.$Router.push({
					path: "/pages/market/complateDetail",
					query: {
						detail: item
					}
				})
			},
			async closeOrder(item) {
				try {
					await this.$u.api.sellApi({
						oid: item.id
					})
					uni.showToast({
						icon: "success",
						title: "平仓成功"
					})
				} catch (e) {
					//TODO handle the exception
				}
			},
			change(index) {
				let t = this;
				if (t.current == index) {
					return false
				}
				t.current = index
				t.currentBak = index
				// initDetail()
				if (t.current === 2) {
					t.initTodayComplateOrder()
				}
			},
			initTodayComplateOrder() {
				let t = this;
				this.$u.api.getTodayComplateOrderSelectApi().then((res) => {
					t.complateList = res.data || []
				})
			}
		},
		onShow() {
			this.current = this.currentBak
			initDetail()
			if (this.current === 2) {
				this.initTodayComplateOrder()
			}
		},
		onLoad() {
			// 获取高度
			this.iStatusBarHeight = uni.getSystemInfoSync().statusBarHeight
			if (this.$Route.query.current) {
				this.current = this.$Route.query.current
			}
		},
		onHide() {
			this.current = -1
		}
	}
</script>

<style lang="scss">
	.container-page {
		min-height: 100vh;
		background: #f5f5f5;

		.custom-nav {
			z-index: 9;
			width: 100%;
			padding: 0 20rpx;
			background: none;

			.header-kefu {
				height: 80rpx;
			}

			.custom-nav-main {
				height: 84rpx;
				line-height: 84rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;

				.custom-left {
					display: flex;
					align-items: center;

					.custom-name {
						color: #000;
						font-size: $baseFontSizeLg;
						font-weight: bold;
					}

					.custom-name-icon {

						width: 44rpx;
						height: 44rpx;
						margin-left: 20rpx;
						background-size: auto 100%;
						background-repeat: no-repeat;
						background-image: url("~@/static/images-new1/common/test-1.png");
					}

					.custom-logo {
						width: 142rpx;
						height: 44rpx;
						margin-left: 20rpx;
						background-size: auto 100%;
						background-repeat: no-repeat;
						background-image: url("~@/static/images-new1/index/top-custom-logo.png");
					}
				}

				.custom-kefu {
					width:50rpx;
					height: 50rpx;
					background-size: 100% 100%;
					background-image: url("~@/static/images-new1/common/kefu.png");
				}
			}
		}


		.charge-system-main {
			.charge-system-top {
				margin: 80rpx auto;
				text-align: center;

				.charge-system-top-header {
					display: flex;
					align-items: center;
					justify-content: center;
				}

				.charge-system-top-logo {
					height: 40rpx;
					width: 40rpx;
					margin-right: 20rpx;
				}

				.charge-system-top-desc {
					color: var(--base-grey);
					margin-top: 20rpx;
					font-size: $baseFontSizeSm;
				}
			}

			.charge-system-box {
				margin: 0 auto;
				background-color: #fff;
				border-radius: 40rpx;
				width: 80%;
				padding: 30rpx 30rpx 0;

				.charge-system-box-item {
					display: flex;
					align-items: center;
					padding-bottom: 30rpx;

					.icon {
						width: 80rpx;
						height: 80rpx;
						background-size: 100% 100%;

						&.box-item-icon-1 {
							background-image: url("~@/static/images-new1/charge/icon-1.png");
						}

						&.box-item-icon-2 {
							background-image: url("~@/static/images-new1/charge/icon-2.png");
						}

						&.box-item-icon-3 {
							background-image: url("~@/static/images-new1/charge/icon-3.png");
						}

						&.box-item-icon-4 {
							background-image: url("~@/static/images-new1/charge/icon-4.png");
						}
					}

					.right {
						width: calc(100% - 100rpx);
						margin-left: 20rpx;

						.title {
							font-size: $baseFontSize;
						}

						.desc {
							margin-top: 5rpx;
							color: var(--base-grey);
							font-size: $baseFontSizeSmX;
							display: -webkit-box;
							-webkit-line-clamp: 2;
							/* 限制行数为2 */
							-webkit-box-orient: vertical;
							overflow: hidden;
						}
					}
				}
			}

			.charge-system-open-member {
				width: 80%;
				margin: 40rpx auto;
				height: 86rpx;
				border-radius: 43rpx;
				text-align: center;
				line-height: 86rpx;
				color: #000;
				background: #F4CF4A;
			}

			.charge-system-login {
				width: 80%;
				margin: 40rpx auto;
				height: 86rpx;
				border-radius: 43rpx;
				text-align: center;
				line-height: 86rpx;
				color: #000;
				background: none;
				border: 1rpx solid #E5E5E5
			}
		}

		.status_bar {
			&::after {
				background: #fff;
			}
		}

		.charge-container {
			height: calc(100vh - var(--status-bar-height) - 44px);
			box-sizing: border-box;

			.member-charge-main {
				margin: 20rpx;
				background: #FFFFFF;
				border-radius: 20rpx;
				overflow: hidden;

				.member-charge-main-box {
					padding: 30rpx;
					background: #F4CF4A;
					border-radius: 20rpx;
					color: #000;

					.member-charge-main-top {
						display: flex;
						align-items: center;
						justify-content: space-between;

						.member-charge-main-top-right {
							text-align: right;
						}

						.member-charge-main-top-title {}

						.member-charge-main-top-number {
							margin-top: 10rpx;
							font-weight: bold;
							font-size: $baseFontSizeLgXX;
						}
					}
				}

				.member-charge-main-bottom {
					display: flex;
					flex-wrap: wrap;
					justify-content: space-between;
					color: #000;

					.member-charge-main-bottom-item {
						margin-top: 20rpx;
						width: calc(50% - 40rpx);
						display: flex;
						justify-content: space-between;
					}
				}

				.member-charge-btn-box {
					color: #000;
					background: #fff;
					height: 100rpx;
					display: flex;
					line-height: 100rpx;
					justify-content: space-between;
					padding: 0 30rpx;

					.member-charge-btn-box-item {
						display: flex;
						align-items: center;

						.item-icon {
							width: 30rpx;
							height: 30rpx;
							margin-right: 10rpx;
							background-size: 100% 100%;

							&.icon-1 {
								background-image: url("~@/static/images-new1/charge/icon-btn-1.png");
							}

							&.icon-2 {
								background-image: url("~@/static/images-new1/charge/icon-btn-2.png");
							}

							&.icon-3 {
								background-image: url("~@/static/images-new1/charge/icon-btn-3.png");
							}

						}
					}
				}
			}

			.tabs {
				height: 84rpx;
				background: #fff;
				border-bottom: 1rpx solid #f3f3f3;
			}

			.scroll-view {
				//#ifdef APP-PLUS
				height: calc(100% - 90rpx - 260rpx - 84rpx - 20rpx - 20rpx - 10rpx);
				//#endif

				//#ifdef H5
				height: calc(100% - 90rpx - 260rpx - 84rpx - 20rpx - 20rpx - 10rpx - 44px);
				//#endif
				padding-bottom: 10rpx;
				background-color: none;
			}

			.scroll-view-expend {
				height: calc(100% - 642rpx);
				background-color: none;
			}
		}

		.more-list {

			// margin: 20rpx 24rpx 0;
			.more-item {
				margin-top: 20rpx;
				padding: 30rpx;
				background: #fff;

				.label {
					font-size: 28rpx;
					color: #000;
				}
			}
		}
	}

	.create-btn {
		position: fixed;
		right: 24rpx;
		bottom: 186rpx;
		width: 104rpx;
		height: 104rpx;
		display: flex;
		justify-content: center;
		align-items: center;
		border-radius: 104rpx;
		background: linear-gradient(90deg, #4586FF 0%, #6EC0FF 100%);
		border: 2px solid #FFFFFF;
		text-align: center;
		color: #fff;
	}

	.account-popup-wrapper {
		min-height: 260rpx;

		.account-list {
			padding: 30rpx;
		}

		.account-item {
			display: flex;
			align-items: center;
			justify-content: space-between;
			border-bottom: 1rpx solid #f5f5f5;
			padding: 20rpx 0;

			.account-item-name {
				margin-left: 20rpx;
				margin-right: 20rpx;
				width: calc(100% - 200rpx);
			}

			.account-item-icon {
				width: 80rpx;
				height: 80rpx;
				margin-right: 10rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/common/logo.png");
			}
			
			
			.charge-select-icon {
				width: 80rpx;
				height: 60rpx;
				background: url("~@/static/images-new1/common/icon-select-yes.png");
				background-size: 100% 100%;
			}
			.account-item-btn{
				width: 80rpx;
				background: #f5f5f5;
				border-radius: $baseRadius;
				text-align: center;
			}
			
		}
	}
</style>