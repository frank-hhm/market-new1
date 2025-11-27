<template>
	<view class="container container-page">
		<customNav title="用户需知"  leftColor="#000"></customNav>
		<view class="about-content editor-content" v-html="getConfig.about_content_know"></view>
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

			};
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

<style lang="scss">
	.container {
		background-color: #f5f5f5;
	}
</style>