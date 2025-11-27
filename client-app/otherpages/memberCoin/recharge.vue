<template>
	<view class="container container-page">
		<customNav title="存款" leftColor="#000000"></customNav>
		<view class="header-box">
			<view class="head-top">
				<view class="head-top-item">
					<view class="head-top-title">完成存款订单</view>
					<view class="head-top-desc">完成存款订单</view>
				</view>
				<view class="head-top-item">
					<view class="head-top-title">财务到账审核</view>
					<view class="head-top-desc">约1-15分钟</view>
				</view>
				<view class="head-top-item">
					<view class="head-top-title">完成存款</view>
					<view class="head-top-desc">转入交易账户</view>
				</view>
			</view>

			<view class="channel-box" @tap="toPopup">
				<view class="channel-box-icon" v-if="chargeIndex == 0"></view>
				<view class="channel-box-icon" v-else :style="{
					backgroundImage: `url(${chargeItem.cover})`
				}">
				</view>
				<view class="channel-box-msg">
					<view class="channel-box-title">{{ chargeIndex == 0 ?"请选存款通道":chargeItem.name}}</view>
					<view class="channel-box-desc" v-if="chargeIndex !== 0 && (chargeItem.min || chargeItem.max)">
						限额{{chargeItem.min}} - {{chargeItem.max}}
					</view>
					<view class="channel-box-desc" v-else>选择存款通道</view>
				</view>
				<view class="channel-box-btn">
					<view>切换</view>
					<view class="channel-box-btn-icon"></view>
				</view>
			</view>


			<view class="charge-money-title">存款金额</view>
			<view class="deposit-input">
				<view class="transfer-input-wrapper">
					<view class="transfer-input-unit">$</view>
					<u-input class="input" v-model="value" type="digit" :customStyle="{
						fontSize:'30rpx',
						fontWeight:550
					}" :clearable="false" placeholder="请输入存款金额" />
				</view>
			</view>
			<view class="charge-money-desc">
				今日汇率<text class="charge-money-desc-text">1</text>美元=<text
					class="charge-money-desc-text">{{systemConfig.usdt_rate || 7}}</text>人民币
			</view>

			<view class="charge-money-desc" v-if="value && chargeIndex != 0">
				兑换后约支付 <text
					class="charge-money-desc-text">{{value && chargeType != 'offline_usdt' ? '￥'+(value * systemConfig.usdt_rate).toFixed(2):''}}{{value && chargeType == 'offline_usdt' ? '$'+value:''}}</text>
			</view>
			<view class="charge-money-desc2">
				支付完约15分钟内到账
			</view>
		</view>
		<view class="deposit-wrapper">
			<view class="common-btn submit-btn" v-if="
				(getConfig.payment_alipays && getConfig.payment_alipays.payment_alipays_status == 1) 
				|| paymentSelect.length > 0" @tap="submit">
				<text>确认支付</text>
				<text
					v-if="value && chargeIndex !== 0">({{value && chargeType != 'offline_usdt' ? '￥'+(value * systemConfig.usdt_rate).toFixed(2):''}}{{value && chargeType == 'offline_usdt' ? '$'+value:''}})</text>
			</view>
			<view class="common-btn submit-btn-none" v-else>暂无支付方式</view>

		</view>

		<view class="tips-box">

			<view class="tips-header">【温馨提示】</view>
			<view class="tips-title">1、每次注资需重新获取收款方信息。往旧收款方转账，造成资金丢失需自行承担。</view>
			<view class="tips-title">2、为避免于行情波幅较大时段集中注资造成注资通道拥挤而影响订单交易，请预留资金于“我的钱包”。</view>
			<view class="tips-title">3.支付完成后15分钟内未到账，请及时联系在线客服核对入金订单。</view>
			<view class="flex justify-center">
				<router-link style="color: #FFC300;" to="/pages/other/kefu">在线客服</router-link>
			</view>
		</view>
		
		<u-popup v-model="popupStatus" mode="bottom" border-radius="20">
			<view class="payment-popup-wrapper">
				<view class="payment-popup-header">
					<view class="payment-popup-header-left">
						<u-icon name="close" size="18" color="var(--base-grey)" @click="closeOrderPopup"></u-icon>
					</view>
					<view class="payment-popup-header-title">请选存款通道</view>
				</view>
				<view class="charge-list">
					<view class="charge-item" v-for="(item,index) in paymentSelect" :key="index" @tap="setChange(item)">
						<view class="yl-icon" :style="{
							backgroundImage:`url(${item.cover})`
						}"></view>
						<view class="flex flex-column wrapper">
							<text class="charge-name">{{item.name}}</text>
							<text class="charge-desc" v-if="item.min || item.max">
								限额{{item.min}} - {{item.max}}
							</text>
						</view>

						<view :class="chargeIndex==item.id?'charge-select-icon':'common-icon'"></view>
					</view>
					<view v-if="getConfig.payment_alipays && getConfig.payment_alipays.payment_alipays_status == 1"
						class="charge-item" @tap="setChange2">
						<view class="alipay-icon"></view>
						<view class="flex flex-column wrapper">
							<text class="charge-name">支付宝支付</text>
						</view>
						<view :class="chargeIndex==-1?'charge-select-icon':'common-icon'"></view>
					</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapMutations,
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {
				chargeMoneyIndex: 0,
				value: "",
				chargeIndex: 0,
				chargeType: '',
				chargeItem: {},
				chargeList: [],
				info: {},
				paymentSelect: [],
				popupStatus: false
			};
		},
		computed: {
			...mapState("member", ["memberCoin"]),
			...mapGetters("app", ["getConfig"]),
			...mapState("app", ["systemConfig"])
		},
		methods: {
			toPopup() {
				this.popupStatus = !this.popupStatus
			},
			setChange(item) {
				this.chargeIndex = item.id
				this.chargeType = item.type.value
				this.chargeItem = item
				this.toPopup()
			},
			setChange2() {
				this.chargeIndex = -1;
				this.chargeType = 'alipays'
				this.chargeItem = {
					name:"支付宝在线支付",
					cover:require("@/static/images-new1/common/icon-alipay2.png")
				}
				this.toPopup()
			},
			//判断是否在微信中  
			isWechat() {
				var ua = window.navigator.userAgent.toLowerCase();
				if (ua.match(/micromessenger/i) == 'micromessenger') {
					//console.log('是微信客户端')    
					return true;
				} else {
					//console.log('不是微信客户端')    
					return false;
				}
			},
			copy(value) {
				this.$Router.push("/pages/webview/kefu")
			},
			rujin() {
				this.$Router.push({
					path: '/pages/deposit/rujin'
				})
			},
			chargeMoneySelect(index) {
				if (this.chargeMoneyIndex == index) {
					return false
				}
				this.chargeMoneyIndex = index
				this.value = this.chargeList[index]
			},
			goWebPage(title, url) {
				this.$Router.push({
					path: "/pages/webview/webview",
					query: {
						title: title,
						url: url
					}
				})
			},
			numFilter(value) {
				// 截取当前数据到小数点后三位
				let tempVal = parseFloat(value).toFixed(3)
				let realVal = tempVal.substring(0, tempVal.length - 1)
				return realVal
			},
			escape2Html(str) {
				var arrEntities = {
					'lt': '<',
					'gt': '>',
					'nbsp': ' ',
					'amp': '&',
					'quot': '"'
				};
				return str.replace(/&(lt|gt|nbsp|amp|quot);/ig, function(all, t) {
					return arrEntities[t];
				});
			},
			async submit() {
				if (!this.value) {
					uni.showToast({
						icon: "none",
						title: "请输入金额"
					})
					return
				}
				if (this.chargeIndex == 0) {
					uni.showToast({
						icon: "none",
						title: "请选择支付通道"
					})
					return
				}
				if ((this.chargeItem.min > 0 && this.value < this.chargeItem.min) || (
						this.chargeItem.max > 0 && this.value > this.chargeItem.max)) {
					let _msg = "该收款渠道充值限额只能输入";
					if (this.chargeItem.min > 0) {
						_msg += this.chargeItem.min
					}
					if (this.chargeItem.max > 0) {
						_msg += '-' + this.chargeItem.max
					}
					uni.showToast({
						icon: "none",
						title: _msg
					})
					return false;
				}
				switch (this.chargeType) {
					//银联转账
					case 'offline_bank':
						this.$Router.push({
							path: "/otherpages/memberCoin/bankCardCharge",
							query: {
								money: this.numFilter(this.value * this.systemConfig.usdt_rate),
								usdt: this.value,
								type: this.chargeType,
								payment_id: this.chargeIndex
							}
						})
						break
						//支付宝转账
					case 'offline_alipay':
						this.$Router.push({
							path: "/otherpages/memberCoin/alipayTransfer",
							query: {
								money: this.numFilter(this.value * this.systemConfig.usdt_rate),
								usdt: this.value,
								type: this.chargeType,
								payment_id: this.chargeIndex
							}
						})
						break;
						//支付宝在线
					case 'alipays':
						try {
							// #ifdef APP-PLUS
							let res = await this.$u.api.alipayH5Api({
								money: this.numFilter(this.value * this.systemConfig.usdt_rate),
								usdt: this.value,
								type: this.chargeType,
								pay_type: 'app'
							})

							let config = res.data.config
							uni.requestPayment({
								provider: 'alipay',
								orderInfo: config, //微信、支付宝订单数据 【注意微信的订单信息，键值应该全部是小写，不能采用驼峰命名】
								success: function(res2) {
									this.$Router.push({
										path: "/otherpages/memberCoin/result",
										query: {
											id: res.data.order_id || 0,
										}
									})
									uni.showToast({
										icon: "none",
										title: JSON.stringify(res2)
									})
									console.log(res2);
								},
								fail: function(err) {
									uni.showToast({
										icon: "none",
										title: JSON.stringify(err)
									})
									console.log(err);
								}
							});
							// #endif

							// #ifdef H5
							if (this.isWechat()) {
								uni.showToast({
									icon: "none",
									title: "不支持微信端，请在其他浏览器打开"
								})
								return
							}
							let res = await this.$u.api.alipayH5Api({
								money: this.numFilter(this.value * this.systemConfig.usdt_rate),
								usdt: this.value,
								type: this.chargeType,
								payment_id: this.chargeIndex
								// pay_type:'app'
							})

							document.querySelector('body').innerHTML = res.data.config;
							//在渲染完立即提交表单，就会进入支付宝支付的界面
							// utils.setStorage("weixinCallback", 3);
							window.document.forms[0].submit()
							// window.location.href = res.data.web_url
							// this.goWebPage("充值", res.data.web_url);
							// #endif
						} catch (e) {
							console.log(e)
							//TODO handle the exception
						}
						break;

					case 'offline_usdt':
						this.$Router.push({
							path: "/otherpages/memberCoin/usdtTransfer",
							query: {
								money: this.value,
								type: this.chargeType,
								payment_id: this.chargeIndex
							}
						})
						break
				}
				try {

					// uni.showToast({
					// 	icon:"success",
					// 	title:"转出成功"
					// })
				} catch (e) {
					//TODO handle the exception
				}
			}
		},
		async onShow() {
			try {
				await this.$u.api.getPaymentSelect().then(res => {
					this.paymentSelect = res.data
				})
				// this.member = await this.$u.api.personInfoApi()
			} catch (e) {
				//TODO handle the exception
			}
		},
		async onLoad() {},

	}
</script>

<style lang="scss">
	.tips-box {
		margin: 30rpx 20rpx;
		color: var(--base-grey);

		.tips-title {
			text-align: left;
			font-size: $baseFontSize;
			margin: 20rpx 0;
		}
	}

	.container {
		background: #f5f5f5;

		.header-box {
			background: #fff;
			border-radius: 20rpx;
			margin: 20rpx;
			padding: 30rpx;

			.head-top {
				display: flex;
				align-items: center;
				justify-content: space-between;

				.head-top-item {
					width: 30%;
					margin-left: 3%;
					position: relative;

					.head-top-title {
						color: #000;
						height: 40rpx;
					}

					.head-top-desc {
						color: var(--base-grey);
						font-size: $baseFontSizeSm;
						margin-top: 10rpx;
					}

					&::after {
						position: absolute;
						content: "";
						height: 12rpx;
						width: 12rpx;
						background: var(--base-grey);
						border-radius: 12rpx;
						left: -20rpx;
						top: 18rpx;
					}
				}
			}

			.channel-box {
				margin-top: 40rpx;
				display: flex;
				align-items: center;

				.channel-box-icon {
					height: 100rpx;
					width: 100rpx;
					background-image: url("~@/static/images-new1/recharge/icon-channel.png");
					background-size: 100% auto;
					background-repeat: no-repeat;
					background-position: center;
				}

				.channel-box-msg {
					margin-left: 20rpx;
					width: calc(100% - 240rpx);

					.channel-box-title {
						font-size: $baseFontSizeLg;
						color: #000000;
					}

					.channel-box-desc {
						font-size: $baseFontSize;
						color: var(--base-grey);
					}
				}

				.channel-box-btn {
					margin-left: 20rpx;
					width: 140rpx;
					color: var(--base-grey);
					display: flex;
					align-items: center;
					justify-content: end;

					.channel-box-btn-icon {
						margin-left: 20rpx;
						height: 16rpx;
						width: 16rpx;
						background-image: url("~@/static/images-new1/recharge/icon-right.png");
						background-size: auto 100%;
						background-repeat: no-repeat;
					}
				}
			}

			.charge-money-title {
				margin-top: 40rpx;
				color: #000;
				font-weight: 550;
			}

			.charge-money-desc {
				.charge-money-desc-text {
					color: var(--base-red);
				}

				padding: 20rpx 0;
				border-bottom: 1rpx solid #F5F6F8;
			}

			.charge-money-desc2 {
				padding: 20rpx 0 0;
				color: var(--base-grey);
			}


			.deposit-input {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-top: 20rpx;

				.transfer-input-wrapper {
					// padding: 30rpx 0 18rpx 0;
					// border-bottom: 2rpx solid #f0f0f0;
					height: 96rpx;
					line-height: 96rpx;
					width: 100%;
					background: #fff;
					border-radius: $baseRadius;
					display: flex;
					align-items: center;
					padding: 24rpx 0;
					border-bottom: 1rpx solid #F5F6F8;

					.transfer-input-unit {
						margin-right: 10rpx;
						margin-bottom: 5rpx;
						font-size: $baseFontSizeLgXX;
						color: #000000;
					}

					.input {
						flex: 1;
						font-size: $baseFontSizeLgXXXX;
						font-weight: 500;
						color: #000000;
					}
				}
			}
		}

		.charge-title {
			margin-top: 40rpx;
			display: flex;
			align-items: center;

			.text {
				font-size: $baseFontSize;
				font-weight: 550;
				margin-right: 10rpx;
			}
		}
		
		.deposit-wrapper{
			margin: 20rpx;
		}


		.common-btn {
			margin-bottom: 40rpx;
			border-radius: 108rpx;
		}

		.submit-btn {
			margin: 60rpx 0;
		}

		.submit-btn-none {
			margin: 60rpx 0;
			height: 108rpx;
			background: #f0f0f0;
			border-radius: $baseRadius;
			font-size: $baseFontSize;
			line-height: 108rpx;
			text-align: center;
			color: #000000;
		}
	}

	.payment-popup-wrapper {
		min-height: 460rpx;

		.payment-popup-header {
			position: relative;
			padding: 40rpx 30rpx;
			height: 40rpx;
			box-sizing: content-box;

			.payment-popup-header-left {
				position: absolute;
				left: 20rpx;
				top: 40rpx;
				height: 40rpx;
				width: 40rpx;
				border-radius: 40rpx;
				background: #F5F5F5;
				text-align: center;
				line-height: 32rpx;
			}

			.payment-popup-header-title {
				font-size: $baseFontSizeDefault;
				text-align: center;
				color: #000000;
			}
		}

		.charge-list {
			margin: 20rpx;
			border-radius: $baseRadius;

			.charge-item {
				padding: 16rpx 28rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 20rpx;
				border-bottom: 1rpx solid #E5E5E5;

				&:last-child {
					border: none;
				}

				.yl-icon {
					width: 72rpx;
					height: 72rpx;
					background-size: 100% 100%;
				}

				.alipay-icon {
					width: 72rpx;
					height: 72rpx;
					background: url("~@/static/images-new1/common/icon-alipay2.png");
					background-size: 100% 100%;
				}

				.usdt-icon {
					width: 72rpx;
					height: 72rpx;
					background: url("~@/static/images-new1/common/icon-usdt.png");
					background-size: 100% 100%;
				}

				.wrapper {
					margin-left: 22rpx;
					margin-right: auto;
				}

				.charge-name {
					font-size: $baseFontSizeDefault;
					color: #000;
				}

				.charge-desc {
					margin-top: 10rpx;
					font-size: $baseFontSize;
					color: var(--base-grey);
				}

				.charge-select-icon {
					width: 60rpx;
					height: 60rpx;
					background: url("~@/static/images-new1/common/icon-select-yes.png");
					background-size: 100% 100%;
				}

				.common-icon {
					width: 60rpx;
					height: 60rpx;
				}
			}
		}
	}
</style>