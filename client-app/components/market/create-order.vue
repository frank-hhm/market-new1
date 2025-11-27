<template>
	<view>
		<u-popup v-model="status" mode="bottom" border-radius="20">
			<view class="order-popup-wrapper">
				<view class="order-popup-header">
					<view class="header-title">{{productDetail.name}}</view>
					<view class="header-right">
						<u-icon name="close" size="18" color="var(--base-grey)" @click="closeOrderPopup"></u-icon>
					</view>
				</view>

				<view class="order-popup-type">
					<view class="sell-popup-btn" :class="type==1?'active':''" @click="onSelectOrderType(1)">
						<view class="btn-text">买跌</view>
						<view class="btn-price">{{fall}}</view>
					</view>
					<view class="buy-popup-btn" :class="type==2?'active':''" @click="onSelectOrderType(2)">
						<view class="btn-text">买涨</view>
						<view class="btn-price">{{rise}}</view>
					</view>
				</view>

				<view class="order-popup-form">
					<view class="order-popup-item">
						<view class="item-title">交易类型</view>
						<view class="item-right">
							<view class="order-popup-item-type" :class="isRobot===0?'active':''"
								@tap="onSelectRobot(0)">
								市价</view>
							<view class="order-popup-item-type" :class="isRobot===1?'active':''"
								@tap="onSelectRobot(1)">
								限价</view>
						</view>
					</view>

					<view class="order-popup-item">
						<view class="item-title">手数</view>
						<view class="form-item-input-box">
							<u-input class="form-item-input" v-model="onumber" type="digit" :height="72"
								@change="onChangeNumber" :maxlength="10" placeholder="输入手数" />
						</view>
					</view>

					<view class="order-popup-item" v-if="isRobot === 1">
						<view class="item-title">标记价格</view>
						<view class="form-item-input-box">
							<u-input class="form-item-input" v-model="limitPrice" type="digit" :height="72"
								:maxlength="10" placeholder="输入价格" />
						</view>
					</view>

					<view class="order-popup-item" v-if="isRobot==0">
						<text class="item-title">止盈止损</text>
						<view class="charge-show-btn" @click="onProfitAndLoss">
							<view class="text">
								{{ isProfitAndLoss?'收起':'展开' }}
							</view>
							<u-icon class="icon" :name="isProfitAndLoss?'arrow-down':'arrow-right'"></u-icon>
						</view>
					</view>

					<template v-if="isRobot==0 && isProfitAndLoss">
						<view class="order-popup-item">
							<text class="item-title">
								设置止损
							</text>
							<view class="form-item-input-box">
								<u-input class="form-item-input" v-model="closeLosePrice" type="digit" :height="72"
									@change="loseChange" placeholder="输入价格" :maxlength="10" />
							</view>
							<view class="preview-error" v-if="closeLosePrice && (
								(type == 1 && Number(closeLosePrice) < Number(isRobot === 1?limitPrice:marketPrice)) 
								|| (type == 2 && Number(closeLosePrice) > Number(isRobot === 1?limitPrice:marketPrice))
								) ">
								触发价格必须{{ type == 2? '低于':'高于'}}开仓价格</view>
						</view>
						<view class="preview-tips">
							<text>当标记价格触达<text class="text-orange">{{closeLosePrice || '--'}}</text>时，</text>
							<text>将会触发市价止损委托平仓当前仓位。</text>
							<text>预期盈亏为:<text
									:class="c_loseCloseDelta > 0?'text-green':'text-red'">{{closeLosePrice&& closeLosePrice!=0 &&c_loseCloseDelta > 0 ?'+':''}}{{closeLosePrice != 0?c_loseCloseDelta:"--"}}</text>
							</text>
						</view>


						<view class="order-popup-item">
							<text class="item-title">设置止盈</text>
							<view class="form-item-input-box">
								<u-input class="form-item-input" v-model="closeEarnPrice" type="digit" :height="80"
									@change="earnChange" placeholder="输入价格" :maxlength="10" />
							</view>

							<view class="preview-error"
								v-if=" closeEarnPrice && ((type == 1 && Number(closeEarnPrice) >  Number(isRobot === 1?limitPrice:marketPrice)) || (type == 2 && Number(closeEarnPrice) <  Number(isRobot === 1?limitPrice:marketPrice))) ">
								触发价格必须{{ type == 1? '低于':'高于'}}开仓价格</view>
						</view>
						<view class="preview-tips">
							<text>当标记价格触达<text class="text-orange">{{closeEarnPrice || '--'}}</text>时，</text>
							<text>将会触发市价止盈委托平仓当前仓位。</text>
							<text>预期盈亏为:<text
									:class="c_earnCloseDelta > 0?'text-green':'text-red'">{{closeEarnPrice && closeEarnPrice!=0 &&c_earnCloseDelta > 0 ?'+':''}}{{closeEarnPrice != 0?c_earnCloseDelta:"--"}}</text>
							</text>
						</view>
					</template>


					<view class="member-coin">
						<text class="money-label">保证金</text>
						<text class="money-text">${{parseFloat(productDetail.pay_choose * onumber).toFixed(2)}}</text>
						<text class="money-label">,可用资金</text>
						<text class="money-text">${{availableBalance || 0}}</text>
						<text class="money-label"></text>
					</view>

					<view class="submit-btn common-btn" :class="productDetail.is_open ?'':'none'" @tap="confirmOrder">
						{{productDetail.is_open?'确认开仓':'休市'}}
					</view>
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapState
	} from "vuex"
	import {
		initDetail
	} from "@/utils/initData.js"
	export default {
		name: "CreateOrder",
		props: {
			pid: {
				type: Number | String,
				required: true
			},
			productDetail: {
				type: Object,
				required: true
			},
			spread: {
				type: Number | String,
				required: true
			}
		},
		data() {
			return {
				isRobot: 0,
				status: false,
				type: 0,
				onumber: 1,
				isProfitAndLoss: false,
				closeEarnPrice: "",
				closeLosePrice: "",
				limitPrice: "",
				submitLoading: false
			};
		},
		watch: {
			status(newValue, oldValue) {
				if (newValue === false) {
					this.onProfitAndLoss(true)
				}
			},
		},
		methods: {
			openPopup(type) {
				this.type = type
				this.status = true
			},
			closeOrderPopup() {
				this.type = 0
				this.status = false
			},
			onSelectOrderType(type) {
				this.type = type
			},
			onSelectRobot(type) {
				this.isRobot = type
			},
			onChangeNumber(num) {
				this.onumber = num
			},
			onProfitAndLoss(isClone = false) {
				if (this.isProfitAndLoss) {
					this.closeEarnPrice = ""
					this.closeLosePrice = ""
				}
				if (isClone !== true) {
					this.isProfitAndLoss = !this.isProfitAndLoss
				}
			},
			loseChange({
				value
			}) {},
			earnChange({
				value
			}) {},
			//确认下单
			confirmOrder() {
				let t = this,
					newPrice = 0;
				if (t.submitLoading === true) {
					return false;
				}
				//市价
				if (t.isRobot === 0) {
					newPrice = parseFloat(t.marketPrice)
				} else {
					newPrice = t.limitPrice
				}
				let params = {
					order_type: t.type,
					order_pid: t.pid,
					order_price: t.productDetail.pay_choose,
					buy_price: newPrice,
					onumber: t.onumber,
					surplus: t.closeEarnPrice,
					loss: t.closeLosePrice,
				}
				t.submitLoading = true;
				uni.showLoading({
					mask: true,
					title: '正在开仓中...'
				})
				//市价
				try {
					if (t.isRobot === 0) {
						t.$u.api.createOrderApi(params).then((res) => {
							uni.hideLoading()
							// initDetail();
							t.submitLoading = false;
							t.$Router.push({
								path: "/otherpages/order/createSuccess",
								query: {
									title: "开仓成功",
									type: t.type,
									robot: t.isRobot,
									ptitle: res.data.ptitle,
									order_id: res.data.id,
									buyprice: res.data.buyprice,
									buytime: res.data.buytime,
									create_time: res.data.create_time,
									onumber: res.data.onumber,
									orderno: res.data.orderno,
								}
							})
						}).catch((err) => {
							console.log(err)
							t.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: "开仓失败!" + (err.data.message || '')
							})
						})
					} //限价 挂单
					else {
						t.$u.api.penddingOrderApi(params).then((res) => {
							uni.hideLoading()
							// initDetail();
							t.submitLoading = false;
							t.$Router.push({
								path: "/otherpages/order/createSuccess",
								query: {
									title: "挂单成功",
									type: t.type,
									robot: t.isRobot,
									ptitle: res.data.ptitle,
									order_id: res.data.id,
									buyprice: res.data.buyprice,
									create_time: res.data.create_time,
									onumber: res.data.onumber,
									// orderno:res.data.orderno,
								}
							})
						}).catch((err) => {
							t.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: "挂单失败!" + (err.data.message || '')
							})
						})
					}
				} catch (e) {
					setTimeout(() => {
						t.submitLoading = false;
						uni.showToast({
							icon: "none",
							title: "开仓失败：" + e
						})
					}, 1000)
					//TODO handle the exception
				}

			}
		},
		computed: {
			...mapState("member", ["memberCoin"]),
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			...mapGetters("market", ["getMarketPrice"]),
			...mapGetters('wallet', [
				'getAvailableBalance'
			]),

			marketPrice() {
				return this.getMarketPrice(this.pid);
			},
			availableBalance() {
				return this.getAvailableBalance;
			},
			//价格
			fall() {
				let _money = parseFloat(this.marketPrice)
				return (parseFloat(this.marketPrice) - parseFloat(this.spread)).toFixed(this.productDetail.decimal)
			},
			rise() {
				let _money = parseFloat(this.marketPrice)
				return (parseFloat(this.marketPrice) + parseFloat(this.spread)).toFixed(this.productDetail.decimal)
			},

			//止盈 价格范围
			c_earnCloseDelta() {
				if (!this.closeEarnPrice) {
					return "-"
				}
				let value
				let _money = parseFloat(this.productDetail.money)
				let orderPrice = parseFloat(String(this.marketPrice));
				if (this.isRobot === 1) {
					orderPrice = this.limitPrice
				}
				//买跌
				if (this.type == 1) {
					value = (orderPrice - parseFloat(this.spread) - this.closeEarnPrice) / parseFloat(this.productDetail
						.number) * parseFloat(this
						.onumber) * _money
				} else {
					value = ((orderPrice + parseFloat(this.spread) - this.closeEarnPrice) / parseFloat(this.productDetail
						.number) * parseFloat(this
						.onumber) * _money) * (-1)
				}
				if (isNaN(value)) {
					return '--'
				}
				return value.toFixed(2)
			},
			//止损 预计盈亏
			c_loseCloseDelta() {
				if (!this.closeLosePrice) {
					return '--'
				}
				let value
				let _money = parseFloat(this.productDetail.money)
				let orderPrice = parseFloat(String(this.marketPrice));
				if (this.isRobot === 1) {
					orderPrice = this.limitPrice
				}
				//买跌
				if (this.type == 1) {
					value = (orderPrice - parseFloat(this.spread) - this.closeLosePrice) / parseFloat(this.productDetail
						.number) * parseFloat(this
						.onumber) * _money
				} else {
					value = ((orderPrice + parseFloat(this.spread) - this.closeLosePrice) / parseFloat(this.productDetail
						.number) * parseFloat(this
						.onumber) * _money) * (-1)
				}
				if (isNaN(value)) {
					return '--'
				}
				return value.toFixed(2)
			},
		}
	};
</script>

<style lang="scss" scoped>
	.order-popup-wrapper {
		min-height: 460rpx;

		.order-popup-header {
			position: relative;
			padding: 20rpx;
			height: 40rpx;
			box-sizing: content-box;

			.header-right {
				position: absolute;
				right: 20rpx;
				top: 20rpx;
				height: 40rpx;
				width: 40rpx;
				border-radius: 40rpx;
				background: #F5F5F5;
				text-align: center;
				line-height: 32rpx;
			}

			.header-title {
				font-size: $baseFontSizeDefault;
				text-align: center;
			}
		}

		.order-popup-type {
			padding: 0 30rpx;
			display: flex;
			justify-content: center;
			justify-content: space-between;

			.sell-popup-btn,
			.buy-popup-btn {
				width: calc(50% - 20rpx);
				height: 88rpx;
				border-radius: $baseRadius;
				line-height: 88rpx;
				border: 1rpx solid #E0E0E0;
				position: relative;
				display: flex;
				align-items: baseline;
				justify-content: center;

				.btn-text {
					font-size: $baseFontSizeSm;
					margin-right: 10rpx;
				}

				.btn-price {
					font-size: $baseFontSizeLgXX;
					font-weight: 500;
				}
			}



			.active {
				&::after {
					position: absolute;
					content: "";
					width: 28rpx;
					height: 28rpx;
					background-size: 100% 100%;
					right: 12rpx;
					bottom: 12rpx;
				}
			}

			.sell-popup-btn {
				.btn-price {
					color: $baseGreenColor;
				}

				&.active {
					border: 1rpx solid $baseGreenColor;
					background: #EBFFEB;

					&::after {
						background-image: url('~@/static/images/icon/icon-yes-green.png');
					}
				}
			}

			.buy-popup-btn {
				.btn-price {
					color: $baseRedColor;
				}

				&.active {
					border: 1rpx solid $baseRedColor;
					background: #FFE6E6;

					&::after {
						background-image: url('~@/static/images/icon/icon-yes-red.png');
					}
				}
			}
		}


		.order-popup-form {
			padding: 0 30rpx 40rpx;

			.order-popup-item {
				display: flex;
				justify-content: space-between;
				align-items: center;
				padding: 10rpx 0;
				margin-top: 15rpx;
				position: relative;

				.item-title {
					font-size: $baseFontSizeSm;
					width: 200rpx;
				}

				.item-right {
					display: flex;

					.order-popup-item-type {
						margin-right: 20rpx;
						background: #F5F5F5;
						height: 56rpx;
						line-height: 54rpx;
						width: 100rpx;
						border-radius: 8rpx;
						text-align: center;
						font-size: $baseFontSizeSm;

						&:last-child {
							margin-right: 0;
						}

						&.active {
							color: #000;
							background: #F1C934;
						}
					}
				}

				.form-item-input-box {
					width: 240rpx;
					padding: 0 20rpx;
					background-color: #f5f5f5;
					border-radius: $baseRadius;

					.form-item-input {
						height: 72rpx;
						line-height: 72rpx;
					}

				}

				.preview-error {
					position: absolute;
					width: 100%;
					bottom: 0;
					right: 0;
					font-size: $baseFontSizeSm;
					color: red;
				}

				.charge-show-btn {
					display: flex;
					color: var(--base-grey);
					font-size: $baseFontSize;

					.icon {
						font-size: $baseFontSizeSmX;
						margin-left: 10rpx;
					}
				}
			}


			.preview-tips {
				font-size: $baseFontSizeSm;
				color: #999;
			}

			.member-coin {
				margin-top: 54rpx;
				font-weight: 400;
				color: #000;
				font-size: $baseFontSizeSm;
				letter-spacing: 0;
				text-align: center;

				.money-label {}

				.money-text {
					color: $baseRedColor;
				}
			}

			.submit-btn {
				height: 94rpx;
				background: $baseColor;
				border-radius: 94rpx;
				margin-top: 20rpx;
				font-size: $baseFontSize;
				line-height: 94rpx;

			}

			.submit-btn.none {
				background: #f0f0f0;
				color: #000000;
			}


		}
	}
</style>