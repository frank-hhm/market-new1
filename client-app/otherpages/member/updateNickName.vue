<template>
	<view class="container container-bg container-page">
		<view class="input-wrapper flex">
			<u-input class="input" v-model="value" type="text" />
		</view>
		<view class="common-btn submit-btn" @tap="modifyNickName">提交</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters,
	} from "vuex"
	export default {
		components: {},
		data() {
			return {
				submitLoading: false,
				value: ""
			};
		},
		computed: {
			...mapState("member", ['member']),
		},
		methods: {
			modifyNickName() {
				if (this.submitLoading === true) {
					return false
				}
				this.submitLoading = true;
				this.$u.api.updateMemberApi({
					nickname: this.value
				}).then(() => {
					this.submitLoading = false;
					this.$Router.back()
				}).catch((err) => {
					this.submitLoading = false;
					uni.showToast({
						icon: "none",
						title: err.data.message
					})
				})
			}
		},
		onLoad() {
			this.value = this.member.nickname || ""
		}
	}
</script>

<style lang="scss">
	.container {
		.input-wrapper {
			margin: 20rpx;
			padding: 10rpx 20rpx;
			background-color: #fff;
		}

		.input {
			flex: 1;
			background-color: #fff;
		}

		.submit-btn {
			margin: 20rpx;
		}
	}
</style>