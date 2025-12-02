<template>
	<view class="custom-container">
		<customNav title="跟单" leftColor="#000" bg="#f5f5f5">
		</customNav>

		<view class="follow-person-header-main">
			<view class="follow-person-header">
				<!-- <image class="header-avater" v-if="detail.avatar" :src="detail.avatar" mode="aspectFill"></image> -->
				<u-image width="80" height="80" border-radius="10" class="header-avater" :src="detail.avatar">
					<view slot="error">
						<u-image width="80" height="80" border-radius="10" class="header-avater"
							:src="getConfig.system_logo"></u-image>
					</view>
				</u-image>
				<view class="header-info">
					<view class="header-info-top">
						<view class="header-name">{{detail.nickname || "--"}}</view>
					</view>
					<view class="header-info-signature">
						{{detail.signature || '--'}}
					</view>
				</view>
				<view v-if="detail.follow_status === true" class="follow-status">
					跟单中
				</view>
			</view>

			<view class="follow-members-box">
				<view class="follow-members-box-left">
					<view class="follow-members-box-text">
						当前跟随人数
					</view>

					<view class="follow-members">
						<text class="follow-members-icon"></text>
						<text
							class="follow-members-text">{{(parseInt(detail.follow_count_text) + parseInt(detail.member_count))  || 0}}</text>
					</view>
				</view>
				<view class="follow-members-box-right">
					已入驻平台<text class="follow-members-box-count">{{detail.create_day || 0}}</text>天
				</view>
			</view>


			<view class="follow-person-item-bottom">
				<view class="follow-person-item-cell">
					<view class="follow-person-items left">
						<view class="title">近1月收益率</view>
						<view class="desc">{{detail.revenue_text || 0}}%</view>
					</view>
					<view class="follow-person-items center">
						<view class="title">近1月胜率</view>
						<view class="desc">{{detail.month_win_rate_text || 0}}%</view>
					</view>
					<view class="follow-person-items right">
						<view class="title">近1月分润(usd)</view>
						<view class="desc">{{detail.month_profit_text || 0}}</view>
					</view>
				</view>
				<view class="follow-person-item-cell">
					<view class="follow-person-items left">
						<view class="title">累计分润率</view>
						<view class="desc">{{detail.total_profit_text || 0}}%</view>
					</view>
					<view class="follow-person-items center">
						<view class="title">当前跟随人数</view>
						<view class="desc">
							{{(parseInt(detail.follow_count_text) + parseInt(detail.member_count))  || 0}}
						</view>
					</view>
					<view class="follow-person-items right">
						<view class="title">交易员保证金(usd)</view>
						<view class="desc">{{detail.deposit_text || 0}}</view>
					</view>
				</view>
				<view class="follow-person-item-cell" v-if="detail.follow_order">
					<view class="follow-person-items left">
						<view class="title">跟单金额</view>
						<view class="desc">{{detail.follow_order.money || 0}}</view>
					</view>
					<view class="follow-person-items center">
						<view class="title">跟单时间</view>
						<view class="desc">{{detail.follow_order.create_day || 0}}</view>
					</view>
					<view class="follow-person-items right ">
						<view class="title">累计收益</view>
						<view class="desc">{{detail.follow_order.total_revenue || 0}}</view>
					</view>
				</view>


			</view>
		</view>


		<view class="trading-main">
			<view class="trading-main-tab">
				<view class="trading-main-tab-item" :class="tabIndex == 0?'active':''" @tap="setTab(0)">
					当前操盘
				</view>
				<view class="trading-main-tab-item" :class="tabIndex == 1?'active':''" @tap="setTab(1)">
					历史操盘
				</view>
			</view>

			<view v-if="!isLoading && list.length == 0" class="empty-list">
				{{ detail.is_show_order == 0 && tabIndex === 1?'该交易员已隐藏持单':'暂无持仓单'}}
			</view>
			<view v-else class="trading-list">
				<view v-if=" detail.is_show_order == 0 && tabIndex === 1" class="empty-list">
					该交易员已隐藏持单
				</view>
				<view v-else class="trading-item" v-for="(item,index) in list" :key="index">
					<view class="flex justify-between holder-item-content">
						<view class="left-wrapper flex flex-column justify-between">
							<view class="holder-item-top">
								<text class="name">{{item.ptitle}}</text>
								<text class="right-text">{{item.ostyle.name}}</text>
								<text class="right-text">/{{item.onumber}}手</text>
							</view>
							<view class="price flex align-center">
								<text class="order-price">{{item.buyprice}}</text>
								<text class="arrow-right" style="padding: 0 15rpx;"> → </text>
								<view class="order-item-market-price" v-if="tabIndex == 0">
									{{ getMarketPrice(item.pid) }}
								</view>
								<view class="order-item-market-price" v-if="tabIndex == 1">
									{{ item.sellprice }}
								</view>
							</view>
							<view class="flex">
								<view class="flex mr-10">
									<text class="label mr-10">止损 :</text>
									<text class="value">{{item.loss?item.loss:'--'}}</text>
								</view>
								<view class="flex">
									<text class="label mr-10">止赢 :</text>
									<text class="value">{{item.surplus?item.surplus:'--'}}</text>
								</view>
							</view>
							<view class="flex" v-if="item.mark_price && item.mark_price != '' && item.mark_price != 0">
								<view class="flex mr-10">
									<text class="label mr-10">触发：</text>
									<text
										class="value">{{item.mark_price > item.trigger_price ? '>=':'<='}}{{item.mark_price}}</text>
								</view>
							</view>
						</view>
						<view class="right-wrapper flex flex-column justify-between">
							<view class="flex justify-end">
								<text class="right-text mr-10">#{{item.orderno}}</text>
							</view>

							<view class="ying-kui" v-if="tabIndex == 1" :style="{
									color:parseFloat(item.ploss)>0?getUpColor:getDownColor
								}">
								{{item.ploss > 0 ?"+":''}}{{item.ploss}}
							</view>
							<orderItemYingKui :order="item" v-if="tabIndex == 0"></orderItemYingKui>
							<view class="time">{{item.buytime}}</view>
						</view>
					</view>
				</view>
				<view v-if="false  && tabIndex == 1" class="trading-item" v-for="(item,index) in list" :key="index">
					<view class="trading-header">
						<view class="trading-header-name">
							{{item.ptitle}}
						</view>
						<view :class="item.ostyle.value == 1?'text-red':'text-green'">
							{{item.ostyle.name}}
						</view>
						<view class="trading-header-onumber">
							#{{item.onumber}}手
						</view>
					</view>
					<view class="trading-cell">
						<view class="trading-cell-item left">
							<view class="trading-cell-item-title">开仓均价</view>
							<view class="trading-cell-item-desc">{{item.buyprice || 0.00}}</view>
						</view>
						<view class="trading-cell-item center">
							<view class="trading-cell-item-title">平仓价</view>
							<view class="trading-cell-item-desc">{{item.sellprice || '--'}}</view>
						</view>
						<view class="trading-cell-item right">
							<view class="trading-cell-item-title">收益率</view>
							<view class="trading-cell-item-desc" :class="item.revenue > 0 ?'text-green':'text-red'">
								{{item.revenue > 0?'+':''}}{{item.revenue || 0.00}}%
							</view>
						</view>
					</view>

					<view class="trading-time">
						<!-- <text>{{item.create_time}}</text> -->
						<text v-if="item.ostaus.value == 1"> {{ item.selltime || '--'}}</text>
					</view>
				</view>
				<view v-if="detail.tabIndex == 0 && detail.is_show_order == 1" class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
				<view v-if="detail.tabIndex == 1 && detail.is_show_order == 1" class="page-bottom">{{isFinish?'仅显示最近3天':'加载更多'}}</view>
			</view>
		</view>
		<view v-if="detail.follow_status === false" class="follow-btn" @tap="onFollow">跟单</view>
		<!-- <view v-if="detail.follow_status === true" class="follow-close-btn" @tap="onEndFollow">结束跟单</view> -->

		<u-popup v-model="popupStatus" mode="bottom" border-radius="20">
			<view class="popup-wrapper">
				<view class="popup-header">
					<view class="header-right">
						<u-icon name="close" size="18" color="var(--base-grey)" @click="onFollow"></u-icon>
					</view>
				</view>
				<view class="popup-follow-person">
					<view class="follow-person-header">
						<image class="header-avater" v-if="detail.avatar" :src="detail.avatar" mode="aspectFill">
						</image>
						<view class="header-info">
							<view class="header-info-top">
								<view class="header-name">{{detail.nickname || "--"}}</view>
							</view>
							<view class="header-info-signature">
								{{detail.signature || '--'}}
							</view>
						</view>
						<view>
							<view></view>
						</view>
					</view>
				</view>

				<view class="popup-content">
					<view class="popup-title">
						理财跟单
					</view>
					<view class="popup-desc">
						说明：系统会按照您跟单的金额，增加交易员仓位风险率，T1日交易员需分润，分润不得低于您投入金额的保底0.6%的收益
					</view>
				</view>


				<view class="popup-bottom">
					<view class="popup-bottom-left">
						<view class="popup-bottom-title">投入跟单金额</view>
						<view class="popup-bottom-form-item">
							<u-input class="form-item-input" v-model="followPrice" type="digit" :height="80"
								placeholder="输入金额" :maxlength="10" />
						</view>
					</view>
					<view class="popup-bottom-right">
						<view class="popup-bottom-title">预计TI日收益</view>
						<view class="popup-bottom-desc">{{getRevenue}}</view>
					</view>
				</view>

				<view v-if="detail.follow_status === false" class="popup-btn" @tap="toFollow">
					确定跟单
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import orderItemYingKui from '@/components/order/order-item-yingkui.vue';
	import {
		mapState,
		mapGetters
	} from "vuex"
	export default {
		components: {
			customNav,
			orderItemYingKui
		},
		data() {
			return {
				detail: {},
				id: 0,
				tabIndex: 0,
				list: [],
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false,
				popupStatus: false,
				followPrice: ""
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
			...mapState("market", ["getMarketPrice"]),
			...mapGetters("market", ["getMarketPrice"]),
			...mapGetters("member", [ "getDownColor", "getUpColor"]),
			getRevenue() {
				if (this.detail) {
					if (this.detail.revenue_type == "lock" && this.detail.revenue_lock) {
						return parseFloat(this.detail.revenue_lock * this.followPrice / 100).toFixed(2) + "usd"
					} else if (this.detail.revenue_type == "auto" && this.detail.revenue_min && this.detail.revenue_min) {
						const min = parseFloat(this.detail.revenue_min);
						const max = parseFloat(this.detail.revenue_max);
						return min + "%~" + max + "%";
					}
				}
				return 0.00;
			},
		},
		methods: {
			setTab(index) {
				this.tabIndex = index
				this.currentPage = 0;
				this.isFinish = false;
				this.list = []
				this.requestList(true);
			},
			onFollow() {
				this.popupStatus = !this.popupStatus
			},
			requestDetail(isInit = false) {
				let t = this;
				uni.showLoading({
					title: '正在获取中...'
				})
				t.isLoading = true;
				t.$u.api.getFollowPersonDetailApi({
					id: this.id
				}).then((res) => {
					t.detail = res.data
					t.isLoading = false;
					uni.hideLoading()
				}).catch((err) => {
					t.isLoading = false;
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			},
			requestList(isInit = false) {
				let t = this;
				if (t.isFinish) {
					return false
				}
				uni.showLoading({
					title: '正在获取中...'
				})
				let obj = {
					page: t.currentPage + 1,
					limit: t.pageSize,
					id: t.id
				}
				if (this.tabIndex === 0) {
					obj.ostaus = 0
				} else if (this.tabIndex === 1) {
					obj.ostaus = 1
				}

				t.isLoading = true;
				t.$u.api.getFollowPersonTradingListApi(obj).then((res) => {
					if (res.data.data.length < t.pageSize) {
						t.isFinish = true
					}
					t.currentPage = parseInt(res.data.current_page)
					if (isInit) {
						t.list = res.data.data
					} else {
						t.list = [...t.list, ...res.data.data]
					}
					t.isLoading = false;
					uni.hideLoading()
				}).catch((err) => {
					t.isLoading = false;
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			},
			toFollow() {
				let t = this;
				let obj = {
					money: t.followPrice,
					id: t.id
				}
				if (t.isLoading) {
					return false;
				}
				uni.showLoading({
					title: '正在提交中...'
				})
				t.isLoading = true;
				t.$u.api.createFollowPersonOrderApi(obj).then((res) => {
					t.isLoading = false;
					uni.hideLoading()
					uni.showToast({
						icon: "none",
						title: (res.data.message || '跟单成功')
					})
					setTimeout(() => {
						t.onFollow();
						t.requestDetail()
					}, 1000)
				}).catch((err) => {
					t.isLoading = false;
					uni.showToast({
						icon: "none",
						title: "提交失败!" + (err.data.message || '')
					})
				})
			},
			onEndFollow() {
				let t = this
				uni.showModal({
					title: '提示',
					content: '确定结束当前跟单吗?',
					cancelText: '取消',
					confirmText: '确定结束',
					success: function(res) {
						if (res.confirm) {
							uni.showLoading({
								title: '正在提交中...'
							})
							t.$u.api.closeFollowPersonOrderApi({
								id: t.id
							}).then((res) => {
								t.isLoading = false;
								uni.hideLoading()
								uni.showToast({
									icon: "none",
									title: (res.data.message || '结束成功')
								})
								setTimeout(() => {
									t.requestDetail()
								}, 1000)
							}).catch((err) => {
								t.isLoading = false;
								uni.showToast({
									icon: "none",
									title: "提交结束失败!" + (err.data.message || '')
								})
							})
						} else if (res.cancel) {

						}
					}
				})
			}
		},
		onLoad() {

		},
		onShow() {
			this.id = this.$Route.query.id
			this.requestDetail();
			this.requestList(true)
		},
		onReachBottom() {
			this.requestList()
		},
	}
</script>

<style lang="scss">
	.custom-container {
		background: #f5f5f5;

		.follow-person-header-main {

			margin: 20rpx;
			background: #fff;
			border-radius: 20rpx;
			padding: 30rpx;

			.follow-person-header {
				display: flex;
				// justify-content: space-between;
				// width: calc(100% - );

				.header-avater {
					width: 80rpx;
					height: 80rpx;
					border-radius: $baseRadius;
				}

				.header-info {
					width: calc(100% - 80rpx - 200rpx);
					margin-left: 20rpx;
				}

				.header-info-top {
					display: flex;
					align-items: center;
				}

				.header-name {
					color: #000;
					margin-right: 10rpx;
				}

				.header-info-signature {
					margin-top: 10rpx;
					font-size: $baseFontSize;
					color: var(--base-grey);
				}

				.follow-status {
					border: 1rpx solid #E38E2A;
					color: #E38E2A;
					height: 50rpx;
					line-height: 50rpx;
					border-radius: $baseRadius;
					padding: 0 20rpx;
					margin-left: 20rpx;
				}
			}

			.follow-members-box {
				display: flex;
				align-items: center;
				justify-content: space-between;
				background: #F9F9F9;
				padding: 30rpx 20rpx;
				margin-top: 20rpx;
				border-radius: $baseRadius;

				.follow-members-box-left {
					display: flex;
					align-items: center;

					.follow-members-box-text {
						color: #000;
						margin-right: 10rpx;
					}
				}

				.follow-members-box-right {
					color: #000;

					.follow-members-box-count {
						color: #FF5733;

					}
				}

				.follow-members {
					display: flex;
					align-items: center;
					background: #ECF1FD;
					border-radius: $baseRadius;
					color: #2A82E4;
					padding: 2rpx 10rpx;

					.follow-members-icon {
						width: 26rpx;
						height: 26rpx;
						background-size: 100% 100%;
						background-image: url("~@/static/images-new1/follow/icon-person.png");
					}
				}
			}

			.follow-person-item-bottom {
				margin-top: 20rpx;

				.follow-person-item-cell {
					display: flex;
					align-items: center;
					justify-content: space-between;
					margin-top: 20rpx;

					.follow-person-items {
						width: 33.333%;

						&.left {
							text-align: left;
						}

						&.center {
							text-align: center;
						}

						&.right {
							text-align: right;
						}

						.title {
							color: #A6A6A6;
							white-space: nowrap;
						}

						.desc {
							margin-top: 10rpx;
							color: #000;
						}
					}
				}
			}
		}

		.trading-main {
			margin: 20rpx;
			background: #fff;
			border-radius: $baseRadius;
			padding: 30rpx;

			.trading-main-tab {
				display: flex;
				align-items: center;
				height: 100rpx;
				line-height: 100rpx;
				background: #F6F6F6;
				border-radius: 100rpx;
				padding: 0 10rpx;

				.trading-main-tab-item {
					width: calc(50% - 5rpx);
					text-align: center;

					&.active {
						background: #fff;
						height: 80rpx;
						line-height: 80rpx;
						border-radius: 80rpx;
					}
				}

			}

			.trading-list {
				margin-top: 20rpx;

				.trading-item {
					border-bottom: 1rpx solid #F7F7F7;
					padding: 30rpx 0;

		.ying-kui{
			
				font-size: $baseFontSizeLg;
				font-weight: bold;
				text-align: right;
		}
					.trading-header {
						display: flex;
						align-items: center;

						.trading-header-name {
							font-size: $baseFontSizeDefault;
							color: #000;
							margin-right: 20rpx;
							font-weight: bold;
						}

						.trading-header-onumber {
							margin-left: 20rpx;
							color: var(--base-grey);
						}
					}

					.trading-cell {
						margin-top: 20rpx;
						display: flex;
						align-items: center;
						justify-content: space-between;

						.trading-cell-item {
							width: 33.33%;

							&.left {
								text-align: left;
							}

							&.center {
								text-align: center;
							}

							&.right {
								text-align: right;
							}

						}

						.trading-cell-item-title {
							color: var(--base-grey);
						}

						.trading-cell-item-desc {
							margin-top: 10rpx;
							font-size: $baseFontSizeLg;
						}
					}

					.trading-time {
						margin-top: 20rpx;
						color: var(--base-grey);
					}
				}

				.holder-item-top {
					display: flex;
					align-items: center;

					.tag {
						margin-left: 20rpx;
						font-size: $baseFontSizeSm;
						color: $baseColor;
					}

					.name {
						margin-right: 20rpx;
						font-weight: bold;
					}

					.right-text {
						font-size: $baseFontSizeSm;
					}

				}

				.order-item-market-price {
					font-size: $baseFontSizeSm;
					color: var(--base-red);

				}

				.holder-item-content {
					min-height: 120rpx;
				}

				.left-wrapper {
					.name {
						// font-size: 28rpx;
						// font-weight: 550;
					}

					.order-price {
						font-size: $baseFontSizeSm;
						// font-weight: bold;
					}

					.now-price {
						font-size: $baseFontSizeSm;
						// font-weight: bold;
					}

					.label {
						font-size: $baseFontSizeSmX;
						;
						color: #999;
					}

					.value {
						font-size: $baseFontSizeSmX;
						;
						color: #999;
					}

				}

				.right-wrapper {
					.ying-kui {
						font-size: $baseFontSizeLg;
						text-align: right;
					}

					.time {
						font-size: $baseFontSizeSmX;
						color: var(--base-grey);
						text-align: right;
					}

					.right-text {
						font-size: $baseFontSizeSmX;
					}
				}

				.mr-10 {
					margin-right: 10rpx;
				}

				.bottom-box {
					margin-top: 40rpx;
					margin-bottom: 20rpx;
					display: flex;
					justify-content: space-between;

					.bottom-btn {
						padding: 0 30rpx;
						background: #f0f0f0;
						font-size: $baseFontSizeSm;
						text-align: center;
						color: #000;
						line-height: 48rpx;
						border-radius: $baseRadius;
					}
				}
			}

			.page-bottom {
				height: 80rpx;
				line-height: 80rpx;
				text-align: center;
				color: var(--base-grey);
				padding-bottom: 150rpx;
			}

			.empty-list {
				margin-top: 100rpx;
				margin-bottom: 100rpx;
				text-align: center;
				color: var(--base-grey);
			}
		}

		.follow-btn {
			width: calc(100% - 40rpx);
			position: fixed;
			bottom: 20rpx;
			left: 20rpx;
			right: 20rpx;
			color: #000;
			background: #F4CF4A;
			border-radius: 90rpx;
			line-height: 90rpx;
			line-height: 90rpx;
			text-align: center;
		}

		.follow-close-btn {
			width: calc(100% - 40rpx);
			position: fixed;
			bottom: 20rpx;
			left: 20rpx;
			right: 20rpx;
			color: #000;
			background: var(--base-grey);
			border-radius: 90rpx;
			line-height: 90rpx;
			line-height: 90rpx;
			text-align: center;
		}
	}

	.popup-wrapper {
		background: #f5f5f5;

		padding-bottom: 60rpx;

		.popup-header {
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

		}

		.popup-follow-person {
			margin: 0 20rpx 20rpx;
			background: #fff;
			border-radius: 20rpx;
			padding: 30rpx;

			.follow-person-header {
				display: flex;
				background: #fff;

				.header-avater {
					width: 80rpx;
					height: 80rpx;
					border-radius: $baseRadius;
				}

				.header-info {
					width: calc(100% - 80rpx - 200rpx);
					margin-left: 20rpx;
				}

				.header-info-top {
					display: flex;
					align-items: center;
				}

				.header-name {
					color: #000;
					margin-right: 10rpx;
				}

				.header-info-signature {
					margin-top: 10rpx;
					font-size: $baseFontSize;
					color: var(--base-grey);
				}
			}
		}

		.popup-content {
			margin: 20rpx;

			.popup-title {
				color: #000;
				font-size: $baseFontSizeDefault;
			}

			.popup-desc {
				color: var(--base-grey);
				font-size: $baseFontSize;
				margin-top: 10rpx;
			}
		}

		.popup-bottom {
			margin: 20rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			.popup-bottom-title {
				color: #000;
				margin-bottom: 10rpx;
			}

			.popup-bottom-form-item {
				width: 320rpx;
				height: 80rpx;
				line-height: 80rpx;
				padding: 0 20rpx;
				border-radius: $baseRadius;
				background: #fff;
				border: 1rpx solid #E5E5E5;
			}

			.popup-bottom-desc {
				height: 82rpx;
				line-height: 82rpx;
				font-weight: bold;
				font-size: $baseFontSizeLg;
			}
		}

		.popup-btn {
			width: calc(100% - 40rpx);
			margin: 20rpx;
			color: #000;
			background: #F4CF4A;
			border-radius: 90rpx;
			line-height: 90rpx;
			line-height: 90rpx;
			text-align: center;
		}
		
	}
</style>