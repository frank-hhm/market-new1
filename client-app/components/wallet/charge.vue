<template>
	<view>
		<view class="top-wrapper-box">
			<view class="reward">
				<view class="reward-value" :style="{
					color:totalProfitClass
				}">
					{{totalProfit || 0.00}}
				</view>
				<view class="label">持仓盈亏</view>
			</view>
			<view @tap="toRecharge" class="deposit-btn">入金</view>
		</view>
		<view class="wallet-main">
			<view class="wallet-main-item">
				<view class="wallet-item">
					<view class="value">{{member.username || member.username || '无'}}</view>
					<view class="label">账号</view>
				</view>
				<view class="wallet-item">
					<view class="value">{{marginRatio || 0.00}}%</view>
					<view class="label">保证金比例</view>
				</view>
				<view class="wallet-item">
					<view class="value">{{netValue || 0.00}}</view>
					<view class="label">净值</view>
				</view>
				<view class="wallet-item">
					<view class="value">{{availableBalance || 0.00}}</view>
					<view class="label">可用</view>
				</view>
				<view class="wallet-item ">
					<view class="value">{{marginUsed || 0.00}}</view>
					<view class="label">占用</view>
				</view>
				<view class="wallet-item">
					<view class="value">{{getBalance || 0.00}}</view>
					<view class="label">余额</view>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters
	} from 'vuex';

	export default {
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor","getBalance"]),
			...mapState("member", ["memberCoin", "member","isMoni"]),
			...mapGetters('wallet', [
			]),
			...mapGetters('wallet', [
				'getTotalMarginUsed',
				"getTotalProfit",
				'getAvailableBalance',
				'getNetValue',
				'getMarginRatio'
			]),
			balance() {
				return this.getBalance;
			},
			availableBalance() {
				return this.getAvailableBalance;
			},
			netValue() {
				return this.getNetValue;
			},
			marginRatio() {
				return this.getMarginRatio;
			},
			marginUsed() {
				return this.getTotalMarginUsed;
			},
			totalProfit() {
				const profit = this.getTotalProfit || 0;
				return `${profit >= 0 ? '+' : ''}${parseFloat(profit).toFixed(2)}`;
			},

			totalProfitClass() {
				if (this.getTotalProfit > 0) return this.getUpColor;
				if (this.getTotalProfit < 0) return this.getDownColor;
				return '';
			}
		},
		methods:{
			toRecharge(){
				if(this.isMoni){
					uni.showToast({
						title:"模拟账号禁用",
						icon:"none"
					})
					return false;
				}else if(this.member.bind_status && (this.member.bind_status.value == 0 || this.member.bind_status.value == 3)){
					this.$Router.push("/otherpages/member/bindCard")
				}else if(this.member.bind_status && this.member.bind_status.value == 2 ){
					uni.showToast({
						title:"实名认证正在审核中...请稍后再试！",
						icon:"none"
					})
					return false;
				} else{
					this.$Router.push("/otherpages/memberCoin/recharge")
				}
			}
		}
	};
</script>


<style scoped lang="scss">
	.top-wrapper-box {
		padding: 20rpx;
		background-color: #fff;
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 140rpx;

		.deposit-btn {
			height: 66rpx;
			line-height: 66rpx;
			border-radius: $baseRadius;
			width: 180rpx;
			text-align: center;
			color: #fff;
			background: $baseColor;
		}

		.reward {
			margin-left: 10rpx;

			.reward-value {
				font-size: 36rpx;
				font-weight: bold;
				line-height: 80rpx;
			}

			.label {
				color: #000000;
			}
		}
	}

	.wallet-main {
		background: #fff;
		padding: 20rpx;
		height: 220rpx;
		margin-top: 20rpx;

		.wallet-main-item {
			display: flex;
			justify-content: space-between;
			flex-wrap: wrap;

			.wallet-item {
				margin-bottom: 20rpx;
				height: 80rpx;
				width: 33.333%;

				.value {
					font-weight: 700;
					color: #000;
					line-height: 50rpx;
				}

				.label {
					// margin-top: 10rpx;
					font-size: 24rpx;
					font-weight: 500;
					color: #A8A8A8;
					line-height: 30rpx;
				}

				&:nth-child(1),
				&:nth-child(4) {
					text-align: left;
				}

				&:nth-child(2),
				&:nth-child(5) {
					text-align: center;
				}

				&:nth-child(3),
				&:nth-child(6) {
					text-align: right;
				}

				&:nth-child(4),
				&:nth-child(5),
				&:nth-child(6) {
					margin-bottom: 0;
				}
			}
		}

		.wallet-main-item.mt {
			margin-top: 40rpx;
		}
	}
</style>