<template>
	<view class="container">
		<view class="card-form">
			<view class="title">请绑定USD地址</view>
			<u-form :model="form" ref="uForm" class="mt-30" label-width="150">
				<u-form-item label="TRC20地址" prop="usdt_trc">
					<u-input v-model="form.usdt_trc" placeholder="请输入USDT-TRC20地址" />
				</u-form-item>
				<!-- 	<u-form-item label="BEP20地址" prop="usdt_bep_card">
					<u-input v-model="form.usdt_bep_card" placeholder="请输入USDT-BEP20地址" />
				</u-form-item> -->
			</u-form>
			<view class="common-btn submit-btn" @tap="submit">确认提交</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
	} from "vuex"
	export default {
		data() {
			return {
				submitLoading: false,
				form: {
					usdt_trc: "",
					usdt_bep_card: "",
				},
				rules: {},
			};
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						if (this.form.usdt_trc === "") {
							uni.showToast({
								icon: "none",
								title: "请输入地址"
							})
							return false
						}
						let form = {
							usdt_card: this.form.usdt_trc,
							usdt_bep_card: this.form.usdt_bep_card
						}
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.bindUsdtApi(form).then(() => {
							uni.showToast({
								icon: "success",
								title: "操作成功"
							})
							this.submitLoading = false;
							setTimeout(() => {
								this.$Router.back(1)
							}, 1000)
						}).catch((err)=>{
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
		async onShow() {
			if (this.member) {
				this.form.usdt_trc = this.member.usdt_card || ''
				this.form.usdt_bep_card = this.member.usdt_bep_card || ''
			}
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
		.submit-btn{
			margin-top:40rpx
		}

	}
</style>