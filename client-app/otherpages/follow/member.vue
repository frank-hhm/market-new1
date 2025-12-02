<template>
	<view class="custom-container">
		<customNav title="我的跟单" leftColor="#000" bg="#f5f5f5">
		</customNav>
		<memberCoinCommon></memberCoinCommon>
		<view class="cell-sort-main">
			<view class="cell-sort-main-item" :class="selectStatus == 1?'active':''" @tap="setSelectStatus(1)">当前跟单
			</view>
			<view class="cell-sort-main-item" :class="selectStatus == 2?'active':''" @tap="setSelectStatus(2)">历史跟单
			</view>
		</view>
		<view class="order-list">
			<view v-if="!isLoading && list.length == 0" class="empty-list">暂无</view>
			<view class="order-item" v-for="(item,index) in list">
				<view class="order-cell-box">
					<view class="follow-person-item-header" @tap="toPersonDetail(item.person_id)">
						<!-- <image class="item-header-avater" :src="item.person.avatar || getConfig.system_logo" mode="aspectFill"></image> -->

						<u-image width="80" height="80" border-radius="10" class="item-header-avater"
							:src="item.person.avatar">
							<view slot="error">
								<u-image width="80" height="80" border-radius="10" class="item-header-avater"
									:src="getConfig.system_logo"></u-image>
							</view>
						</u-image>
						<view class="item-header-info">
							<view class="item-header-info-top">
								<view class="item-header-name">{{item.person.nickname}}</view>
							</view>
							<view class="item-header-info-signature">
								{{item.person.signature}}
							</view>
						</view>
						<!-- 	<view class="follow-status" :class="`text-${item.status.color}`">
							{{item.status.name2}}
						</view> -->

						<view class="follow-info">
							<view class="follow-info-icon"></view>
							<view class="follow-info-title">交易员信息</view>
						</view>
					</view>
					<view class="follow-members-box">
						<view class="follow-members-box-left">
							<view class="follow-members-box-text">
								当前跟随人数
							</view>
							<view class="follow-members">
								<text class="follow-members-icon"></text>
								<text
									class="follow-members-text">{{(parseInt(item.person.follow_count_text) + parseInt(item.member_count))  || 0}}</text>
							</view>
						</view>
						<view class="follow-members-box-right">
							已入驻平台<text class="follow-members-box-count">{{item.person.create_day || 0}}</text>天
						</view>
					</view>


					<view class="follow-order-defail">
						<view class="follow-order-defail-item">
							<view class="follow-order-defail-item-title">跟单金额</view>
							<view class="follow-order-defail-item-desc">{{item.money || 0.00}}</view>
						</view>
						<view class="follow-order-defail-item">
							<view class="follow-order-defail-item-title">跟单时间</view>
							<view class="follow-order-defail-item-desc">{{item.create_day || '--'}}</view>
						</view>
						<view class="follow-order-defail-item">
							<view class="follow-order-defail-item-title">上日分润</view>
							<view class="follow-order-defail-item-desc">{{item.yesterday_revenue}}</view>
						</view>
						<view class="follow-order-defail-item">
							<view class="follow-order-defail-item-title">累计收益(usd)</view>
							<view class="follow-order-defail-item-desc desc-red">{{item.total_revenue || 0.00}}</view>
						</view>
					</view>
				</view>

				<view class="follow-order-bottom" v-if="item.status.value === 1">
					<view v-if="item.status.value === 1" class="follow-add-btn" @tap="onFollow(item,item.person_id)">增加跟单金额</view>
					<view v-if="item.status.value === 1" class="follow-close-btn" @tap="onEndFollow(item.person_id)">
						结束跟单</view>
				</view>
			</view>
			<view class="page-bottom">{{isFinish?'没有更多了':'加载更多'}}</view>
		</view>
		
		
		
		<u-popup v-model="popupStatus" mode="bottom" border-radius="20">
			<view class="popup-wrapper">
				<view class="popup-header">
					<view class="header-title">继续投入</view>
					<view class="header-right">
						<u-icon name="close" size="18" color="var(--base-grey)" @click="onFollow"></u-icon>
					</view>
				</view>
				<view class="popup-follow-person">
					<view class="follow-person-header">
						<image class="header-avater" v-if="personDetail.avatar" :src="personDetail.avatar" mode="aspectFill">
						</image>
						<view class="header-info">
							<view class="header-info-top">
								<view class="header-name">{{personDetail.nickname || "--"}}</view>
							</view>
							<view class="header-info-signature">
								{{personDetail.signature || '--'}}
							</view>
						</view>
						<view>
							<view></view>
						</view>
					</view>
				</view>
		
				<view class="popup-content">
					<view class="popup-title">
						理财跟单
					</view>
					<view class="popup-desc">
						说明：系统会按照您跟单的金额，增加交易员仓位风险率，T1日交易员需分润，分润不得低于您投入金额的保底0.6%的收益
					</view>
				</view>
		
				<view class="popup-bottom">
					<view class="popup-bottom-left">
						<view class="popup-bottom-title">投入跟单金额</view>
						<view class="popup-bottom-form-item">
							<u-input class="form-item-input" v-model="followPrice" type="digit" :height="80"
								placeholder="输入金额" :maxlength="10" />
						</view>
					</view>
					<view class="popup-bottom-right">
						<view class="popup-bottom-title">预计TI日收益</view>
						<view class="popup-bottom-desc">{{getRevenue}}</view>
					</view>
				</view>
		
				<view class="popup-btn" @tap="toFollow">
					确定跟单
				</view>
			</view>
		</u-popup>
		<diyFollowBottomNav :curIndex="1"></diyFollowBottomNav>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import memberCoinCommon from './member-coin.vue';
	import diyFollowBottomNav from '@/components/diy-follow-bottom-nav/index.vue';
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			memberCoinCommon,
			diyFollowBottomNav
		},
		data() {
			return {

				list: [],
				currentPage: 0,
				pageSize: 20,
				isFinish: false,
				isLoading: false,
				selectStatus: 1,
				popupStatus:false,
				selectPopupId:0,
				personDetail:{},
				followPrice: ""
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
			getRevenue() {
				if (this.personDetail && this.popupStatus) {
					if (this.personDetail.revenue_type == "lock" && this.personDetail.revenue_lock) {
						return parseFloat(this.personDetail.revenue_lock * this.followPrice / 100).toFixed(2) + "usd"
					} else if (this.personDetail.revenue_type == "auto" && this.personDetail.revenue_min && this.personDetail.revenue_max) {
						const min = parseFloat(this.personDetail.revenue_min);
						const max = parseFloat(this.personDetail.revenue_max);
						return min + "%~" + max + "%";
					}
				}
				return 0.00;
			},
		},
		methods: {
			onFollow(item,id) {
				console.log(item.person)
				if(this.popupStatus === true){
					this.selectPopupId = 0
					this.personDetail = {}
					this.followPrice = ""
				}else{
					this.selectPopupId = id || 0
					this.personDetail = item.person || {}
				}
				this.$nextTick(()=>{
					this.popupStatus = !this.popupStatus
				})
			},
			setSelectStatus(c) {
				this.selectStatus = c
				this.$nextTick(() => {
					this.requestList(true)
				})
			},
			toPersonDetail(personId) {
				this.$Router.push("/otherpages/follow/person?id=" + personId)
			},

			onEndFollow(personId) {
				let t = this
				uni.showModal({
					title: '提示',
					content: '确定结束当前跟单吗?',
					cancelText: '取消',
					confirmText: '确定结束',
					success: function(res) {
						if (res.confirm) {
							uni.showLoading({
								title: '正在提交中...'
							})
							t.$u.api.closeFollowPersonOrderApi({
								id: personId
							}).then((res) => {
								t.isLoading = false;
								uni.hideLoading()
								uni.showToast({
									icon: "none",
									title: (res.data.message || '结束成功')
								})
								setTimeout(() => {
									t.requestList(true)
								}, 1000)
							}).catch((err) => {
								t.isLoading = false;
								uni.showToast({
									icon: "none",
									title: "提交结束失败!" + (err.data.message || '')
								})
							})
						} else if (res.cancel) {

						}
					}
				})
			},
			
			toFollow() {
				let t = this;
				let obj = {
					money: t.followPrice,
					id: t.selectPopupId
				}
				if (t.isLoading) {
					return false;
				}
				uni.showLoading({
					title: '正在提交中...'
				})
				t.isLoading = true;
				t.$u.api.createFollowPersonOrderApi(obj).then((res) => {
					t.isLoading = false;
					uni.hideLoading()
					uni.showToast({
						icon: "none",
						title: (res.data.message || '跟单成功')
					})
					setTimeout(() => {
						t.onFollow();
						t.requestList(true)
					}, 1000)
				}).catch((err) => {
					t.isLoading = false;
					uni.showToast({
						icon: "none",
						title: "提交失败!" + (err.data.message || '')
					})
				})
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
				obj.status = t.selectStatus
				t.isLoading = true;
				t.$u.api.getFollowPersonOrderListApi(obj).then((res) => {
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
		min-height: 100vh;

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

		.order-list {
			margin: 20rpx;
			padding-bottom: 100rpx;

			.order-item {
				margin-bottom: 20rpx;
				background: #EDEDED;
				border-radius: 20rpx;

				.order-cell-box {

					background: #fff;
					border-radius: 20rpx;
					padding: 20rpx;
				}

				.follow-person-item-header {
					display: flex;
					justify-content: space-between;

					.follow-status {
						height: 50rpx;
						line-height: 50rpx;
						border-radius: $baseRadius;
						padding: 0 20rpx;
						margin-left: 20rpx;
					}

					.follow-info {
						width: 160rpx;

						.follow-info-title {
							margin-top: 6rpx;
							font-size: $baseFontSizeSm;
						}

						.follow-info-icon {
							margin: 0 auto;
							width: 24rpx;
							height: 26rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/follow/icon-person-info.png");
						}
					}

					.item-header-avater {
						width: 80rpx;
						height: 80rpx;
						border-radius: $baseRadius;
					}

					.item-header-info {
						width: calc(100% - 80rpx - 160rpx);
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
				}

				.follow-members-box {
					display: flex;
					align-items: center;
					justify-content: space-between;
					background: #F9F9F9;
					padding: 30rpx 20rpx;
					margin-top: 20rpx;
					border-radius: $baseRadius;

					.follow-members-box-left {
						display: flex;
						align-items: center;

						.follow-members-box-text {
							color: #000;
							margin-right: 10rpx;
						}
					}

					.follow-members-box-right {
						color: #000;

						.follow-members-box-count {
							color: #FF5733;

						}
					}

					.follow-members {
						display: flex;
						align-items: center;
						background: #ECF1FD;
						border-radius: $baseRadius;
						color: #2A82E4;
						padding: 2rpx 10rpx;

						.follow-members-icon {
							width: 26rpx;
							height: 26rpx;
							background-size: 100% 100%;
							background-image: url("~@/static/images-new1/follow/icon-person.png");
						}
					}
				}

				.follow-order-defail {
					margin: 20rpx 0;
					display: flex;
					// flex-wrap: wrap;
					align-items: center;
					justify-content: space-between;
					background: #f5f5f5;
					padding: 20rpx 20rpx;
					border-radius: $baseRadius;

					.follow-order-defail-item {
						margin-bottom: 20rpx;
						// width: 50%;

						.follow-order-defail-item-title {
							// font-size: $baseFontSizeSm;
						}

						.follow-order-defail-item-desc {
							margin-top: 10rpx;
							font-weight: bold;
							font-size: $baseFontSizeDefault;

							&.desc-red {
								color: #FF5733;
							}
						}
					}
				}

				.follow-order-bottom {
					margin-top: 20rpx;
					display: flex;
					justify-content: space-around;
					padding-bottom: 20rpx;
					font-size: $baseFontSize;

					.follow-close-btn {
						height: 74rpx;
						line-height: 74rpx;
						border-radius: 74rpx;
						width: calc(50% - 40rpx);
						text-align: center;
						background: #fff;
					}

					.follow-add-btn {
						height: 74rpx;
						line-height: 74rpx;
						border-radius: 74rpx;
						width: calc(50% - 40rpx);
						text-align: center;
						background: #F4CF4A;
						color: #000;
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
		
		
		
		.popup-wrapper {
			background: #f5f5f5;
		
			padding-bottom: 60rpx;
		
			.popup-header {
				position: relative;
				padding: 20rpx;
				height: 40rpx;
				box-sizing: content-box;
				.header-title{
					text-align: center;
				}
				.header-right {
					position: absolute;
					right: 20rpx;
					top: 20rpx;
					height: 40rpx;
					width: 40rpx;
					border-radius: 40rpx;
					background: #F5F5F5;
					text-align: center;
					line-height: 32rpx;
				}
		
			}
		
			.popup-follow-person {
				margin: 0 20rpx 20rpx;
				background: #fff;
				border-radius: 20rpx;
				padding: 30rpx;
		
				.follow-person-header {
					display: flex;
					background: #fff;
		
					.header-avater {
						width: 80rpx;
						height: 80rpx;
						border-radius: $baseRadius;
					}
		
					.header-info {
						width: calc(100% - 80rpx - 200rpx);
						margin-left: 20rpx;
					}
		
					.header-info-top {
						display: flex;
						align-items: center;
					}
		
					.header-name {
						color: #000;
						margin-right: 10rpx;
					}
		
					.header-info-signature {
						margin-top: 10rpx;
						font-size: $baseFontSize;
						color: var(--base-grey);
					}
				}
			}
		
			.popup-content {
				margin: 20rpx;
		
				.popup-title {
					color: #000;
					font-size: $baseFontSizeDefault;
				}
		
				.popup-desc {
					color: var(--base-grey);
					font-size: $baseFontSize;
					margin-top: 10rpx;
				}
			}
		
			.popup-bottom {
				margin: 20rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
		
				.popup-bottom-title {
					color: #000;
					margin-bottom: 10rpx;
				}
		
				.popup-bottom-form-item {
					width: 320rpx;
					height: 80rpx;
					line-height: 80rpx;
					padding: 0 20rpx;
					border-radius: $baseRadius;
					background: #fff;
					border: 1rpx solid #E5E5E5;
				}
		
				.popup-bottom-desc {
					height: 82rpx;
					line-height: 82rpx;
					font-weight: bold;
					font-size: $baseFontSizeLg;
				}
			}
		
			.popup-btn {
				width: calc(100% - 40rpx);
				margin: 20rpx;
				color: #000;
				background: #F4CF4A;
				border-radius: 90rpx;
				line-height: 90rpx;
				line-height: 90rpx;
				text-align: center;
			}
		}
	}
</style>