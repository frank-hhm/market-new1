<template>
	<view class="container container-page">
		<customNav title="全球资讯" isBgImage leftColor="#fff">
		</customNav>
		<uni-list class="uni-lists" v-show="current==0" :border="false" style="background-color: none;">
			<uni-list-item class="news-list-items" :border="false" v-for="(item,index) in newsList" :key="index">
				<template v-slot:body>
					<router-link class="flex justify-between information-list-item"
						:to="{path:'/otherpages/article/detail',query:{id:item.id}}">
						<view class="flex flex-column justify-between">
							<text class="title">{{item.title}}</text>
							<text class="time">{{item.create_time}}</text>
						</view>
						<u-image class="item-image" mode="scaleToFill" width="200rpx" height="140rpx"
							border-radius="12rpx" :src="item.cover"></u-image>
					</router-link>
				</template>
			</uni-list-item>
			<uni-load-more :status="newsStatus"></uni-load-more>
		</uni-list>
		<uni-list class="uni-list kuaixun-list" v-show="current==1" :border="false" style="background-color: none;">
			<uni-list-item class="news-list-item" :border="false" v-for="(item,index) in informationList" :key="index">
				<view class="flex align-center time-wrapper">
					<view class="dot"></view>
					<view class="line"></view>
					<view class="time">{{item.time.split(" ")[1]}}</view>
				</view>
				<view class="content">{{item.body}}</view>

			</uni-list-item>
			<uni-load-more :status="informationStatus"></uni-load-more>
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
				list: [{
					name: '资讯'
				}, {
					name: '快讯'
				}],
				current: 0,

				informationList: [],
				informationStatus: "more",
				informationCurrentPage: 0,
				informationPageSize: 10,

				newsList: [],
				newsStatus: "more",
				newsCurrentPage: 0,
				newsPageSize: 10,
			};
		},
		methods: {
			change(index) {
				this.current = index;
				if (!this.informationList.length) {
					this.rImformation()
				}
			},
			async requestNewsList() {
				if (this.newsStatus != "more") {
					return false
				}
				try {
					let res = await this.$u.api.getArticleListApi({
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
			if (this.current == 0) {
				this.requestNewsList()
			}
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

		.stick-placeholder {
			&::after {
				content: " ";
				height: 90rpx;
				display: block;
			}
		}

		.u-tabs {
			position: fixed;
			left: 0;
			right: 0;
			width: 100%;
			background-color: #fff;
			z-index: 10;

		}

		.uni-list,.uni-lists {
			min-height: 400rpx;
			background: none;
		}

		.kuaixun-list {
			padding-top: 40rpx;
		}

		.list-item {
			padding: 0rpx;
			border: none;

		}

		.time-wrapper {
			position: relative;
			left: -8rpx;
			top: -48rpx;
		}

		.information-list-item {
			width: 100%;

			.title {
				height:80rpx;
				line-height:40rpx;
				font-size: 28rpx;
				color: #000;
				display: -webkit-box;
				-webkit-box-orient: vertical;
				overflow: hidden;
				text-overflow: ellipsis;
				-webkit-line-clamp: 2;
			}

			.time {
				font-size: 24rpx;
				color: #B3B3B3;
			}

			.item-image {
				margin-left: 50rpx;
				width: 300rpx;
				flex-shrink: 0;
				height: 200rpx;
				border-radius: 5rpx;
			}
		}

		.news-list-items {
			background: #fff;
			border:1rpx solid $color-gray;
		}

		.news-list-item {
			// border-left: 1rpx solid $color-gray;
			// border-left: 1rpx solid #eee;
			// padding: 30rpx;
			margin: 0 20rpx 0 50rpx;
			padding: 30rpx 0;
			background: none;

			.dot {
				width: 12rpx;
				height: 12rpx;
				border-radius: 10rpx;
				background-color: $color-blue;
			}

			.line {
				width: 75rpx;
				height: 1rpx;
				background-color: #eee;
			}

			.title {
				font-size: 30rpx;
				color: #333;
			}

			.time {
				padding: 4rpx 10rpx;
				font-size: 24rpx;
				border-radius: 4rpx;
				color: $color-blue;
				background-color: $color-gray;
			}

			.content {
				position: relative;
				left: -4rpx;
				top: -44rpx;

				padding: 20rpx 10rpx 0 85rpx;
				font-size: 28rpx;
				color: #333;
				line-height: 34rpx;
			}
		}
	}
</style>