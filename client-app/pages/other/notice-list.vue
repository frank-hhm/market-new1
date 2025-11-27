<template>
	<view class="container container-page">
		<customNav title="公告">
		</customNav>
		<uni-list class="uni-lists" :border="false" style="background-color: none;">
			<uni-list-item class="news-list-items" :border="false" v-for="(item,index) in newsList" :key="index">
				<template v-slot:body>
					<router-link class="news-list-item" :to="{path:'/pages/other/notice-detail',query:{id:item.id}}">
						<view class="title">
							{{item.title}}
						</view>
					</router-link>
				</template>
			</uni-list-item>
			<uni-load-more :status="newsStatus"></uni-load-more>
		</uni-list>
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

				newsList: [],
				newsStatus: "more",
				newsCurrentPage: 0,
				newsPageSize: 20,
			};
		},
		methods: {
			async requestNewsList() {
				if (this.newsStatus != "more") {
					return false
				}
				try {
					let res = await this.$u.api.getNoticeListApi({
						page: this.newsCurrentPage + 1,
						limit: this.newsPageSize
					})
					this.newsCurrentPage = parseInt(res.data.current_page)
					this.newsList = [...this.newsList, ...res.data.data]
					if (this.newsCurrentPage >= res.data.last_page) {
						this.newsStatus = "noMore"
					} else {
						this.newsStatus = "more"
					}
				} catch (e) {
					//TODO handle the exception
					this.newsStatus = "more"
				}
			}
		},
		onReachBottom() {
			this.requestNewsList()
		},
		onLoad() {
			this.requestNewsList()
		}
	}
</script>
<style lang="scss">
	page {
		background-color: #f5f5f5;
	}

	.container {
		height: auto;
		background-color: #f5f5f5;

		.uni-list,
		.uni-lists {
			min-height: 400rpx;
			background: none;
		}

		.news-list-items {
			padding: 10rpx;
			border-top: 1rpx solid #f3f3f3;
		}

		.news-list-item {
			width: 100%;

			.title {
				height: 40rpx;
				line-height: 40rpx;
				color: #000;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
			}
		}
	}
</style>