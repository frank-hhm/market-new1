<template>
	<view class="container container-page">
		<customNav title="" isBgImage></customNav>


		<view class="login-header">
			<view class="login-header-icon">

			</view>
			<view class="login-header-main">

				<view class="login-header-main-icon">

				</view>
				<view class="login-header-main-title">
					HK{{getConfig.system_name || '亮点国际'}}
				</view>
			</view>
		</view>
		<view class="login-container">
			<view class="register-title">注册</view>
			<view class="register-type-main">
				<view class="register-type-item" :class="type == 'email'?'active':''" @tap="setType('email')">邮箱</view>
				<view class="register-type-item" :class="type == 'phone'?'active':''" @tap="setType('phone')">手机号</view>
			</view>
			<view class="login-form">
				<u-form :model="form" ref="uForm" class="mt-30" label-width="30">
					<view class="form-item" v-if="type == 'phone'">
						<view class="form-item-wrapper">
							<u-form-item prop="phone" :border-bottom="false" errorIsFixed>
								<u-input v-model="form.phone" placeholder-style="font-size:28rpx;color:#A6A6A6"
									placeholder="请输入手机号码" />
							</u-form-item>
						</view>
					</view>
					<view class="form-item" v-if="type == 'email'">
						<view class="form-item-wrapper">
							<u-form-item prop="email" :border-bottom="false" errorIsFixed>
								<u-input v-model="form.email" placeholder-style="font-size:28rpx;color:#A6A6A6"
									placeholder="请输入邮箱" />
							</u-form-item>
						</view>
					</view>
					<view class="form-item">
						<view class="form-item-wrapper">
							<u-form-item prop="vcode" :border-bottom="false" errorIsFixed>
								<view class="form-item-wrapper-box">

									<u-input class="vcode-input" v-model="form.vcode"
										placeholder-style="font-size:28rpx;color:#A6A6A6" placeholder="请输入验证码" />
									<view class="vcode-btn common-btn" @tap="getVcode">{{vcodeBtnStatus}}</view>
								</view>
							</u-form-item>
						</view>
					</view>
					<view class="form-item">
						<view class="form-item-wrapper">
							<u-form-item prop="password" :border-bottom="false" errorIsFixed>
								<u-input type="password" v-model="form.password"
									placeholder-style="font-size:28rpx;color:#A6A6A6" placeholder="请输入账号密码"
									password-icon />
							</u-form-item>
						</view>
					</view>
					<view class="form-item">
						<view class="form-item-wrapper">
							<u-form-item prop="rPassword" :border-bottom="false" errorIsFixed>

								<u-input type="password" v-model="form.rPassword"
									placeholder-style="font-size:28rpx;color:#A6A6A6" placeholder="请再次输入账号密码"
									password-icon />

							</u-form-item>
						</view>
					</view>
					<view class="form-item">
						<view class="form-item-wrapper">
							<u-form-item prop="code" :border-bottom="false" errorIsFixed>
								<u-input v-model="form.code" placeholder-style="font-size:28rpx;color:#999"
									placeholder="请输入邀请码" />
							</u-form-item>
						</view>
					</view>
				</u-form>
				<view class="bottom-btn-box">
					<router-link to="/pages/login/index" class=" ">已有账号?去<text
							class="text-blue">登录</text></router-link>
				</view>

				<view class="login-btn-kefu-box">
				<router-link class="login-btn-kefu" to="/pages/other/kefu"></router-link></view>
				<view class="register-btn common-btn" @tap="submit">开户</view>
				<view class="register-bottom">
					<u-checkbox class="register-bottom-checkbox" v-model="checked"></u-checkbox>
					<text>我已阅读并同意</text>
					<router-link class="artical common-btn"
						:to="{path:'/otherpages/agreement/artical?type=agreement_customer&title=客户协议'}">
						《客户协议》
					</router-link>
					<router-link class="artical common-btn"
						:to="{path:'/otherpages/agreement/artical?type=agreement_risk&title=风险披露'}">
						《风险披露》
					</router-link>
					<router-link class="artical common-btn"
						:to="{path:'/otherpages/agreement/artical?type=agreement_disclaimers&title=免责声明'}">
						《免责声明》
					</router-link>
				</view>
				<!-- <view class="bottom-kefu">
					如60秒内无法接收到验证码，请联系
					<router-link class="artical common-btn" :to="{path:'/pages/other/kefu'}">客服</router-link>
				</view> -->
			</view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapState,
		mapGetters
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {
				submitLoading: false,
				vcodeBtnStatus: "获取验证码",
				timer: null,
				checked: false,
				member_code: "",
				info: {},
				type: "email",
				form: {
					email: "",
					phone: "",
					account: "",
					password: "",
					rPassword: "",
					vcode: "",
					code: ""
				},
				rules: {
					// 字段名称
					phone: [{
							required: true,
							message: '请输入手机号',
							trigger: ['change', 'blur'],
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								// 上面有说，返回true表示校验通过，返回false表示不通过
								// this.$u.test.mobile()就是返回true或者false的
								return this.$u.test.mobile(value);
							},
							message: '手机号码不正确',
							// 触发器可以同时用blur和change
							trigger: ['change', 'blur'],
						}
					],
					email: [{
							required: true,
							message: '请输入邮箱',
							trigger: ['change', 'blur'],
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								// 上面有说，返回true表示校验通过，返回false表示不通过
								// this.$u.test.mobile()就是返回true或者false的
								return this.$u.test.email(value);
							},
							message: '邮箱不正确',
							// 触发器可以同时用blur和change
							trigger: ['change', 'blur'],
						}
					],
					account: {
						required: true,
						message: '请输入用户名',
						trigger: ['change', 'blur'],
					},
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
					code: {
						required: true,
						message: '请输入邀请码',
						trigger: ['change', 'blur'],
					},
				}
			};
		},
		computed: {
			...mapState("app", ["systemConfig"]),
			...mapGetters("app", ["getConfig"])
		},
		methods: {
			setType(type) {
				this.type = type
			},
			getVcode() {
				if (this.timer) {
					return false
				}
				if (this.type == 'phone' && !this.$u.test.mobile(this.form.phone)) {
					uni.showToast({
						icon: "none",
						title: "手机号码格式有误"
					})
					return false
				} else if (this.type == 'email' && !this.$u.test.email(this.form.email)) {
					uni.showToast({
						icon: "none",
						title: "邮箱格式有误"
					})
					return false
				}
				let number = ""
				if (this.type == 'phone') {
					number = this.form.phone
				} else if (this.type == 'email') {
					number = this.form.email

				}
				uni.showLoading({
					title: "正在发送..."
				})
				this.$u.api.getNewVcodeApi({
					number: number,
					type: this.type,
					temp: "register"
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
				let t = this;
				t.$refs.uForm.validate(valid => {
					if (valid) {
						if (!t.checked) {
							uni.showToast({
								icon: "none",
								title: "请先同意协议"
							})
							return false
						}
						let form = {
							username: t.form.account,
							mobile: t.form.phone,
							email: t.form.email,
							register_type: t.type,
							pwd: t.form.password,
							conf_pwd: t.form.rPassword,
							code: t.form.vcode,
							invite_code: t.form.code
						}
						if (t.submitLoading === true) {
							return false
						}
						uni.showLoading({
							title: '开户中...'
						})
						t.submitLoading = true;
						t.$u.api.registerApi(form).then((res) => {
							t.submitLoading = false;
							uni.showToast({
								icon: "success",
								title: res.message
							})
							setTimeout(() => {
								t.$Router.push("/pages/login/index")
							}, 1000)
						}).catch((err) => {
							t.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: "开户失败!" + (err.data.message || '')
							})
						})
					} else {
						console.log('验证失败');
					}
				});
			}
		},
		// 必须要在onReady生命周期，因为onLoad生命周期组件可能尚未创建完毕
		async onReady() {
			this.$refs.uForm.setRules(this.rules);
			try {} catch (e) {
				//TODO handle the exception
			}
		},
		async onLoad() {
			try {
				this.member_code = this.$Route.query.code || ""
				if (!this.member_code) {
					uni.getStorage({
						key: 'invite_member',
						success: (res) => {
							this.member_code = res.data;
						},
					})
				}
				if (this.member_code) {
					this.form.code = this.member_code
					uni.setStorage({
						key: 'invite_member',
						data: this.member_code,
						success: () => {},
						fail: (e) => {}
					});
				}
			} catch (e) {
				//TODO handle the exception
			}
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
	.login-header {
		height: 320rpx;
		background: #F1C934;
		position: relative;
		z-index: -1;

		.login-header-icon {
			position: absolute;
			height: 80%;
			width: 100%;
			left: 0;
			top: -20%;
			background-size: auto 100%;
			background-repeat: no-repeat;

			background-image: url("~@/static/images-new1/common/login-top-2.png");
		}

		.login-header-main {
			height: calc(100% - 80rpx);
			display: flex;
			align-items: center;
			justify-content: center;

			.login-header-main-icon {
				height: 80rpx;
				width: 80rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/common/login-top.png");
			}

			.login-header-main-title {
				margin-left: 20rpx;
				font-size: $baseFontSizeLgXX;
				font-weight: bold;
			}
		}
	}

	.flex-wrap {
		margin-top: 20rpx;
		flex-wrap: wrap;

		font-size: 22rpx;
		position: fixed;
		bottom: 2%;
	}

	.register-title {
		color: #000;
		font-size: $baseFontSizeDefault;
	}

	.register-type-main {
		margin-top: 20rpx;
		display: flex;
		align-items: center;
		height: 80rpx;
		line-height: 80rpx;
		background: #F6F6F6;
		border-radius: 80rpx;
		justify-content: space-between;
		padding: 10rpx;

		.register-type-item {
			width: calc(50% - 20rpx);
			text-align: center;
			height: 70rpx;
			line-height: 70rpx;
			border-radius: 70rpx;
			color: #000;

			&.active {
				background: #fff;
				font-weight: bold;
			}
		}
	}

	.login-container {
		margin-top: -60rpx;
		padding: 30rpx 40rpx;
		box-sizing: border-box;
		background: #fff;
		border-radius: 20rpx;

		.login-form {
			box-sizing: border-box;

			.title {
				font-size: 32rpx;
				font-weight: 600;
				color: #333;
			}

			.form-item {
				border-bottom: 1rpx solid #F7F7F7;
				display: flex;
				align-items: center;

				.form-item-header {
					position: relative;
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
				margin-bottom: 0;
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
				color: #000;
				line-height: 50rpx;
				text-align: center;
				background: #F5F6F8;
			}
		}

		.register-bottom {
			margin-top: 50rpx;
			display: flex;
			font-size: 22rpx;
			align-items: center;
			justify-content: center;
		}

		.register-bottom-checkbox {
			display: block;
			margin-right: 10rpx;
		}

		.register-btn {
			margin-top: 50rpx;
			font-size: 28rpx;
			color: #000;
			height: 88rpx;
			border-radius: 88rpx;
			background: $baseColor;
			line-height: 88rpx;
			text-align: center;
		}

		.bottom-kefu {
			margin-top: 20px;
			font-size: 12px;
			// position: fixed;
			display: flex;
			align-items: center;
			justify-content: center;
			// bottom: 2%;
			width: 100%;
			// left: 0;
		}

		.artical {
			color: $baseColor;
			margin-left: 5rpx;
		}
	}

	.register-image {
		width: 100%;
		height: 320rpx;
	}
	.login-btn-kefu-box{
		margin-top: 40rpx;
		margin-bottom: 40rpx;
		display: flex;
		justify-content:end;
	}
	.login-btn-kefu{
			height: 60rpx;
			width: 60rpx;
				background-size: 100% 100%;
				background-image: url("~@/static/images-new1/common/login-kefu.png");
	}

	.bottom-btn-box {
		margin-top: 20rpx;
		display: flex;
		align-items: center;
		justify-content: space-between;

	}
</style>