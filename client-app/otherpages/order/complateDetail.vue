<template>
	<view class="container container-bg container-page">
		<customNav title="订单详情" bg="#f5f5f5">
		</customNav>
		<view v-if="orderDetail.id">
			<view class="order-detail">
				<view class="order-detail-header">
					<view class="order-detail-header-name">收益总览</view>
					<view class="order-detail-header-no">
						<text class="order-detail-header-no-title">订单号:</text>
						<text class="order-detail-header-no-desc">{{orderDetail.orderno}}</text>
					</view>
					<view class="order-detail-header-bottom">
						<view class="order-detail-header-bottom-left">
							<view class="order-detail-header-bottom-label">{{orderDetail.ostyle.name}}</view>
							<view class="order-detail-header-bottom-name">{{orderDetail.ptitle}}</view>
						</view>
						<view class="order-detail-header-bottom-right">
							<text class="order-detail-header-bottom-right-number">{{orderDetail.ploss}}</text>
							<text class="order-detail-header-bottom-right-unit">usd</text>
						</view>
					</view>
				</view>
				<view class="order-detail-cell">
					<view class="order-detail-cell-item">
						<view class="label">开仓时间</view>
						<view class="value">{{orderDetail.buytime||time}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">平仓时间</view>
						<view class="value">{{orderDetail.selltime||time}}</view>
					</view>
				</view>
				<view class="order-detail-cell cell2">
					<view class="order-detail-cell-item">
						<view class="label">平仓手数</view>
						<view class="value">{{orderDetail.onumber}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">开仓价格</view>
						<view class="value">{{orderDetail.buyprice || '--'}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">平仓价格</view>
						<view class="value">{{orderDetail.sellprice || '--'}}</view>
					</view>
				</view>
				<view class="order-detail-cell cell2">
					<view class="order-detail-cell-item">
						<view class="label">开仓方向</view>
						<view class="value">{{orderDetail.ostyle.name}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">止盈价格:</view>
						<view class="value">{{orderDetail.surplus?orderDetail.surplus:'--'}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">止损价格</view>
						<view class="value">{{orderDetail.loss?orderDetail.loss:'--'}}</view>
					</view>
				</view>
				<view class="order-detail-cell" v-if="orderDetail.mark_price && orderDetail.mark_price != 0 && orderDetail.mark_price != ''">
					<view class="order-detail-cell-item">
						<view class="label">标记价格</view>
						<view class="value">{{orderDetail.trigger_price || '--'}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">触发价格</view>
						<view class="value">{{orderDetail.mark_price || '--'}}</view>
					</view>
				</view>
				<view class="order-detail-cell">
					<view class="order-detail-cell-item">
						<view class="label">交易费</view>
						<view class="value">{{orderDetail.sx_fee || '--'}}</view>
					</view>
					<view class="order-detail-cell-item">
						<view class="label">平仓类型</view>
						<view class="value">{{orderDetail.sell_type.name || '--'}}</view>
					</view>
				</view>
			</view>


		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {
				orderId: 0,
				orderDetail: {}
			};
		},
		methods: {
			requestDetail() {
				let t = this
				uni.showLoading({
					title: '正在获取中...'
				})
				t.$u.api.getOrderDetailApi({
					id: t.orderId
				}).then((res) => {

					t.orderDetail = res.data
					uni.hideLoading()
				}).catch((err) => {
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			}
		},
		onLoad() {
			this.orderId = this.$Route.query?.id || 0
			this.requestDetail()
		}
	}
</script>

<style lang="scss">
	.container {
		.order-detail-header {
			margin: 20rpx;
			padding: 30rpx;
			background: #F4CF4A;
			border-radius: 20rpx;
			color: #000;

			.order-detail-header-name {
				font-weight: bold;
			}

			.order-detail-header-no {
				margin-top: 10rpx;
			}

			.order-detail-header-bottom {
				display: flex;
				align-items: center;
				justify-content: space-between;
				margin-top: 20rpx;
			}

			.order-detail-header-bottom-left {
				display: flex;
				align-items: center;

				.order-detail-header-bottom-label {
					background: #000;
					color: #F4CF4A;
					font-size: $baseFontSizeSm;
					height: 36rpx;
					line-height: 36rpx;
					padding: 0 12rpx;
					border-radius: 8rpx;
					margin-right: 10rpx;
				}

				.order-detail-header-bottom-name {
					font-weight: bold;
					font-size: $baseFontSizeLg;
				}
			}

			.order-detail-header-bottom-right {
				.order-detail-header-bottom-right-number {
					font-size: $baseFontSizeLgXX;
					font-weight: bold;
				}

				.order-detail-header-bottom-right-unit {

					margin-left: 8rpx;
				}
			}
		}
		.order-detail-cell{
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin: 20rpx;
			.order-detail-cell-item{
				width: calc(50% - 10rpx);
				background: #fff;
				border-radius: 10rpx;
				padding: 20rpx;
				.label{
					color: #595959;
				}
				.value{
					margin-top: 15rpx;
					color: #000;
					font-size: $baseFontSizeDefault;
				}
			}
			&.cell2{
				.order-detail-cell-item{
					width: calc(33.333% - 10rpx);
				}
			}
		}
	}
</style>