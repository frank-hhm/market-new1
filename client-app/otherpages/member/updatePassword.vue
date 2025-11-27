<template>
	<view class="container">
		<u-form :model="form" ref="uForm" class="form" label-width="200">
			<u-form-item label="原密码" prop="old_pwd">
				<u-input type="password" class="input" v-model="form.old_pwd" placeholder="请输入原密码" />
			</u-form-item>
			<u-form-item label="新密码" prop="pwd">
				<u-input type="password" class="input" v-model="form.pwd" placeholder="请输入新密码" />
			</u-form-item>
			<u-form-item label="确认新密码" prop="conf_pwd">
				<u-input type="password" class="input" v-model="form.conf_pwd" placeholder="请输入新密码" />
			</u-form-item>
		</u-form>
		<!-- <view class="tip-label">
			友情提示
		</view>
		<view class="tip-desc">
			当您修改密码后，指纹登录功能会自动关闭，需要重新开启，请知晓
		</view> -->
		<view class="common-btn submit-btn" @tap="submit">提交</view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				submitLoading:false,
				form: {
					old_pwd: "",
					pwd: "",
					conf_pwd: "",
				},
				rules: {
					old_pwd: {
						required: true,
						message: '请输入原密码',
						trigger: ['change', 'blur'],
					},
					pwd: {
						required: true,
						message: '请输入新密码',
						trigger: ['change', 'blur'],
					},
					conf_pwd: [{
							required: true,
							message: '请再次确认新密码',
							trigger: ['change', 'blur'],
						},
						{
							// 自定义验证函数，见上说明
							validator: (rule, value, callback) => {
								return value === this.form.pwd
							},
							message: '2次密码输入不一致',
							// 触发器可以同时用blur和change
							trigger: ['change', 'blur'],
						}
					],
				}
			};
		},
		methods: {
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.updateMemberPasswordApi(this.form).then(() => {
							uni.showToast({
								icon: "success",
								title: "重置成功"
							})
							this.submitLoading = false;
							setTimeout(() => {
								this.$Router.push("/pages/login/index")
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

		},
		// 必须要在onReady生命周期，因为onLoad生命周期组件可能尚未创建完毕
		onReady() {
			this.$refs.uForm.setRules(this.rules);
		},
	}
</script>

<style lang="scss">
	.container {
		padding-top: 18rpx;

		.form {
			padding-left: 30rpx;
			background-color: #fff;

			.input {
				flex-basis: 450rpx;
				flex-grow: 0;
			}
		}

		.submit-btn {
			margin: 60rpx 30rpx;
		}
	}
</style>