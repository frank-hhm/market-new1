<template>
	<view class="container container-page">
		<customNav title="支付宝入金订单"  leftColor="#000000"></customNav>
		
		<rechargeBox :query="$Route.query" channel="支付宝入金"></rechargeBox>
		
		<view class="recharge-qrcode-tips" v-if="paymentConfig.is_qrcode == 1 ">
			<text>支付金额</text>
			<text class="recharge-qrcode-tips-money">{{$Route.query.money}}</text>
			<text>元</text>
		</view>
		
		<view class="bank-form" v-if="paymentConfig.setting">
			<view v-for="(item,index) in paymentConfig.setting" :key="index">
				<view v-if="paymentConfig.is_qrcode != 1 && (item.type ==='text' || item.type ==='textarea')"
					class="form-item">
					<view class="label">{{item.key}}</view>
					<view class="value">{{item.value || ''}}</view>
					<view class="copy-icon" @tap="copy(item.value || '')">复制</view>
				</view>
				<view class="form-item" v-if="paymentConfig.is_qrcode != 1 && item.type ==='image'">
					<view class="label">{{item.key}}</view>
					<view class="form-item-right" @tap="onPreView(item.value || '')">查看</view>
				</view>

				<view class="item-qrcode" v-if="paymentConfig.is_qrcode == 1 && item.type ==='image'">
					<image class="qrcode-bg" v-if="item.value" :src="item.value" mode="widthFix"></image>
				</view>
			</view>
		</view>

		<view class="pay-form">
			<view class="pay-form-item">
				<text class="label-name">付款账号:</text>
				<view  class="pay-input" ><u-input type="text" v-model="payAccount" /></view>
			</view>
			<view class="pay-form-item">
				<text class="label-name">备注:</text>
				<view  class="pay-input" ><u-input type="text" v-model="remark" /></view>
			</view>
			<view class="pay-form-item">
				<text class="label-name">支付凭证:</text>
				<u-upload ref="uUpload" :action="fileUploadAction" width="160" name="image" height="160"
					:file-list="fileList" max-count="1" :show-progress="false" :multiple="false"
					@on-change="onUploadChange" :before-upload="onBeforeUpload"></u-upload>
			</view>
		</view>

		<view class="tip-wrapper">
			<view class="tip-head">注意事项</view>
			<view class="tip-item">1、进去付款页面后，请按实际订单金额付款，修改金额造成的损失用户自行承担</view>
			<view class="tip-item">2、重复支付，导致的损失用户自行承担</view>
			<view class="tip-item">3、用户发起入金订单，请在15分钟内完成支付，超时需重新发起入金订单。</view>
		</view>
		
		<view class="bottom-btn-box">
			<view class="close-btn" @tap="close">取消支付</view>
				<view class="common-btn submit-btn" @tap="onSubmit">支付成功，提醒收款</view>
		</view>
		
	

		<u-popup v-model="qrcodeStatus" mode="center" border-radius="24" @close="qrcodePopupClose">
			<view class="qrcode-wrapper">
				<image class="qrcode-bg" v-if="qrcodeSrc" :src="qrcodeSrc" mode="widthFix"></image>
			</view>
			<view slot="close" class="qrcode-close" @tap="qrcodePopupClose"><u-icon name="close-circle"
					class="icon"></u-icon> </view>
		</u-popup>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import rechargeBox from '@/components/wallet/recharge-box.vue';
	import {
		BASE_URL
	} from "@/common/config.js"
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			rechargeBox
		},
		data() {
			return {
				alipayInfo: {},
				payName: "",
				usdt: "",
				payAccount: "",
				remark: "",
				qrcodeStatus: false,
				qrcodeSrc: "",
				paymentConfig: {},
				submitLoading:false,
				fileUploadAction: "",
				fileList: [],
				fileList2: [],
				voucher_url: ""
			};
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
		},
		methods: {
			close(){
				uni.navigateBack({
					back:-1
				})
			},
			onSubmit() {
				let t = this
				if (t.submitLoading === true) {
					return false
				}
				if (!t.payAccount) {
					uni.showToast({
						icon: "none",
						title: "请输入付款人账号"
					})
					return false
				}
				t.submitLoading = true;
				let res = t.$u.api.rechargeTransferApi({
					money: t.$Route.query.money,
					usdt: t.usdt,
					account: t.payAccount,
					remark: t.remark,
					pay_type: t.$Route.query.type,
					payment_id: t.$Route.query.payment_id,
					voucher_url: t.voucher_url
				}).then((res) => {
					uni.showToast({
						icon: "none",
						title: res.message
					})
					if (res.code === 1) {
						setTimeout(() => {
							t.$Router.back(1)
						}, 1000)
					} else {
						t.submitLoading = false
					}
				}).catch((err) => {
					t.submitLoading = false;
					uni.showToast({
						icon: "none",
						title: "充值申请提交失败!" + (err.data.message || '')
					})
				})
			},
			copy(value) {
				uni.setClipboardData({
					data: value,
					success: function() {
						uni.showToast({
							icon: "success",
							title: "复制成功"
						})
					}
				});
			},
			onPreView(v) {
				this.qrcodeSrc = v
				this.qrcodeStatus = true
			},
			qrcodePopupClose() {
				this.qrcodeSrc = ''
				this.qrcodeStatus = false
			},
			onBeforeUpload() {
				uni.showLoading({
					title: '凭证上传中...'
				})
			},
			onUploadChange(res, index, lists, name) {
				// 返回一个promise
				this.voucher_url = lists[0].response.data.voucher_url
				uni.hideLoading()
			}
		},
		async onShow() {
			try {
				await this.$u.api.getPaymentConfig({
					id: this.$Route.query.payment_id
				}).then(res => {
					this.paymentConfig = res.data
				})
			} catch (e) {
				//TODO handle the exception
			}
		},
		async onLoad() {
			try {
				this.fileUploadAction = BASE_URL + "api/common.publics/uploadImage"
				this.usdt = this.$Route.query.usdt
			} catch (e) {
				//TODO handle the exception
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background: #f5f5f5;
		padding-bottom: 200rpx;

		.red-tip {
			padding: 10rpx 30rpx;
			font-size: 28rpx;
			font-weight: bold;
			line-height: 40rpx;
			color: #fe3c5d;
		}

		.bank-icon-header {
			padding: 24rpx;
			background: #E7F1FF;

			.bank-icon {
				width: 180rpx;
				height: 46rpx;
				background: url("~@/static/images/icon/bank-icon.png");
				background-size: 100% 100%;
			}
		}

		.order-info {
			margin: 0rpx 24rpx 30rpx;
			padding: 0 30rpx;
			height: 102rpx;
			background: $baseColor;
			box-shadow: 0px 4rpx 20rpx 0px rgba(47, 109, 253, 0.25);
			border-radius: 12rpx;

			.label {
				margin-right: 10rpx;
				font-size: 24rpx;
				color: #fff;
			}

			.value {
				font-size: 32rpx;
				color: #fff;
				font-weight: bold;
			}
		}

		.bank-form {

			margin: 20rpx;
			background-color: #fff;
			padding: 30rpx;
			border-radius: 20rpx;

			.form-item {

				margin-bottom: 20rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
				
				.label {
					width: 160rpx;
					color: var(--base-grey);
				}
				
				.value {
					margin-left: 26rpx;
					margin-right: auto;
					font-size: $baseFontSize;
					color: #000;
					text-align: right;
					width: calc(100% - 280rpx);
				}
				
				.copy-icon {
					color: #2A82E4;
					width: 80rpx;
					text-align: right;
					font-size: $baseFontSizeSm;
				}
			}

			.item-qrcode {
				width: calc(100% - 20rpx);
				margin: 10rpx;
			}

		}

		.pay-form {
			margin: 20rpx;
			background-color: #fff;
			padding: 30rpx;
			border-radius: 20rpx;
		
			.pay-form-item {
				display: flex;
				align-items: center;
				margin-bottom: 20rpx;
				.label-name {
					margin-right: 30rpx;
					font-size: $baseFontSize;
					width: 140rpx;
				}
				.pay-input{
					width: calc(100% - 160rpx);
					padding:0 20rpx;
					background: #f5f5f5;
					border-radius: 10rpx;
				}
			}
		}

		
		.bottom-btn-box{
			position: fixed;
			width: 100%;
			bottom: 0;
			left: 0;
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 30rpx;
			background: #f5f5f5;
			.submit-btn {
				margin: 0;
				border-radius:96rpx;
				padding: 0 60rpx;
			}
			.close-btn {
				margin: 0;
				border-radius:96rpx;
				height: 96rpx;
				line-height: 96rpx;
				padding: 0 60rpx;
				background:#EEEEEE ;
			}
		}
		.tip-wrapper {
			margin: 20rpx;
			background-color: #fff;
			padding: 30rpx;
			border-radius: 20rpx;
			color: #000;
			.tip-head{
				font-size: $baseFontSize;
			}
			.tip-item{
				margin-top: 20rpx;
				font-size: $baseFontSizeSm;
			}
			.tip-text {
				font-size: $baseFontSizeSm;
				color: #999999;
			}
		}
	}

	.qrcode-wrapper {
		width: 600rpx;
	}

	.qrcode-bg {
		width: 100%;
	}
	.recharge-qrcode-tips{
		margin: 20rpx;
		color: #000;
		font-size: $baseFontSizeDefault;
		.recharge-qrcode-tips-money{
			color: #FF5733;
			font-size: $baseFontSizeLgXX;
			font-weight: bold;
			margin: 0 20rpx;
		}
	}
</style>