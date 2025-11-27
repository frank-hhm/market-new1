<template>
	<view class="container container-bg container-page">
		<customNav title="实名认证" isBgImage leftColor="#000"></customNav>
		<view class="head-main">
			<view class="head-main-bg"></view>
			<view class="head-main-box">
				<view class="head-main-title">实名认证</view>
				<view class="head-main-desc">完成实名认证即可正常入金</view>
			</view>
		</view>

		<view class="cell-main">
			<!-- member.realName && member.IDNumber -->
			<view class="cell-recognize"
				v-if="member.bind_status && (member.bind_status.value == 1 || member.bind_status.value == 2)">
				<view class="recognize-complate-icon" v-if="member.bind_status.value == 1"></view>
				<view class="recognize-complate-text" v-if="member.bind_status.value == 2">
					身份证认证审核中
				</view>
				<view class="item">
					<text class="recognize-label">姓名</text>
					<text class="recognize-value">{{member.real_name?member.real_name:'--'}}</text>
				</view class="item">
				<view class="flex justify-between mt-30">
					<text class="recognize-label">证件号码</text>
					<text class="recognize-value">{{member.card_number?member.card_number:"--"}}</text>
				</view>
			</view>
			<template v-else>
				<view class="cell-head-tip">
					请您使用有效身份证信息认证
				</view>
				<u-form :model="form" ref="uForm" class="form mt-30" label-width="0">
					<u-form-item prop="name" :border-bottom="false">
						<view class="form-item-wrapper">
							<text class="label">姓名</text>
							<u-input class="input" v-model="form.name" placeholder="请填写您的姓名" />
						</view>
					</u-form-item>
					<u-form-item prop="number" :border-bottom="false">
						<view class="form-item-wrapper">
							<text class="label">证件号码</text>
							<u-input class="input" v-model="form.number" placeholder="请填写您的证件号码" />
						</view>
					</u-form-item>
				</u-form>
				<view class="cell-btn common-btn" @tap="submit">
					确认并提交
				</view>
			</template>
		</view>

		<view class="cell-tip-main">
			<view class="cell-tip-icon"></view>
			<view class="cell-tip-content">
				实名仅用于您是否为真人用户，不会对信息做任何采集与保留，请放心使用。
			</view>
		</view>
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapMutations,
		mapGetters,
		mapState
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
					number: ""
				},
				rules: {
					name: [{
						required: true,
						message: '请填写您的姓名',
						// 可以单个或者同时写两个触发验证方式 
						trigger: ['change', 'blur'],
					}],
					number: [{
						required: true,
						message: '请填写您的证件号码',
						trigger: ['change', 'blur'],
					}, {
						message: '证件号码有误',
						validator: (rule, value, callback) => {
							return this.$u.test.idCard(value)
						},
						trigger: ['change', 'blur'],
					}]
				}
			};
		},
		computed: {
			...mapState("member", ["member"]),
		},
		async onShow() {
			try {} catch (e) {
				//TODO handle the exception
			}

		},
		methods: {
			submit() {
				this.$refs.uForm.validate(valid => {
					if (valid) {
						if (this.submitLoading === true) {
							return false
						}
						this.submitLoading = true;
						this.$u.api.bindMemberRealApi({
							real_name: this.form.name,
							card_number: this.form.number
						}).then(res => {
							this.submitLoading = false;
							uni.showToast({
								icon: "none",
								title: res.message
							})
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
					}
				})
			}
		},
		// 必须要在onReady生命周期，因为onLoad生命周期组件可能尚未创建完毕
		onReady() {
			if (this.$refs.uForm) {
				this.$refs.uForm.setRules(this.rules);
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background: F5F5F5;

		.recognize-wrapper {
			padding: 40rpx 40rpx 34rpx;
			background: #FFFFFF;
			box-shadow: 0rpx 0rpx 10rpx 4rpx rgba(192, 192, 192, 0.04);
			border-radius: 16rpx;

			.label-wrapper {
				.label {
					font-size: 32rpx;
					font-weight: 500;
					color: #000000;
				}

				.label-desc {
					margin-top: 10rpx;
					font-size: 24rpx;
					color: $color-pink;
				}
			}

			.to-recognize-link {
				.recognize-text {
					color: $color-pink;
					margin-right: 20rpx;
					font-size: 24rpx;
				}

				&::after {
					content: " ";
					width: 0;
					height: 0;
					border-top: 10rpx solid transparent;
					border-right: 10rpx solid transparent;
					border-bottom: 10rpx solid transparent;
					border-left: 10rpx solid $color-pink;
				}
			}
		}

		.head-main {
			width: 100%;
			height: 180rpx;
			position: relative;
			padding: 0 24rpx;
			display: flex;
			align-items: center;
			background: $baseColor;
			color: #000;

			.head-main-bg {
				height: 228rpx;
				width: 200rpx;
				position: absolute;
				z-index: -1;
				top: 0;
				right: 16rpx;
			}

			.head-main-box {
				.head-main-title {
					font-size: 48rpx;
					font-weight: 400;
					font-family: "alimama", sans-serif;
				}

				.head-main-desc {
					font-size: 24rpx;
					font-weight: 500;
					margin-top: 16rpx;
				}
			}
		}

		.cell-main {
			width: calc(100% - 48rpx);
			margin: 40rpx 24rpx 0;
			background: #FFFFFF;
			border-radius: $baseRadius;
			padding: 104rpx 24rpx 52rpx;
			position: relative;
			overflow: hidden;

			.cell-head-tip {
				position: absolute;
				width: 100%;
				left: 0;
				top: 0;
				height: 84rpx;
				background: #D9EAFF;
				line-height: 84rpx;
				padding: 0 24rpx;
				color: #000000;
				font-size: 24rpx;
				font-weight: 500;
			}

			.cell-recognize {
				margin: 20rpx 0;

				.recognize-complate-icon {
					position: absolute;
					top: 40rpx;
					left: calc(50% - 64rpx);
					width: 128rpx;
					height: 46rpx;
					background: url("~@/static/images/icon/recognize-complate-icon.png");
					background-size: 100% 100%;
				}

				.recognize-complate-text {
					position: absolute;
					top: 40rpx;
					left: 0;
					height: 46rpx;
					width: 100%;
					text-align: center;
					color: red;
				}

				.item {
					margin-top: 40rpx;
					display: flex;
					justify-content: space-between;
				}

				.recognize-label {
					font-size: 30rpx;
					color: #333333;
				}

				.recognize-value {
					font-size: 30rpx;
					color: #999999;
				}
			}

			.form-item-wrapper {
				display: flex;
				width: 100%;
				background: #F5F5F5;
				border-radius: $baseRadius;
				height: 96rpx;
				padding: 14rpx 50rpx;

				.label {
					width: 160rpx;
					text-align: left;
				}
			}

			.cell-btn {
				width: 100%;
				height: 96rpx;
				line-height: 96rpx;
				border-radius: $baseRadius;
				margin-top: 40rpx;
				text-align: center;
				color: #FFFFFF;
				background: $baseColor;

			}
		}

		.cell-tip-main {
			margin: 40rpx 24rpx 0;
			background: #FFFFFF;
			border-radius: $baseRadius;
			height: 128rpx;
			width: calc(100% - 48rpx);
			padding: 24rpx;
			display: flex;
			align-items: center;

			.cell-tip-icon {
				height: 56rpx;
				width: 56rpx;
				background-image: url('~@/static/images/icon/recognize-icon-1.png');
				background-size: 100% 100%;
			}

			.cell-tip-content {
				width: calc(100% - 56rpx - 20rpx);
				line-height: 40rpx;
				margin-left: 20rpx;
				color: #ABABAB;
				font-size: 28rpx;
			}
		}
	}
</style>