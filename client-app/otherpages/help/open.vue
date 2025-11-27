<template>
	<view class="container container-page">
		<customNav title="开户" leftColor="#000"></customNav>
		<view class="content">
			<view class="topView">
				注册与开户
			</view>
			<view class="botView lettSpace">
				<p>注册&开户时间：7x24小时均可开户</p>
				<p>开户方式：可通过网站或APP注册开户，提供手机号码及真实姓名、设置登录密码、同意并签署协议即完成开户。</p>
			</view>
			<view class="topView">
				登录&找回密码
			</view>
			<view class="botView lettSpace">
				使用注册时填写的手机号码、已设置的密码登录网站个人中心、APP。忘记密码可通过手机+短信验证码的方式找回。
			</view>
			<!-- <view class="topView">
				MT4账号
			</view>
			<view class="botView lettSpace">
				注册&开户成功后，将通过短信发送MT4账号和初始密码，用于MT4交易软件的登录。
			</view> -->
			<view class="topView">
				实名认证
			</view>
			<view class="botView lettSpace">
				<view class="content-explain">
					实名认证是保护账户安全的必要措施，账户出金（取款）前必须完成实名认证。
				</view>
				<view class="content-explain">
					可通过网站或APP的个人中心提交身份资料和银行资料，审核时间一般为1~2个工作日。
				</view>
				<view class="content-explain">
					身份证明资料应提供本人身份证正反面照片，银行证明资料应提供银行卡正面照片，身份证明文件、银行卡持有人应与账户的真实姓名一致。
				</view>
			</view>
			<view class="topView">
				平台介绍
			</view>
			<view class="botView lettSpace">
				平台是专业领先的国际网上外汇交易、大宗商品差价合约交易及相关服务经纪商。本公司的使命是透过提供创新……
			</view>
		</view>
		<!-- <view class="btnStyle">
			入金领赠金
		</view> -->
	</view>
</template>

<script>
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {

			};
		},
		methods: {
			clickLeft() {
				uni.navigateBack()
			}
		}
	}
</script>

<style lang="scss" scoped>
	.page {
		position: relative;
		padding-bottom: 100rpx;
	}

	.navSty {
		position: fixed;
		width: 100%;
		height: 100rpx;
		background-image: linear-gradient(to bottom, #2343b0, #3157c6, #4c71e4);
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.navText {
		width: 100%;
		height: 60rpx;
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0 20rpx 0 10rpx;
	}

	.titleSty {
		color: #fff;
		font-size: $baseFontSize;
	}

	.btnStyle {
		position: fixed;
		bottom: 0%;
		width: 100%;
		height: 100rpx;
		background-color: var(--base-default);
		color: #fff;
		text-align: center;
		line-height: 100rpx;
	}

	.content {
		padding-top: 20rpx;

		.topView {
			padding: 25rpx 25rpx;
			color: var(--base-default);
			font-weight: 600;
			font-size: $baseFontSize;
		}

		.botView {
			padding: 15rpx 25rpx 35rpx;
			background-color: #f2f2f2;
			color: #939393;
			font-size: $baseFontSizeSm;

			.content-explain {
				margin-bottom: 20rpx;
			}
		}
	}

	.lettSpace {
		letter-spacing: .2em;
		line-height: 3em;
	}
</style>
