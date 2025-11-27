<template>
	<view class="container container-page">
		<view class="custom-top" v-if="statusBarHeight" :style="{
			height:statusBarHeight + 'px'
		}">
		</view>
		<!-- 	<customNav title="详细" isBgImage leftColor="#fff">
		</customNav> -->
		<view class="custom-left" :style="{
			top:`calc(${statusBarHeight}px + 20rpx)`
		}" @tap="navigateBack">
			<view class="icon icon-zuo-1"></view>
		</view>
		
		<view class="detail-header" :style="[
			statusBarHeight > 0 ? `top:${statusBarHeight}px`:``
		]">
			<view class="detail-header-bg" :style="{
				backgroundImage: 'url('+ensureHttp(info.cover)+')'
			}"></view>
		</view>
		<view class="detail-main" :style="[
			statusBarHeight > 0 ? `margin-top:calc(380rpx + ${statusBarHeight}px)`:'380rpx'
		]">
			<view class="title">{{info.title}}</view>
			<view class="time ">{{info.time}}</view>
			<view class="desc" v-if="(info.title_d)">{{info.title_d}}</view>
			<view class="detail-cc">
				<rich-text :nodes="content"></rich-text>
			</view>
		</view>
		<view class="detail-main-tips">
			<view class="title">免责声明：</view>
			<view class="desc">
				本文版权归第三方作者所有，相关授权事宜请联系原作者。文中观点均来自原作者，不代表本平台观点及立场。特别提醒，本文内容仅供参考，不作为实际操作建议，交易风险自担。
			</view>
		</view>
	</view>
</template>

<script>
	import {
		isString
	} from 'lodash';
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {
				info: {},
				content: '',
				statusBarHeight: 0
			};
		},
		created() {
			// 获取系统信息
			uni.getSystemInfo({
				success: (res) => {
					// 获取状态栏高度
					this.statusBarHeight = res.statusBarHeight;
				}
			});
		},
		methods: {

			ensureHttp(url) {
				if (!/^(http|https):\/\//i.test(url)) {
					url = 'http://' + url;
				}
				return url;
			},
			navigateBack() {
				// 返回上一页
				try {
					uni.navigateBack({
						fail(e) {
							console.log("返回错误", e)
						}
					});
				} catch (e) {
					//TODO handle the exception
					uni.switchTab({
						url: '/pages/index/index'
					});
				}
			},

			async requestNewsDetail() {
				try {
					uni.showLoading()
					await this.$u.api.getArticleDetailApi({
						id: this.id
					}).then(res => {
						this.info = res.data
						this.content = this.info.content.replace(/auto/g, '100%')

						this.content = this.content.replace(/<p style="/g, '<p style="margin: 20px 0;')
						this.content = this.content.replace(/<img/g, '<img style="max-width: 100%;"')
						setTimeout(function() {
							uni.hideLoading()
						}, 500)
					})
				} catch (e) {
					//TODO handle the exception
				}
			}
		},
		onLoad() {
			this.id = this.$Route.query.id
			this.requestNewsDetail();
			console.log("this.info", this.info)
			// console.log(this.content)
			// this.content = this.content.replace(/<p/g,'<p class="c_p"')
		}
	}
</script>

<style lang="scss">
	.container {
		height: 100vh;


		.custom-top {
			position: fixed;
			background: #fff;
			top: 0;
			left: 0;
			z-index: 999;
			width: 100%;

		}

		.custom-left {
			width: 60rpx;
			/* background: red; */
			height: 60rpx;
			text-align: center;
			border-radius: 60rpx;
			color: #fff;
			background-color: rgba(0, 0, 0, 0.5);
			position: fixed;
			top: 20rpx;
			left: 30rpx;
			display: flex;
			justify-content: center;
			align-items: center;
			z-index: 999;

			.icon {
				font-size: 40rpx;
			}
		}

		.detail-header {
			width: 100%;
			background: none;
			position: fixed;
			top: 0;
			left: 0;
			z-index: -1;
		}

		.detail-header-bg {
			width: 100%;
			height: 432rpx;
			background-size: 100% auto;
			background-position: center;
			background-repeat: no-repeat;
		}

		.detail-main {
			margin-top: 380rpx;
			padding: 30rpx;
			background-color: #fff;
			position: relative;
			border-top-left-radius: 36rpx;
			border-top-right-radius: 36rpx;

			.title {
				font-size: 36rpx;
				font-weight: 550;
				color: #000;
				margin-bottom: 20rpx;
			}

			.time {
				font-size: 24rpx;
				color: #B3B3B3;
				margin-bottom: 28rpx;
			}

			.desc {
				margin-bottom: 50rpx;
				padding: 60rpx;
				border-radius: 5rpx;
				background-color: $color-gray;
			}

			.content {
				padding: 30rpx 0;
			}

			.detail-cc {
				line-height: 2;
				color: #666;
				font-size: 16px;

			}
		}

		.detail-main-tips {
			margin-top: 24rpx;
			background: #fff;
			padding: 30rpx;
			color: #B3B3B3;
			font-size: 28rpx;
			line-height: 40rpx;

			.desc {
				margin-top: 20rpx;
				font-size: 24rpx;
			}
		}
	}
</style>