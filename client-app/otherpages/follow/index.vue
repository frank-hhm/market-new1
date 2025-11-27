<template>
	<view class="custom-container">
		<customNav title="理财跟单" leftColor="#000" bg="#f5f5f5">
		</customNav>
		
		<memberCoinCommon></memberCoinCommon>
		<view class="cell-main">
			<view class="cell-main-left">
				<view class="cell-main-left-title">可跟单交易员列表</view>
				<view class="cell-main-left-desc">数据每小时更新一次</view>
			</view>
			<router-link to="/otherpages/about/follow" class="cell-main-rgiht"><text class="icon icon-bangzhu"></text>跟单规则需知</router-link>
		</view>
		<view class="cell-sort-main">
			<view class="cell-sort-main-item" :class="sort == 'all'?'active':''" @tap="setSort('all')">综合排名</view>
			<view class="cell-sort-main-item" :class="sort == 'revenue'?'active':''" @tap="setSort('revenue')">收益率排名
			</view>
		</view>

		<view class="follow-person-list">
			<view v-if="!isLoading && list.length == 0" class="empty-list">暂无</view>
			<view class="follow-person-item" v-for="(item,index) in list" :key="index" @tap="toDetail(item.id)">
				<view class="follow-person-item-header">
					<!-- <image class="item-header-avater" :src="item.avatar" mode="aspectFill"></image> -->
					<u-image width="80" height="80" border-radius="10" class="item-header-avater" :src="item.avatar">
						<view slot="error">
							<u-image width="80" height="80" border-radius="10" class="item-header-avater"
								:src="getConfig.system_logo"></u-image>
						</view>
					</u-image>
					<view class="item-header-info">
						<view class="item-header-info-top">
							<view class="item-header-name">{{item.nickname}}</view>
							<view class="item-header-member">
								<text class="item-header-member-icon"></text>
								<text class="item-header-member-text">{{(parseInt(item.follow_count_text) + parseInt(item.member_count))  || 0}}</text>
							</view>
						</view>
						<view class="item-header-info-signature">
							{{item.signature}}
						</view>
					</view>
					<view class="follow-status" v-if="item.follow_status === true">
						跟单中
					</view>
					<view class="follow-status-none" v-else>
						跟单
					</view>
				</view>
				<view class="follow-person-item-bottom">
					<view class="follow-person-item-cell">
						<view class="follow-person-items left">
							<view class="title">近1月收益率</view>
							<view class="desc">{{item.revenue_text || '--'}}%</view>
						</view>
						<view class="follow-person-items center">
							<view class="title">近1月胜率</view>
							<view class="desc">{{item.month_win_rate_text || '--'}}%</view>
						</view>
						<view class="follow-person-items right">
							<view class="title">近1月分润(usd)</view>
							<view class="desc">{{item.month_profit_text || '--'}}</view>
						</view>
					</view>
					<view class="follow-person-item-cell">
						<view class="follow-person-items left">
							<view class="title">累计分润率</view>
							<view class="desc">{{item.total_profit_text || '--'}}%</view>
						</view>
						<view class="follow-person-items center">
							<view class="title">当前跟随人数</view>
							<view class="desc">{{(parseInt(item.follow_count_text) + parseInt(item.member_count))  || 0}}</view>
						</view>
						<view class="follow-person-items right">
							<view class="title">交易员保证金(usd)</view>
							<view class="desc">{{item.deposit_text || '--'}}</view>
						</view>
					</view>
				</view>
			</view>
			<view class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
		</view>
		<diyFollowBottomNav :curIndex="0"></diyFollowBottomNav>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import diyFollowBottomNav from '@/components/diy-follow-bottom-nav/index.vue';
	import memberCoinCommon from './member-coin.vue';
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			diyFollowBottomNav,
			memberCoinCommon
		},
		data() {
			return {
				sort: "all",

				list: [],
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
			...mapGetters("member", ["getBalance"]),
			...mapState("app", ["systemConfig"]),
			...mapState("member", ["member","memberCoin"]),
			balance() {
				return this.getBalance;
			},
		},

		methods: {
			setSort(v) {
				this.sort = v
				this.currentPage = 0
				this.isFinish = false;
				this.requestList(true)
			},
			toDetail(personId) {
				this.$Router.push("/otherpages/follow/person?id=" + personId)
			},
			requestList(isInit = false) {
				let t = this;
				if (isInit) {
					t.currentPage = 0
				} else if (t.isFinish) {
					return false
				}
				uni.showLoading({
					title: '正在获取中...'
				})
				let obj = {
					page: t.currentPage + 1,
					limit: t.pageSize
				}
				if (this.sort !== "all") {
					obj.sort = "revenue"
				}

				t.isLoading = true;
				t.$u.api.getFollowPersonListApi(obj).then((res) => {
					if (res.data.data.length < t.pageSize) {
						t.isFinish = true
					}
					t.currentPage = parseInt(res.data.current_page)
					if (isInit) {
						t.list = res.data.data
					} else {
						t.list = [...t.list, ...res.data.data]
					}
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
			this.requestList()
		},
		onLoad() {
			// this.requestList(true)
		},
		onShow() {
			this.requestList(true)
		}
	}
</script>

<style lang="scss">
	.custom-container {
		background: #f5f5f5;

		.header-main {
			margin: 20rpx;
			background: #fff;
			border-radius: 20rpx;
			overflow: hidden;

			.header-main-top {
				background: #F4CF4A;
				overflow: hidden;
				border-radius: 20rpx;
				position: relative;
				.title {
					color: #000;
				}
			}
			.apply-person{
				position: absolute;
				background: #fff;
				top: 40rpx;
				right: 30rpx;
				height: 60rpx;
				line-height: 60rpx;
				padding: 0 20rpx;
				border-radius: 60rpx;
			}

			.header-main-top1 {
				padding: 30rpx;
				color: #000;

				.header-main-top1-number {
					font-size: $baseFontSizeLgXXX;
					margin-right: 10rpx;
					font-weight: bold;
				}
			}

			.header-main-top2 {
				display: flex;
				justify-content: space-between;
					text-align: center;
				background: #EAC538;
				padding: 30rpx;

				.header-main-top2-item {
					width: 50%;
				}

				.header-main-top2-item2 {
				}

				.header-main-top2-number {
					color: #000;
					font-weight: bold;
					font-size: $baseFontSizeLg;
				}
			}

			.header-main-bottom {
				display: flex;
				color: #000;
				padding: 20rpx 30rpx;
				justify-content: space-between;

				.transfer-btn {
					width: calc(50% - 20rpx);
					height: 80rpx;
					line-height: 80rpx;
					background: #F4CF4A;
					text-align: center;
					border-radius: 80rpx;
				}

				.transfer-out-btn {
					width: calc(50% - 20rpx);
					height: 80rpx;
					line-height: 80rpx;
					background: #EEEEEE;
					text-align: center;
					border-radius: 80rpx;
				}
			}

		}

		.cell-main {
			margin: 20rpx 20rpx 0;
			padding: 30rpx;
			background-image: linear-gradient(to bottom, #fff, #fff 30%, #f5f5f5 100%);
			border-radius: 20rpx;
			display: flex;
			justify-content: space-between;
			// align-items: center;

			.cell-main-left-title {
				font-size: $baseFontSizeDefault;
				color: #000;
			}

			.cell-main-left-desc {
				font-size: $baseFontSize;
				color: var(--base-grey);
			}
			.cell-main-rgiht{
				font-size: $baseFontSizeSm;
				color: var(--base-blue);
			}
		}

		.cell-sort-main {
			display: flex;
			align-items: center;
			justify-content: start;
			margin: 0 20rpx;
			padding: 0 10rpx;

			.cell-sort-main-item {
				color: var(--base-grey);
				background: #F2F3FB;
				padding: 10rpx 20rpx;
				border-radius: $baseRadius;
				margin-right: 20rpx;
				border: 1rpx solid #F2F3FB;

				&.active {
					color: $baseRedColor;
					background: #fff;
					border: 1rpx solid $baseRedColor;
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

		.follow-person-list {
			margin: 20rpx;

			.follow-person-item {
				margin-bottom: 20rpx;
				background: #fff;
				border-radius: 20rpx;
				padding: 20rpx;

				.follow-person-item-header {
					display: flex;
					justify-content: space-between;

					.follow-status {
						border: 1rpx solid #E38E2A;
						color: #E38E2A;
						height: 50rpx;
						line-height: 50rpx;
						border-radius: $baseRadius;
						padding: 0 20rpx;
						margin-left: 20rpx;
					}

					.follow-status-none {
						background: #2A82E4;
						color: #fff;
						border-radius: $baseRadius;
						height: 50rpx;
						line-height: 50rpx;
						padding: 0 20rpx;
						margin-left: 20rpx;

					}

					.item-header-avater {
						width: 80rpx;
						height: 80rpx;
						border-radius: $baseRadius;
					}

					.item-header-info {
						width: calc(100% - 80rpx - 200rpx);
						margin-left: 20rpx;
					}

					.item-header-info-top {
						display: flex;
						align-items: center;
					}

					.item-header-name {
						color: #000;
						margin-right: 10rpx;
					}

					.item-header-info-signature {
						margin-top: 10rpx;
						font-size: $baseFontSize;
						color: var(--base-grey);
					}

					.item-header-member {
						display: flex;
						align-items: center;
						background: #ECF1FD;
						border-radius: $baseRadius;
						color: #2A82E4;
						padding: 2rpx 10rpx;

						.item-header-member-icon {
							width: 26rpx;
							height: 26rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/follow/icon-person.png");
						}
					}
				}

				.follow-person-item-bottom {
					margin-top: 20rpx;

					.follow-person-item-cell {
						display: flex;
						align-items: center;
						justify-content: space-between;
						margin-top: 20rpx;

						.follow-person-items {
							width: 33.333%;

							&.left {
								text-align: left;
							}

							&.center {
								text-align: center;
							}

							&.right {
								text-align: right;
							}

							.title {
								color: #A6A6A6;
								white-space: nowrap;
							}

							.desc {
								margin-top: 10rpx;
								color: #000;
								margin-left:40rpx;
							}

							&.right {
								.desc {
									margin-right:40rpx;
								}
							}
						}
					}
				}
			}
		}
	}
</style>