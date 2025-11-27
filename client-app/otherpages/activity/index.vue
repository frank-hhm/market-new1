<template>
	<view class="container container-page">
		<customNav title="活动" leftColor="#000">
		</customNav>
		<view class="activity-list">
			<view class="activity-item" @click="goToPath('/otherpages/activity/turntable')">
				<view class="activity-item-bg bg-1"></view>
				<view class="activity-item-title">入金抽现金</view>
			</view>
			<view class="activity-item"  @click="goToPath('/otherpages/activity/deliver')">
				<view class="activity-item-bg bg-2"></view>
				<view class="activity-item-title">下单送好礼</view>
			</view>
			<view class="activity-item" @click="goToPath('/otherpages/share/index')">
				<view class="activity-item-bg bg-3"></view>
				<view class="activity-item-title">邀请领佣金</view>
			</view>
			<view v-if="!isLogin" class="activity-item" @click="goToPath('/pages/login/index')">
				<view class="activity-item-bg bg-4"></view>
				<view class="activity-item-title">注册领$50000体验金</view>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters,
		mapMutations
	} from "vuex"
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {}
		},
		computed: {
			...mapState("member", ["isLogin"]),
		},
		methods: {
			goToPath(path) {
				this.$Router.push({
					path
				})
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background: #f5f5f5;

		.activity-list {
			.activity-item {
				margin: 20rpx;
				background: #fff;
				border-radius: 20rpx;
				overflow: hidden;

				.activity-item-bg {
					width: 100%;
					height:200rpx;
						background-size:  100% auto;
						background-repeat: no-repeat;
						display: inline-block;
						background-position: center;

					&.bg-1 {
						background-image: url('@/static/images-new1/activity/bg-1.png');
					}
					&.bg-2 {
						background-image: url('@/static/images-new1/activity/bg-2.png');
					}
					&.bg-3 {
						background-image: url('@/static/images-new1/activity/bg-3.png');
					}
					&.bg-4 {
						background-image: url('@/static/images-new1/activity/bg-4.png');
					}
				}
				.activity-item-title{
					font-size: $baseFontSizeDefault;
					color: #000;
					margin:10rpx 20rpx 20rpx;
				}
			}
		}
	}
</style>