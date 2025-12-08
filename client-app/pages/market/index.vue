<template>
	<view class="container container-page">
		<customNav isCustomTitle :hideLeft="true">
			<template #left>
				<view class="custom-tab">
					<view class="item " :class="isCollect?'active':''" @click="onSetCollect(true)">自选</view>
					<view class="item " :class="isCollect?'':'active'" @click="onSetCollect(false)">市场</view>
				</view>
			</template>
		</customNav>

		<marketLoading v-if="!marketStatus"></marketLoading>
		<view class="container-main" v-if="marketStatus">
			<view class="sector-tabs" v-if="!isCollect">
				<view v-for="(tab, index) in sector" :key="index" class="sector-tab-item"
					:class="{
						active:sectorIndex === index
					}" @click="onSelectSector(index)">
					{{ tab.sector_name }}
				</view>
			</view>
			<view class="sector-tabs-mask" v-if="!isCollect"></view>
			<swiper-item v-show="isCollect">
				<view class="product-list" scroll-x="true">
					<view v-if="markets.length == 0" class="empty-data">暂无数据</view>
					<scroll-view scroll-y="true" class="product-scroll-view">

						<view @click.stop="toDetail(item.id)" class="product-item" v-for="(item,index) in markets"
							:key="index"
							v-if="inArray(item.id,member.collects) || inArray(item.id,systemConfig.member_default_product || [])">
							<view class="scroll-item-title">
								<view class="name">{{item.name}}</view>
								<!-- <view class="spread" v-if="item.spread && item.spread > 0">{{item.spread}}</view> -->
								<view class="code">{{item.code}}</view>
							</view>
							<view class="scroll-item-center">
								<view class="up-status" :style="{color:item.up_status?getUpColor:getDownColor}">
									<text
										:class="item.is_open?'':'market-none-open-color'">{{getMarketPrice(item.id)}}</text>
								</view>
								<view class="low">{{item.low_price ||'--'}}</view>
							</view>

							<view class="scroll-item-right">
								<itemPriceChange :item="item" isBg></itemPriceChange>
							</view>
						</view>
					</scroll-view>
				</view>
			</swiper-item>

			<swiper class="swiper-tab" v-show="!isCollect" :current="sectorIndex" @change="onSwiperSectorChange">
				<swiper-item v-for="(items, idx) in sector" :key="idx">
					<view class="product-list" scroll-x="true">
						<scroll-view scroll-y="true" class="product-scroll-view">
							<view v-for="(item,index) in markets" :key="item.index" @click.stop="toDetail(item.id)"
								class="product-item" v-if="item.sector_id ===  items.id && item.status.value === 1">
								<view class="scroll-item-title">
									<view class="name">{{item.name}}</view>
									<view class="code">{{item.code}}</view>
								</view>
								<view class="scroll-item-center">
									<view class="up-status" :style="{color:item.up_status?getUpColor:getDownColor}">
										<text
											:class="item.is_open?'':'market-none-open-color'">{{getMarketPrice(item.id)}}</text>
									</view>
									<view class="low">{{item.low_price ||'--'}}</view>
								</view>
								<view class="scroll-item-right">
									<itemPriceChange :item="item" isBg></itemPriceChange>
								</view>
							</view>
						</scroll-view>
					</view>
				</swiper-item>
			</swiper>
		</view>
		<router-link class="login-modal" to="/pages/login/index" v-if="!isLogin">
			<view class="login-modal-content">
				<view>{{getConfig.system_name|| ""}}</view>
				<view class="desc">成功的投资者都在这里</view>
			</view>
			<view class="login-modal-btn">
				登录/开户
			</view>
		</router-link>
	</view>
</template>

<script>
	import marketLoading from '@/components/market/loading.vue';
	import customNav from '@/components/custom-nav/index.vue';
	import itemPriceChange from '@/components/market/item-price-change.vue';
	import {
		sendSocketMessage
	} from "@/websocket/index.js"
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		components: {
			customNav,
			marketLoading,
			itemPriceChange
		},
		data() {
			return {
				isCollect: false,
				sectorIndex: 0
			}
		},

		computed: {
			...mapState("app", ["systemConfig"]),
			...mapState("market", ["markets", "sector", "marketStatus"]),
			...mapState("member", ["member", "isLogin"]),
			...mapGetters("market", ["getMarketPrice"]),
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			...mapGetters("dataArray", ["inArray"]),
			...mapGetters("app", ["getConfig"]),
		},
		methods: {
			convertToPositive(num) {
				let number = num;
				return (number * 100).toFixed(2)
			},
			onSetCollect(collect) {
				this.isCollect = collect
			},
			onSelectSector(index) {
				this.sectorIndex = index;
			},

			onSwiperSectorChange(e) {
				this.sectorIndex = e.detail.current;
			},
			toDetail(id) {
				this.$Router.push({
					path: "/pages/market/detail",
					query: {
						id
					},
				})
			},
			onShow() {
				this.$store.commit('market/setMarketId', 'all')
				sendSocketMessage(JSON.stringify({
					type: 'ping',
					market_subscribe: 'all'
				}))

				this.$u.api.getProductConfigApi().then((res) => {
					this.$store.commit('market/setMarkets', res.data.list || [])
				})
			}
		},
	}
</script>

<style lang="scss">
	.custom-tab {
		display: flex;
		color: #fff;

		.item {
			height: 78rpx;
			line-height: 78rpx;
			margin-right: 40rpx;
			text-align: center;
			font-size: $baseFontSizeDefault;
			color: #000000;
		}

		.active {
			background: #fff;
			font-weight: bold;
			font-size: $baseFontSizeLg;
		}
	}

	.sector-tabs {
		display: flex;
		justify-content:space-around;
		background-color: #fff;
		border-bottom: 1rpx solid #f3f3f3;
		position: fixed;
		width: 100%;
		z-index: 9999;
		height: 80rpx;
		line-height: 80rpx;

		.sector-tab-item {
			color: #666;
			// width: 180rpx;
			// text-align: center;
			&:nth-child(2){
				// width: calc(100% - 320rpx);
			}
		}

		.sector-tab-item.active {
			color: #F4CF4A;
			font-weight: bold;
		}

	}

	.product-list {
		height: 100%;

		.product-scroll-view {
			height: 100%;
			background-color: none;

			.product-item {
				padding: 30rpx;
				display: flex;
				align-items: center;
				justify-content: space-between;
				border-bottom: 1rpx solid #F2F2F2;


				.scroll-item-title{
					width: 120rpx;
					white-space: nowrap;
				}
				.scroll-item-center {
					width: calc(100% - 320rpx);
				}

				.scroll-item-center {
					text-align: center;
				}

				.scroll-item-right {
					width: 140rpx;
					text-align: right;
				}

				.code {
					margin-top: 10rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}

				.spread {
					margin-top: 10rpx;
					font-size: $baseFontSizeSmX;
					color: var(--base-grey);
				}

				.name {
					color: #000000;
				}

				.up-status {
					font-weight: bold;
				}

				.low {
					margin-top: 8rpx;
					font-size: $baseFontSizeSm;
					color: var(--base-grey);
				}
			}
		}
	}

	.sector-tabs-mask {
		width: 100%;
		z-index: -1;
		height: 80rpx;
	}

	.swiper-tab {
		flex: 1;
		position: relative;
		z-index: 1;
		height: calc(100vh - var(--status-bar-height) - 44px - 84rpx - 90rpx);
	}

	.empty-data {
		text-align: center;
		margin-top: 200rpx;
		color: var(--base-grey);
	}

	.text-title {
		margin-top: 20px;
		text-align: center;
	}

	.text-price {
		font-size: 26px;
		color: red;
	}

	.login-modal {
		position: fixed;
		z-index: 999999;
		left: 0;
		bottom: 100rpx;
		width: calc(100% - 40rpx);
		margin: 20rpx;
		border-radius: $baseRadius;
		background: rgb(0, 0, 0, 0.8);
		color: #fff;
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 20rpx;

		.login-modal-btn {
			color: #000000;
			background: #F4CF4A;
			border-radius: $baseRadius;
			padding: 10rpx 20rpx;
		}

		.login-modal-content {
			.desc {
				font-size: $baseFontSizeSm;
			}
		}
	}
</style>