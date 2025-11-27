<template>
	<view class="container container-page">
		<customNav title="公司介绍" leftColor="#000">
		</customNav>
		<view class="about-content editor-content" v-html="getConfig.about_content"></view>
	</view>
</template>


<script>
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapGetters
	} from "vuex"
	export default {
		components: {
			customNav
		},
		data() {
			return {

			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
		},
		methods: {
			onScroll(res) {
				uni.$emit('onPageScroll', res.detail.scrollTop); //传递参数
			},
		},
	}
</script>
<style>
</style>
<style scoped lang="less">
	.about-content {
		// padding: 20rpx;
	}

	.page-main {
		width: 100%;
		height: auto;
		height: 100vh;

		.main-icon {
			width: 100%;
			height: 100%;
			display: block;
		}

		.main-icon-2 {
			margin-top: -2rpx;
		}
	}
</style>