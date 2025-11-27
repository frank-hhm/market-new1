<template>
	<view class="container container-bg container-page">
		<customNav title="绑定银行卡"></customNav>
		<view class="card-form">
			<view class="title">请绑定本人的银行卡</view>
			<u-form :model="form" ref="uForm" class="mt-30" label-width="150">
				<u-form-item class="card-form-item" label="持卡人" prop="name">
					<u-input v-model="form.name" placeholder="请输入您的姓名" />
				</u-form-item>
				<u-form-item class="card-form-item" prop="account" label="银行卡号">
					<view class="form-item-wrapper">
						<u-input v-model="form.account" placeholder="请输入银行卡号" />
					</view>
				</u-form-item>
				<u-form-item class="card-form-item" prop="bank" label="发卡银行">
					<view class="form-item-wrapper">
						<u-input v-model="form.bank" type="select" @click="bankShow = true" />
					</view>
				</u-form-item>
				<!-- <u-form-item prop="area" label="开户省市">
					<view class="form-item-wrapper">
						<u-input v-model="form.area" type="select" @click="areaShow = true" />
					</view>
				</u-form-item> -->
				<u-form-item class="card-form-item" prop="openArea" label="开户支行">
					<view class="form-item-wrapper ">
						<u-input v-model="form.openArea" placeholder="请输入开户支行" />
					</view>
				</u-form-item>
			</u-form>
			<view class="common-btn submit-btn" @tap="submit">确认提交</view>
			<u-picker v-model="bankShow" mode="selector" :range="bankList" @confirm="bankConfirm"></u-picker>
			<u-picker v-model="areaShow" mode="region" @confirm="areaConfirm"></u-picker>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		bankList
	} from "@/common/config.js"
	import {
		mapState,
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {
				submitLoading: false,
				form: {
					name: "",
					account: "",
					bank: "",
					area: "",
					openArea: ""
				},
				rules: {
					name: {
						required: true,
						message: '请输入真实姓名',
						trigger: ['change', 'blur'],
					},
					account: {
						required: true,
						message: '请输入银行卡号',
						trigger: ['change', 'blur'],
					},
					bank: {
						required: true,
						message: '请输入发卡银行',
						trigger: ['change', 'blur'],
					},
					area: {
						required: true,
						message: '请输入开户省市',
						trigger: ['change', 'blur'],
					},
					openArea: {
						required: true,
						message: '请输入开户支行',
						trigger: ['change', 'blur'],
					}
				},
				bankShow: false,
				bankList: bankList,
				areaShow: false
			};
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			bankConfirm(value) {
				this.form.bank = this.bankList[value[0]]
			},
			areaConfirm(value) {
				this.form.area = `${value.province.label}-${value.city.label}-${value.area.label}`
			},
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						let form = {
							// address:this.form.area,
							bank_child: this.form.openArea,
							bank_code: this.form.account,
							bank_name: this.form.bank,
							bank_real_name: this.form.name
						}
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.bindBankCardApi(form).then((res) => {
							uni.showToast({
								icon: "success",
								title: res.message
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
				this.form.name = this.member.bank_real_name || ''
				this.form.account = this.member.bank_code || ''
				this.form.bank = this.member.bank_name || ''
				this.form.openArea = this.member.bank_child || ''
			}
		}
	}
</script>

<style lang="scss">
	.container {
		.card-form {
			.title {
				font-size: 24rpx;
				padding-left: 30rpx;
				margin-top: 20rpx;
				color: #999;
			}
		}

		.card-form-item {
			background-color: #fff;
			padding: 30rpx 30rpx;
			margin-top: 20rpx;
			position: relative;
			height: 140rpx;
			line-height: 70rpx;
		}

		.form-item-wrapper {
			flex: 1;
		}

		.common-btn {
			margin: 20rpx;
		}
	}

	.u-form-item__message {
		position: absolute;
		bottom: 10rpx;
	}
</style>