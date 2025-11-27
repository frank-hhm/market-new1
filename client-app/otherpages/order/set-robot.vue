<template>
	<view class="container  container-page">
		<customNav :title="robotType == 1?'设置委托':'委托改单'">
		</customNav>
		<view class="tab-main">
			<view class="tab-item" :class="tabActive === 0 ?'active':''" @tap="onSetTab(0)">
				{{robotType == 1 ?'触发止损/止盈':'修改订单'}}
			</view>
			<view class="tab-item" :class="tabActive === 1 ?'active':''" @tap="onSetTab(1)">设置止损止赢</view>
		</view>
		<view class="form mt-30" v-if="orderDetail">
			<view class="form-item mb-20" v-if="orderDetail.ostyle">
				<text class="label">{{orderDetail.ptitle}} #{{orderDetail.id}}</text>
				<text class="value"
					:class="ostyle==2?'text-red':'text-green'">{{orderDetail.ostyle.name}}/{{orderDetail.onumber}}手</text>
			</view>

			<view class="form-item mb-20" v-if="robotType==1">
				<text class="label">开仓价格</text>
				<view class="price">{{orderDetail.buyprice}}</view>
			</view>

			<view class="form-item mb-20">
				<text class="label">标记价格</text>
				<view class="price">{{getMarketPrice(orderDetail.pid)}}</view>
			</view>
			<view class="form-item mb-20" v-if="robotType == 2">
				<text class="label">触发价格</text>
				<view class="form-item-input" v-if="tabActive === 0">
					<u-input v-model="limitPrice" type="digit" :height="80" @change="limitPriceChange"
						:maxlength="10" />
				</view>
				<view v-if="tabActive === 1" class="price">{{limitPrice}}</view>

			</view>

			<view v-if="robotType!=2 && tabActive === 0">
				<view class="form-item mb-20">
					<view class="flex flex-column">
						<text class="label">触发价格</text>
					</view>
					<view class="form-item-input">
						<u-input v-model="markPrice" type="digit" :height="80" placeholder="输入价格" :maxlength="10" />
					</view>
				</view>
				<view class="preview">预计盈亏 <text
						:class="c_markPrice > 0?'text-red':'text-green'">{{markPrice && markPrice!=0 &&  c_markPrice > 0 ?'+':''}}{{c_markPrice || '--'}}</text>
				</view>
			</view>

			<view v-if="tabActive === 1">
				<view class="form-item mb-20">
					<view class="flex flex-column">
						<text class="label">设置止损</text>
						<text class="preview-error" v-if=" closeLosePrice && (
							(ostyle == 1 && parseFloat(closeLosePrice) < parseFloat(orderDetail.buyprice))
							 || (ostyle == 2 && parseFloat(closeLosePrice) > parseFloat(orderDetail.buyprice))
							 ) ">触发价格必须{{ ostyle == 2? '低于':'高于'}}开仓价格</text>
					</view>
					<view class="form-item-input">
						<u-input v-model="closeLosePrice" type="digit" :height="80" @change="loseChange"
							placeholder="输入价格" :maxlength="10" />
					</view>
				</view>
				<view class="preview">
					<view>当标记价格触达<text class="text-orange">{{closeLosePrice || '--'}}</text>时，</view>
					<view>将会触发市价止损委托平仓当前仓位。</view>
					<view>预期盈亏为 <text
							:class="c_loseCloseDelta > 0?'text-red':'text-green'">{{closeLosePrice&& closeLosePrice!=0 &&c_loseCloseDelta > 0 ?'+':''}}{{closeLosePrice != 0?c_loseCloseDelta:"--"}}</text>。
					</view>
				</view>
			</view>

			<view class="mt-30" v-if="tabActive === 1">
				<view class="form-item mb-20">
					<view class="flex flex-column">
						<text class="label">设置止盈</text>
						<text class="preview-error" v-if=" closeEarnPrice && (
							(ostyle == 1 && parseFloat(closeEarnPrice) > parseFloat(orderDetail.buyprice))
							|| (ostyle == 2 && parseFloat(closeEarnPrice) < parseFloat(orderDetail.buyprice))
							) ">触发价格必须{{ ostyle == 1? '低于':'高于'}}开仓价格</text>
					</view>
					<view class="form-item-input">
						<u-input v-model="closeEarnPrice" type="digit" :height="80" @change="earnChange"
							placeholder="输入价格" :maxlength="10" />
					</view>
				</view>
				<view class="preview">
					<view>当标记价格触达<text class="text-orange">{{closeEarnPrice || '--'}}</text>时，</view>
					<view>将会触发市价止盈委托平仓当前仓位。</view>
					<view>预期盈亏为 <text
							:class="c_earnCloseDelta > 0?'text-red':'text-green'">{{closeEarnPrice && closeEarnPrice!=0 &&c_earnCloseDelta > 0 ?'+':''}}{{closeEarnPrice != 0?c_earnCloseDelta:"--"}}</text>。
					</view>
				</view>
			</view>
		</view>
		<view class="common-btn submit-btn" @tap="onSubmit">提交</view>
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
				tabActive: 0,
				robotType: 1,
				orderDetail: {},
				orderId: 0,
				limitPrice: "",
				markPrice: "",
				closeLosePrice: "",
				closeEarnPrice: "",
				ostyle: ""

			};
		},
		watch: {
			limitPrice(newValue, oldValue) {
				if (this.ostyle == 2 && oldValue) {
					this.orderDetail.buyprice = newValue
				}
			}
		},
		computed: {
			...mapGetters("member", ["getToken", "getUserInfo"]),
			...mapGetters("market", ["getMarketById", "getMarketPrice"]),

			productDetail() {
				if (this.orderDetail.pid) {
					return this.getMarketById(this.orderDetail.pid);
				}
				return {};
			},

			//标记 触发
			c_markPrice() {
				if (!this.orderDetail.ostyle || !this.markPrice) {
					return '--'
				}
				let _price = 0;
				let _money = parseFloat(this.productDetail.money)
				// 计算盈亏
				if (this.ostyle == 2) {
					// 买涨
					_price = ((this.markPrice - this.orderDetail.buyprice) / parseFloat(this.productDetail.number) *
						parseFloat(this.orderDetail.onumber)) * _money
				} else {
					// 买跌
					_price = (this.orderDetail.buyprice - this.markPrice) / parseFloat(this.productDetail.number) *
						parseFloat(this.orderDetail.onumber) * _money
				}
				return _price.toFixed(2)
			},

			//止损 预计盈亏
			c_loseCloseDelta() {
				if (!this.orderDetail.ostyle || !this.orderDetail.buyprice) {
					return "--"
				}
				let value

				let _money = parseFloat(this.productDetail.money)
				let orderPrice
				if (this.robotType == 1) {
					orderPrice = this.orderDetail.buyprice
				} else {
					orderPrice = parseFloat(String(this.limitPrice));
				}
				//卖出
				if (this.ostyle == 1) {
					value = (orderPrice - this.closeLosePrice) / parseFloat(this.productDetail.number) * parseFloat(this
						.orderDetail.onumber) * _money
				} else {
					value = ((orderPrice - this.closeLosePrice) / parseFloat(this.productDetail.number) * parseFloat(this
						.orderDetail.onumber) * _money) * -1
				}
				return value.toFixed(2)
			},


			//止盈 预计盈亏
			c_earnCloseDelta() {
				if (!this.orderDetail.ostyle || !this.orderDetail.buyprice) {
					return "--"
				}
				let value
				let _money = parseFloat(this.productDetail.money)
				let orderPrice
				if (this.robotType == 1) {
					orderPrice = this.orderDetail.buyprice
				} else {
					orderPrice = parseFloat(String(this.limitPrice));
				}
				//卖出
				if (this.ostyle == 1) {
					value = (orderPrice - this.closeEarnPrice) / parseFloat(this.productDetail.number) * parseFloat(this
						.orderDetail.onumber) * _money
				} else {
					value = ((orderPrice - this.closeEarnPrice) / parseFloat(this.productDetail.number) * parseFloat(this
						.orderDetail.onumber) * _money) * -1
				}
				return value.toFixed(2)
			},
		},
		methods: {
			onSetTab(index) {
				this.tabActive = index
			},
			onInitData() {
				let t = this;
				uni.showLoading({
					title: '加载中...'
				})
				if (t.robotType == 2) {
					t.$u.api.getOrderRobotDetailApi({
						id: t.orderId
					}).then((res) => {
						t.orderDetail = res.data
						t.ostyle = res.data.ostyle.value
						t.limitPrice = res.data.buyprice
						uni.hideLoading()
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: "获取失败!" + (err.data.message || '')
						})
					})
				} else if (t.robotType == 1) {
					t.$u.api.getOrderDetailApi({
						id: t.orderId
					}).then((res) => {
						t.orderDetail = res.data
						t.ostyle = res.data.ostyle.value
						t.buyPrice = res.data.buyprice
						uni.hideLoading()
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: "获取失败!" + (err.data.message || '')
						})
					})
				}
			},
			limitPriceChange({
				value
			}) {

			},
			onSubmit() {
				let t = this;
				let params = {
					surplus: t.closeEarnPrice,
					loss: t.closeLosePrice,
					oid: t.orderDetail.id,
				}
				try {
					if (t.robotType == 1) {
						if (t.markPrice) {
							params = Object.assign({}, params, {
								mark_price: t.markPrice,
								trigger_price: t.getMarketPrice(t.orderDetail.pid),
							})
						}
						uni.showLoading({
							title: '正在处理中...'
						})
						t.$u.api.holdModifyApi(params).then((res) => {
							setTimeout(() => {
								uni.hideLoading()
								uni.showToast({
									icon: "none",
									title: res.message
								})
							}, 800)
							setTimeout(() => {
								t.$Router.back(1)
							}, 1500)
						}).catch((err) => {
							uni.showToast({
								icon: "none",
								title: "处理失败!" + (err.data.message || '')
							})
						})
					} else {
						uni.showLoading({
							title: '正在处理中...'
						})
						params = Object.assign({}, params, {
							order_pid: t.orderDetail.id,
							newprice: parseFloat(String(t.limitPrice)),
						})
						t.$u.api.peddingModifyApi(params).then((res) => {
							setTimeout(() => {
								uni.hideLoading()
								uni.showToast({
									icon: "none",
									title: res.message
								})
							}, 800)
							setTimeout(() => {
								t.$Router.back(1)
							}, 1500)
						}).catch((err) => {
							uni.showToast({
								icon: "none",
								title: "处理失败!" + (err.data.message || '')
							})
						})
					}
				} catch (err) {
					uni.showToast({
						icon: "none",
						title: "处理失败！" + err
					})
				}
			}
		},
		onLoad() {

		},
		onShow() {
			let t = this;
			t.orderId = t.$Route.query.id || 0;
			//1持单设置止损止盈  2挂单修改
			t.robotType = t.$Route.query.robot || 1;
			t.onInitData()
		},
		onUnload() {}
	}
</script>

<style lang="scss">
	.container {
		background: #f5f5f5;

		.color-gray {
			color: var(--base-grey);
		}

		.mb-30 {
			margin-bottom: 30rpx;
		}

		.order-info {
			margin: 20rpx;
			padding: 20rpx 0;
			background: #fff;
			border-radius: $baseRadius;

			.label {
				margin-right: 10rpx;
				font-size: $baseFontSizeSm;
				color: var(--base-grey);
			}

			.value {
				font-size: $baseFontSizeSm;
			}
		}

		.form {
			padding: 0 30rpx 40rpx;
			margin: 24rpx;
			background-color: #fff;
			border-radius: $baseRadius;

			.header {
				height: 50rpx;

				.header-text {
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.header-desc {
					font-size: $baseFontSizeSm;
					color: $color-blue;
				}
			}

			.form-item {
				height: 100rpx;
				display: flex;
				justify-content: space-between;
				align-items: center;

				.form-item-input {
					width: 320rpx;
					height: 80rpx;
					line-height: 80rpx;
					padding: 0 20rpx;
					background-color: #f5f5f5;
					border-radius: $baseRadius;
				}

				.price {
					font-weight: bold;
				}
			}

			.label {}

			.range {
				margin-top: 10rpx;
				font-size: $baseFontSizeSm;
			}

			.preview {
				margin-top: 10rpx;
				font-size: $baseFontSizeSm;
				color: var(--base-grey);
			}

			.preview-error {
				margin-top: 10rpx;
				font-size: $baseFontSizeSm;
				color: red;

			}
		}

		.bottom {
			margin: 20rpx;
			background-color: #fff;
			border-radius: $baseRadius;
			padding: 20rpx;

			.left-item {
				flex: 1;
				font-size: $baseFontSize;
				text-align: center;
			}

			.right-item {
				flex: 1;
				font-size: $baseFontSize;
				text-align: center;
			}
		}

		.submit-btn {
			margin: 60rpx 30rpx;
			border-radius: 98rpx;
		}
	}

	.tab-main {
		height: 100rpx;
		line-height: 100rpx;
		display: flex;
		justify-content: space-between;
		background-color: #fff;
		border-radius: 20rpx;
		margin: 20rpx;
		padding: 10rpx;

		.tab-item {
			width: calc(100% - 20rpx);
			margin: 0 10rpx;
			text-align: center;
			border-radius: $baseRadius;
			height: 80rpx;
			line-height: 80rpx;
		}

		.tab-item.active {
			color: #000;
			font-weight: bold;
			background-color:#F1C934;
		}
	}
</style>