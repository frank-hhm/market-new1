<template>
	<view class="container container-bg container-page">
		<view class="top-list">
			<view class="top-list-item " @tap="turnBank">
				<text class="label">银行卡</text>
				<view class="flex align-center mr-24">
					<text class="value"
						:class="member.bank_code?'text-green':'text-grey'">{{member.bank_code?'已绑定':'未绑定'}}</text>
					<u-icon size="24" color="#ccc" name="arrow-right"></u-icon>
				</view>
			</view>
			<view class="top-list-item " @tap="turnUsdt">
				<text class="label">USDT地址</text>
				<view class="flex align-center mr-24">
					<text class="value"
						:class="member.usdt_card?'text-green':'text-grey'">{{member.usdt_card?'已绑定':'未绑定'}}</text>
					<u-icon size="24" color="#ccc" name="arrow-right"></u-icon>
				</view>
			</view>
			<view class="top-list-item " @tap="turnAlipay">
				<text class="label">支付宝</text>
				<view class="flex align-center mr-24">
					<text class="value"
						:class="member.alipay_card?'text-green':'text-grey'">{{member.alipay_card?'已绑定':'未绑定'}}</text>
					<u-icon size="24" color="#ccc" name="arrow-right"></u-icon>
				</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters,
	} from "vuex"
	export default {
		data() {
			return {};
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			turnBank() {
				if (this.member.bank_code) {
					this.$Router.push("/otherpages/member/bankCardDetail")
				} else {
					this.$Router.push("/otherpages/member/bindBankCard")
				}
			},
			turnUsdt() {
				if (this.member.usdt_card) {
					this.$Router.push("/otherpages/member/usdtDetail")
				} else {
					this.$Router.push("/otherpages/member/bindUsdt")
				}
			},
			turnAlipay() {
				if (this.member.alipay_card) {
					this.$Router.push("/otherpages/member/alipayDetail")
				} else {
					this.$Router.push("/otherpages/member/bindAlipayCard")
				}
			},
			toRecognize() {
				this.$Router.push("/otherpages/member/bindCard")
			},
		},
		onShow() {}
	}
</script>

<style lang="scss">
	.container {
		.top-list {
			margin: 20rpx;

			.top-list-item {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding-left: 24rpx;
				height: 96rpx;
				background-color: #fff;
				border-radius: $baseRadius;
				margin-bottom: 20rpx;

				&:last-child {
					border-bottom: none;
				}

				.label {
					font-size: 30rpx;
					color: #000;
				}

				.mr-24 {
					margin-right: 24rpx;
				}

				.value {
					margin-right: 22rpx;
					font-size: 28rpx;
				}
			}
		}
	}
</style>