<template>
	<view class="tabs flex">
		<view class="tab" @tap="$emit('change',index)" :style="{width:100/tabs.length+'%'}"
			:class="index==currentActive?'tab-active':''" v-for="(item,index) in tabs" :key="item.name">{{item.name}}
		</view>
		<view class="tab-select-bg" :style="{width:100/tabs.length+'%',transform:'translateX('+100*currentActive+'%)'}">
		</view>
	</view>
</template>

<script>
	export default {
		name: "MarketTimeTab",
		props: {
			tabs: {
				type: Array,
				required: true
			},
			currentActive: {
				type: Number,
				required: true
			},
		},
		data() {
			return {

			};
		}
	}
</script>

<style lang="scss" scoped>
	.tabs {
		position: relative;
		height: 60rpx;
		background-color: #fff;

		.tab {
			position: relative;
			height: 100%;
			font-size: 24rpx;
			text-align: center;
			line-height: 60rpx;
			z-index: 10;
		}

		.tab-active {
			color: #FE4E34;
			font-weight: bold;
			position: relative;
		}

		.tab-active::after {
			position: absolute;
			content: "";
			height: 2rpx;
			width: 100%;
			left: 0;
			bottom: 0;
			background: #FE4E34;
		}

		.tab-select-bg {
			position: absolute;
			left: 0;
			height: 100%;
			top: 0;
			transition: all .3s ease;
		}
	}
</style>