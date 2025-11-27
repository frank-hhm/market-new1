<template>

	<view class="container container-bg container-page">
		<customNav title="入金-银行卡" leftColor="#000000"></customNav>

		<rechargeBox :query="$Route.query" channel="网银转账"></rechargeBox>

		<view class="bank-form" v-if="paymentConfig.setting">
			<view v-for="(item,index) in paymentConfig.setting" :key="index">
				<view v-if="item.type ==='text' || item.type ==='textarea'" class="form-item">
					<view class="label">{{item.key}}</view>
					<view class="value">{{item.value || ''}}</view>
					<view class="copy-icon" @tap="copy(item.value || '')">复制</view>
				</view>
				<view class="form-item" v-if="item.type ==='image'">
					<view class="label">{{item.key}}</view>
					<view class="form-item-right" @tap="onPreView(item.value || '')">查看</view>
				</view>
			</view>
		</view>
		<view class="pay-form">
			<view class="pay-form-item">
				<text class="label-name">付款人姓名:</text>
				<view  class="pay-input" ><u-input type="text" v-model="payName" /></view>
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
			<view class="tip-item">1、请使用账户本人名下的银行卡进行注资，如通过非本人名下银行卡进行注资操作，资金原路退回并需承担退回所产生的相关费用。</view>
			<view class="tip-item">2、不同支付通道可能存在汇率偏差，请以支付通道“兑换后约支付”的对应人民币金额进行转账。转账金额错误，或将导致不到账或资金退回。</view>
			<view class="tip-item">3、请勿频繁提交失败的注资订单或多次取消注资订单，频繁操作将限制提交注资。</view>
		</view>

		<view class="bottom-btn-box">
			<view class="close-btn" @tap="close">取消支付</view>
			<view class="common-btn submit-btn" @tap="submit">支付成功，提醒收款</view>
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
				bankInfo: {},
				payName: "",
				usdt: '',
				member: {},
				remark: "",
				paymentConfig: {},
				qrcodeStatus: false,
				qrcodeSrc: "",
				submitLoading: false,
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
			submit() {
				console.log(1)
				let t = this
				if (!t.payName) {
					uni.showToast({
						icon: "none",
						title: "请输入付款人姓名"
					})
					return false
				}
				if (t.submitLoading === true) {
					return false
				}
				t.submitLoading = true;
				uni.showLoading({
					title: '申请提交中...'
				})
				let res = t.$u.api.rechargeTransferApi({
					money: t.$Route.query.money,
					account: t.payName,
					usdt: t.usdt,
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
				console.log(e)
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
		padding-bottom: 200rpx;
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

		.bank-form {
			background-color: #FFFFFF;
			margin: 20rpx;
			padding: 30rpx 30rpx 10rpx;
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
</style>