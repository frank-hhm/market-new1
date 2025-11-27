<template>
	<view class="turntable-body">
		<customNav title="入金抽现金" bg="none">
		</customNav>
		<view class="turntable-title-main">
			<image class="turntable-title" src="@/static/images/activity/turntable-title.png" mode="heightFix" />
			<image class="turntable-title-2" src="@/static/images/activity/turntable-title-2.png" mode="heightFix">
		</view>
		<my-turntable-draw ref="raffleWheel" :width="boxData.width" :height="boxData.height" :prizeList="prizeList"
			:targetIndex="targetIndex" :colors="['#ffffff','#FEFDD7']" :canvasWidth="300" :canvasHeight="300"
			nameColor="#EDB721" @befoterClick="befoterClick" @afterClick="afterClick">
		</my-turntable-draw>

		<view class="turntable-btn-main">
			<view class="turntable-btn" @click="onTurnTable">
				立即抽奖
			</view>
			<view class="turntable-btn-bottom">

				<view class="turntable-btn-main-tips">
					<view class="tips-number-title">剩余次数</view>
					<view class="tips-number-number">
						<text>{{indexNum}}</text>
					</view>
				</view>
				<router-link class="turntable-record" to="/otherpages/activity/turntable-record">
					<view>中奖记录</view>
					<u-icon name="arrow-right" style="font-size:20rpx;"></u-icon>
				</router-link>
			</view>
		</view>


		<view class="turntable-rule">
			<view class="turntable-rule-title">活动规则</view>
			<view class="turntable-rule-list">
				<view class="turntable-rule-item">
					1.入金1000USD或得1次抽奖机会
				</view>
				<view class="turntable-rule-item">
					2.以此叠加每入金1000USD可获得1次抽奖机会
				</view>
				<view class="turntable-rule-item">
					3.获得抽奖机会后，如未进行抽奖的用户，抽奖机会将在48小时后清零抽奖次数
				</view>
				<view class="turntable-rule-item">
					4.活动最终解释权归亮点国际公司所有
				</view>
				<!-- <view class="turntable-rule-item">
					5.些活动100%中奖，抽中直接入账可提现
				</view>
				<view class="turntable-rule-item">
					6.活动最终解释规亮点国际所有
				</view> -->
			</view>
		</view>
		<u-popup mode="center" :popup="false" v-model="allModalShow " :border-radius="20">
			<view class="modal-main" v-if="prizeList[targetIndex]">
				<image class="modal-main-title" src="@/static/images/20250227/turntable-title.png" mode="widthFix">
				</image>
				<view class="modal-main-box">
					<view class="modal-main-box-content">
						<view class="box-title">
							<text class="number">{{prizeList[targetIndex].name || ''}}</text>
							<text class="unit">USD</text>
						</view>
						<view class="box-desc">入金专享</view>
					</view>
				</view>
				<view class="modal-main-btn" @click="allModalShow = false">
					再抽一次
				</view>
			</view>
		</u-popup>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import myTurntableDraw from '@/components/my-turntable-draw/my-turntable-draw'
	import {
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			myTurntableDraw
		},
		data() {
			return {
				boxData: {
					width: 240,
					height: 240
				},
				prizeList: [],
				// 中奖下标
				targetIndex: 0,
				// 需多次获取
				indexNum: 1,
				isLoad: false,
				allModalShow: false
			};
		},
		onLoad() {
			setTimeout(() => {
				this.prizeList = [{
						name: '3',
						count: 10,
						image: '/static/images/activity/turntable-icon.png'
					},
					{
						name: '8',
						count: 10,
						image: '/static/images/activity/turntable-icon.png'
					},
					{
						name: '苹果17',
						count: 10,
						image: '/static/images-new1/activity/turntable-17.png'
					},
					{
						name: '88',
						count: 0,
						image: '/static/images/activity/turntable-icon.png'
					},
					{
						name: '再抽一次',
						count: 0
					},
					{
						name: '888',
						count: 0,
						image: '/static/images/activity/turntable-icon.png'
					},
					{
						name: '3888',
						count: 0,
						image: '/static/images/activity/turntable-icon.png'
					},
					{
						name: 'MateX6',
						count: 0,
						image: '/static/images-new1/activity/turntable-icon-x6.png'
					}
				]
			}, 1500)
		},
		computed: {
			...mapState("member", ["member"]),
		},
		methods: {
			getRandomIndex() {
				let randomNum = Math.random(); // 生成0到1之间的随机数
				if (randomNum < 0.4) { // 0的概率是50%
					return 0;
				} else if (randomNum < 0.8) { // 1的概率是30%，即从0.5累加到0.8
					return 1;
				} else { // 2的概率是20%，即从0.8累加到1
					return 2;
				}
			},
			onTurnTable() {
				let t = this
				if (this.isLoad === true) {
					return false;
				}
				let _n = this.getRandomIndex();
				this.targetIndex = _n
				this.isLoad = true;
				if (this.indexNum > 0) {
					this.toMemberTurn(_n);
				} else {
					uni.showModal({
						title: '提示',
						content: "暂无抽奖次数，可以去充值获取抽奖次数",
						confirmText: "去充值",
						cancelText: "关闭",
						success: function(res) {
							if (res.confirm) {
								t.$Router.push("/otherpages/memberCoin/recharge")
								console.log('用户点击确定');
							} else if (res.cancel) {
								console.log('用户点击取消');
							}
						}
					})
					this.isLoad = false;
				}
			},
			toMemberTurn(_n) {
				if (!this.prizeList[_n] || !this.prizeList[_n].name) {
					uni.showToast({
						icon: "none",
						title: "抽奖程序正在加载中...请稍等!"
					})
					this.isLoad = false;
					return false;
				}
				// console.log('选中商品项：' + _n)
				// console.log('选中商品名：' + this.prizeList[_n].name)
				this.$u.api.turnTableMemberApi({
					count: this.prizeList[_n].name
				}).then((res) => {
					this.toBefoterClick()
					this.$refs.raffleWheel.rotoreAction(_n)
				}).catch(() => {
					this.isLoad = false;
				})
			},
			befoterClick(data) {
				if (data.type == 'start') {}
			},
			afterClick(data) {
				if (data.type == 'end') {
					data.callback && data.callback()
					// uni.showToast({
					// 	icon:"none",
					// 	title:'恭喜抽中了：' + this.prizeList[this.targetIndex].name
					// })
					this.allModalShow = true;
					this.isLoad = false;
				}
			},
			// 多次执行
			toBefoterClick(n) {
				console.log('剩余次数：' + this.indexNum)
				if (this.indexNum <= 0) {
					return false
				}
				setTimeout(() => {
					this.indexNum--
					this.$refs.raffleWheel.handleAction()
				}, 100)
			}
		},
		onShow() {
			this.indexNum = this.member.turntable || 0
			try {
				uni.showLoading()
				this.$u.api.getMemberTurnTableCountApi().then((res) => {
					this.indexNum = res.data.turntable || 0
					uni.hideLoading()
				})
			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>

<style scoped lang="scss">
	page {
		background-color: #FFFCF8;
	}

	.turntable-body {
		width: 100%;
		padding: 20rpx 0;
		background-repeat: no-repeat;
		background-position: center center;
		background-size: contain;
		background-image: url("~@/static/images-new1/activity/turntable-bg.png?t=1");
		background-size: 100% 100%;
		background-color: #FFFCF8;
	}

	.turntable-title-main {
		width: 80%;
		margin: 20rpx 10%;
		text-align: center;
	}

	.turntable-title {
		height: 116rpx;
	}

	.turntable-title-2 {
		margin: 0 auto;
		height: 84rpx;
	}

	.turntable-btn-main {
		margin: 30rpx auto;
		width: 80%;
		padding-top: 88rpx;
		position: relative;
		z-index: 1;
	}


	.turntable-btn {
		background: linear-gradient(180deg, #FCD09D 0%, #F0A12B 0%, #FFD54D 69.21%, #FCE592 100%);

		border: 2px solid #FFFFC2;
		box-shadow: inset 3px 4px 5px #FFF5D4;
		border-radius: 100rpx;
		height: 120rpx;
		width: 100%;
		text-align: center;
		line-height: 120rpx;
		color: #fff;
		font-size: $baseFontSizeLgXX;
		letter-spacing: 6rpx;

	}
	.turntable-btn-bottom{
		display: flex;
		justify-content: space-between;
	}
	.turntable-btn-main-tips {
		padding: 20rpx;
		display: flex;
	}

	.tips-number-title {
		font-size: $baseFontSize;
		color: #B5AB87;
	}

	.tips-number-number {
		font-size: $baseFontSizeDefault;
		text-align: center;
		color: var(--base-red)
	}

	.turntable-record {
		color: #B5AB87;
		display: flex;
		text-align: center;
		justify-content: center;
		align-items: center;
	}

	.turntable-rule {
		width: 90%;
		margin: 60rpx auto 0;
		padding-bottom: 80rpx;
		color: #B5AB87;

		.turntable-rule-title {
			text-align: center;
			font-weight: bold;
			font-size: $baseFontSize;
		}

		.turntable-rule-list {
			margin-top: 20rpx;

			.turntable-rule-item {
				margin-bottom: 10rpx;
			}
		}
	}

	.modal-main {
		padding: 20rpx 0 40rpx;
		width: calc(100vw - 80rpx);
		background-repeat: no-repeat;
		background-position: center center;
		background-size: contain;
		background-color: none;
		background-image: url("~@/static/images/20250227/turntable-bg-1.png");
		background-size: 100% auto;
		text-align: center;

		.modal-main-title {
			margin: 40rpx auto 20rpx;
			width: 60%;
			max-height: 200rpx;
		}

		.modal-main-btn {
			width: 80%;
			margin: 0 auto;
			background: linear-gradient(to right, #FFBC7E 0%, #FB9349 100%);
			color: #fff;
			text-align: center;
			height: 100rpx;
			font-size: $baseFontSize;
			line-height: 100rpx;
			border-radius: 100rpx;
		}

		.modal-main-box {
			margin: 50rpx auto;
			width: 80%;
			height: 500rpx;
			// background: #fff;
			background-repeat: no-repeat;
			background-position: center center;
			background-size: contain;
			background-image: url("~@/static/images/20250227/turntable-bg.png");
			background-size: 100% auto;
			position: relative;

			.modal-main-box-content {
				position: absolute;
				left: calc(50% - 150rpx);
				top: calc(50% - 100rpx);
				width: 300rpx;
				height: 200rpx;

				// background-color: red;
				.box-title {
					height: 100rpx;
					line-height: 100rpx;
					color: #783737;
					font-size: $baseFontSize;
					font-weight: bold;
					display: flex;
					align-items: baseline;
					justify-content: center;

					.number {
						font-size: 60rpx;
						margin-right: 10rpx;
					}

				}

				.box-desc {
					height: 40rpx;
					line-height: 40rpx;
					color: #783737;
					font-size: $baseFontSize;
					font-weight: bold;
				}
			}
		}
	}
</style>