<template>
	<view class="container container-page">
		<customNav title="登录" isBgImage>

		</customNav>
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
			<view class="login-form">
				<u-form :model="form" ref="uForm" label-width="30">
					<view class="form-item">
						<!-- <view class="form-item-header">
							<u-icon name="account" size="28"></u-icon>
							账号
						</view> -->
						<u-form-item prop="username" :border-bottom="false" errorIsFixed>
							<view class="form-item-wrapper">
								<u-input v-model="form.username" placeholder-style="font-size:30rpx;color:#A6A6A6"
									placeholder="请输入账号/邮箱/手机" />
							</view>
						</u-form-item>
					</view>
					<view class="form-item">
						<!-- <view class="form-item-header">
							<u-icon name="lock" size="28"></u-icon>
							密码
						</view> -->
						<u-form-item prop="password" :border-bottom="false" errorIsFixed>
							<view class="form-item-wrapper">
								<u-input type="password" v-model="form.password"
									placeholder-style="font-size:30rpx;color:#A6A6A6" placeholder="请输入密码"
									password-icon />
							</view>
						</u-form-item>
					</view>
				</u-form>
				<view class="bottom-btn-box">
					<router-link to="/pages/login/register" class=" ">没有账号?去<text
							class="text-blue">注册</text></router-link>
					<router-link to="/otherpages/member/forgetPassword"
						class="forget-link common-btn">找回密码</router-link>
				</view>

			</view>

			<view class="login-btn common-btn" @tap="submit">登录</view>
			<view class="login-bottom">
				<u-checkbox class="login-bottom-checkbox" v-model="checked"></u-checkbox>
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
			<router-link class="login-btn-kefu" to="/pages/other/kefu"></router-link>

		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		tabbarUrl
	} from "@/common/config.js"
	import {
		mapMutations,
		mapState,
		mapGetters
	} from "vuex"
	import {
		initDetail
	} from "@/utils/initData.js"
	export default {
		components: {
			customNav
		},
		data() {
			return {
				submitLoading: false,
				checked: false,
				isShowPassword: false,
				form: {
					username: "",
					password: ""
				},
				rules: {
					username: [{
						required: true,
						message: '请输入账号',
						// 可以单个或者同时写两个触发验证方式 
						trigger: ['change', 'blur'],
					}],
					password: [{
						required: true,
						message: '请输入账号密码',
						trigger: ['change', 'blur'],
					}]
				}
			};
		},
		onShow() {},
		computed: {
			...mapState("app", ["systemConfig"]),
			...mapGetters("app", ["getConfig"])
		},
		methods: {
			...mapMutations("member", ['setMember', 'setToken']),
			back() {
				this.$Router.back(1)
			},
			submit() {
				let t = this;
				if (!t.checked) {
					uni.showToast({
						icon: "none",
						title: "请先同意协议"
					})
					return false
				}
				t.$refs.uForm.validate(valid => {
					if (valid) {
						if (t.submitLoading === true) {
							return false
						}
						uni.showLoading({
							title: '正在登录中...'
						})
						t.submitLoading = true;
						t.$u.api.loginApi(t.form).then((res) => {
							uni.showToast({
								icon: "none",
								title: "登录成功"
							})
							t.setToken(res.data.token)
							t.setMember(res.data.member)
							initDetail()
							t.submitLoading = false;
							setTimeout(() => {
								t.$Router.pushTab("/pages/index/index")
							}, 1500)
						}).catch((err) => {
							t.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: "登录失败!" + (err.data.message || '')
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
			try {
				// let res = await this.$u.api.initConfigApi()
				// this.info = res
			} catch (e) {
				//TODO handle the exception
			}
		},
	}
</script>

<style lang="scss">
	.login-header {
		height: 360rpx;
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


	.container {
		.login-logo-main {
			.logo-image {
				width: 100%;
				height: 320rpx;
			}
		}

		.login-container {
			margin-top: -60rpx;
			padding: 30rpx 40rpx;
			box-sizing: border-box;
			background: #fff;
			border-radius: 20rpx;


			.navigation {
				height: 80rpx;
			}

			.login-form {
				position: relative;
				border-radius: 25rpx;
				box-sizing: border-box;

				.form-item {
					margin-bottom: 40rpx;
					border-bottom: 1rpx solid #E3E3E3;

					.form-item-header {
						position: relative;
						font-size: $baseFontSize;
						font-weight: 500;
						height: 36rpx;
						line-height: 36rpx;
						margin-top: 40rpx;

						&::after {
							position: absolute;
							content: "";
							height: 32rpx;
							width: 32rpx;
							background-size: 100% 100%;
							left: 0;
						}
					}
				}

				.title {
					font-size: $baseFontSize;
					font-weight: 600;
					color: #333;
				}

				.form-item-wrapper {
					width: 100%;
					box-sizing: border-box;
					border-radius: 50rpx;
				}
			}


			.register-link {
				margin-top: 36rpx;
				height: 88rpx;
				text-align: center;
				line-height: 88rpx;
				border-radius: $baseRadius;
				font-size: $baseFontSize;
				background: #E3E3E3;
			}

		}
	}

	.bottom-btn-box {
		display: flex;
		align-items: center;
		justify-content: space-between;

		.forget-link {
			font-size: $baseFontSize;
			font-weight: 500;
			display: flex;
			justify-content: end;
		}
	}

	.login-bottom {
		margin-top: 40rpx;
		width: calc(100% - 60rpx);
		display: flex;
		font-size: 22rpx;
		align-items: center;
		flex-wrap: wrap;
		justify-content: center;
	}

	.login-bottom-checkbox {
		display: block;
		margin-right: 10rpx;
	}

	.login-btn {
		position: fixed;
		bottom: 100rpx;
		width: calc(100% - 40rpx);
		left: 20rpx;
		font-size: $baseFontSize;
		color: #000;
		height: 88rpx;
		border-radius: 88rpx;
		background: $baseColor;
		line-height: 88rpx;
		text-align: center;
	}

	.login-btn-kefu {
		position: fixed;
		bottom: 240rpx;
		right: 20rpx;
		height: 60rpx;
		width: 60rpx;
		background-size: 100% 100%;
		background-image: url("~@/static/images-new1/common/login-kefu.png");
	}
</style>