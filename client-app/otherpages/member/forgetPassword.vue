<template>
	<view class="container container-page">
		<view class="forget-form">
			<u-form :model="form" ref="uForm" class="mt-30" label-width="30">

				<view class="form-item">
					<view class="form-item-wrapper">
						<u-form-item prop="phone" :border-bottom="false">
							<u-input v-model="form.phone" placeholder-style="font-size:28rpx;font-weight:600;color:#999"
								placeholder="请输入邮箱/手机号码" />
						</u-form-item>
					</view>
				</view>
				<view class="form-item">
					<view class="form-item-wrapper">
						<u-form-item prop="vcode" :border-bottom="false">
							<view class="form-item-wrapper-box">
								<u-input class="vcode-input" v-model="form.vcode"
									placeholder-style="font-size:28rpx;font-weight:600;color:#999"
									placeholder="请输入验证码" />
								<view class="vcode-btn" @tap="getVcode">{{vcodeBtnStatus}}</view>
							</view>
						</u-form-item>
					</view>
				</view>
				<view class="form-item">
					<view class="form-item-wrapper">
						<u-form-item prop="password" :border-bottom="false">
							<u-input type="password" v-model="form.password"
								placeholder-style="font-size:28rpx;font-weight:600;color:#999" placeholder="请输入账号密码"
								password-icon />
						</u-form-item>
					</view>
				</view>
				<view class="form-item">
					<view class="form-item-wrapper">
						<u-form-item prop="rPassword" :border-bottom="false">
							<u-input type="password" v-model="form.rPassword"
								placeholder-style="font-size:28rpx;font-weight:600;color:#999" placeholder="请再次输入账号密码"
								password-icon />
						</u-form-item>
					</view>
				</view>
			</u-form>
			<view class="save-btn common-btn" @tap="submit">重置密码</view>
		</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				submitLoading: false,
				vcodeBtnStatus: "获取验证码",
				timer: null,
				form: {
					phone: "",
					password: "",
					rPassword: "",
					vcode: "",

				},
				rules: {
					// 字段名称
					phone: [],
					password: {
						required: true,
						message: '请输入密码',
						trigger: ['change', 'blur'],
					},
					rPassword: [{
							required: true,
							message: '请再次确认密码',
							trigger: ['change', 'blur'],
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								return value === this.form.password
							},
							message: '2次密码输入不一致',
							// 触发器可以同时用blur和change
							trigger: ['change', 'blur'],
						}
					],
					vcode: {
						required: true,
						message: '请输入验证码',
						trigger: ['change', 'blur'],
					},
				},
				type:"email"
			};
		},
		methods: {
			getVcode() {
				if (this.timer) {
					return false
				}
				if(!this.$u.test.mobile(this.form.phone) && !this.$u.test.email(this.form.phone)){
					uni.showToast({
						icon: "none",
						title: "邮箱或手机号格式有误"
					})
					return false
				}
				if (this.$u.test.email(this.form.phone)) {
					this.type = "email"
				}else if (this.$u.test.mobile(this.form.phone)) {
					this.type = "phone"
				}else{
					uni.showToast({
						icon: "none",
						title: "邮箱或手机号格式有误"
					})
					this.type = ""
					return false
				}
				uni.showLoading({
					title: "正在发送..."
				})
				this.$u.api.getNewVcodeApi({
					number:this.form.phone,
					type:this.type,
					temp:"forget-password"
				}).then((res) => {
					uni.hideLoading()
					uni.showToast({
						icon: "none",
						title: res.message
					})
					let count = 60
					this.vcodeBtnStatus = count + "s"
					this.timer = setInterval(() => {
						if (count <= 0) {
							this.vcodeBtnStatus = "获取验证码"
							clearInterval(this.timer)
							this.timer = null
							return
						}
						count--
						this.vcodeBtnStatus = count + "s"
					}, 1000)
				}).catch((err) => {
					uni.hideLoading()
					console.log(err)
					uni.showToast({
						icon: "none",
						title: err.data.message || err.message || "未知错误"
					})
				})
			},
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						let form = {
							mobile: this.form.phone,
							pwd: this.form.password,
							code: this.form.vcode,
							type:this.type,
						}
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.resetPasswordApi(form).then(() => {
							uni.showToast({
								icon: "success",
								title: "修改成功"
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
			}
		},
		// 必须要在onReady生命周期，因为onLoad生命周期组件可能尚未创建完毕
		onReady() {
			this.$refs.uForm.setRules(this.rules);
		},
		onUnload() {
			if (this.timer) {
				clearInterval(this.timer)
				this.timer = null
			}
		}
	}
</script>

<style lang="scss">
	.container {

		.forget-form {
			padding: 60rpx 30rpx;
			padding: 0 30rpx;
			box-sizing: border-box;

			.title {
				font-size: 32rpx;
				font-weight: 600;
				color: #333;
			}

			.form-item {
				border-bottom: 1rpx solid #E3E3E3;
				display: flex;
				align-items: center;

				.form-item-header {
					position: relative;
					padding-left: 40rpx;
					font-size: 28rpx;
					font-weight: 500;
					height: 36rpx;
					line-height: 36rpx;
					display: block;

					&::after {
						position: absolute;
						content: "";
						height: 32rpx;
						width: 32rpx;
						background-size: 100% 100%;
						left: 0;
						top: 0;
					}

				}
			}


			.form-item-wrapper {
				width: calc(100% - 40rpx);
				box-sizing: border-box;
				margin-bottom: 10rpx;
				padding: 0 20rpx;
				border-radius: 50rpx;

				.form-item-wrapper-box {
					width: 100%;
					display: flex;
					align-items: center;
					justify-content: space-between;
				}
			}


			.vcode-input {
				width: calc(100% - 170rpx);
				margin-right: 30rpx;
			}

			.vcode-btn {
				height: 50rpx;
				width: 170rpx;
				border-radius: 25rpx;
				font-size: 24rpx;
				color: #000000;
				background: #F5F6F8;
				line-height: 50rpx;
				text-align: center;
			}
		}

		.save-btn {
			margin-top: 50rpx;
			font-size: 28rpx;
			color: #000000;
			height: 88rpx;
			border-radius: 88rpx;
			line-height: 88rpx;
			text-align: center;
			background: $baseColor;
		}
	}
</style>