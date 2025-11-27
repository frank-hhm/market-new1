<template>
	<view class="container container-page">
		<customNav title="合约属性"  leftColor="#000"></customNav>
		<view class="page-main">
			<!-- <view class="page1"> -->
			<view class="main-item-box">
				<view class="title">{{info.ptitle}}</view>
				<view class="desc">{{info.heyue_danwei || '无配置'}}</view>
				<view class="item-box-list">
					<view class="item-box-items">
						<view class="box-title">货币单位</view>
						<view class="box-desc">{{info.money_danwei || '无配置'}}</view>
					</view>
					<view class="item-box-items">
						<view class="box-title">点差</view>
						<view class="box-desc">{{info.dian_cha || '无配置'}}</view>
					</view>
					<view class="item-box-items">
						<view class="box-title">单笔交易数</view>
						<view class="box-desc">{{info.buy_min || '无配置'}}手~{{info.buy_max || '无配置'}}手</view>
					</view>
				</view>
			</view>
			<view class="main-item time">
				<view class="title">
					<view class="text">交易时间 GMT+0</view>
				</view>
				<view class="desc"><rich-text :nodes="info.opentimetext|| '无配置'"></rich-text></view>
			</view>
			<view class="main-item">
				<view class="title">结算时间
				</view>
				<view class="desc">{{info.selldate || '无配置'}}(GMT+0)</view>
			</view>
			<view class="main-item">
				<view class="title">单手保证金</view>
				<view class="desc">{{info.pay_choose || '无配置'}}USD/手</view>
			</view>
			<view class="main-item-tips">注：例如数值为2，表示报价为2位小数点，即{{Number(2).toFixed(info.xiaoshu)}}。</view>
	
			<!-- <view class="text2">
					<view class="box">手数值差</view>
					<view class="box1">0.01手</view>
				</view>
				<view class="text3">
					<text class="box7">最小挂单距离</text>
					<view class="box4">
						<text class="num">50</text>
						<text class="num2 lettSpace">注：当您进行交易时，可在挂单区间看到范围提示。</text>
					</view>
				</view> -->
			<view class="main-item-tips">注：建仓时，您所付出作为买卖双方确保履约的担保费用;该产品为固定保证金。</view>
			<view class="main-item">
				<view class="title">强制保证金</view>
				<view class="desc">{{info.baozhengjin_rate || '无配置'}}%</view>
			</view>
			<view class="main-item-tips">注：当你的保证金比例低于该数值，会平仓亏损最大的持仓订单。</view>
			<view class="main-item">
				<view class="title">隔夜保证金</view>
				<view class="desc">{{info.geye_baozhengjin_rate || '无配置'}}%</view>
			</view>
			<view class="main-item-tips">注：当时间至结算时间你的保证金比例低于该数值，会平仓亏损最大的持仓订单。</view>
			<view class="main-item">
				<view class="title">报价小数点</view>
				<view class="desc">{{info.decimal || '无配置'}}</view>
			</view>
			<view class="main-item-tips red">注：本公司保留对以上数据可因市场情况而调整的权利</view>
			<!-- </view> -->
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
				info: {},
			};
		},
		async onShow() {
			try {
				let res = await this.$u.api.getProductParamsDetail({
					id: this.$Route.query.id,
				})
				this.info = res.data
			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>


<style lang="scss" scoped>
	.container {
		background: #f5f5f5;
	}

	.page-main {
		/* padding-left: 30rpx; */
		width: 100%;
		background: none;
		margin-top: 16rpx;
	}
	
	.main-item-box{
		margin: 20rpx;
		padding: 20rpx;
			background: #F4CF4A;
			border-radius: 20rpx;
			position: relative;
			text-align: center;
			.title{
				font-weight: bold;
				font-size: $baseFontSizeLg;
			}
			.desc{
			margin-top:10rpx;
				color: #7D6200;
			}
		.item-box-list{
			margin-top: 20rpx;
			display: flex;
			justify-content: space-between;
			.item-box-items{
				width: calc(33.333% - 10rpx);
				background: rgba(255, 255, 255, 0.3);
				border-radius: 12rpx;
				padding: 20rpx 0;
				.box-title{
					font-size: $baseFontSizeSm;
					color: #7D6200;
				}
				.box-desc{
				font-weight: bold;
				color: #000000;
					
						margin-top: 10rpx;
						font-size: $baseFontSize;
				}
			}
		}
	}

	.main-item {
		margin: 24rpx 24rpx 0;
		width: calc(100% - 48rpx);
		height: 92rpx;
		background: #fff;
		display: flex;
		align-items: center;
		font-size: $baseFontSizeLg;
		border-radius: $baseRadius;
		padding: 24rpx;

		.title {
			font-size: $baseFontSize;
			// width: 160rpx;
		}

		.desc {
			font-size: $baseFontSize;
			font-weight: bold;
			margin-left: 20rpx;
		}
		&.time {
			align-items: normal;
			height: auto;
			display: block;
			.title {
				// width: auto;
			}
			.desc {
				margin-left: 0;
			}
		}

	}

	.main-item-tips {
		margin-left: 36rpx;
		color: var(--base-grey);
		font-size: $baseFontSizeSm;
		margin-top: 12rpx;

		&.red {
			color:#FF5733;
			margin-bottom: 40rpx;
		}
	}

	.main-item2 {
		margin: 24rpx 24rpx 0;
		display: flex;
		justify-content: space-between;

		.main-items {
			width: calc(33.33% - 20rpx);
			background: #fff;
			margin-right: 20rpx;
			padding: 32rpx;
			border-radius: $baseRadius;
			text-align: center;

			&:last-child {
				margin-right: 0;
			}

			.title {
				color: var(--base-grey);
				font-size: $baseFontSize;
			}

			.desc {
				margin-top: 6rpx;
				font-size: $baseFontSizeSm;
				color: #000000
			}
		}
	}
</style>