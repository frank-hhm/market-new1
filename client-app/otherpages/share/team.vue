<template>
	<view class=" custom-container">
		<customNav leftColor="#000" bg="none" title="我的团队">
		</customNav>
		<view class="share-mian">
			<image class="share-mian-bg" src="@/static/images-new1/share/top-bg.png" mode="widthFix" />

			<view class="share-member">
				<u-image width="120" height="120" border-radius="120" class="avatar" :src="info.avatar">
					<view slot="error">
						<u-image width="120" height="120" border-radius="120" class="avatar"
							src="/static/images-new1/share/default-avatar.png"></u-image>
					</view>
				</u-image>
				<router-link class="share-member-right" to="/pages/member/shareDetailLevel">
					<view class="right-top">
						<view class="right-name">{{info.username}}</view>
						<!-- <view class="share-level">Lv:{{info.people_level || 1}}</view> -->
					</view>
					<view class="right-desc">创建者</view>
				</router-link>
			</view>
			<view class="share-number">
				<view class="number-item">
					<view class="number-title">推广总人数</view>
					<view class="number-count">{{info.people || 0}}</view>
				</view>
				<view class="number-item">
					<view class="number-title">推广业绩</view>
					<view class="number-count">{{info.commission_total || 0}}</view>
				</view>
			</view>
		</view>

		<view class="team-list">
			<view class="team-item" v-for="(item,index) in teamList" :key="index">
				<view class="team-item-head">
					<u-image width="80" height="80" border-radius="80" class="avatar" :src="item.avatar">
						<view slot="error">
							<u-image width="80" height="80" border-radius="80" class="avatar"
								src="/static/images-new1/share/default-avatar.png"></u-image>
						</view>
					</u-image>
					<view class="team-item-right">
						<!-- <view class="right-name">{{item.username || item.real_name}}</view> -->
						<view class="right-name">{{item.email || item.mobile ||item.username}}</view>
						<!-- <view class="share-level">Lv:{{item.people_level || 1}}</view> -->
					</view>
				</view>
				<view class="team-bottom">
					<view class="team-bottom-item">
						<view class="title">今日合约</view>
						<view class="count">{{item.today_order_total || 0}}</view>
					</view>
					<view class="team-bottom-item">
						<view class="title">今日跟单</view>
						<view class="count">{{item.today_follow_order_total || 0}}</view>
					</view>
					<view class="team-bottom-item">
						<view class="title">跟单佣金</view>
						<view class="count">{{item.follow_commission_total || 0}}</view>
					</view>
					<view class="team-bottom-item">
						<view class="title">合约佣金</view>
						<view class="count">{{item.commission_balance || 0}}</view>
					</view>
				</view>
			</view>
			<view class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
		</view>
		<diyBottomNav :curIndex="1"></diyBottomNav>
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
				info: {},
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false,
				teamList: []
			}
		},
		methods: {
			requestListDetail() {
				if (this.isFinish) {
					return false
				}
				this.isLoading = true;
				try {
					this.$u.api.getTeamListApi({
						coin: "usdt",
						page: this.currentPage + 1,
						limit: this.pageSize
					}).then((res)=>{
						
						if (res.data.data.length < this.pageSize) {
							this.isFinish = true
						}
						this.currentPage = parseInt(res.data.current_page)
						this.teamList = [...this.teamList, ...res.data.data]
						this.isLoading = false;
					})
				} catch (e) {
					this.isLoading = false;
					//TODO handle the exception
				}
			}
		},
		onReachBottom() {
			this.requestListDetail()
		},
		 onLoad() {
			this.requestListDetail()
		},
		onShow() {
			this.$u.api.getMemberDetailApi().then(res => {
				this.info = res.data.member || {}
			})
		}
	}
</script>

<style scoped lang="scss">
	page {
		background-color: #f1f1f1;

	}

	.share-mian {
		margin-top: -120rpx;
		width: 100%;
		position: relative;
		z-index: 1;
		min-height: 600rpx;

		.share-mian-bg {
			width: 100%;
		}

		.share-member {
			position: absolute;
			width: calc(100% - 48rpx);
			top: 170rpx;
			left: 24rpx;
			right: 24rpx;
			display: flex;

			.avatar {
				// border: 4rpx solid #fff;
			}

			.share-member-right {
				margin-left: 20rpx;

				.right-top {
					display: flex;
					height: 60rpx;
					align-items: center;
				}

				.right-name {
					font-size: $baseFontSize;
					font-weight: bold;
					margin-right: 10rpx;
				}
			}
		}

		.share-number {
			position: absolute;
			width: calc(100% - 48rpx);
			top: 320rpx;
			left: 24rpx;
			right: 24rpx;
			display: flex;
			justify-content: space-between;

			.number-item {
				text-align: center;
				width: 50%;
				.number-title {
					font-size: $baseFontSize;
				}

				.number-count {
					font-size: $baseFontSizeLg;
					font-weight: bold;
					margin-top: 10rpx;
				}
			}
		}
	}

	.team-list {
		position: relative;
		z-index: 2;
		width: calc(100% - 48rpx);
		margin: -160rpx 20rpx 0;

		.team-item {
			margin-bottom: 20rpx;
			background-color: #ffffff;
			border-radius: 20rpx;
			padding: 30rpx;
			// box-shadow: 0 5px 20px 0 rgba(69, 67, 96, 0.1);

			.team-item-head {
				display: flex;
			}
		}

		.avatar {
			border: 4rpx solid #fff;
		}

		.team-item-right {
			margin-left: 20rpx;
			display: flex;
			align-items: center;

			.right-top {}

			.right-name {
				font-weight: bold;
				margin-right: 10rpx;
			}
			.right-desc{
				// font-size: $baseFontSizeSm;
			}
		}

		.team-bottom {
			margin-top: 40rpx;
			display: flex;
			align-items: center;
			justify-content: space-between;
			text-align: center;

			.title {
				color: var(--base-grey);
			}

			.count {
				margin-top: 10rpx;
				font-weight: bold;
			}
		}
	}

	.share-level {
		padding: 3rpx 12rpx;
		border-radius: 24rpx;
		font-size: $baseFontSizeSmX;
		display: inline-block;
		color: #fff;
		background: linear-gradient(142deg, #94C9FF 0%, #006EDC 100%);
	}

	.page-bottom {
		margin-top: 200rpx;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		color: #9E9E9E;
	}
</style>