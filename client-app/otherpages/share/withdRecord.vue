<template>
	<view class="custom-container">
		<customNav title="佣金提现记录" :isBgImage="true" leftColor="#fff">
		</customNav>
		<view class="list">
			<view v-if="!isLoading && list.length == 0" class="empty-list">暂无</view>
			<view class="list-item flex justify-between align-center" v-for="item in list" :key="item.id">
				<view class="flex flex-column">
					<view class="flex">
						<text class="name">{{item.describe}}</text>
					</view>
					<text class="time">{{item.source.name||'未知'}}</text>
					<text class="time">{{item.create_time||time}}</text>
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
			async requestMoneyDetail() {
				if (this.isFinish) {
					return false
				}
				this.isLoading = true;
				try {
					let res = await this.$u.api.getWaterApi({
						coin: "usdt",
						source:"commission",
						page: this.currentPage + 1,
						limit: this.pageSize
					})
					if (res.data.data.length < this.pageSize) {
						this.isFinish = true
					}
					this.currentPage = parseInt(res.data.current_page)
					this.list = [...this.list, ...res.data.data]
					this.isLoading = false;
				} catch (e) {
					this.isLoading = false;
					//TODO handle the exception
				}
			}
		},
		onReachBottom() {
			this.requestMoneyDetail()
		},
		async onLoad() {
			this.requestMoneyDetail()
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
					font-size: $baseFontSizeSmX;
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