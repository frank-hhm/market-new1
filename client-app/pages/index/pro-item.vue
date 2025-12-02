<template>
	<view class="pro-list ">
		<view class="pro-item" v-for="(item,index) in priceList" :key="index" @tap.shop="toStockDetail(item)">
			<view class="pro-item-top">
				<view class="stock-item-top-left">
					<view class="name">{{item.name}}</view>
					<view class="pro-item-price" :style="{color:item.change_num>=0?getUpColor:getDownColor}">
						<text>{{getMarketPrice(item.id)}}</text>
						<!-- <text class="change">{{item.change_num>=0?"↑":"↓"}}</text> -->
					</view>

				</view>
				<view class="stock-item-top-right">
					<view :prop="item.echartData" :change:prop="echarts.updateEcharts(item,index)"
						:id="'echarts-stock-'+index" class="stock-echarts">
					</view>
				</view>
			</view>
			<view class="pro-item-change">
				<detail-price-change v-if="getMarketById(item.id)" :productDetail="getMarketById(item.id)"></detail-price-change>
			</view>
			<view class="pro-item-hr " v-if="item.kaicang_all">
				<view class="up-hr" :style="{
										width:getHrWidth(item.kaicang_all.die_)
									}"></view>

				<view class="down-hr" :style="{
										width:getHrWidth(item.kaicang_all.zhang_)
									}"></view>
			</view>
			<view class="pro-item-bottom">
				<view class="item">
					<text class="up-label">多</text>
					<text class="up-number"
						v-if="item.kaicang_all && item.kaicang_all.die_">{{item.kaicang_all.die_ || 0.00}}%</text>
					<text class="up-number" v-else>0.00%</text>
				</view>
				<view class="item">
					<text class="down-label">空</text>
					<text class="down-number"
						v-if="item.kaicang_all && item.kaicang_all.zhang_">{{item.kaicang_all.zhang_ || 0.00}}%</text>
					<text class="down-number" v-else>0.00%</text>
				</view>
			</view>
		</view>
	</view>
</template>
<script>
	import {
		mapGetters,
	} from "vuex"
	import detailPriceChange from '@/components/market/detail-price-change.vue';
	export default {
		components: {
			detailPriceChange
		},
		props: {
			priceList: {
				type: Array,
				required: true
			},
		},
		data() {
			return {
				mainData: [{
					k: 0,
				}, {
					k: 1,
				}, {
					k: 2,
				}],
			}
		},
		watch: {
			priceList(newValue, oldValue) {
				this.$nextTick(() => {
					newValue.forEach((item, index) => {
						if (this.mainData[index]) {
							this.mainData[index] = JSON.parse(JSON.stringify(Object.assign(item, this
								.mainData[index])))
						}
					})
				})
			}
		},
		computed: {
			...mapGetters("member", ["getDownColor", "getUpColor"]),
			...mapGetters("market", ["getMarketById", "getMarketPrice"])
		},
		methods: {
			getHrWidth(die_, zhan_, key = 'die_') {
				let number = 50.00;
				if (die_ != 0 && number != 0) {
					if (key == "die_") {
						number = die_
					} else {
						number = zhan_
					}
				}
				return number + "%";
			},
			invokeGetKData() {
				this.$emit('invokeGetKData', this.getOptionData())
			},
			toStockDetail(item) {
				this.$Router.push({
					path: '/pages/market/detail',
					query: {
						id: item.id
					}
				})
			},
			getOptionData(proLine = {}) {
				let base = +new Date(2023, 12, 26);
				let oneDay = 24 * 3600 * 1000;
				let date = [];
				let data = [];
				for (let i = 1; i < 12; i++) {
					var now = new Date((base += oneDay));
					date.push([now.getFullYear(), now.getMonth() + 1, now.getDate()].join('/'));
					data.push(Math.floor(Math.random() * 4) + 4);
				}
				return {
					tooltip: {
						trigger: 'axis',
					},
					xAxis: {
						show: false,
						type: 'category',
						boundaryGap: false,
						data: date
					},
					yAxis: {
						show: false,
						type: 'value',
						boundaryGap: [0, '100%']
					},
					series: [{
						type: 'line',
						symbol: 'none',
						sampling: 'lttb',
						smooth: true,
						itemStyle: {
							color: '#FF4242'
						},
						silent: true,
						areaStyle: {
							color: 'rgba(255, 66, 66,0.5)'
						},
						data: data
					}],
					grid: {
						bottom: 10,
						right: 0,
						left: 0,
						top: 0
					}
				};
			},
		}
	}
</script>

<script module="echarts" lang="renderjs">
	let myCharts = [];
	let myStockChart = [];
	let echartElement = [];

	let echartStockElement = []
	let echartStockIsInitCom = false;
	let echartLength = 3;
	let echartStatus = false;
	export default {
		mounted() {
			if (typeof window.echarts === 'function') {
				this.initEcharts(this)
			} else {
				// 动态引入较大类库避免影响页面展示
				const script = document.createElement('script')
				// view 层的页面运行在 www 根目录，其相对路径相对于 www 计算
				script.src = 'static/echarts.js'
				script.onload = this.initEcharts.bind(this)
				document.head.appendChild(script)
			}
		},
		onReady() {},
		methods: {
			async initEcharts() {
				let count = 0;
				while (!await this.initStockEcharts()) {
					// 等待一段时间再进行下一次尝试
					console.log('等待一段时间再进行下一次尝试:' + count);
					count++;
					await this.delay(1500); // 100 毫秒，可以根据实际情况调整
				}
				// #ifdef H5
				this.invokeGetKData()
				// #endif

				// #ifdef APP-PLUS
				this.$ownerInstance.callMethod('invokeGetKData')
				// #endif
				console.log('Echarts 初始化完成');
				return true;
			},

			async initStockEcharts() {
				if (typeof window.echarts === 'undefined' || echartStockIsInitCom) {
					return false;
				}
				try {
					for (let i = 0; i < echartLength; i++) {
						echartStockElement[i] = document.getElementById('echarts-stock-' + i);
						if (echartStockElement[i]) {
							myStockChart[i] = echarts.init(echartStockElement[i]);
							let _options = {}

							// #ifdef H5
							_options = this.getOptionData()
							// #endif

							// #ifdef APP-PLUS
							let base = +new Date(2023, 12, 26);
							let oneDay = 24 * 3600 * 1000;
							let date = [];
							let data = [];
							for (let i = 1; i < 12; i++) {
								var now = new Date((base += oneDay));
								date.push([now.getFullYear(), now.getMonth() + 1, now.getDate()].join('/'));
								data.push(Math.floor(Math.random() * 4) + 4);
							}
							_options = {
								tooltip: {
									trigger: 'axis',
								},
								xAxis: {
									show: false,
									type: 'category',
									boundaryGap: false,
									data: date
								},
								yAxis: {
									show: false,
									type: 'value',
									boundaryGap: [0, '100%']
								},
								series: [{
									type: 'line',
									symbol: 'none',
									sampling: 'lttb',
									smooth: true,
									itemStyle: {
										color: '#FF4242'
									},
									silent: true,
									areaStyle: {
										color: 'rgba(255, 66, 66,0.5)'
									},
									data: data
								}],
								grid: {
									bottom: 10,
									right: 0,
									left: 0,
									top: 0
								}
							}
							// #endif

							setTimeout(function() {
								myStockChart[i].setOption(_options);
							}, 10);
						}
					}
					return true;
				} catch (e) {
					return false;
				}
			},

			delay(time) {
				// 使用 Promise 包装 setTimeout，实现异步延时
				return new Promise(resolve => setTimeout(resolve, time));
			},
			updateEcharts(newValue, index, ownerInstance, instance) {
				// console.log(newValue)

				// myStockChart[index].setOption(newValue, true);
			},
		},
	}
</script>
<style lang="scss">
	.pro-list {
		display: flex;
		justify-content:flex-start;
		margin-bottom:20rpx;
		margin-left:20rpx;
		margin-right:20rpx;

		.pro-item {
			width: calc(33.33% - 12rpx);
			padding:20rpx 10rpx;
			margin-right: 10rpx;
			background: #F7FAFF;
			border-radius: $baseRadius;
			// border: 1rpx solid #f3f3f3;

			.pro-item-top {
				width: 100%;
				display: flex;
				justify-content: space-between;

				// align-items: center;
				.stock-item-top-left {
					width: calc(100% - 76rpx - 20rpx);
				}

				.name {
					width: 100%;
					height: 32rpx;
					line-height: 32rpx;
					font-size: 24rpx;
					overflow: hidden;
					text-overflow: ellipsis;
					white-space: nowrap;
				}

				.icon {
					color: #999999;
				}


				.stock-item-top-right {
					width: 76rpx;
					margin-top: 10rpx;

					.stock-echarts {
						width: 76rpx;
						height: 32rpx;
					}

					.stock-value {
						text-align: right;
						margin-top: 4rpx;

						.price {
							font-size: 28rpx;
						}
					}

				}

			}


			.pro-item-price {
				// margin-top: 10rpx;
				font-weight: bold;
				display: flex;
				align-items: center;
				line-height: 46rpx;

				.change {
					margin-left: 6rpx;
					font-size: 24rpx;
					line-height: 20rpx;
				}
			}

			.pro-item-change {
				display: flex;
				font-size: 24rpx;

				.percent {
					margin-left: 20rpx;
				}
			}

			.pro-item-hr {
				display: flex;
				margin-top: 10rpx;

				.up-hr {
					height: 12rpx;
					background: $color-pink;
					border-top-left-radius: 4rpx;
					border-bottom-left-radius: 4rpx;
				}

				.down-hr {
					height: 12rpx;
					background: $color-green ;
					border-top-right-radius: 4rpx;
					border-bottom-right-radius: 4rpx;
				}
			}

			.pro-item-bottom {
				margin-top: 10rpx;
				display: flex;
				justify-content: space-between;
				.item{
					display: flex;
					align-items: center;
				}

				.label {
					font-weight: bold;
				}

				.up-label,
				.down-label {
					font-size: 24rpx;
				}

				.up-number,
				.down-number {
					font-size: 20rpx;
					margin-left:4rpx;
				}

				.up-number {
					color: $color-pink;
				}

				.down-number {
					color: $color-green ;
				}
			}
		}
	}
</style>