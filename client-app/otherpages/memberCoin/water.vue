<template>
	<view class="custom-container">
		<customNav title="资金流水" leftColor="#000">
		</customNav>
		<view class="list">
			<view v-if="!isLoading && list.length == 0" class="empty-list">暂无</view>
			<view class="list-item flex justify-between align-center" v-for="item in list" :key="item.id">
				<view class="flex flex-column">
					<view class="flex">
						<text class="name">{{item.describe}}</text>
					</view>
					<text class="time">{{item.source.name||'未知'}}</text>
					<text class="time">{{item.create_time}}</text>
				</view>
				<view class="flex flex-column align-end">
					<text class="value"><text class="unit">$</text>{{parseFloat(item.money).toFixed(2) }}</text>
					<text class="type" :class="`text-${item.type.color}`">{{item.type.name}}</text>
				</view>
			</view>
			<view class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
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
				list: [],
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false
			};
		},
		methods: {
			requestWater() {
				let t = this;
				if (t.isFinish) {
					return false
				}
				uni.showLoading({
					title: '正在获取中...'
				})
				t.isLoading = true;
				t.$u.api.getWaterApi({
					coin: "usdt",
					page: t.currentPage + 1,
					limit: t.pageSize
				}).then((res) => {
					if (res.data.data.length < t.pageSize) {
						t.isFinish = true
					}
					t.currentPage = parseInt(res.data.current_page)
					t.list = [...t.list, ...res.data.data]
					t.isLoading = false;
					uni.hideLoading()
				}).catch((err) => {
					t.isLoading = false;
					uni.showToast({
						icon: "none",
						title: "获取失败!" + (err.data.message || '')
					})
				})
			}
		},
		onReachBottom() {
			this.requestWater()
		},
		onLoad() {
			this.requestWater()
		}
	}
</script>

<style lang="scss">
	.custom-container {
		background-color: #f5f5f5;
		height: 100%;

		.list {
			// margin: 20rpx;

			.list-item {
				padding: 20rpx;
				background: #ffffff;
				// margin-bottom: 20rpx;
				border-bottom: 1rpx solid #f3f3f3;
				// border-radius: $baseRadius;

				.name {
					font-size: $baseFontSize;
					color: #000;
				}

				.time {
					margin-top: 20rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.value {
					font-size: $baseFontSize;
					color: #000;

					.unit {}
				}

				.type {
					margin-top: 10rpx;
				}

				.status {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.tag {
					margin-left: 20rpx;
					font-size: $baseFontSizeSm;
					color: $baseColor;
				}
			}
		}
	}

	.empty-list {
		text-align: center;
		color: var(--base-grey);
	}

	.page-bottom {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		color: var(--base-grey);
	}
</style>