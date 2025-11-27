<template>
	<view>
		<view v-if="isFixed" class="custom-navbar-s" :style="{
					height:'calc('+statusBarHeight + 'px'+' + 80rpx)'
				}">
		</view>
		<view class="custom-navbar" :style="{
					height:'calc('+statusBarHeight + 'px'+' + 80rpx)',
					background:(!isBgImage && !isOpacity ) || scrollTop > statusBarHeight + 40 + 50?bg:'none'
				}">
			<view class="custom-bg" v-if="isBgImage" :class="bgImage !='' ?`is`:''" :style="{
				height:`calc(${statusBarHeight}px + 80rpx);`,
				backgroundImage:bgImage != ''?`url(${bgImage})`:'#F1C934;'
			}">
			</view>
			<view class="custom-navbar-main">
				<view class="custom-status-main" :style="{
					height:statusBarHeight + 'px'
				}"></view>
				<view class="custom-title-main">
					<view class="custom-left" v-if="!hideLeft || isPrevPage" @tap="navigateBack">

						<u-icon name="arrow-left" style="font-size:30rpx;" :style="{
													color:leftColor
												}"></u-icon>
					</view>
					<view class="custom-left-main" v-else>
						<slot name="left"></slot>
					</view>
					<template v-if="isCustomTitle">
						<slot name="title">

						</slot>
					</template>
					<view v-else class="custom-title" :style="{
							color:leftColor
						}">{{title}}</view>
					<view class="custom-right" :style="{
						top:statusBarHeight+'rpx'
					}">
						<slot name="right">

						</slot>
					</view>
				</view>
			</view>
		</view>
	</view>
	</view>
</template>

<script>
	export default {
		name: "customNav",
		props: {
			isBgImage: {
				type: Boolean,
				default: false,
			},
			title: {
				type: String,
				default: ''
			},
			bgImage: {
				type: String,
				default: ''
			},
			bg: {
				type: String,
				default: '#ffffff'
			},
			isFixed: {
				type: Boolean,
				default: true,
			},
			isOpacity: {
				type: Boolean,
				default: false,
			},
			hideLeft: {
				type: Boolean,
				default: false,
			},
			leftColor: {
				type: String,
				default: '#000000'
			},
			isCustomTitle: {
				type: Boolean,
				default: false
			}
		},
		data() {
			return {
				statusBarHeight: 0,
				scrollTop: 0,
				isPrevPage: true
			}
		},
		created() {
			// 获取系统信息
			uni.getSystemInfo({
				success: (res) => {
					// 获取状态栏高度
					this.statusBarHeight = res.statusBarHeight;
				}
			});
			var pages = getCurrentPages();
			var prevPage = pages[pages.length - 2];
			if (!prevPage) {
				this.isPrevPage = false
			}
		},
		mounted() {
			uni.$on('onPageScroll', (scroll) => {
				this.scrollTop = scroll
			});
		},
		methods: {
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
			}
		},
	}
</script>

<style scoped>
	.custom-navbar-s {
		width: 100%;
		height: 176rpx;
		background: none;
	}

	.custom-navbar {
		width: 100%;
		background-color: none;
		height: 176rpx;
		top: 0;
		position: fixed;
		z-index: 9999;
		transition: all 0.1s;
	}

	.custom-navbar-main {
		width: 100%;
		padding: 0 15px;
		z-index: 9999;
	}

	.custom-status-main {
		width: 100%;
	}

	.custom-title-main {
		display: flex;
		justify-content: center;
		align-items: center;
		height: 80rpx;
		line-height: 80rpx;
	}

	.custom-title {
		font-weight: 500;
		width: 360rpx;
		text-align: center;
		font-size: 32rpx;
	}

	.custom-left {
		width: 60rpx;
		/* background: red; */
		height: 80rpx;
		line-height: 80rpx;
		position: absolute;
		left: 30rpx;
		display: flex;
		align-items: center;
	}

	.custom-left-main {
		height: 80rpx;
		position: absolute;
		left: 30rpx;
	}

	.custom-right {
		position: absolute;
		right: 30rpx;
		height: 100%;
		display: flex;
		align-items: center;
		justify-content: end;
	}

	.custom-bg {
		width: 100%;
		height: 100%;
		position: absolute;
		top: 0;

		z-index: -1;
		display: block;
		background:var(--base-default);
	}

	.custom-bg.is {
		background-size: 100% auto;
		background-repeat: no-repeat;
		background-position: top;
	}
</style>