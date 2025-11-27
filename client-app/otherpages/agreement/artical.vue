<template>
	<view class="page">
		<view v-html="html"></view>
	</view>
</template>

<script>
	export default {
		data() {
			return {
				html:""
			};
		},
		async onLoad(options) {
			const id=options.id
			try{
				if(options.title){
					uni.setNavigationBarTitle({
						title:options.title,
						})
				}
			
				const res = await this.$u.api.getAgreementApi({type:options.type})
				this.html=res.data.content || ''
				
			}catch(e){
				//TODO handle the exception
			}
		}
	}
</script>

<style lang="less">
.page{
	padding: 30rpx;
}
</style>
