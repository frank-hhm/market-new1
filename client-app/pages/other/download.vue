<template>
	<view class="container-page">
		<view class="custom-nav" :class="Number(opcity) > 0.01 ? 'border' : ''">
			<view class="custom-navbar-s" v-if="iStatusBarHeight > 0" :style="{
				height:'calc('+iStatusBarHeight + 'px'+')'
			}">
			</view>
			<view class="custom-nav-main">
				<image class="custom-logo" src="@/static/images/logo-a1.png" mode="aspectFit">
				</image>
			</view>
		</view>
		<view class="download-title">
			<image class="download-title-image" src="@/static/images/other-download/body-title.png" mode="widthFix">
			</image>
			<image class="download-title-icon" src="@/static/images/other-download/header-icon.png"
				mode="heightFix"></image>
		</view>
		<view class="download-body">
			<image class="body-market" src="@/static/images/other-download/body-market.png" mode="widthFix"></image>
			<view class="download-body-mask"></view>
		</view>

		<view class="download-advantage">
			<view class="header">
				·5大优势·
			</view>
			<view class="list">
				<view class="list-item">
					<image class="icon" src="@/static/images/other-download/advantage-icon-1.png" mode="aspectFit">
					</image>
					<view class="title">成本优势</view>
				</view>
				<view class="list-item">
					<image class="icon" src="@/static/images/other-download/advantage-icon-2.png" mode="aspectFit">
					</image>
					<view class="title">资金安全</view>
				</view>
				<view class="list-item">
					<image class="icon" src="@/static/images/other-download/advantage-icon-3.png" mode="aspectFit">
					</image>
					<view class="title">软件优势</view>
				</view>
				<view class="list-item">
					<image class="icon" src="@/static/images/other-download/advantage-icon-4.png" mode="aspectFit">
					</image>
					<view class="title">交易优势</view>
				</view>
				<view class="list-item">
					<image class="icon" src="@/static/images/other-download/advantage-icon-5.png" mode="aspectFit">
					</image>
					<view class="title">服务优势</view>
				</view>
			</view>
		</view>

		<view class="main-2">
			<view class="header-title">
				<view class="icon-left">·</view>
				<view>产品丰富 一站全配置</view>
				<view class="icon-right">·</view>
			</view>
			<view class="header-desc">
				涵盖外汇、贵金属、原油、股指等30+等全球热门交易产品
			</view>
			<view class="main-body">
				<view class="main-body-content">
					<view class="main-body-center">
						<view class="main-body-center-title">
							<text class="number">30</text>
							<text class="unit">+</text>
							全球热门</br>交易产品
						</view>
					</view>

					<view class="item-icon item-1">
						<image class="icon" src="@/static/images/other-download/main-2-icon-1.png"></image>
						<view class="title">
							外汇
						</view>
						<view class="desc">涵盖国际最大交易量之欧元美元、澳元美元等20+热门交易货币</view>
					</view>

					<view class="item-icon item-2">
						<image class="icon" src="@/static/images/other-download/main-2-icon-2.png"></image>
						<view class="title">
							股指
						</view>
						<view class="desc">涵盖深受A股投资者青睐的全球热门指数，沪深300、标普500等</view>
					</view>

					<view class="item-icon item-3">
						<image class="icon" src="@/static/images/other-download/main-2-icon-3.png"></image>
						<view class="title">
							贵金属
						</view>
						<view class="desc">涵盖最受欢迎交易的黄金、白银可抵御通胀，也可投资增值</view>
					</view>

					<view class="item-icon item-4">
						<image class="icon" src="@/static/images/other-download/main-2-icon-4.png"></image>
						<view class="title">
							原油
						</view>
						<view class="desc">涵盖目前投资市场上最大原油产品分别是美国石油及英国石油</view>
					</view>
				</view>
			</view>
		</view>
		<view class="main-3">
			<view class="header-title">
				<view class="icon-left">·</view>
				<view>资金安全 即财富保障</view>
				<view class="icon-right">·</view>
			</view>
			<view class="header-desc">
				拥有FSA VFSC ASIC业内最严格资质牌照
			</view>
			<view class="main-body">
				<view class="item">
					<view class="item-left">
						<view class="text">公平 公正 公开</view>
						<image class="item-bg" src="@/static/images/other-download/main-3-icon-1.png"
							mode="widthFix"></image>
					</view>
					<view class="item-right">
						<view class="item-title">
							每一笔订单公开透明，安全可查询
						</view>
						<view class="item-desc">
							每笔历史分笔数据于交易后台随时查询，为每口价格配置唯一的报价序号，且该唯一的报价序号将展示在每笔成交订单上。
						</view>
					</view>
				</view>
				<view class="item">
					<view class="item-left">
						<view class="text">免赔付结余保证</view>
						<image class="item-bg" src="@/static/images/other-download/main-3-icon-2.png"
							mode="widthFix"></image>
					</view>
					<view class="item-right">
						<view class="item-title">
							严格控制您的交易风险
						</view>
						<view class="item-desc">
							特设免赔付结余保证的风险管理系统，以确保您的账户不会出现负数结余，把交易风险严格控制在绝对范围内。
						</view>
					</view>
				</view>
				<view class="item">
					<view class="item-left">
						<view class="text">信息安全</view>
						<image class="item-bg" src="@/static/images/other-download/main-3-icon-3.png"
							mode="widthFix"></image>
					</view>
					<view class="item-right">
						<view class="item-title">
							加密保护信息、技术支持信息安全
						</view>
						<view class="item-desc">
							国际专业的技术团队，从安全技术、内部建设、交易订单和隐私管理等方面用最先进稳定的技术，确保您的交易安全以及满意度。
						</view>
					</view>
				</view>
			</view>
		</view>

		<view class="main-4">
			<view class="header-title">
				快来下载APP体验吧
			</view>
			<view class="main-body">
				<view class="download-btn common-btn download-iphone" @click="downloadIos">
					<image class="btn-icon" src="@/static/images/other-download/iphone.png"></image>
					<view class="title">iphone 下载</view>
				</view>
				<view class="download-btn common-btn download-android" @click="downloadAndroid">
					<image class="btn-icon" src="@/static/images/other-download/android.png"></image>
					<view class="title">安卓 下载</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	export default {
		components: {},
		data() {
			return {
				header_bgPaddingTop: 40,
				header_bgHeight: 500,
				header_shimingHeight: 60,
				opcity: 0,
				scrollH: 0,
				iStatusBarHeight: 0,
				configData: {
					android: null,
					ios: null
				},
				isDownload: false
			};
		},
		methods: {
			downloadIos() {
				let iosData = this.configData.ios
				if (iosData == null || !iosData.download_url) {
					uni.showToast({
						icon: "none",
						title: "暂不可下载"
					})
					return false;
				}
				window.open(iosData.download_url, '_blank')
			},
			async requestDownloadApp() {
				uni.showLoading()
				try {
					let res = await this.$u.api.getDownloadApp({})
					this.configData = res.data
					uni.hideLoading()

				} catch (e) {
					uni.hideLoading()
					//TODO handle the exception
				}
			},
			downloadAndroid() {
				let androidData = this.configData.android
				if (androidData == null || !androidData.download_url) {
					uni.showToast({
						icon: "none",
						title: "暂不可下载"
					})
					return false;
				}
				this.isDownload = true;

				// #ifdef APP-PLUS
				uni.downloadFile({
					url: androidData.download_url, //仅为示例，并非真实的资源
					success: (res) => {
						this.isDownload = false;
						if (res.statusCode === 200) {
							console.log('下载成功');
						}
					},
					fail: (err) => {
						this.isDownload = false;
						uni.showToast({
							icon: "none",
							title: "下载失败:" + JSON.stringify(err)
						})
					}
				});
				// #endif

				// #ifdef H5
				window.open(androidData.download_url, '_blank')
				//#endif
			}
		},
		async onLoad() {
			try {
				this.requestDownloadApp()

				uni.getSystemInfo({
					success: (res) => {
						this.scrollH = res.windowWidth;
					},
				});
				// let res = await this.$u.api.initConfigApi()
				// this.web_name = res.name
				// this.configInfo = res
			} catch (e) {
				//TODO handle the exception
			}
			// 获取高度
			this.iStatusBarHeight = uni.getSystemInfoSync().statusBarHeight
			/*#ifdef APP-PLUS*/
			this.header_bgHeight = this.header_bgHeight + this.iStatusBarHeight;
			this.header_bgPaddingTop = this.header_bgPaddingTop + this.iStatusBarHeight;
			/*#endif*/
		},
		onPageScroll: function(e) {
			//nvue暂不支持滚动监听，可用bindingx代替
			let _opcity = e.scrollTop / Number(this.scrollH);
			this.opcity = _opcity;
		},
	}
</script>

<style scoped lang="scss">
	.container-page {
		min-height: 100vh;
		background-color: #D1EFFF;


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
				background-color: #D1EFFF;
			}


			.custom-nav-main {
				height: 84rpx;

				.custom-logo {
					height: 84rpx;
					width: 240rpx;
				}
			}

		}

		.download-title {
			padding-top: 124rpx;
			height: 300rpx;
			position: relative;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 1;

			.download-title-image {
				width: 80%;
			}

			.download-title-icon {
				position: absolute;
				z-index: -1;
				height: 300rpx;
				width: auto;
				right: 24rpx;
			}
		}

		.download-body {
			padding-top: 56rpx;
			position: relative;
			max-height: 100vh;
			width: 100%;
			background-image: url('~@/static/images/other-download/body-bg.png');
			background-repeat: no-repeat;
			display: flex;
			justify-content: center;
			z-index: 1;

			.body-market {
				width: calc(100% - 48rpx);
				z-index: 1;
			}

			.download-body-mask {
				position: absolute;
				width: 100%;
				height: 80rpx;
				left: 0;
				bottom: 0;
				background: #C8E8FB;
				box-shadow: 0px -14px 16px 0px #C8E8FB;
				z-index: 2;
			}
		}

		.download-advantage {
			padding: 34rpx 36rpx 50rpx;
			background-color: #C8E8FB;
			display: flex;
			justify-content: center;
			flex-direction: column;

			.header {
				height: 90rpx;
				width: 480rpx;
				background: linear-gradient(to right, #4D77E6 0%, #80C6FE 100%);
				border-top-right-radius: 24rpx;
				border-top-left-radius: 24rpx;
				text-align: center;
				line-height: 90rpx;
				color: #fff;
				margin: 0 auto;
				font-weight: 600;
				font-size: 35rpx;
				letter-spacing: 4rpx;
			}

			.list {
				display: flex;
				justify-content: space-between;
				padding: 30rpx 24rpx;
				background-color: #fff;
				background-image: url('~@/static/images/other-download/advantage-bg2.png');
				border-radius: 24rpx;

				.list-item {
					width: 20%;
					text-align: center;

					.icon {
						margin: 0 auto;
						width: 80rpx;
						height: 80rpx;
					}

					.title {
						font-size: 24rpx;
						font-weight: 600;
					}
				}
			}
		}

		.main-2 {
			padding-top: 28rpx;
			min-height: 500rpx;
			width: 100%;
			background-color: #C8E8FB;

			.header-title {
				position: relative;
				text-align: center;
				font-size: 35rpx;
				font-weight: 600;
				height: 100rpx;
				line-height: 100rpx;
				color: #000;
				display: flex;
				justify-content: center;

				.icon-left {
					margin-right: 20rpx;
					margin-left: 10rpx;
					position: relative;
				}

				.icon-left::after {
					position: absolute;
					content: '';
					width: 100rpx;
					height: 4rpx;
					border-radius: 4rpx;
					top: 50%;
					left: -100rpx;
					background: linear-gradient(90deg, rgba(217, 217, 217, 0) 0%, #202020 100%);
				}

				.icon-right {
					margin-left: 20rpx;
					position: relative;
				}

				.icon-right::after {
					position: absolute;
					content: '';
					width: 100rpx;
					height: 4rpx;
					border-radius: 4rpx;
					right: -100rpx;
					top: 50%;
					background: linear-gradient(90deg, #202020 0%, rgba(217, 217, 217, 0) 100%);
				}
			}

			.header-desc {
				font-size: 24rpx;
				text-align: center;
				color: #979797;
				max-width: 90%;
				margin: 0 auto;
				line-height: 24rpx;
			}

			.main-body {
				padding: 120rpx 14rpx 108rpx;

				.main-body-content {
					position: relative;
					padding: 284rpx 0 266rpx;
					background-image: url('~@/static/images/other-download/main-2.png');
					background-size: 100%;
					background-position: center;
					background-repeat: no-repeat;
					display: flex;
					align-items: center;
					justify-content: center;

					.item-icon {
						position: absolute;
						width: calc(100% / 3);
						text-align: center;

						.icon {
							height: 128rpx;
							width: 128rpx;
						}

						.title {
							font-size: 24rpx;
							font-weight: bold;
						}

						.desc {
							margin-top: 6rpx;
							font-size: 20rpx;
							line-height: 24rpx;
							color: #202020;
							font-weight: 400;
						}
					}

					.item-icon.item-1 {
						top: 0;
					}

					.item-icon.item-2 {
						left: 0;
					}

					.item-icon.item-3 {
						right: 0;
					}

					.item-icon.item-4 {
						bottom: 0;
					}
				}

				.main-body-center {
					height: 200rpx;
					width: 200rpx;
					text-align: center;
					border-radius: 200rpx;
					background: linear-gradient(90deg, #618CFF 0%, #81C8FE 100%);
					display: flex;
					align-items: center;
					justify-content: center;

					.main-body-center-title {
						color: #fff;
						font-weight: 550;

						.number {
							font-size: 32rpx;
							color: #FBC949;
						}

						.unit {
							color: #FBC949;
						}
					}
				}
			}
		}

		.main-3 {
			padding-top: 28rpx;
			width: 100%;

			.header-title {
				position: relative;
				text-align: center;
				font-size: 35rpx;
				font-weight: 600;
				height: 100rpx;
				line-height: 100rpx;
				color: #000;
				display: flex;
				justify-content: center;

				.icon-left {
					margin-right: 20rpx;
					margin-left: 10rpx;
					position: relative;
				}

				.icon-left::after {
					position: absolute;
					content: '';
					width: 100rpx;
					height: 4rpx;
					border-radius: 4rpx;
					top: 50%;
					left: -100rpx;
					background: linear-gradient(90deg, rgba(217, 217, 217, 0) 0%, #202020 100%);
				}

				.icon-right {
					margin-left: 20rpx;
					position: relative;
				}

				.icon-right::after {
					position: absolute;
					content: '';
					width: 100rpx;
					height: 4rpx;
					border-radius: 4rpx;
					right: -100rpx;
					top: 50%;
					background: linear-gradient(90deg, #202020 0%, rgba(217, 217, 217, 0) 100%);
				}
			}

			.header-desc {
				font-size: 24rpx;
				text-align: center;
				color: #979797;
				max-width: 90%;
				margin: 0 auto;
				line-height: 24rpx;
			}

			.main-body {
				padding: 60rpx 14rpx 38rpx;

				.item {
					display: flex;
					justify-content: space-between;
					background-color: #fff;
					border-radius: 24rpx;
					overflow: hidden;
					height: 212rpx;
					margin-bottom: 24rpx;

					.item-left {
						text-align: center;
						line-height: 212rpx;
						color: #fff;
						width: 212rpx;
						position: relative;
						// background-color: red;
						z-index: 1;

						.text {
							position: absolute;
							z-index: 9;
							height: 100%;
							width: 100%;
							background-color: rgba(0, 0, 0, 0.3);
							font-weight: 600;
						}

						.item-bg {
							position: absolute;
							left: 0;
							top: 0;
							// background-color: red;
							height: 100%;
							width: 100%;
							z-index: 2;
						}
					}

					.item-right {
						width: calc(100% - 212rpx - 24rpx);
						margin-left: 12rpx;
						margin-right: 12rpx;
						padding: 12rpx 0;

						.item-title {
							font-weight: bold;
							color: #527FE9;
							line-height: 80rpx;
							white-space: nowrap;
							/* 防止文本换行 */
							overflow: hidden;
							/* 隐藏超出容器宽度的内容 */
							text-overflow: ellipsis;
							/* 在被截断的文本末尾添加省略号 */
							height: 80rpx;
						}

						.item-desc {
							font-size: 20rpx;
							color: #000000;
							line-height: 32rpx;
							text-align: left;
							height: 96rpx;
							display: -webkit-box;
							-webkit-box-orient: vertical;
							-webkit-line-clamp: 3;
							/* 设置你想要显示的最大行数 */
							overflow: hidden;
							text-overflow: ellipsis;
							white-space: normal;
							/* 允许文本换行 */
						}
					}
				}
			}
		}

		.main-4 {
			padding-bottom: 120rpx;
			width: 100%;

			.header-title {
				width: 100%;
				text-align: center;
				color: var(--base-red);
				font-size: 32rpx;
				line-height: 100rpx;
				font-weight: bold;
			}

			.main-body {
				display: flex;
				flex-direction: column;
				justify-content: center;
				width: 80%;
				margin: 0 auto;

				.download-btn {
					margin-top: 60rpx;
					width: 100%;
					display: flex;
					align-items: center;
					justify-content: center;
					background: linear-gradient(to right, #56D5FF 0%, #7BA2FF 100%);
					line-height: 120rpx;
					color: #fff;
					border-radius: 120rpx;
					font-size: 35rpx;
					font-weight: bold;
					text-align: center;

					.btn-icon {
						height: 60rpx;
						width: 60rpx;
						margin-right: 20rpx;
					}
				}
			}
		}
	}
</style>