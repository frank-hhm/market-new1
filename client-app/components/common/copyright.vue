<template>
	<view class="copyright-main">
		<view class="copyright-main-header">
			<view class="copyright-name">{{getConfig.system_name ||"亮点国际"}}值得信赖的期货合约平台</view>
			<view class="copyright-name-right"></view>
		</view>
		<view class="copyright-main-box">
			<view class="copyright-main-box-item">
				<view class="item-title">10年
				</view>
				<view class="item-desc">稳定运营</view>
			</view>
			<view class="copyright-main-box-item">
				<view class="item-title">362万
				</view>
				<view class="item-desc">平台用户量</view>

			</view>
			<view class="copyright-main-box-item">
				<view class="item-title">5亿
				</view>
				<view class="item-desc">订单处理数量</view>

			</view>
		</view>
	</view>
</template>

<script>
	import {
		mapState,
		mapGetters
	} from 'vuex';

	export default {
		computed: {
			...mapGetters("app", ["getConfig"]),
		},
	}
</script>

<style scoped lang="scss">
	.copyright-main {
		color: var(--base-grey);
		padding: 30rpx 0 60rpx;
		background: none;

		.copyright-main-header {
			display: flex;
			align-items: center;
			justify-content: center;
			font-size: $baseFontSizeLg;
		}

		.copyright-name-right {
			width: 30rpx;
			height: 30rpx;
			background-size: 100% 100%;
			background-image: url("~@/static/images-new1/common/right-grey.png");

		}

		.copyright-main-box {
			display: flex;
			align-items: center;
			justify-content: space-around;
			margin-top: 50rpx;

			.copyright-main-box-item {
				width: 33.33%;
				text-align: center;

				.item-title {
					position: relative;
					&::after{
						
							position: absolute;
							content: "+";
							top: -10rpx;
					}
				}

				.item-title {
				}
			}
		}
	}
</style>