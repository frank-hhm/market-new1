<template>
	<view>
		<web-view :src="getConfig.kefu_other"></web-view>
	</view>
</template>

<script>
	import {
		mapGetters
	} from "vuex"
	export default {
		data() {
			return {
				kefuUrl: ''
			}
		},
		computed: {
			...mapGetters("app", ["getConfig"]),
		},
		methods: {}
	}
</script>

<style lang="scss">
</style>