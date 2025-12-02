<template>
	<view class="container-page">
		<view class="custom-nav" :class="Number(opcity) > 0.01 ? 'border' : ''">
			<view class="custom-navbar-s" v-if="iStatusBarHeight > 0" :style="{
				height:'calc('+iStatusBarHeight + 'px'+')'
			}">
			</view>
			<view class="custom-nav-main">
				<view class="custom-left">
					<view class="custom-name">{{getConfig.system_name ||""}}</view>
					<view class="custom-logo"></view>
				</view>
				<router-link class="custom-kefu" to="/pages/other/kefu"></router-link>
			</view>
		</view>

		<view class="banner-main">
			<u-swiper mode="round" border-radius="0" bgColor="none" indicatorPos="bottomRight" class="swiper"
				:list="banner" name="cover" @click="onBannerChange" keyName="cover"></u-swiper>
		</view>


		<view class="nav-list">
			<router-link class="nav-item" to="/otherpages/memberCoin/recharge">
				<view class="nav-icon nav-1"></view>
				<view class="title">
					在线入金
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/memberCoin/withdraw">
				<view class="nav-icon nav-2"></view>
				<view class="title">
					立即提现
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/activity/index">
				<view class="nav-icon nav-3"></view>
				<view class="title">
					优惠活动
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/know/index">
				<view class="nav-icon nav-4"></view>
				<view class="title">
					用户须知
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/follow/index">
				<view class="nav-icon nav-5"></view>
				<view class="title">
					策略跟单
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/share/index">
				<view class="nav-icon nav-6"></view>
				<view class="title">
					推荐分享
				</view>
			</router-link>
			<router-link class="nav-item" to="/otherpages/about/product">
				<view class="nav-icon nav-7"></view>
				<view class="title">
					产品细则
				</view>
			</router-link>
			<router-link class="nav-item" to="/pages/other/kefu">
				<view class="nav-icon nav-8"></view>
				<view class="title">
					在线客服
				</view>
			</router-link>
		</view>

		<router-link class="notice-mian" to="/pages/other/notice-list" v-if="noticeList.length > 0">
			<view class="notice-left">
				<view class="notice-icon"></view>
				<view class="notice-title">公告</view>
				<view class="notice-desc" v-for="(item, index) in noticeList" :key="index">
					<view class="notice-item-title">
						{{ item.title }}
					</view>
				</view>
			</view>
			<view class="notice-right icon icon-yao-1"></view>
		</router-link>
		<view class="pro-mian" v-if="priceList.length > 0 && marketStatus">
			<view class="header flex justify-between align-center">
				<view class="header-item">
					<text class="title">热门产品</text>
				</view>
			</view>
			<scroll-view scroll-x="true" class="scroll-view">
				<proItemComponent :priceList="priceList" @invokeGetKData="invokeGetKData"></proItemComponent>
				<!-- <view class="pro-item-r"></view> -->
			</scroll-view>
		</view>

		<view class="activity-main">
			<view class="header">
				<view class="header-item">
					<text class="title">热门推荐</text>
				</view>
			</view>
			<view class="activity-list">
				<view class="activity-item item-left" @click="goToPath('/otherpages/activity/turntable')">
					<view class="activity-item-header">
						<view class="activity-item-left-icon"></view>
						<view class="activity-item-title">
							亮点活动
						</view>
					</view>
					<view class="activity-item-bottom">
						<view class="desc">
							[抽奖活动] 入金抽华为苹果手机USD现金
						</view>
						<!-- <view class="content">
							免费参与，每月一次，前20名可获积分，第一名可获...
						</view> -->
						<view class="activity-item-bottoms">
							<view class="text">999+人已参与</view>
							<view class="activity-bottoms-btn">立即参与</view>
						</view>
					</view>
				</view>
				<view class="activity-item item-right">
					<view class="activity-items activity-items-top" @click="goToPath('/otherpages/follow/index')">

						<view class="activity-item-header">
							<view class="activity-item-icon"></view>
							<view class="activity-item-title">
								热门跟单
							</view>
						</view>
						<view class="activity-item-bottom">
							<view class="desc">
								实时跟随交易员，稳定收益省用盯盘精力
							</view>
							<view class="btn">
								<text>查看详细</text>
								<text class="btn-icon"></text>
							</view>
						</view>
					</view>

					<view class="activity-items activity-items-bottom">
						<view class="activity-item-header">
							<view class="activity-item-icon"></view>
							<view class="activity-item-title">
								最新公告
							</view>
						</view>
						<router-link class="activity-item-bottom"  :to="'/pages/other/notice-detail?id='+noticeList[0].id" v-if="noticeList.length > 0">
							<view class="desc">
								{{noticeList[0].title || "【通知】關於對长期未登錄的MT5交易帳戶管理..."}}
							</view>
							<view class="btn">
								<text>查看详细</text>
								<text class="btn-icon"></text>
							</view>
						</router-link>
					</view>
				</view>
			</view>
		</view>

		<view class="prohot-main">
			<view class="header">
				<view class="header-item">
					<text class="title">HOT</text>
				</view>
			</view>
			<view class="prohot-list">
				<hotProItemComponent></hotProItemComponent>
			</view>
			<router-link class="prohot-more"  to="/pages/market/index">
				<view class="prohot-more-title">更多</view>
				<view class="prohot-more-icon"></view>
			</router-link>
		</view>
		<copyright></copyright>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import proItemComponent from '@/pages/index/pro-item.vue';
	import hotProItemComponent from '@/pages/index/hot-pro-item.vue';
	import copyright from '@/components/common/copyright.vue';
	import _, {
		forEach
	} from "lodash"
	import {
		mapState,
		mapGetters,
		mapMutations
	} from "vuex"
	import {
		createWebsocket,
		sendSocketMessage
	} from '@/websocket/index.js'

	const option = {
		tooltip: {
			show: false
		},
		series: []
	};

	export default {
		components: {
			customNav,
			proItemComponent,
			hotProItemComponent,
			copyright
		},
		data() {
			return {
				tabList: [],
				configInfo: {},
				//实名认证弹框
				recognizeFlag: false,
				//是否实名
				isRecognizeComplate: false,
				//首页数据
				homeData: {},
				//轮播图
				banner: [],
				//消息列表
				noticeList: [],
				//行情
				priceList: [{
					k: 0,
				}, {
					k: 1,
				}, {
					k: 2,
				}],
				iStatusBarHeight: 0,
				activitys: [],
				opcity: 0,
				scrollH: 0
			}
		},
		async onLoad() {
			try {
				uni.getSystemInfo({
					success: (res) => {
						this.scrollH = res.windowWidth;
					},
				});
			} catch (e) {
				//TODO handle the exception
			}
			// 获取高度
			this.iStatusBarHeight = uni.getSystemInfoSync().statusBarHeight
		},
		async onShow() {
			this.invokeHomeApi()
			try {
				this.personInfo = await this.$u.api.personInfoApi()

				if (this.personInfo.agent_kefu) {
					this.agent_kefu_status = 1;
				}
			} catch (e) {
				//TODO handle the exception
			}
		},
		onHide() {
			if (this.timer) {
				clearInterval(this.timer)
				this.timer = null
			}
		},
		onPageScroll: function(e) {
			//nvue暂不支持滚动监听，可用bindingx代替
			let _opcity = e.scrollTop / Number(this.scrollH);
			this.opcity = _opcity;
		},
		computed: {
			...mapGetters("member", ["getToken", "getDownColor", "getUpColor"]),
			...mapState("market", ["marketStatus"]),
			...mapGetters("app", ["getConfig"]),
		},
		methods: {
			onBannerChange(res) {
				if (this.banner[res].link) {
					window.location.href = this.banner[res].link
				}
			},
			goToPath(path) {
				this.$Router.push({
					path
				})
			},
			//跳转登入页
			toLogin() {
				this.$Router.push({
					name: "login",
					query: {
						redirect: "/pages/index/index"
					}
				})
			},
			toMarketList() {
				// #ifdef APP-PLUS
				uni.switchTab({
					url: "/pages/market/index"
				})
				// #endif

				//#ifdef H5
				this.$Router.push("/pages/market/index")
				//#endif
			},
			async invokeHomeApi() {
				uni.showLoading()
				setTimeout(() => {
					uni.hideLoading();
				}, 3000)
				try {
					let res = await this.$u.api.getHomeApi()
					let bind_status = res.data.bind_status
					let banner = res.data.banner
					this.noticeList = res.data.notice || []
					//轮播图
					this.banner = banner
					this.activitys = res.data.activitys
					this.setChartData()

				} catch (e) {
					//TODO handle the exception
					// this.noticeList = ["测试测试", "测试2222222"]
				}
				uni.hideLoading()
				// uni.stopPullDownRefresh();
			},
			async invokeGetKData(obj = {}) {
				try {
					let res = await this.$u.api.getProHome()
					let lists = res.data.pro_data || []
					this.$nextTick(() => {
						lists.forEach((item, index) => {
							if (this.priceList[index]) {
								item = Object.assign(this.priceList[index], item)
								let echartData = obj;
								echartData.series[0].data = item.k_line.data || []
								echartData.xAxis.date = item.k_line.time || []
								item.echartData = _.cloneDeep(echartData)
								this.priceList[index] = item
							}
						})
						this.priceList = JSON.parse(JSON.stringify(this.priceList))
					})
				} catch (e) {
					//TODO handle the exception
				}
			},
		},
	}
</script>
<style lang="scss">
	.container-page {
		background: #F5F5F5;
		padding-bottom: 100rpx;
	}

	.custom-nav {
		position: fixed;
		z-index: 9;
		width: 100%;
		top: 0;
		left: 0;
		padding: 0 20rpx;
		background: none;
		transition: background-color all 0.1s;

		&.border {
			background-color: #fff;
		}

		.header-kefu {
			height: 80rpx;

			.header-kefu-icon {
				height: 80rpx;
				width: 80rpx;
			}
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
				width: 40rpx;
				height: 40rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/common/kefu.png");
			}
		}
	}

	.banner-main {
		margin-top: 86rpx;
		height: auto;
		background: none;
		z-index: -1;
		position: relative;
		min-height: 200rpx;
	}


	.nav-list {
		border-radius: $baseRadius;
		padding: 40rpx 20rpx 0;
		display: flex;
		flex-wrap: wrap;
		z-index: 99;

		.nav-item {
			width: 25%;
			text-align: center;
			margin-bottom: 40rpx;

			.nav-icon {
				margin: 0 auto;
				height: 60rpx;
				width: 60rpx;
				background-size: 100% 100%;

				&.nav-1 {
					background-image: url("~@/static/images-new1/index/nav-icon-1.png");
				}

				&.nav-2 {
					background-image: url("~@/static/images-new1/index/nav-icon-2.png");
				}

				&.nav-3 {
					background-image: url("~@/static/images-new1/index/nav-icon-3.png");
				}

				&.nav-4 {
					background-image: url("~@/static/images-new1/index/nav-icon-4.png");
				}

				&.nav-5 {
					background-image: url("~@/static/images-new1/index/nav-icon-5.png");
				}

				&.nav-6 {
					background-image: url("~@/static/images-new1/index/nav-icon-6.png");
				}

				&.nav-7 {
					background-image: url("~@/static/images-new1/index/nav-icon-7.png");
				}

				&.nav-8 {
					background-image: url("~@/static/images-new1/index/nav-icon-8.png");
				}
			}

			.title {
				margin-top: 10rpx;
			}
		}
	}



	.notice-mian {
		margin: 10rpx 20rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		background: linear-gradient(90deg, rgba(255, 252, 232, 0.72) 0%, rgba(255, 254, 242, 0.72) 100%);
		padding: 16rpx 0;
		border: 1rpx solid #fff;
		border-radius: $baseRadius;

		.notice-left {
			display: flex;
			align-items: center;
			width: calc(100% - 56rpx);

			.notice-icon {
				height: 32rpx;
				width: 32rpx;
				background-image: url("~@/static/images-new1/index/notice-icon.png");
				background-size: 100%;
				margin-right: 8rpx;
			}

			.notice-title {
				height: 32rpx;
				line-height: 32rpx;
				color: #000;
				font-size: 24rpx;
				width: 80rpx;
			}



			.notice-swiper {
				flex: 1;
				height: 40rpx;
			}

			.notice-desc {
				display: flex;
				align-items: center;
				overflow: hidden;
			}

			.notice-desc .notice-item-title {
				flex: 1;
				white-space: nowrap;
				animation: marqueeAnim 10s linear infinite;
				color: rgba(146, 116, 90, 1);
				/* 使用动画 */
			}


			.notice-desc {
				color: #000;
				width: calc(100% - 80rpx - 42rpx);
			}
		}

		.notice-right {
			height: 36rpx;
			line-height: 36rpx;
			text-align: center;
			width: 36rpx;
			color: var(--base-grey);
		}
	}

	.pro-mian {
		margin-top: 20rpx;
		position: relative;
		margin: 20rpx;
		z-index: 1;
		background: #fff;
		border-radius: $baseRadius;

		.header {
			padding: 20rpx;

			.header-item {
				display: flex;
				align-items: center;
			}

			.title {
				color: #000;
			}
		}
	}


	.activity-main {
		position: relative;
		margin: 20rpx;
		z-index: 1;
		background: #fff;
		border-radius: $baseRadius;

		.header {
			padding: 20rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.header-item {
				display: flex;
				align-items: center;
			}

			.title {
				color: #000;
			}
		}

		.activity-list {
			margin-left: 20rpx;
			padding-bottom: 20rpx;
			display: flex;
			flex-wrap: wrap;

			.activity-item {
				width: calc(50% - 20rpx);
				border-radius: $baseRadius;

				.activity-item-title {
					font-size: $baseFontSizeDefault;
					font-weight: 550;
				}

				.activity-item-header {
					display: flex;
					align-items: center;

					.activity-item-left-icon {
						height: 30rpx;
						width: 30rpx;
						background-size: 100% 100%;
						background-image: url("~@/static/images-new1/index/activity-header-icon.png");
						margin-right: 10rpx;
					}
				}

				&.item-left {
					background-color: #FFFFF5;
					border-radius: $baseRadius;
					margin-right: 20rpx;
					padding: 20rpx;
					position: relative;
					z-index: 1;

					&::after {
						position: absolute;
						content: "";
						width: 60%;
						height: 60%;
						background-image: url("~@/static/images-new1/index/activity-left-icon.png");
						background-size: 100% 100%;
						background-repeat: no-repeat;
						background-position: center;
						bottom: 0;
						right: 0;
						z-index: -1;
					}
				}

				&.item-right {
					.activity-items {
						padding: 20rpx;
					}

					.activity-items.activity-items-top {
						background-color: #FFF5F7;
						border-radius: $baseRadius;

						.activity-item-icon {
							height: 30rpx;
							width: 30rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/index/activity-right-top-header-icon.png");
							margin-right: 10rpx;
						}
					}

					.activity-items.activity-items-bottom {
						margin-top: 20rpx;
						background-color: #FFFAF5;
						border-radius: $baseRadius;

						.activity-item-icon {
							height: 30rpx;
							width: 30rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/index/activity-right-bottom-header-icon.png");
							margin-right: 10rpx;
						}
					}
				}

				.activity-item-bottom {
					margin-top: 6rpx;
					position: relative;
					height: calc(100% - 60rpx);

					.desc {
						width: 100%;
						margin-top: 10rpx;
						font-size: $baseFontSizeSm;
						color: #383838;
						display: -webkit-box;
						-webkit-line-clamp: 2;
						/* 限制行数为2 */
						-webkit-box-orient: vertical;
						overflow: hidden;
					}

					.btn {
						display: flex;
						font-size: $baseFontSizeSmX;
						margin-top: 10rpx;
						align-items: center;
						height: 40rpx;
						color: #DB4242;

						.btn-icon {
							height: 30rpx;
							width: 30rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/index/activity-right-icon.png");
							margin-right: 10rpx;
						}

					}

					.content {
						margin-top: 10rpx;
						color: #383838;
						font-size: $baseFontSizeSm;
					}

					.activity-item-bottoms {
						position: absolute;
						bottom: 0;
						left: 0;

						.text {
							font-size: $baseFontSizeSmX;
							color: #808080;

						}

						.activity-bottoms-btn {
							margin-top: 20rpx;
							padding: 0 30rpx;
							font-size: $baseFontSizeSm;
							height: 60rpx;
							line-height: 60rpx;
							border-radius: 60rpx;
							background: #F4CF4A;
							color: #000;
						}
					}
				}
			}

			.activity-item.item-2,
			.activity-item.item-4 {
				margin-right: 0 !important;
			}

			.activity-item.item-4 {
				background-color: rgba(242, 249, 255, 1);
			}

			.activity-item.item-1 .item-right {
				background-image: url("~@/static/images/20250227/index-icon-1.png");
			}

			.activity-item.item-2 .item-right {
				background-image: url("~@/static/images/20250227/index-icon-2.png");
			}

			.activity-item.item-3 .item-right {
				background-image: url("~@/static/images/20250227/index-icon-3.png");
			}

			.activity-item.item-4 .item-right {
				background-image: url("~@/static/images/20250227/index-icon-4.png");
			}
		}
	}

	.prohot-main {
		position: relative;
		margin: 20rpx 20rpx 40rpx;
		z-index: 1;
		background: #fff;
		border-radius: $baseRadius;

		.header {
			padding: 20rpx;
			display: flex;
			justify-content: space-between;
			align-items: center;

			.header-item {
				display: flex;
				align-items: center;
			}

			.title {
				color: #000;
			}
			border-bottom: 1rpx solid #f5f5f5;
		}

		.prohot-list {
			display: flex;
			flex-wrap: wrap;
		}

		.prohot-more {
			margin-top: 20rpx;
			padding-bottom: 40rpx;
			height: 40rpx;
			width: 100%;
			display: flex;
			align-items: center;
			color: #A6A6A6;
			justify-content: center;
			font-size: $baseFontSizeSmX;

			.prohot-more-icon {
				margin-top: 5rpx;
				height: 30rpx;
				width: 30rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/common/right-grey.png");
			}
		}
	}
</style>