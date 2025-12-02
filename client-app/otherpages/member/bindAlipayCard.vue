<template>
	<view class="container">
		<view class="card-form">
			<view class="title">请绑定支付宝账号</view>
			<u-form :model="form" ref="uForm" class="mt-30" label-width="150">
				<u-form-item label="支付宝账号" prop="alipay_card">
					<u-input v-model="form.alipay_card" placeholder="请输入支付宝账号" />
				</u-form-item>
				<u-form-item label="支付宝姓名" prop="alipay_name">
					<u-input v-model="form.alipay_name" placeholder="请输入支付宝姓名" />
				</u-form-item>
				<u-form-item label="支付宝姓名" prop="alipay_img">
					<u-upload ref="uUpload" :action="fileUploadAction" width="160" name="image" height="160"
						:file-list="fileList" max-count="1" :show-progress="false" :multiple="false"
						@on-change="onUploadChange" :before-upload="onBeforeUpload" :custom-btn="member && form.alipay_img ? true:false">
						<view slot="addBtn" v-if="form.alipay_img" class="slot-btn" hover-class="slot-btn__hover"
							hover-stay-time="150">
							<image :src="form.alipay_img" mode="widthFix" style="width: 160rpx;height: 160rpx;"></image>
						</view>
					</u-upload>
				</u-form-item>


			</u-form>
			<view class="common-btn submit-btn" @tap="submit">确认提交</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
	} from "vuex"
	import {
		BASE_URL
	} from "@/common/config.js"
	export default {
		data() {
			return {
				submitLoading: false,
				form: {
					alipay_card: "",
					alipay_name: "",
					alipay_img: "",
				},
				rules: {},
				fileUploadAction: "",
				fileList: [],
				fileList2: [],
			};
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						if (this.form.alipay_card === "") {
							uni.showToast({
								icon: "none",
								title: "请输入账号"
							})
							return false
						}
						if (this.form.alipay_name === "") {
							uni.showToast({
								icon: "none",
								title: "请输入姓名"
							})
							return false
						}
						if (this.form.alipay_img === "") {
							uni.showToast({
								icon: "none",
								title: "请上传收款码"
							})
							return false
						}
						let form = {
							alipay_card: this.form.alipay_card,
							alipay_name: this.form.alipay_name,
							alipay_img: this.form.alipay_img
						}
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.bindAlipayApi(form).then(() => {
							uni.showToast({
								icon: "success",
								title: "操作成功"
							})
							this.submitLoading = false;
							setTimeout(() => {
								this.$Router.back(1)
							}, 1000)
						}).catch((err) => {
							this.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: err.data.message
							})
						})
					} else {
						console.log('验证失败');
					}
				});
			},
			onBeforeUpload() {
				uni.showLoading({
					title: '上传中...'
				})
			},
			onUploadChange(res, index, lists, name) {
				// 返回一个promise
				this.form.alipay_img = lists[0].response.data.voucher_url
				console.log(lists)
				uni.hideLoading()
			}
		},
		// 必须要在onReady生命周期，因为onLoad生命周期组件可能尚未创建完毕
		onReady() {
			this.$refs.uForm.setRules(this.rules);
		},
		async onShow() {
			this.fileUploadAction = BASE_URL() + "/api/common.publics/uploadImage"
			setTimeout(() => {
				this.$nextTick(() => {
					if (this.member) {
						console.log(this.member)
						this.form.alipay_img = this.member.alipay_img || ''
						this.form.alipay_name = this.member.alipay_name || ''
						this.form.alipay_card = this.member.alipay_card || ''
					}
				})
			}, 200)
		}
	}
</script>

<style lang="scss">
	.container {
		.card-form {
			padding: 30rpx;
			background-color: #FFFFFF;

			.title {
				font-size: 24rpx;
				color: #999;
			}
		}

		.form-item-wrapper {
			flex: 1;
		}

		.submit-btn {
			margin-top: 40rpx
		}

	}
</style>