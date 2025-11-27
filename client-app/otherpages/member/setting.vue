<template>
	<view class="container">
		<view class="list">
			<router-link class="item flex justify-between align-center" to="/otherpages/member/updatePassword">
				<view class="label">修改密码</view>
				<u-icon name="arrow-right" size="24" color="#A3A3A3"></u-icon>
			</router-link>
			<view class="item flex justify-between align-center">
				<view class="label">显示设置</view>
				<view class="color-list">
					<view class="color-item" :class="getMemberDefaultColor==item.value ? 'active':''"
						v-for="(item,index) in colorList" :key="index" @tap="onSetColor(item)">
						{{item.name}}
					</view>
					<!-- <text class="value">{{getMemberDefaultColor=="red"?'红涨绿跌':'绿涨红跌'}}</text> -->
				</view>
			</view>
		</view>
		<view class="logout-btn" @tap="exit">退出登录</view>
	</view>
</template>

<script>
	import {
		mapMutations,
		mapGetters
	} from "vuex"
	export default {
		data() {
			return {
				colorList: [{
						name: '红涨绿跌',
						value: "red"
					},
					{
						name: '绿涨红跌',
						value: "green"
					},

				],
				value: "red"
			};
		},
		computed: {
			...mapGetters("member", ["getMemberDefaultColor"])
		},
		methods: {
			...mapMutations("member", ["logout", "setMemberDefaultColor"]),
			exit() {
				this.logout()
				this.$Router.replace("/pages/login/index")
			},
			onSetColor(item) {
				console.log(item)
				this.setMemberDefaultColor(item.value)
				this.value = item.value
			}
		}
	}
</script>

<style lang="scss">
	.container {
		background: #F5F5F5;
		padding-top: 20rpx;

		.list {
			.item {
				padding: 30rpx 20rpx;
				margin: 0 20rpx 20rpx;
				background-color: #fff;
				border-radius: $baseRadius;

				.label {
					width: 140rpx;
					font-size: $baseFontSize;
				}

				.value {
					margin-right: 10rpx;
					margin-left: auto;
					font-size: $baseFontSize;
					color: #333333;
				}

				.color-list {
					width: calc(100% - 200rpx);
					display: flex;
					justify-content: flex-end;
					align-items: center;
					height: 56rpx;

					.color-item {
						height: 56rpx;
						line-height: 54rpx;
						font-size: 24rpx;
						font-weight: 500;
						padding: 0 14rpx;
						border-radius: $baseRadius;
						background: rgba(245, 245, 245, 1);
						border: 1rpx solid rgba(245, 245, 245, 1);
						margin-left: 20rpx;
					}

					.color-item.active {
						border: 1rpx solid rgba(36, 80, 255, 1);
						background: rgba(214, 222, 255, 1);
						color: rgba(36, 80, 255, 1);
					}
				}
			}
		}

		.logout-btn {
			margin: 60rpx 30rpx;
			height: 84rpx;
			background: #E3E3E3;
			border-radius: $baseRadius;
			font-size: $baseFontSize;
			line-height: 84rpx;
			text-align: center;
		}
	}
</style>