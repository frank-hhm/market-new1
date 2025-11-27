<template>
	<view class=" container-page">
		<customNav title="公告详细">
		</customNav>
		<view class="detail-main">
			<view class="detail-header">

				<view class="title">{{info.title}}</view>
				<view class="time ">{{info.create_time}}</view>
			</view>
			<view class="detail-cc">
				<rich-text :nodes="content"></rich-text>
			</view>
		</view>
	</view>
</template>

<script>
	import {
		isString
	} from 'lodash';
	import customNav from '@/components/custom-nav/index.vue';
	export default {
		components: {
			customNav
		},
		data() {
			return {
				info: {},
				content: '',
			};
		},
		methods: {

			async requestNewsDetail() {
				try {
					uni.showLoading()
					await this.$u.api.getNoticeDetailApi({
						id: this.id
					}).then(res => {
						this.info = res.data
						this.content = this.info.content.replace(/auto/g, '100%')

						this.content = this.content.replace(/<p style="/g, '<p style="margin: 20px 0;')
						this.content = this.content.replace(/<img/g, '<img style="max-width: 100%;"')
						setTimeout(function() {
							uni.hideLoading()
						}, 500)
					})
				} catch (e) {
					//TODO handle the exception
				}
			}
		},
		onLoad() {
			this.id = this.$Route.query.id
			this.requestNewsDetail();
		}
	}
</script>

<style lang="scss">
	.container-page {
		background-color: #f5f5f5;


		.detail-main {
			padding: 20rpx;
			background-color: #fff;
			position: relative;

			.detail-header {
				margin-bottom: 20rpx;
			}

			.title {
				font-size: 36rpx;
				font-weight: 550;
				color: #000;
				margin-bottom: 20rpx;
			}

			.time {
				color: #999;
				margin-bottom: 20rpx;
			}
			.content {
				padding: 30rpx 0;
			}

			.detail-cc {
				line-height: 2;
				color: #666;
				font-size: 16px;

			}
		}
	}
</style>