<template>
	<view class="container container-page">
		<customNav title="交易" isBgImage  leftColor="#fff"></customNav>
		<view class="content">
			<view class="topView">
				买价、卖价与点差
			</view>
			<view class="botView lettSpace">
				买涨做多时，对应买价。买跌做空时，对应卖价。买卖价之间的差值即为点差。根据客户的账户级别，平台给予不同的点差优惠，该项优惠在平仓时返还。
			</view>
			<view class="topView">
				即时行情
			</view>
			<view class="botView lettSpace">
				平台的报价以买价（买涨价）为报价基础计算涨跌幅、涨跌额并绘制图表。行情报价由交易所提供并即时刷新。
			</view>
			<view class="topView">
				交易记录
			</view>
			<view class="botView lettSpace">
				通过APP或网址，客户可方便地查询平仓记录，请注意查询可能有时间限制。
			</view>
			<view class="topView">
				挂单
			</view>
			<view class="botView lettSpace">
				客户可提前设定交易方向、交易价格、交易数量、止盈止损进行挂单操作。挂单价须与当前价保持一定的价格距离。
			</view>
			<view class="topView">
				止盈止损
			</view>
			<view class="botView lettSpace">
				客户可对持仓设置和修改止盈止损，或在挂单时提前设置止盈止损。<br />
				止盈止损价须与当前价或挂单价保持一定的价格距离。<br />
				目前止盈止损的设置在撤销前一直有效。
			</view>
		</view>
		<!-- <view class="btnStyle">
			入金领赠金
		</view> -->
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

			};
		},
		methods: {
			clickLeft() {
				uni.navigateBack()
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		position: relative;
		padding-bottom: 100rpx;
	}

	.navSty {
		position: fixed;
		width: 100%;
		height: 100rpx;
		background-image: linear-gradient(to bottom, #2343b0, #3157c6, #4c71e4);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.navText {
		width: 100%;
		height: 60rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0 20rpx 0 10rpx;
	}

	.titleSty {
		color: #fff;
		font-size: 35rpx;
	}

	.btnStyle {
		position: fixed;
		bottom: 0%;
		width: 100%;
		height: 100rpx;
		background-color: var(--base-default);
		color: #fff;
		text-align: center;
		line-height: 100rpx;
	}

	.content {
		padding-top: 20rpx;

		.topView {
			padding: 25rpx 25rpx;
			color:var(--base-default);
			font-weight: 600;
			font-size: $baseFontSize;
		}

		.botView {
			padding: 15rpx 25rpx 35rpx;
			background-color: #f2f2f2;
			color: #939393;
			font-size: 24rpx;

			.content-explain {
				margin-bottom: 20rpx;
			}
		}
	}

	.lettSpace {
		letter-spacing: .2em;
		line-height: 3em;
	}
</style>
