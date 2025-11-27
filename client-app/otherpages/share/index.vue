<template>
	<view class="container container-page">
		<customNav title="我要推荐" leftColor="#000" bg="#f5f5f5">
		</customNav>
		<view class="share-main">
			<view class="share-main-top">
				<view class="share-main-item">
					<view class="share-main-item-title">佣金余额(USD)</view>
					<view class="share-main-item-number number-lg">{{info.commission_balance || 0}}</view>
				</view>
				<router-link class="share-main-btn" to="/otherpages/share/withd">
					佣金提现
				</router-link>
			</view>
			<view class="share-main-bottom">
				<view class="share-main-item">
					<view class="share-main-item-title">待入账(USD)</view>
					<view class="share-main-item-number">{{info.commission_settlement_price || 0}}</view>
				</view>
				<view class="share-main-item">
					<view class="share-main-item-title">累计佣金(USD)</view>
					<view class="share-main-item-number">{{info.commission_total || 0}}</view>
				</view>
			</view>
		</view>
		<view class="team-main">
			<view class="team-head">
				<view class="team-title">我的邀请</view>
				<view class="team-right">
					<u-icon name="arrow-right"></u-icon>
				</view>
			</view>
			<view class="team-box">
				<view class="team-share-list">
					<view class="team-share-list-item">
						<view class="item-title">已注册</view>
						<view class="item-count">{{info.people || 0}}</view>
					</view>
					<view class="team-share-list-item">
						<view class="item-title">已体验</view>
						<view class="item-count">{{info.people_moni || 0}}</view>
					</view>
					<view class="team-share-list-item">
						<view class="item-title">已激活</view>
						<view class="item-count">{{info.people_start || 0}}</view>
					</view>
				</view>
			</view>
		</view>

		<view class="member-share-main">

			<view class="member-share-main-head">
				<view class="member-share-main-head-icon"></view>
				<view class="member-share-main-head-title">您的推荐二维码</view>
			</view>

			<view class="member-share-main-qrcode">
				<view class="member-share-main-qrcode-title">
					新时代的交易平台
				</view>
				<view class="member-share-main-qrcode-desc">
					轻松、极速买卖
				</view>
				<view class="member-share-main-qrcode-desc2">
					诚邀您的加入
				</view>
				<view class="member-share-main-qrcode-box" @tap="download">
					<u-image v-if="info.invite_url" class="item-image" mode="scaleToFill" width="160" height="160"
						border-radius="12rpx" :src="info.invite_url"></u-image>
				</view>
			</view>
			<view class="member-share-main-head">
				<view class="member-share-main-number-icon"></view>
				<view class="member-share-main-head-title">您的推荐码</view>
			</view>
			<view class="member-share-main-number-desc">
				在官网或APP注册时填写上您的推荐码
			</view>
			<view class="member-share-main-number" @tap="copy(info.invite_code)">
				<view class="member-share-main-number-text">
					{{info.invite_code || 123456}}
				</view>
				<view class="member-share-copy">
					复制
				</view>
			</view>
			<view class="member-share-main-head">
				<view class="member-share-main-link-icon"></view>
				<view class="member-share-main-head-title">您的推荐链接</view>
			</view>
			<view class="member-share-main-link" @tap="copy(info.invite_url2)">
				<view class="member-share-main-link-text">
					{{info.invite_url2 || ""}}
				</view>
				<view class="member-share-copy">
					复制链接
				</view>
			</view>


			<view class="cell-main">
				<view class="cell-head">
					<view class="cell-title">温馨提示</view>
				</view>
				<view class="cell-box">
					<view class="cell-box-head">
						<view class="cell-box-head-count">
							1
						</view>
						<view class="cell-box-head-title">
							推广佣金规则
						</view>
					</view>
					<view class="cell-box-content">
						推荐<text class="cell-box-content-bold">1-5名</text>有效用户将获得，
						推荐用户所产生手续费的<text class="cell-box-content-red">10%</text>
					</view>
					<view class="cell-box-content">
						推荐<text class="cell-box-content-bold">6-20名</text>有效用户将获得
						，推荐用户所产生手续费的<text class="cell-box-content-red">20%</text>
					</view>
					<view class="cell-box-content">
						推荐<text class="cell-box-content-bold">21-60名</text>有效用户将获得
						，推荐用户所产生手续费的<text class="cell-box-content-red">60%</text>
					</view>
				</view>
				<view class="cell-box">
					<view class="cell-box-head">
						<view class="cell-box-head-count">
							2
						</view>
						<view class="cell-box-head-title">
							佣金发放时间
						</view>
					</view>
					<view class="cell-box-content">
						佣金将于交易日<text class="cell-box-content-bold">次日18:00</text>发放至账户，具体佣金金额已实际发放为准
					</view>
				</view>
			</view>
		</view>


		<diyBottomNav></diyBottomNav>
	</view>
</template>


<script>
	import customNav from '@/components/custom-nav/index.vue';
	import diyBottomNav from '@/components/diy-bottom-nav/index.vue';
	import {
		mapGetters
	} from "vuex"
	export default {
		components: {
			customNav,
			diyBottomNav
		},
		data() {
			return {
				showDialogCode: false,
				showDialogQrcode: false,
				showDialogLink: false,
				info: {}
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
		},
		methods: {

			download() {
				//#ifdef APP-PLUS
				this.saveImageApp();
				//#endif

				//#ifdef H5
				this.saveImageApp();
				//#endif
			},
			async saveImageH5() {
				// const link = document.createElement('a');
				// link.href = this.info.invite_url;
				// link.download = '我的分享二维码.png';
				// link.click();

				let a = document.createElement("a")
				let clickEvent = document.createEvent("MouseEvents");
				a.setAttribute("href", this.info.invite_url)
				a.setAttribute("download", '我的分享二维码.png')
				clickEvent.initEvent('click', true, true)
				a.dispatchEvent(clickEvent);
			},
			async saveImageApp() {
				try {
					const res = await uni.downloadFile({
						url: this.info.invite_url,

						success: (res) => {
							if (res.statusCode === 200) {
								uni.saveImageToPhotosAlbum({
									filePath: res.tempFilePath,
									success: () => {
										uni.showToast({
											title: '图片保存成功',
											icon: 'success'
										});
									},
									fail: (err) => {
										console.error('保存失败', err);
										uni.showToast({
											title: '保存失败',
											icon: 'none'
										});
									}
								});
							} else {
								console.error('下载失败', res);
								uni.showToast({
									title: '下载失败',
									icon: 'none'
								});
							}
						},
						fail: (err) => {
							console.error('下载失败', err);
							uni.showToast({
								title: '下载失败',
								icon: 'none'
							});
						}
					});
				} catch (error) {
					console.error('保存图片出错', error);
					uni.showToast({
						title: '保存图片出错',
						icon: 'none'
					});
				}
			},
			copy(value) {
				uni.setClipboardData({
					data: String(value),
					success: function() {
						uni.showToast({
							icon: "success",
							title: "复制成功"
						})
					},
					fail(e) {
						console.log("复制错误", e)
					}
				});
			},
		},
		async onShow() {
			try {
				await this.$u.api.getMemberDetailApi().then(res => {
					this.info = res.data.member
				})
			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>
<style scoped lang="scss">
	.container {
		padding-bottom: 100rpx;
		background-color: #f5f5f5;

	}

	.share-main {
		margin: 0 20rpx 20rpx;
		background: #F4CF4A;
		padding: 30rpx;
		border-radius: 20rpx;
		color: #000000;

		.share-main-top,
		.share-main-bottom {
			display: flex;
			align-items: center;
			justify-content: space-between;
		}

		.share-main-bottom {
			margin-top: 20rpx;
		}

		.share-main-btn {
			height: 60rpx;
			line-height: 60rpx;
			border-radius: 30rpx;
			padding: 0 30rpx;
			background: #fff;
		}

		.share-main-item {
			.share-main-item-number {
				margin-top: 12rpx;
				font-size: $baseFontSizeLg;
				font-weight: 550;
			}

			.number-lg {
				font-size: 40rpx;
			}
		}
	}

	.share-code {
		display: flex;
		margin: 20rpx auto;
		width: 80%;
		justify-content: space-between;
		text-align: center;

		.share-code-item-icon {
			margin: 0 auto;
			height: 120rpx;
			width: 120rpx;
			border-radius: 120rpx;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #fff;
		}

		.share-code-item-icon.one {
			background: var(--base-blue);
		}

		.share-code-item-icon.two {
			background: var(--base-red);
		}

		.share-code-item-icon.three {
			background: var(--base-purple);
		}

		.item-title {
			margin-top: 10rpx;
		}
	}


	.team-main {
		margin: 30rpx;
		background: #fff;
		border-radius: 24rpx;
		overflow: hidden;

		.team-head {
			height: 96rpx;
			line-height: 96rpx;
			display: flex;
			padding: 0 30rpx;
			justify-content: space-between;

			.team-title {
				font-weight: bold;
			}
		}

		.team-box {
			padding: 0 30rpx;
		}


		.team-share-list {
			display: flex;
			justify-content: space-between;
			margin-top: 20rpx;
			margin-bottom: 40rpx;

			.team-share-list-item {
				text-align: center;
				color: #000000;

				.item-title {
					font-size: $baseFontSize;
					font-weight: 550;
				}

				.item-count {
					margin-top: 20rpx;
					font-size: $baseFontSizeLgX;
					font-weight: 550;
				}
			}
		}
	}

	.member-share-main {
		margin: 20rpx;
		background: #fff;
		border-radius: 20rpx;
		padding: 10rpx 30rpx 30rpx;

		.member-share-main-head {
			height: 46rpx;
			line-height: 46rpx;
			display: flex;
			align-items: center;
			color: #000000;
			margin-top: 20rpx;

			.member-share-main-head-icon {
				margin-right: 10rpx;
				height: 30rpx;
				width: 30rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/share/head-qrcode.png");
			}

			.member-share-main-number-icon {

				margin-right: 10rpx;
				height: 30rpx;
				width: 30rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/share/head-number.png");
			}

			.member-share-main-link-icon {
				margin-right: 10rpx;
				height: 30rpx;
				width: 30rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/share/head-link.png");
			}
		}

		.member-share-main-qrcode {
			border-radius: 20rpx;
			padding: 30rpx;
			margin-top: 20rpx;
			background-color: #FFF9CF;
			position: relative;
			z-index: 1;
			overflow: hidden;

			&::after {
				position: absolute;
				content: "";
				height: 100%;
				width: 80%;
				right: -20%;
				bottom: 0;
				background-image: url("~@/static/images-new1/share/qrcode-main-icon.png");
				background-size: 100% auto;
				background-repeat: no-repeat;
				background-position: right;
				z-index: -1;
			}

			.member-share-main-qrcode-title {
				color: #FFA600;
				font-size: 40rpx;
			}

			.member-share-main-qrcode-desc {
				color: #FFC300;
				font-size: 32rpx;
			}

			.member-share-main-qrcode-desc2 {
				display: inline-block;
				background: linear-gradient(90deg, #FFC300 0%, #FFDD00 100%);
				border-radius: 50rpx;
				padding: 2rpx 20rpx;
				font-size: $baseFontSizeSm;
				color: #fff;
			}

			.member-share-main-qrcode-box {
				margin-top: 20rpx;
				width: 160rpx;
				height: 160rpx;
				background: #fff;
			}
		}

		.member-share-main-number-desc {
			margin-top: 10rpx;
			color: var(--base-grey);
		}

		.member-share-main-number {
			margin-top: 10rpx;
			border: 1rpx solid #E5E5E5;
			background: #fff;
			padding: 10rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			border-radius: $baseRadius;

			.member-share-main-number-text {
				font-weight: bold;
				color: #000000;
				font-size: 40rpx;
			}
		}

		.member-share-main-link {
			margin-top: 10rpx;
			border: 1rpx solid #E5E5E5;
			background: #fff;
			padding: 10rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			border-radius: $baseRadius;

			.member-share-main-link-text {
				width: calc(100% - 240rpx);
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
			}
		}

		.member-share-copy {
			color: #000000;
			padding: 10rpx 30rpx;
			background: #F4CF4A;
			border-radius: $baseRadius;
		}

	}

	.cell-main {
		margin-top: 30rpx;
		border-radius: 20rpx;
		background: #FEFDF9;
		padding: 30rpx;
		border: 1rpx solid #FFDD00;

		.cell-title {
			color: #FF8D1A;
			font-size: $baseFontSizeDefault;
			font-weight: 550;
		}

		.cell-box {
			.cell-box-head {
				display: flex;
				align-items: center;
				margin: 20rpx 0;

				.cell-box-head-count {
					height: 40rpx;
					width: 40rpx;
					border-radius: 40rpx;
					background: #F4CF4A;
					text-align: center;
					line-height: 40rpx;
					color: #fff;
					margin-right: 10rpx;
				}

				.cell-box-head-title {
					color: #000000;
				}
			}

			.cell-box-content {
				color: var(--base-grey);
				margin-bottom: 10rpx;

				.cell-box-content-bold {
					color: #000000;
					font-weight: 550;
				}

				.cell-box-content-red {
					font-weight: 550;
					color: #FF5733;
				}
			}
		}
	}
</style>