<template>
	<view class="container container-page">
		<customNav title="账号类型" isBgImage leftColor="#fff"></customNav>
		<view class="padding-box">
			<!-- 内容的改变 -->
			<!-- <view class="content">
				<p>默认最低入金（单位：USD）：100</p>
				<p>最小交易数量：0.01</p>
				<p>手数差值：0.01</p>
				<p>最大订单量：30手</p>
			</view> -->
			<!-- 表格 -->
			<uni-table border emptyText="暂无更多数据">
				<!-- 表头行 -->
				<uni-tr class="back-tr">
					<uni-th width="10" align="center" class="blackText">产品名称</uni-th>
					<uni-th width="10" align="center" class="blackText">手续费</uni-th>
					<uni-th width="40" align="center" class="blackText">单笔最小交易数</uni-th>
					<uni-th width="40" align="center" class="blackText">单笔最大交易数</uni-th>
				</uni-tr>
				<!-- 表格数据行 -->
				<!-- <uni-tr>
					<uni-td align="center">EURUSD欧元/美元</uni-td>
					<uni-td align="center">1.9</uni-td>
					<uni-td align="center">1.4</uni-td>
					<uni-td align="center">30手</uni-td>
				</uni-tr> -->
				<uni-tr v-for="(item, index) in markets" :index="index" :key="index">
					<uni-td align="center">{{item.name}}</uni-td>
					<uni-td align="center">{{item.fee_buy}}</uni-td>
					<uni-td align="center">{{item.buy_min}}</uni-td>
					<uni-td align="center">{{item.buy_max}}</uni-td>
				</uni-tr>

				<!-- 末尾行 -->
				<uni-tr>
					<uni-td colspan="4" align="center">点差标准说明提示：以上浮动点差标准仅供参考，极端市况下个别产品点差或有变动</uni-td>
				</uni-tr>
			</uni-table>
		</view>
	</view>
</template>
<script>
	import uniTable from "@/components/uni-table/uni-table/uni-table.vue";
	import uniTr from "@/components/uni-table/uni-tr/uni-tr.vue";
	import uniTd from "@/components/uni-table/uni-td/uni-td.vue";
	import uniTh from "@/components/uni-table/uni-th/uni-th.vue";
	import customNav from '@/components/custom-nav/index.vue';
	import {
		mapState
	} from "vuex"
	export default {
		data() {
			return {
				// 默认激活样式是第一个
				act: 0,
				list: [{
						id: 0,
						name: '迷你账户'
					},
					{
						id: 1,
						name: '标准账户'
					},
					{
						id: 2,
						name: '高端账户'
					}

				],
				info: {},
				content: ''
			};
		},
		components: {
			uniTable,
			uniTr,
			uniTd,
			uniTh,
			customNav
		},
		computed: {
			...mapState("market", ["markets"]),
		},
		async onShow() {
			// 页面默认显示的是list列表中第一条数据
			this.content = this.list[0]
		},
		methods: {
			changeAct(item) {
				// 激活样式是当前点击的对应下标--list中对应id
				this.act = item.id;
				// 可以根据点击事件改变内容
				this.content = item
			},
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
		z-index: 999;
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

	.padding-box {
		padding: 20rpx 20rpx 0rpx 20rpx;
		background-color: #f2f2f2;
	}

	.nav {
		height: 80rpx;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: $baseFontSize;
		color: #5f5f5f;
	}

	.nav-list {
		display: flex;
		flex: 1;
		height: 80rpx;
		line-height: 80rpx;
		text-align: center;
		background-color: #fff;
		border-right: 1rpx solid #dcdcdc;
	}

	.nav-list .name {
		color: #000;
		width: 100%;
		background-color: #dedede;
	}

	.nav-list .nameB {
		width: 100%;
	}

	/* 内容 */
	.content {
		margin: 20rpx 0;
		background-color: #dedede;
		padding: 20rpx;
	}

	.back-tr {
		background-color: #dedede;
	}

	.blackText {
		color: black;
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
</style>