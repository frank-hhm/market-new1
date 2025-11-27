<template>
	<view class="detail-price-change"  :class="productDetail.is_open?'':'market-none-open-color'" :style="{color:productDetail.price>=productDetail.old_price?getUpColor:getDownColor}">
		<view class="detail-price-change-item">
			{{changeNumSign}}{{changeNum}}
		</view>
		<view class="detail-price-change-item" :style="{color:color}">
			{{changeValueSign}}{{changeValue}}%
		</view>
	</view>
</template>

<script>
	import {
		mapGetters,
		mapState
	} from "vuex"
	export default {
		name: "DetailPriceChange",
		props: {
			productDetail: {
				type: Object,
				required: true
			}
		},
		data() {
			return {
				changeValue: "0.00",
				changeNum: "0"
			};
		},
		watch: {
			'productDetail.price': {
				immediate: true,
				handler() {
					this.calculateChange();
				}
			},
			'productDetail.open_price': {
				immediate: true,
				handler() {
					this.calculateChange();
				}
			}
		},
		methods: {
			calculateChange() {
				const price = parseFloat(this.productDetail.price);
				const openPrice = parseFloat(this.productDetail.open_price);

				if (isNaN(price) || isNaN(openPrice)) {
					this.changeValue = "0.00";
					return;
				}

				let liuChu = openPrice || price;
				if (liuChu === 0) {
					this.changeValue = "0.00";
					return;
				}

				let _price = price - openPrice;
				let changeZhi = _price / liuChu;

				if (changeZhi < 0) {
					const changeJuedui = Math.ceil(Math.abs(changeZhi) * 10000) / 10000;
					changeZhi = changeJuedui * -1;
				}

				this.changeNum = (_price / 100).toFixed(this.productDetail.decimal || 2);

				this.changeValue = (changeZhi * 100).toFixed(2);
			}
		},
		computed: {
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			color() {
				const val = parseFloat(this.changeValue);
				if (val === 0) return "";
				return val > 0 ? this.getUpColor : this.getDownColor;
			},
			changeValueSign() {
				return parseFloat(this.changeValue) > 0 ? "+" : "";
			},
			changeNumSign() {
				return parseFloat(this.changeNum) > 0 ? "+" : "";
			}
		}
	};
</script>

<style  lang="scss" scoped>
	.detail-price-change{
		display: flex;
		font-size:$baseFontSizeSm;
		color: $baseNotOpen;
		.detail-price-change-item{
			margin-right: 20rpx;
		}
	}
</style>