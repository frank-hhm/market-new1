<template>
	<view>
		<view class="diy-tab-nav">
			<view v-for="(item,index) in bottomNav" :key="index" class="diy-tab-nav-item" @tap="toPath(item)">
				<image :src="curIndex == index ? item.icon_cur : item.icon" class="layout-1" mode="aspectFill" />
				<view class="diy-layout-1" :style="{color:curIndex == index ? cur_color:color}">
					{{item.title}}
				</view>
			</view>
		</view>
		<view class="diy-tab-nav-bottom"></view>
	</view>
</template>

<script>
	export default {
		props: {
			cur_color: {
				type: String,
				default: "#202020",
			},
			color: {
				type: String,
				default: "#202020",
			},
			curIndex: {
				type: Number,
				default: 0
			}
		},
		data() {
			return {
				bottomNav: [{
					title: '首页',
					icon: '/static/images-new1/share/index.png',
					icon_cur: '/static/images-new1/share/index.png',
					page: "/otherpages/share/index"
				}, {
					title: '我的团队',
					icon: '/static/images-new1/share/tean_cur.png',
					icon_cur: '/static/images-new1/share/tean_cur.png',
					page: "/otherpages/share/team"
				}, {
					title: '我的',
					icon: '/static/images-new1/share/member.png',
					icon_cur: '/static/images-new1/share/member.png',
					page: "/pages/member/index",
					isTab: true
				}]
			};
		},
		computed: {},
		methods: {
			toPath(item) {
				if (item.isTab) {
					uni.switchTab({
						url: item.page
					});
				} else {

					this.$Router.push({
						path:item.page
					})
				}
			}
		}
	}
</script>

<style scoped lang="scss">
	.diy-tab-nav {
		position: fixed;
		bottom: 0;
		left: 0;
		right: 0;
		background: white;
		display: flex;
		border-top: 1rpx solid #f5f5f5;
		/* box-shadow:0  2rpx 10rpx rgb(0,0 ,0 ,0.3); */
		z-index: 9999;
		height: 100rpx;
	}

	.diy-tab-nav-border {
		position: absolute;
		left: 0;
		top: 0;
		width: 100%;
		transform: scaleY(0.5);
	}

	.diy-tab-nav-item {
		flex: 1;
		text-align: center;
		display: flex;
		justify-content: center;
		align-items: center;
		flex-direction: column;
		position: relative;
	}

	.diy-tab-nav-item .diy-layout-1 {
		margin-top: 5rpx;
		font-size: $baseFontSizeSmX;
		height: 24rpx;
		line-height: 24rpx;
	}

	.diy-tab-nav-item-cart-count {
		height: 28rpx;
		background: var(--base-red);
		padding: 0 10rpx;
		color: #fff;
		border-radius: 25rpx;
		display: flex;
		align-items: center;
		top: 10rpx;
		right: calc(50% - 50rpx);
		position: absolute;
		z-index: 1;
		font-size: 20rpx;
	}

	.diy-tab-nav-item .layout-1 {
		width: 40rpx;
		height: 40rpx;
	}

	.diy-tab-nav-bottom {
		height: 104rpx
	}
</style>