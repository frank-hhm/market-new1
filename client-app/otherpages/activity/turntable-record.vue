<template>
	<view class="container container-page">
		<customNav title="中奖记录" leftColor="#000">
		</customNav>
		<view class="list">
			<view v-if="list.length == 0" class="empty-list">暂无</view>
			<view class="list-item" v-for="item in list" :key="item.id">
				<view class="item-time">
					{{item.create_time||time}}
				</view>
				<view class="item-number">
					{{parseFloat(item.number).toFixed(2) }}
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
					let res = await this.$u.api.getTurnTableRecordListApi({
						coin: "usdt",
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
	.container {
		background-color: #f5f5f5;
		height: 100%;

		.list {
			// margin: 20rpx;

			.list-item {
				padding:30rpx 20rpx;
				background: #ffffff;
				// margin-bottom: 20rpx;
				border-bottom:1rpx solid #f3f3f3;
				// border-radius: $baseRadius;
					
				display: flex;
				justify-content: space-between;
				align-items: center;

				.item-time {
					color:  var(--base-grey);
				}
				.item-number{
					font-weight: bold;
				}

			}
		}
	}
	.empty-list{
		text-align: center;
		line-height: 400rpx;
		color: var(--base-grey);
	}

	.page-bottom {
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		color: var(--base-grey);
	}
</style>