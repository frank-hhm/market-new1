<template>
	<view class="container container-bg container-page" v-if="productDetail">
		<customNav :title="productDetail.name" bg="#fff" leftColor="#000">

			<template slot='right'>
				<view class="custom-nav-right-box" v-if="marketStatus">
					<image src="/static/images-new1/product/pro-icon-1.png" @tap="goContract" class="right-icon2" />
					<image v-if="!inArray(productDetail.id,member.collects)"
						src="/static/images-new1/common/favorites.png" @tap="onCollect" class="right-icon" />
					<image v-else src="/static/images-new1/common/favorites-fill.png" @tap="onCollect"
						class="right-icon" />
				</view>
			</template>
		</customNav>
		<marketLoading v-if="!marketStatus"></marketLoading>
		<view v-if="marketStatus">
			<view class="detail-main">
				<view class="detail-main-left">
					<view class="detail-price" :class="productDetail.is_open?'':'market-none-open-color'"
						:style="{color:marketPrice>=productDetail.old_price?getUpColor:getDownColor}">
						{{marketPrice}}
					</view>
					<detail-price-change :productDetail="productDetail"></detail-price-change>
				</view>

				<view class="detail-main-right">
					<view class="detail-main-right-item">
						<view class="detail-main-right-items">
							<view class="title">今开:</view>
							<view class="number">{{productDetail.open_price}}</view>
						</view>
						<view class="detail-main-right-items">
							<view class="title">昨收:</view>
							<view class="number">{{productDetail.last_close || 0}}</view>
						</view>
					</view>
					<view class="detail-main-right-item">
						<view class="detail-main-right-items">
							<view class="title">最高:</view>
							<view class="number">{{productDetail.height_price || 0}}</view>
						</view>
						<view class="detail-main-right-items">
							<view class="title">最低:</view>
							<view class="number">{{productDetail.low_price ||  0}}</view>
						</view>
					</view>
				</view>
			</view>

			<market-time-tab class="market-time-tab" :tabs="timeTabs" :currentActive="timeTabIndex"
				@change="timeChange"></market-time-tab>

			<market-echarts :newPrice="Number(marketPrice)" :decimal="productDetail.decimal"
				:currentActive="timeTabIndex" :chartData="chartData" @invokeGetKData="invokeGetKData"
				ref="marketEchartsRef">
			</market-echarts>

			<view class="market-main-bottom">
				<view class="sell-btn" v-if="productDetail.is_open" @click="createOrder(1)">
					<text class="sell-btn-title">买跌</text> <text class="sell-btn-price">{{fall}}</text>
				</view>
				<view class="buy-btn" v-if="productDetail.is_open" @click="createOrder(2)">
					<text class="sell-btn-title">买涨</text> <text class="sell-btn-price">{{rise}}</text>
				</view>
				<view class="market-none-open" v-if="!productDetail.is_open">
					休市
				</view>
			</view>
			<create-order v-if="productId" :productDetail="productDetail" :spread="spread" :pid="productId"
				ref="createOrderRef"></create-order>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import detailPriceChange from '@/components/market/detail-price-change.vue';
	import marketTimeTab from '@/components/market/market-time-tab.vue';
	import createOrder from '@/components/market/create-order.vue';
	import marketEcharts from '@/components/market/market-echarts.vue';
	import marketLoading from '@/components/market/loading.vue';

	import {
		sendSocketMessage
	} from "@/websocket/index.js"
	import {
		mapGetters,
		mapState
	} from "vuex"
	import {
		initDetail
	} from "@/utils/initData.js"
	export default {
		components: {
			customNav,
			marketLoading,
			detailPriceChange,
			marketTimeTab,
			createOrder,
			marketEcharts,
		},
		computed: {
			...mapState("market", ["markets", "marketStatus"]),
			...mapState("member", ["isLogin", "member"]),
			...mapGetters("market", ["getMarketById", "getMarketPrice"]),
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			...mapGetters("dataArray", ["inArray"]),
			productDetail() {
				if (this.productId) {
					return this.getMarketById(this.productId);
				}
				return {};
			},
			marketPrice() {
				if (this.productId) {
					return this.getMarketPrice(this.productId);
				}
				return {};
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
		},
		data() {
			return {
				productId: 0,
				timeTabs: [{
					name: "分时",
					value: 1,
					timer: 1
				}, {
					name: "M15",
					value: 15,
					timer: 15
				}, {
					name: "M30",
					value: 30,
					timer: 30
				}, {
					name: "H1",
					value: 60,
					timer: 62
				}, {
					name: "H4",
					value: 240,
					timer: 242
				}, {
					name: "M1",
					value: 1,
					timer: 1
				}, {
					name: "M5",
					value: 5,
					timer: 5
				}, {
					name: "日K线",
					value: "d",
					timer: 60 * 24
				}],
				timeTabIndex: 1,
				isRequest: false,
				chartData: {},
				requestTimer: null,
				spread: 0
			}
		},
		onReady() {},
		onLoad() {
			this.productId = this.$Route.query.id
		},
		onShow() {
			this.productId = this.$Route.query.id
			this.$store.commit('market/setMarketId', this.productId)
			this.requestKdata(true)
			if (!this.requestTimer && !this.firstLoad) {
				this.setRequestKdataTimer()
			}
			this.initProductDetail();
			// this.$u.api.getProductConfigApi().then((res) => {
			// 	this.$store.commit('market/setMarkets', res.data.list || [])
			// })
		},
		methods: {
			initProductDetail() {
				let t = this;
				this.$u.api.getProductDetailApi({
					id: this.productId
				}).then((res) => {
					t.spread = res.data.spread || 0
				}).catch((err) => {})
			},
			goContract() {
				this.$Router.push({
					path: '/otherpages/market/contract',
					query: {
						id: this.productId
					}
				})
			},
			onCollect() {
				let t = this
				if (this.isLogin === false) {
					uni.showToast({
						icon: "none",
						title: "请先登录"
					})
					setTimeout(() => {
						t.$Router.push('/pages/login/index')
					}, 1000)
				}
				if (t.inArray(t.productDetail.id, t.member.collects)) {
					uni.showLoading({
						mask: true,
						title: '正在从自选中删除...'
					})
					this.$u.api.delOptionalProductApi({
						id: this.$Route.query.id,
					}).then((res) => {
						// initDetail()
						uni.showToast({
							icon: "none",
							title: res.message || "删除自选成功"
						})
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: (err.data.message || '')
						})
					})
				} else {
					uni.showLoading({
						mask: true,
						title: '添加自选中...'
					})
					this.$u.api.addOptionalProductApi({
						id: t.$Route.query.id
					}).then((res) => {
						// initDetail()
						uni.showToast({
							icon: "none",
							title: res.message || "添加自选成功"
						})
					}).catch((err) => {
						uni.showToast({
							icon: "none",
							title: (err.data.message || '')
						})
					})
				}
			},
			timeChange(index) {
				if (this.isRequest) {
					return false;
				}
				if (index == this.timeTabIndex) {
					return
				}
				this.timeTabIndex = index
				this.setRequestKdataTimer()
			},

			createOrder(type) {
				if (this.isLogin === true) {
					// initDetail();
					this.$refs.createOrderRef.openPopup(type)
				} else {
					uni.showToast({
						icon: "none",
						title: "请先登录"
					})
					setTimeout(() => {
						this.$Router.push('/pages/login/index')
					}, 1000)
				}
				// this.createOrderStatus = true
			},
			onCreateOrderChange(res) {
				console.log(res)
			},
			requestKdata() {
				let t = this
				if (t.isRequest == true || !t.productId) {
					return false;
				}
				t.isRequest = true;

				let obj = {
					pid: t.productId,
					interval: t.timeTabs[t.timeTabIndex].value,
					num: 1000,
					z: 1,
				};
				let res = t.$u.api.getKDataApi(obj).then((res) => {
					t.chartData = res.data
					t.isRequest = false;
				}).catch(() => {
					t.isRequest = false;
				})
			},
			setRequestKdataTimer() {
				this.clearRequestTimer()
				this.requestKdata()
				// this.requestTimer = setInterval(() => {
				// 	this.requestKdata()
				// }, 1000 * 60 * (this.timeTabs[this.timeTabIndex].timer) / 2)
			},
			clearRequestTimer() {
				if (this.requestTimer) {
					clearInterval(this.requestTimer)
					this.requestTimer = null
				}
			},
			invokeGetKData() {
				//k线
				this.setRequestKdataTimer()
			}
		}
	}
</script>

<style lang="scss" scoped>
	.detail-main {
		padding: 20rpx;
		background: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;

		.detail-main-left {
			.detail-price {
				font-size: 50rpx;
				font-weight: bold;
			}
		}

		.detail-main-right {

			.detail-main-right-item {
				display: flex;
				align-items: center;
			}

			.detail-main-right-items {
				display: flex;
				justify-content: end;
				text-align: right;
				align-items: center;
				margin-left: 30rpx;

				.title {
					color: var(--base-grey);
					font-size: $baseFontSizeSm;
				}

				.number {
					font-size: $baseFontSize;
					margin-left: 12rpx;
					color: #000;
				}
			}
		}
	}

	.market-time-tab {
		// margin-top: 20rpx;
		border-bottom: 1rpx solid #F2F2F2;
	}

	.market-main-bottom {
		position: fixed;
		bottom: 0%;
		width: 100%;
		display: flex;
		justify-content: space-around;
		align-items: center;
		padding: 20rpx;
		background: #f5f5f5;

		.sell-btn {
			width: calc(50% - 20rpx);
			height: 80rpx;
			line-height: 80rpx;
			background: $baseGreenColor;
			border-radius: $baseRadius;
			color: #fff;
			text-align: center;
		}

		.buy-btn {
			width: calc(50% - 20rpx);
			height: 80rpx;
			line-height: 80rpx;
			background: $baseRedColor;
			border-radius: $baseRadius;
			color: #fff;
			text-align: center;
		}

		.sell-btn-price {
			font-size: 42rpx;
			margin-left: 10rpx;
		}

		.market-none-open {
			height: 80rpx;
			width: calc(100% - 60rpx);
			line-height: 80rpx;
			text-align: center;
			border-radius: $baseRadius;
			background: #f0f0f0;
		}
	}

	.custom-nav-right-box {
		height: 100%;
		display: flex;
		justify-content: space-between;
		align-items: center;

		.right-icon {
			width: 40rpx;
			height: 40rpx;
		}

		.right-icon2 {
			width: 40rpx;
			height: 40rpx;
			margin-right: 40rpx;
		}
	}
</style>