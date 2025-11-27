<template>
	<view class="container container-page">
		<customNav leftColor="#000" bg="none">
		</customNav>
		<view class="moni-mian">
			<image class="moni-mian-title" src="@/static/images/activity/moni-title.png" mode="heightFix" />
			<view class="moni-mian-time">
				<view class="moni-mian-time-box" v-if="config.time">{{config.time}}</view>
			</view>
			<image class="moni-mian-icon-1" src="@/static/images/activity/moni-icon-1.png" mode="widthFix" />
			<view class="moni-mian-bottom">
				<image class="moni-mian-icon-2" src="@/static/images/activity/moni-icon-2.png" mode="aspectFit" />
				<view class="moni-mian-bottom-right">
					<view class="moni-mian-bottom-right-list">

						<view class="moni-mian-bottom-right-item" v-for="(item,index) in config.numbers" :key="index">
							<text class="title">第{{index + 1}}名</text>
							<text style="margin-left: 10rpx;">奖励</text>
							<text class="number">{{item}}</text>
							<text style="margin-left: 10rpx;">USD</text>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="ranking-main">
			<view class="ranking-main-bg"></view>
			<view class="ranking-main-top">
				<view class="ranking-main-top-item ranking-main-top-1" v-if="list[1]">
					<image class="item-icon" :src="require(`@/static/images/activity/moni-icon-top2.png`)"
						mode="widthFix">
					</image>
					<view class="ranking-main-item-avater">
						<image class="item-avater-image2" :src="require(`@/static/images/activity/moni-icon-top1s.png`)"
							mode="widthFix"></image>
						<image class="item-avater-image" :src="require(`@/static/images/icon/member-default-icon.png`)"
							mode="widthFix"></image>
					</view>
					<view class="name">{{list[1].member.username_text || '无名称'}}</view>
					<view class="number">盈利{{list[1].balance}}</view>
				</view>
				<view class="ranking-main-top-item ranking-main-top-2" v-if="list[0]">
					<image class="item-icon" :src="require(`@/static/images/activity/moni-icon-top1.png`)"
						mode="widthFix">
					</image>
					<view class="ranking-main-item-avater">
						<image class="item-avater-image2" :src="require(`@/static/images/activity/moni-icon-top2s.png`)"
							mode="widthFix"></image>
						<image class="item-avater-image" :src="require(`@/static/images/icon/member-default-icon.png`)"
							mode="widthFix"></image>
					</view>
					<view class="name">{{list[0].member.username_text || '无名称'}}</view>
					<view class="number">盈利{{list[0].balance}}</view>
				</view>
				<view class="ranking-main-top-item ranking-main-top-3" v-if="list[2]">
					<image class="item-icon" :src="require(`@/static/images/activity/moni-icon-top3.png`)"
						mode="widthFix">
					</image>
					<view class="ranking-main-item-avater">
						<image class="item-avater-image2" :src="require(`@/static/images/activity/moni-icon-top3s.png`)"
							mode="widthFix"></image>
						<image class="item-avater-image" :src="require(`@/static/images/icon/member-default-icon.png`)"
							mode="widthFix"></image>
					</view>
					<view class="name">{{list[2].member.username_text || '无名称'}}</view>
					<view class="number">盈利{{list[2].balance}}</view>
				</view>
			</view>
			<view class="ranking-main-list">
				<view class="ranking-main-list-item" v-for="(item,index) in list" :key="index"
					v-if="index > 2 && item.member">
					<view class="item-sort">{{index > 8 ?'':'0'}}{{index + 1}}</view>
					<view class="item-right">
						<image class="item-avater-image" :src="require(`@/static/images/icon/member-default-icon.png`)"
							mode="widthFix"></image>
						<view class="item-member">

							<view class="name">{{item.member.username_text || '无名称'}}</view>
							<view class="number">盈利{{item.balance}}</view>
						</view>
					</view>
				</view>
			</view>
		</view>
		<view class="tips-desc">活动结束奖励发入至实盘交易账户可以提现下单</view>
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
				config: {
					numbers: [],
					time: ''
				},
				list: []
			}

		},
		methods: {
			requestMemberCoinSort() {
				let t = this
				uni.showLoading({
					title: '正在获取中...'
				})
				t.$u.api.getMemberCoinSort({}).then((res) => {
					t.config = res.data.config
					t.list = res.data.list
					uni.hideLoading()
				}).catch((err) => {

					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			},
			onLoad() {
				this.requestMemberCoinSort()
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background-image: url('@/static/images/activity/moni-bg.png');
		padding-bottom: 100rpx;
	}

	.moni-mian {
		width: 100%;
		position: relative;
		z-index: 1;

		.moni-mian-title {
			margin-top: 50rpx;
			width: 80%;
			left: 10%;
			display: block;
			height: 112rpx;
		}

		.moni-mian-icon-1 {
			position: absolute;
			top: 0;
			width: 100%;
			display: block;
			z-index: -1;
		}

		.moni-mian-bottom {
			margin-top: 40rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;

			.moni-mian-icon-2 {
				width: 436rpx;
				height: 468rpx;
				// height: auto;
				display: block;
			}

			.moni-mian-bottom-right {
				margin-right: 40rpx;
				margin-top: 200rpx;
				color: #B36A25;

				.moni-mian-bottom-right-item {
					font-size: $baseFontSizeSm;

					.title {}
				}

				.number {
					font-weight: bold;
					margin-left: 10rpx;
					color: var(--base-red);
					font-size: $baseFontSizeLg;
				}
			}
		}

		.moni-mian-time {
			margin-top: 20rpx;
			width: 100%;
			display: flex;
			justify-content: center;
		}

		.moni-mian-time-box {
			height: 48rpx;
			border-radius: 48rpx;
			display: inline-block;
			color: #fff;
			font-weight: bold;
			font-size: $baseFontSize;
			line-height: 48rpx;
			padding: 0 20rpx;
			background: linear-gradient(96deg, #FFAD19 0%, #EE941B 35%, #D66E1E 100%);
		}
	}

	.ranking-main {
		width: calc(100% - 24rpx);
		margin: -60rpx 12rpx 0;
		min-height: 800rpx;
		position: relative;
		z-index: 9;

		&::after {
			position: absolute;
			content: "";
			height: 80rpx;
			background-image: url('@/static/images/activity/moni-box-1.png');
			background-size: 100% 100%;
			top: 0;
			left: 0;
			width: 100%;
			z-index: 1;
			display: block;
		}

		.ranking-main-bg {
			position: absolute;
			height: calc(100% - 156rpx);
			background-image: url('@/static/images/activity/moni-box-2.png');
			background-size: 100% 100%;
			top: 78rpx;
			bottom: 78rpx;
			left: 0;
			width: 100%;
			z-index: -1;
			display: block;
		}

		&::before {
			position: absolute;
			content: "";
			height: 80rpx;
			background-image: url('@/static/images/activity/moni-box-3.png');
			background-size: 100% 100%;
			bottom: 0;
			left: 0;
			width: 100%;
			display: block;
		}

		.ranking-main-top {

			position: relative;
			z-index: 99;
			padding: 50rpx 60rpx 0;
			display: flex;
			align-items: center;
			justify-content: space-between;
			max-height: 500rpx;

			.ranking-main-top-item {
				text-align: center;
				width: calc(33.33% - 20rpx);
			}

			.ranking-main-top-1 {
				margin-top: 60rpx;

			}

			.ranking-main-top-3 {
				margin-top: 60rpx;
			}

			.item-icon {
				height: 40rpx;
				width: 82rpx;
			}

			.ranking-main-item-avater {
				width: 100%;
				position: relative;
			}

			.item-avater-image2 {
				width: 182rpx;
				height: 212rpx;
				margin-left: 20rpx;
			}

			.item-avater-image {
				position: absolute;
				height: 120rpx;
				width: 120rpx;
				top: 64rpx;
				left: 32rpx;
			}

			.name {
				font-weight: bold;
				line-height: 24rpx;
				color: #000;
			}

			.number {
				margin-top: 20rpx;
				border-radius: 40rpx;
				padding: 5rpx 15rpx;
				font-size: $baseFontSizeSm;
				display: inline-block;
				color: #fff;
				background: linear-gradient(to right, #FDCA6C 0%, #CB8B16 100%);
			}
		}

		.ranking-main-list {
			padding: 50rpx 60rpx 80rpx;
			z-index: 9999;

			.ranking-main-list-item {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 24rpx;

				.item-sort {
					text-align: center;
					height: 72rpx;
					line-height: 56rpx;
					color: #fff;
					width: 70rpx;
					background-image: url('@/static/images/activity/moni-icon-item.png');
					background-size: 100% 100%;
					background-repeat: no-repeat;
				}

				.item-right {
					margin-left: 20rpx;
					width: calc(100% - 90rpx);
					background: linear-gradient(90deg, #FBDEB5 0%, #FFB77D 100%);
					border-radius: 48rpx;
					border: 1px solid #CE8F1B;
					height: 96rpx;
					display: flex;
					align-items: center;
					justify-content: space-between;

					.item-avater-image {
						margin-top: -8rpx;
						margin-left: -8rpx;
						height: 92rpx;
						width: 92rpx;
						border-radius: 96rpx;
						border: 2px solid #fff;
					}

					.item-member {
						width: calc(100% - 120rpx);
						display: flex;
						justify-content: space-between;

						.name {
							font-weight: bold;
							color: #CB8B16;
						}

						.number {
							border-radius: 40rpx;
							padding: 8rpx 20rpx;
							font-size: 24rpx;
							display: inline-block;
							color: #fff;
							background: linear-gradient(to right, #FDCA6C 0%, #CB8B16 100%);
							margin-right: 24rpx;
						}
					}
				}
			}
		}
	}

	.tips-desc {
		text-align: center;
		margin-top: 20rpx;
	}
</style>