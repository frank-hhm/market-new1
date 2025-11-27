<template>
	<view class="echarts-wrapper">
		<view class="template-wrapper" v-show="currentActive!=0">
			<view class="indicators flex align-center">
				<view class="indicators-left" @tap="echarts.showPopupEvent">
					<u-icon class="icon" name="setting"></u-icon>
					<!-- <view class="indicators-title">{{mainIndicators[mainIndicatorsIndex]}}</view> -->
					<!-- <view class="kdj-data">
						<text v-if="mainIndicatorsIndex==1">(5,10,15)</text>
						<text v-else-if="mainIndicatorsIndex==2">(5,10,20)</text>
						<text v-else-if="mainIndicatorsIndex==3">(3,6,12,24)</text>
						<text v-else-if="mainIndicatorsIndex==4">(12)</text>
						<text v-else-if="mainIndicatorsIndex==5">(20,2)</text>
					</view> -->
				</view>
				<view class="indicators-right">
					<view class="indicators-item" v-if="mainIndicatorsIndex==1">
						<!-- <view class="indicators-items" @tap="echarts.showPopupEvent">
							<u-icon class="indicators-setting-icon" name="setting"></u-icon>
						</view> -->
						<!-- <text class="kdj-data">(5,10,15)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_MA" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-else-if="mainIndicatorsIndex==2">
						<!-- <text class="kdj-data">(5,10,20)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_EMA" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-else-if="mainIndicatorsIndex==3">
						<!-- <text class="kdj-data">(3,6,12,24)</text> -->
						<view class="indicators-items">
							<view class="icon" :style="{background:c_BBI.color}"></view>
							<text class="label" :style="{color:c_BBI.color}">BBI:</text>
							<text class="value" :style="{color:c_BBI.color}">{{c_BBI.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-else-if="mainIndicatorsIndex==4">
						<!-- <text class="kdj-data">(12)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_MIKE" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-else-if="mainIndicatorsIndex==5">
						<!-- <text class="kdj-data">(20,2)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_boll" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>

				</view>
			</view>
			<view class="echarts" id="echarts" :prop="mainData" @touchmove="echarts.onTouchMove"
				@touchstart="echarts.onTouchStart" :change:prop="echarts.updateEcharts" :price="newPrice"
				:change:price="echarts.updatePriceEcharts" :currentActive="currentActive"
				:change:currentActive="echarts.updateCurrentActive" :isInitKLine="isInitKLine"
				:change:isInitKLine="echarts.updateInitKLine"></view>
			<!--  @click.stop="echarts.onClick" -->
			<view class="indicators tow">
				<view class="indicators-left" @tap="echarts.showPopupEvent">
					<u-icon class="icon" name="setting"></u-icon>
					<!-- <view class="kdj-data">
						<text v-if="assistantIndicatorsIndex==1">(9,3,3)</text>
						<text v-else-if="assistantIndicatorsIndex==2">(26,12,9)</text>
						<text v-else-if="assistantIndicatorsIndex==3">(26)</text>
						<text v-else-if="assistantIndicatorsIndex==4">(14)</text>
						<text v-else-if="assistantIndicatorsIndex==5">(6,12,24)</text>
						<text v-else-if="assistantIndicatorsIndex==6">(9,3)</text>
						<text v-else-if="assistantIndicatorsIndex==7">(14)</text>
					</view> -->
				</view>
				<view class="indicators-right">
					<view class="indicators-item" v-if="assistantIndicatorsIndex==1">
						<!-- <text class="kdj-data">(9,3,3)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_kdj" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==2">
						<!-- <text class="kdj-data">(26,12,9)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_macd" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==3">
						<!-- <text class="kdj-data">(26)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_arbr" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==4">
						<!-- <text class="kdj-data">(14)</text> -->
						<view class="indicators-items">
							<view class="icon" :style="{background:c_atr.color}"></view>
							<text class="label" :style="{color:c_atr.color}">ATR:</text>
							<text class="value" :style="{color:c_atr.color}">{{c_atr.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==5">
						<!-- <text class="kdj-data">(6,12,24)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_bias" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==6">
						<!-- <text class="kdj-data">(9,3)</text> -->
						<view class="indicators-items" v-for="(item,index) in c_kd" :key="item.label">
							<view class="icon" :style="{background:item.color}"></view>
							<text class="label" :style="{color:item.color}">{{item.label}}:</text>
							<text class="value" :style="{color:item.color}">{{item.value}}</text>
						</view>
					</view>
					<view class="indicators-item" v-if="assistantIndicatorsIndex==7">
						<!-- <text class="kdj-data">(14)</text> -->
						<view class="indicators-items">
							<view class="icon" :style="{background:c_atr.color}"></view>
							<text class="label" :style="{color:c_atr.color}">WR:</text>
							<text class="value" :style="{color:c_atr.color}">{{c_atr.value}}</text>
						</view>
					</view>
				</view>
				<!-- <view class="indicators-item" v-if="assistantIndicatorsIndex==8">
					<text class="kdj-data">(20,2)</text>
					<text class="flex mr-10" v-for="(item,index) in c_boll" :key="item.label">
						<text class="label" :style="{color:item.color}">{{item.label}}:</text>
						<text class="value" :style="{color:item.color}">{{item.value}}</text>
					</text>
				</view> -->
			</view>
			<view class="assistant-echarts" id="assistantEcharts" :prop="assistantData"
				:change:prop="echarts.updateEcharts">
			</view>

			<u-popup v-model="show" mode="center" width="600" border-radius="25" z-index="10000">
				<view class="popup-container">
					<view class="title">主图指标</view>
					<view class="flex flex-wrap mt-20">
						<text class="main-item" v-for="(item,index) in mainIndicators"
							:class="mainIndicatorsIndex==index?'active-item':''" :key="index"
							@tap="mainSelect(index)">{{item}}</text>
					</view>
					<view class="title mt-20">
						副图指标
					</view>
					<view class="flex flex-wrap mt-20">
						<text class="assistant-item" :class="assistantIndicatorsIndex==index?'active-item':''"
							v-for="(item,index) in assistantIndicators" :key="index"
							@tap="assistantSelect(index)">{{item}}</text>
					</view>
				</view>
			</u-popup>
		</view>
		<view class="echarts-line" id="echartsLine" v-show="currentActive==0" :prop="lineData"
			:currentActive="currentActive" :change:currentActive="echarts.updateCurrentActive"
			:change:prop="echarts.updateEcharts" @touchmove.stop="echarts.onTouchMove"
			@touchstart.stop="echarts.onTouchStart">
		</view>

	</view>

</template>

<script>
	import {
		mapState,
		mapMutations,
		mapGetters
	} from "vuex"
	import {
		upColor,
		downColor,
		MAOptions,
		MA5Color,
		MA10Color,
		MA15Color,
		MA30Color,
		MA45Color,
		MA60Color,
		kColor,
		dColor,
		jColor,
		MIKEOptions
	} from "@/common/echartsOptions/constant.js"
	import candlestickOption from "@/common/echartsOptions/candlestickOption.js"
	import barOpton from '@/common/echartsOptions/barOptions.js'
	import lineOption from "@/common/echartsOptions/lineOption.js"
	import kdjOption from "@/common/echartsOptions/kdjOption.js"
	import kdOption from "@/common/echartsOptions/kdOption.js"
	import bollOption from "@/common/echartsOptions/bollOption.js"
	import macdOption from "@/common/echartsOptions/macdOption.js"
	import arbrOption from "@/common/echartsOptions/arbrOption.js"
	import atrOption from "../../common/echartsOptions/atrOption.js"
	import calc_KDJ from "@/common/echartsOptions/kdj.js"
	import calc_MACD from "@/common/echartsOptions/macd.js"
	import calc_BBI from "@/common/echartsOptions/bbi.js"
	import calculateMA from "@/common/echartsOptions/ma.js"
	import calculateEMA from "@/common/echartsOptions/ema.js"
	import calc_MIKE from "@/common/echartsOptions/mike.js"
	import calc_ARBR from "@/common/echartsOptions/arbr.js"
	import calc_atr from "@/common/echartsOptions/atr.js"
	import calc_BIAS from "@/common/echartsOptions/bias.js"
	import calc_WR from "@/common/echartsOptions/wr.js"
	import calc_BOLL from "@/common/echartsOptions/boll.js"

	import {
		cloneDeep
	} from "lodash"

	export default {
		props: {
			chartData: {
				type: Object,
				required: true
			},
			currentActive: {
				type: Number,
				required: true
			},
			newPrice: {
				type: Number,
				required: true
			},
			decimal: {
				type: Number,
				required: true
			},

		},
		computed: {
			...mapState("echarts", ["mainIndicatorsIndex", "mainIndicators", "assistantIndicatorsIndex",
				"assistantIndicators"
			]),
			...mapGetters("member", ["getUpColor", "getDownColor"]),
			c_MIKE() {
				if (this.mainData.series === undefined || !this.mainData.series[1]) {
					return [{
						label: "WR",
						value: "-",
						color: MA5Color
					}, {
						label: "MR",
						value: "-",
						color: MA10Color
					}, {
						label: "SR",
						value: "-",
						color: MA15Color
					}, {
						label: "WS",
						value: "-",
						color: MA30Color
					}, {
						label: "MS",
						value: "-",
						color: MA45Color
					}, {
						label: "SS",
						value: "-",
						color: MA60Color
					}]
				} else {
					let WR = parseFloat(this.mainData.series[1].data[this.endValue])
					let MR = parseFloat(this.mainData.series[2].data[this.endValue])
					let SR = parseFloat(this.mainData.series[3].data[this.endValue])
					let WS = parseFloat(this.mainData.series[1].data[this.endValue])
					let MS = parseFloat(this.mainData.series[2].data[this.endValue])
					let SS = parseFloat(this.mainData.series[3].data[this.endValue])
					return [{
						label: "WR",
						value: WR ? WR.toFixed(this.decimal) : "-",
						color: MA5Color
					}, {
						label: "MR",
						value: MR ? MR.toFixed(this.decimal) : "-",
						color: MA10Color
					}, {
						label: "SR",
						value: SR ? SR.toFixed(this.decimal) : "-",
						color: MA15Color
					}, {
						label: "WS",
						value: WS ? WS.toFixed(this.decimal) : "-",
						color: MA5Color
					}, {
						label: "MS",
						value: MS ? MS.toFixed(this.decimal) : "-",
						color: MA10Color
					}, {
						label: "SS",
						value: SS ? SS.toFixed(this.decimal) : "-",
						color: MA15Color
					}]
				}
			},
			c_BBI() {
				if (this.mainData.series === undefined || !this.mainData.series[1]) {
					return {
						value: "-",
						color: MA5Color
					}
				} else {
					let BBI = parseFloat(this.mainData.series[1].data[this.endValue])
					return {
						value: BBI ? BBI.toFixed(this.decimal) : "-",
						color: MA5Color
					}
				}
			},
			c_atr() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0]) {
					return {
						value: "-",
						color: MA5Color
					}
				} else {
					let ATR = parseFloat(this.assistantData.series[0].data[this.endValue])
					return {
						value: ATR ? ATR.toFixed(this.decimal) : "-",
						color: MA5Color
					}
				}
			},
			c_macd() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0].data.length) {
					return [{
						label: "MACD",
						value: "-",
						color: kColor
					}, {
						label: "DEA",
						value: "-",
						color: dColor
					}, {
						label: "DIF",
						value: "-",
						color: jColor
					}]
				} else {
					let MACD = parseFloat(this.assistantData.series[0].data[this.endValue][1])
					let DEA = parseFloat(this.assistantData.series[2].data[this.endValue])
					let DIF = parseFloat(this.assistantData.series[1].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "MACD",
						value: MACD ? MACD.toFixed(this.decimal) : "-",
						color: kColor
					}, {
						label: "DEA",
						value: DEA ? DEA.toFixed(this.decimal) : "-",
						color: dColor
					}, {
						label: "DIF",
						value: DIF ? DIF.toFixed(this.decimal) : "-",
						color: jColor
					}]
				}
			},
			c_boll() {
				if (this.mainData.series === undefined || !this.mainData.series[0].data.length) {
					return [{
						label: "MID",
						value: "-",
						color: dColor
					}, {
						label: "UP",
						value: "-",
						color: jColor
					}, {
						label: "LOW",
						value: "-",
						color: downColor
					}]
				} else {
					let kData = parseFloat(this.mainData.series[1].data[this.endValue])
					let dData = parseFloat(this.mainData.series[2].data[this.endValue])
					let jData = parseFloat(this.mainData.series[3].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "MID",
						value: kData ? kData.toFixed(this.decimal) : "-",
						color: dColor
					}, {
						label: "UP",
						value: dData ? dData.toFixed(this.decimal) : "-",
						color: jColor
					}, {
						label: "LOW",
						value: jData ? jData.toFixed(this.decimal) : "-",
						color: downColor
					}]
				}
			},
			c_kdj() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0].data.length) {
					return [{
						label: "K",
						value: "-",
						color: kColor
					}, {
						label: "D",
						value: "-",
						color: dColor
					}, {
						label: "J",
						value: "-",
						color: jColor
					}]
				} else {
					let kData = parseFloat(this.assistantData.series[0].data[this.endValue])
					let dData = parseFloat(this.assistantData.series[1].data[this.endValue])
					let jData = parseFloat(this.assistantData.series[2].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "K",
						value: kData ? kData.toFixed(this.decimal) : "-",
						color: kColor
					}, {
						label: "D",
						value: dData ? dData.toFixed(this.decimal) : "-",
						color: dColor
					}, {
						label: "J",
						value: jData ? jData.toFixed(this.decimal) : "-",
						color: jColor
					}]
				}
			},
			c_kd() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0].data.length) {
					return [{
						label: "K",
						value: "-",
						color: kColor
					}, {
						label: "D",
						value: "-",
						color: dColor
					}]
				} else {
					let kData = parseFloat(this.assistantData.series[0].data[this.endValue])
					let dData = parseFloat(this.assistantData.series[1].data[this.endValue])
					return [{
						label: "K",
						value: kData ? kData.toFixed(this.decimal) : "-",
						color: kColor
					}, {
						label: "D",
						value: dData ? dData.toFixed(this.decimal) : "-",
						color: dColor
					}]
				}
			},
			c_bias() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0].data.length) {
					return [{
						label: "BIAS1",
						value: "-",
						color: kColor
					}, {
						label: "BIAS2",
						value: "-",
						color: dColor
					}, {
						label: "BIAS3",
						value: "-",
						color: jColor
					}]
				} else {
					let BIAS1 = parseFloat(this.assistantData.series[0].data[this.endValue])
					let BIAS2 = parseFloat(this.assistantData.series[1].data[this.endValue])
					let BIAS3 = parseFloat(this.assistantData.series[2].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "BIAS1",
						value: BIAS1 ? BIAS1.toFixed(this.decimal) : "-",
						color: kColor
					}, {
						label: "BIAS2",
						value: BIAS2 ? BIAS2.toFixed(this.decimal) : "-",
						color: dColor
					}, {
						label: "BIAS3",
						value: BIAS3 ? BIAS3.toFixed(this.decimal) : "-",
						color: jColor
					}]
				}
			},
			c_arbr() {
				if (this.assistantData.series === undefined || !this.assistantData.series[0].data.length) {
					return [{
						label: "AR",
						value: "-",
						color: kColor
					}, {
						label: "BR",
						value: "-",
						color: dColor
					}]
				} else {
					let AR = parseFloat(this.assistantData.series[0].data[this.endValue])
					let BR = parseFloat(this.assistantData.series[1].data[this.endValue])
					return [{
						label: "AR",
						value: AR ? AR.toFixed(this.decimal) : "-",
						color: kColor
					}, {
						label: "BR",
						value: BR ? BR.toFixed(this.decimal) : "-",
						color: dColor
					}]
				}
			},
			c_MA() {
				if (this.mainData.series === undefined || !this.mainData.series[1]) {
					return [{
						label: "MA5",
						value: "-",
						color: MA5Color
					}, {
						label: "MA10",
						value: "-",
						color: MA10Color
					}, {
						label: "MA15",
						value: "-",
						color: MA15Color
					}]
				} else {
					let MA5 = parseFloat(this.mainData.series[1].data[this.endValue])
					let MA10 = parseFloat(this.mainData.series[2].data[this.endValue])
					let MA15 = parseFloat(this.mainData.series[3].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "MA5",
						value: MA5 ? MA5.toFixed(this.decimal) : "-",
						color: MA5Color
					}, {
						label: "MA10",
						value: MA10 ? MA10.toFixed(this.decimal) : "-",
						color: MA10Color
					}, {
						label: "MA15",
						value: MA15 ? MA15.toFixed(this.decimal) : "-",
						color: MA15Color
					}]
				}
			},
			c_EMA() {
				if (this.mainData.series === undefined || !this.mainData.series[1]) {
					return [{
						label: "EMA5",
						value: "-",
						color: MA5Color
					}, {
						label: "EMA10",
						value: "-",
						color: MA10Color
					}, {
						label: "EMA20",
						value: "-",
						color: MA15Color
					}]
				} else {
					let EMA5 = parseFloat(this.mainData.series[1].data[this.endValue])
					let EMA10 = parseFloat(this.mainData.series[2].data[this.endValue])
					let EMA20 = parseFloat(this.mainData.series[3].data[this.endValue])
					// let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
					return [{
						label: "EMA5",
						value: EMA5 ? EMA5.toFixed(this.decimal) : "-",
						color: MA5Color
					}, {
						label: "EMA10",
						value: EMA10 ? EMA10.toFixed(this.decimal) : "-",
						color: MA10Color
					}, {
						label: "EMA20",
						value: EMA20 ? EMA20.toFixed(this.decimal) : "-",
						color: MA15Color
					}]
				}
			}
		},
		data() {
			return {
				mainData: {},
				assistantData: barOpton,
				lineData: lineOption,
				show: false,
				endValue: 0,
				timer: null,
				isInitKLine: 0
			}
		},
		watch: {
			chartData(newValue, oldValue) {
				this.calc_options(newValue)
			},
			mainIndicatorsIndex(newValue, oldValue) {
				this.calc_options(this.chartData)
			},
			assistantIndicatorsIndex(newValue, oldValue) {
				this.calc_options(this.chartData)
			},
			newPrice(newValue, oldValue) {
				this.calc_new_price(newValue)
			}
		},
		created() {
			let t = this
			t.mainData = JSON.parse(JSON.stringify(candlestickOption(t.decimal)));
			t.timer = setInterval(() => {
				const now = new Date(); // 获取当前时间
				const seconds = now.getSeconds(); // 获取当前秒数
				const minutes = now.getMinutes();
				const hours = now.getHours();
				if (seconds === 0) {
					t.isInitKLine++;
				}
			}, 1000)
		},
		beforeDestroy() {
			clearInterval(this.timer)
			this.timer = null;
		},
		methods: {
			...mapMutations("echarts", ["modifyMainIndicatorsIndex", "modifyAssistantIndicatorsIndex"]),
			showPopup() {
				this.show = true
			},
			mainSelect(index) {
				this.modifyMainIndicatorsIndex(index)
				this.show = false
			},
			assistantSelect(index) {
				this.modifyAssistantIndicatorsIndex(index)
				this.show = false
			},
			updateEndValue(newValue) {
				this.endValue = newValue
			},
			updateDataZoom({
				newStart,
				newEnd
			}) {
				// let defaultOption = JSON.parse(JSON.stringify(candlestickOption(this.decimal)));
				// this.mainData.dataZoom[0].startValue = defaultOption.dataZoom[0].startValue
				// this.mainData.dataZoom[0].endValue = defaultOption.dataZoom[0].endValue
				this.assistantData.dataZoom[0].startValue = newStart
				this.assistantData.dataZoom[0].endValue = newEnd
				this.lineData.dataZoom[0].startValue = newStart
				this.lineData.dataZoom[0].endValue = newEnd
			},
			setMa(value) {
				this.mainData.series.push(...MAOptions)
				//[开盘值, 收盘值, 最低值, 最高值]
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.mainData.series[1].data = calculateMA(5, data, this.decimal)
				this.mainData.series[2].data = calculateMA(10, data, this.decimal)
				this.mainData.series[3].data = calculateMA(15, data, this.decimal)
				// this.mainData.series[4].data = this.calculateMA(30, data,this.decimal)

				//顶部均线索引
				this.endValue = data.length - 1
			},
			setEMA(value) {
				this.mainData.series.push(...MAOptions)

				//[开盘值, 收盘值, 最低值, 最高值]
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.mainData.series[1].data = calculateEMA(data, 5, this.decimal)
				this.mainData.series[2].data = calculateEMA(data, 10, this.decimal)
				this.mainData.series[3].data = calculateEMA(data, 20, this.decimal)

				this.mainData.series[1].name = "EMA5"
				this.mainData.series[2].name = "EMA10"
				this.mainData.series[3].name = "EMA20"
				// this.mainData.series[4].data = this.calculateMA(30, data)

				//顶部均线索引
				this.endValue = data.length - 1
			},
			setBBI(value) {
				this.mainData.series.push(MAOptions[0])
				//[开盘值, 收盘值, 最低值, 最高值]
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.mainData.series[1].data = calc_BBI(data, this.decimal)
				this.mainData.series[1].name = "BBI"

				//顶部均线索引
				this.endValue = data.length - 1
			},
			setMike(value) {
				this.mainData.series.push(...MIKEOptions)
				//[开盘值, 收盘值, 最低值, 最高值]
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				let mikeList = calc_MIKE(data, 12, this.decimal)
				this.mainData.series[1].data = mikeList[0]
				this.mainData.series[2].data = mikeList[1]
				this.mainData.series[3].data = mikeList[2]
				this.mainData.series[4].data = mikeList[3]
				this.mainData.series[5].data = mikeList[4]
				this.mainData.series[6].data = mikeList[5]

				this.mainData.series[1].name = "WR"
				this.mainData.series[2].name = "MR"
				this.mainData.series[3].name = "SR"
				this.mainData.series[4].name = "WS"
				this.mainData.series[5].name = "MS"
				this.mainData.series[6].name = "SS"
				//顶部均线索引
				this.endValue = data.length - 1
			},
			resetSeries() {
				this.mainData.series = [this.mainData.series[0]]
			},
			//设置K线图
			setCandlestickOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2], item[5], item[0]])
				this.mainData = JSON.parse(JSON.stringify(cloneDeep(candlestickOption(this.decimal))));
				// this.mainData = cloneDeep(candlestickOption(this.decimal))
				if (!this.mainData.dataZoom[0].startValue) {
					this.mainData.dataZoom[0].startValue = data.length - 30
					this.mainData.dataZoom[0].endValue = data.length + 1
					this.mainData.dataZoom[1].startValue = data.length - 30
					this.mainData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.mainData.dataZoom
					this.mainData.dataZoom = dataZoom
				}
				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					// return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
					return `${month}-${day} ${hour}:${minus}`;
				});

				this.mainData.xAxis[0].data = date
				this.mainData.xAxis[0].max = date.length + 2
				this.mainData.series[0].data = data
				if (data[data.length - 1][1]) {
					this.mainData.series[0].markLine.data = [{
						yAxis: this.newPrice ? this.newPrice : data[data.length - 1][1]
					}]
				}
			},
			//设置量
			setBarOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])

				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(barOpton)));
				// this.assistantData = cloneDeep(barOpton)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
					// return `${hour}:${minus}`;
				});
				let number = value.items.map((item, index) => [
					index,
					item[5],
					item[4] < item[1] ? 1 : item[4] == item[1] ? 0 : -1,
				])
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				this.assistantData.series[0].data = number

				//设置滚动条
				this.assistantData.dataZoom[0].startValue = data.length - 30
				this.assistantData.dataZoom[0].endValue = data.length

				this.assistantData.dataZoom[1].startValue = data.length - 30
				this.assistantData.dataZoom[1].endValue = data.length
			},
			//设置分时
			setLineOption(value) {
				this.lineData = JSON.parse(JSON.stringify(cloneDeep(lineOption)));
				// this.lineData = cloneDeep(lineOption)
				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					// return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
					return `${hour}:${minus}`;
				});
				let data = value.items.map((item) => item[1])
				this.lineData.xAxis[0].data = date
				this.lineData.xAxis[0].max = date.length + 2
				this.lineData.series[0].data = data
				if (data[data.length - 1]) {
					this.lineData.series[0].markLine.data = [{
						yAxis: this.newPrice ? this.newPrice : data[data.length - 1][1]
					}]
				}

				//设置滚动条  2024-11-1 frank
				// this.lineData.dataZoom[0].startValue = data.length - 30
				// this.lineData.dataZoom[0].endValue = data.length + 2

				// this.lineData.dataZoom[1].startValue = data.length - 30
				// this.lineData.dataZoom[1].endValue = data.length
			},
			//设置KDJ
			setKDJOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(kdjOption)));
				// this.assistantData = cloneDeep(kdjOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}
				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					// return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
					return `${month}-${day} ${hour}:${minus}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				let kjdList = calc_KDJ(value)
				this.assistantData.series[0].data = kjdList[0]
				this.assistantData.series[1].data = kjdList[1]
				this.assistantData.series[2].data = kjdList[2]


				//设置滚动条
				this.assistantData.dataZoom[0].startValue = data.length - 30
				this.assistantData.dataZoom[0].endValue = data.length

				this.assistantData.dataZoom[1].startValue = data.length - 30
				this.assistantData.dataZoom[1].endValue = data.length

				//kdj 索引
				this.endValue = data.length - 1
			},
			setKDOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(kdOption)));
				if (!this.assistantData.dataZoom[0].startValue) {
					// this.assistantData = cloneDeep(kdOption)
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					// this.assistantData = cloneDeep(kdOption)
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				let kjdList = calc_KDJ(value)
				this.assistantData.series[0].data = kjdList[0]
				this.assistantData.series[1].data = kjdList[1]


				//设置滚动条
				this.assistantData.dataZoom[0].startValue = data.length - 30
				this.assistantData.dataZoom[0].endValue = data.length

				this.assistantData.dataZoom[1].startValue = data.length - 30
				this.assistantData.dataZoom[1].endValue = data.length

				//kdj 索引
				this.endValue = data.length - 1
			},
			setMACDOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(macdOption)));
				// this.assistantData = cloneDeep(macdOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					// this.assistantData = cloneDeep(macdOption)
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				let macd = calc_MACD(data)
				this.assistantData.series[0].data = macd[2].map((item, index) => ([index, item, item >= 0 ? 1 : -1]))
				this.assistantData.series[1].data = macd[0]
				this.assistantData.series[2].data = macd[1]

				//kdj 索引
				this.endValue = data.length - 1
			},
			setBoll(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.mainData = JSON.parse(JSON.stringify(cloneDeep(bollOption)));
				// this.mainData = cloneDeep(bollOption)
				if (!this.mainData.dataZoom[0].startValue) {
					this.mainData.dataZoom[0].startValue = data.length - 30
					this.mainData.dataZoom[0].endValue = data.length

					this.mainData.dataZoom[1].startValue = data.length - 30
					this.mainData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.mainData.dataZoom
					this.mainData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.mainData.xAxis[0].data = date
				this.mainData.xAxis[0].max = date.length + 2
				this.mainData.series[0].data = data
				if (data[data.length - 1][1]) {
					this.mainData.series[0].markLine.data = [{
						yAxis: this.newPrice ? this.newPrice : data[data.length - 1][1]
					}]
				}
				const [ma20, upList, downList] = calc_BOLL(data, 20)
				// this.mainData.series[0].data = value.items.map((item, index) => [index, item[1], item[4], item[3],
				// 	item[2]
				// ])
				this.mainData.series[1].data = ma20
				this.mainData.series[2].data = upList
				this.mainData.series[3].data = downList

				//kdj 索引
				this.endValue = data.length - 1
			},
			setARBROption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(arbrOption)));
				// this.assistantData = cloneDeep(arbrOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				let arbrList = calc_ARBR(data, 26)
				this.assistantData.series[0].data = arbrList[0]
				this.assistantData.series[1].data = arbrList[1]


				//设置滚动条
				this.assistantData.dataZoom[0].startValue = data.length - 30
				this.assistantData.dataZoom[0].endValue = data.length

				this.assistantData.dataZoom[1].startValue = data.length - 30
				this.assistantData.dataZoom[1].endValue = data.length

				//kdj 索引
				this.endValue = data.length - 1
			},
			setATROption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(arbrOption)));
				// this.assistantData = cloneDeep(arbrOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}
				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				//[开盘值, 收盘值, 最低值, 最高值]
				this.assistantData.series[0].data = calc_atr(data, 14)

				//设置滚动条
				this.assistantData.dataZoom[0].startValue = data.length - 30
				this.assistantData.dataZoom[0].endValue = data.length

				this.assistantData.dataZoom[1].startValue = data.length - 30
				this.assistantData.dataZoom[1].endValue = data.length

				this.endValue = data.length - 1
			},
			setWROption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(atrOption)));
				// this.assistantData = cloneDeep(atrOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				this.assistantData.series[0].data = calc_WR(data, 14)

				//设置滚动条
				// this.assistantData.dataZoom[0].startValue = data.length - 30
				// this.assistantData.dataZoom[0].endValue = data.length

				// this.assistantData.dataZoom[1].startValue = data.length - 30
				// this.assistantData.dataZoom[1].endValue = data.length

				this.endValue = data.length - 1
			},
			setBIASOption(value) {
				let data = value.items.map((item) => [item[1], item[4], item[3], item[2]])
				this.assistantData = JSON.parse(JSON.stringify(cloneDeep(kdjOption)));
				// this.assistantData = cloneDeep(kdjOption)
				if (!this.assistantData.dataZoom[0].startValue) {
					this.assistantData.dataZoom[0].startValue = data.length - 30
					this.assistantData.dataZoom[0].endValue = data.length

					this.assistantData.dataZoom[1].startValue = data.length - 30
					this.assistantData.dataZoom[1].endValue = data.length
				} else {
					const dataZoom = this.assistantData.dataZoom
					this.assistantData.dataZoom = dataZoom
				}

				let date = value.items.map((item) => {
					const date = new Date(item[0] * 1000);
					const year = date.getFullYear();
					const month = date.getMonth() + 1;
					const day = date.getDate();
					const hour = date.getHours();
					const minus = date.getMinutes();
					const seconds = date.getSeconds();
					return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
				});
				this.assistantData.xAxis[0].data = date
				this.assistantData.xAxis[0].max = date.length + 2
				let biasList_6 = calc_BIAS(data, 6)
				let biasList_12 = calc_BIAS(data, 12)
				let biasList_24 = calc_BIAS(data, 24)

				this.assistantData.series[0].data = biasList_6
				this.assistantData.series[1].data = biasList_12
				this.assistantData.series[2].data = biasList_24


				//设置滚动条
				// this.assistantData.dataZoom[0].startValue = data.length - 30
				// this.assistantData.dataZoom[0].endValue = data.length

				// this.assistantData.dataZoom[1].startValue = data.length - 30
				// this.assistantData.dataZoom[1].endValue = data.length

				//kdj 索引
				this.endValue = data.length - 1

			},
			invokeCalc() {
				if (this.chartData.items) {
					this.calc_options(this.chartData)
				}
			},
			invokeGetKData() {
				this.$emit('invokeGetKData')
			},
			calc_options(value) {
				// 如果是分时
				if (this.currentActive == 0) {
					this.setLineOption(value)
				} else {
					this.setCandlestickOption(value)
					switch (this.mainIndicatorsIndex) {
						case 0:
							this.resetSeries()
							break;
							//均线MA
						case 1:
							this.setMa(value)
							break
							//EMA
						case 2:
							this.setEMA(value)
							break
							//BBI

						case 3:
							this.setBBI(value)
							break
						case 4:
							this.setMike(value)
							break
						case 5:
							this.setBoll(value)
							break
						default:
							break
					}
					switch (this.assistantIndicatorsIndex) {
						//成交量
						case 0:
							this.setBarOption(value)
							break
							//kdj
						case 1:
							this.setKDJOption(value)
							break
							//MACD
						case 2:
							this.setMACDOption(value)
							break
							//arbr
						case 3:
							this.setARBROption(value)
							break
							//ATR
						case 4:
							this.setATROption(value)
							break
						case 5:
							this.setBIASOption(value)
							break
							//kd
						case 6:
							this.setKDOption(value)
							break
							//wr
						case 7:
							this.setWROption(value)
							break
							// boll

						default:
							break
					}
				}
			},
			calc_new_price(value) {
				if (this.currentActive == 0) {
					this.lineData.series[0].markLine.data = [{
						yAxis: this.newPrice
					}]
					this.lineData.series[0].data[this.lineData.series[0].data.length - 1] = this.newPrice
				} else {
					// this.mainData.series[0].markLine.data = [{
					// 	yAxis: this.newPrice
					// }]
					// if (this.mainData.series[0].data[this.mainData.series[0].data.length - 1]) {
					// 	this.mainData.series[0].data[this.mainData.series[0].data.length - 1][1] = this.newPrice
					// }
				}
				// console.log(this.newPrice)
			}
		},
		mounted() {
			//设置涨跌颜色
			barOpton.visualMap.pieces = [{
				value: 1,
				color: this.getDownColor
			}, {
				value: -1,
				color: this.getUpColor,
			}]
			candlestickOption(this.decimal).series[0].itemStyle.normal = {
				//阳线 图形的颜色。
				color: this.getUpColor,
				//阴线 图形的颜色。
				color0: this.getDownColor,
				//阳线 图形的描边颜色。
				borderColor: this.getUpColor,
				//阴线 图形的描边颜色。
				borderColor0: this.getDownColor,

			}
			macdOption.visualMap.pieces = [{
				value: 1,
				color: this.getUpColor,
			}, {
				value: -1,
				color: this.getDownColor
			}]
		}

	}
</script>
<script module="echarts" lang="renderjs">
	let mainEcharts
	let mainElement
	let assistantElement
	let assistantEcharts
	let lineElement
	let lineEcharts
	let startX
	let startY
	let currentActive = 5
	let isDataZoom = false;
	let isPush = false;
	let marketPrice = 0
	export default {
		mounted() {
			if (typeof window.echarts === 'function') {
				this.initEcharts()
			} else {
				// 动态引入较大类库避免影响页面展示
				const script = document.createElement('script')
				// view 层的页面运行在 www 根目录，其相对路径相对于 www 计算
				script.src = 'static/echarts.js'
				script.onload = this.initEcharts.bind(this)
				document.head.appendChild(script)
			}
		},
		methods: {
			initEcharts() {
				try {
					mainElement = document.getElementById('echarts')
					mainEcharts = echarts.init(mainElement)

					assistantElement = document.getElementById('assistantEcharts')
					assistantEcharts = echarts.init(assistantElement)

					echarts.connect([mainEcharts, assistantEcharts]);
					mainEcharts.on('dataZoom', function(params) {
						this.calcEndValue()
					}, this)
					mainEcharts.on('click', (params) => {
						mainEcharts.dispatchAction({
							type: 'showTip',
							seriesIndex: 0,
							dataIndex: params.dataIndex,
						})
					});
					this.initKlineData();
					window.addEventListener('resize', () => {
						mainEcharts.resize()
						assistantEcharts.resize()
					})
				} catch (e) {
					console.log("initEcharts:", e)
				}
			},
			initKlineData() {
				// #ifdef H5
				this.invokeGetKData()
				// #endif

				// #ifdef APP-PLUS
				this.$ownerInstance.callMethod('invokeGetKData')
				// #endif
			},
			showPopupEvent(event, ownerInstance) {
				mainEcharts.dispatchAction({
					type: 'hideTip'
				})
				ownerInstance.callMethod('showPopup')
			},
			onClick(event, ownerInstance) {
				// 调用 service 层的方法 
				const touche = event.changedTouches[0]
				const x = touche.pageX;
				const y = touche.pageX - event.target.offsetTop;
				var xIndex = mainEcharts.convertFromPixel({
					seriesIndex: 0
				}, [x, y])[0];
				mainEcharts.dispatchAction({
					type: 'showTip',
					seriesIndex: 0,
					dataIndex: xIndex,
				})

			},
			updateEcharts(newValue, oldValue, ownerInstance, instance) {
				// 监听 service 层数据变更
				switch (instance.$el) {
					case mainElement:
						if (newValue.tooltip) {

							newValue.tooltip.formatter = function(seriesData) {
								let label = "<div>"
								seriesData.forEach(item => {
									if (item.seriesType === "candlestick") {
										//[开盘值, 收盘值, 最低值, 最高值]
										label +=
											`<span>开盘</span>: ${item.value[1]}</br><span>收盘</span>: ${item.value[2]}</br><span>最低</span>: ${item.value[4]}</br><span>最高</span>: ${item.value[3]}</br><span>成交量</span>: ${item.value[5]}</br>`
									} else {
										label += `${item.seriesName}: ${item.value} </br>`
									}
								})

								label += '</div>'
								return label
							}
						}
						if (mainEcharts !== undefined) {
							const option = mainEcharts.getOption();
							if (isDataZoom === false && option !== undefined) {
								delete newValue.dataZoom
							}
						}
						mainEcharts.setOption(newValue, false)
						break
					case assistantElement:
						assistantEcharts.setOption(newValue, false)
						break
					case lineElement:
						lineEcharts.setOption(newValue, false)
						break
					default:
						break
				}
			},
			updateTimeData(newValue = 0) {
				let t = this;
				if (newValue === 0) {
					return false;
				}
				// 获取当前图表的配置
				if (mainEcharts !== undefined) {
					let seriesData = [];
					let dataLeng = 0;
					const option = mainEcharts.getOption();
					// 确保数据存在
					if (option !== undefined && option.series && option.series[0] && option.series[0].data) {
						// 获取第一个系列的数据数组
						seriesData = option.series[0].data;
						dataLeng = seriesData.length
						const lastIndex = seriesData.length - 1;
						seriesData[lastIndex][1] = newValue;
						const now = new Date(); // 获取当前时间
						const seconds = now.getSeconds(); // 获取当前秒数
						const minutes = now.getMinutes();
						const hours = now.getHours();
						const timestampInSeconds = Math.floor(Date.now() / 1000);
						if (currentActive == 5) {
							// console.log(Number(checkKtime) > Number(seriesData[lastIndex][5]) && Number(seriesData[lastIndex]
							// 		[5]) + 60 > Number(checkKtime))
							// console.log(timestampInSeconds, isPush,seconds,"-----------------5")
							if (seconds === 0 && isPush === false) {
								isPush = true;
								seriesData.push([newValue,
									newValue,
									newValue,
									newValue,
									0
								])
								setTimeout(function() {
									isDataZoom = false
								}, 500);
							} else {
								if (newValue < seriesData[lastIndex][3]) {
									seriesData[lastIndex][3] = newValue
								}
								if (newValue > seriesData[lastIndex][2]) {
									seriesData[lastIndex][2] = newValue
								}
							}
							if (seconds > 0) {
								isPush = false;
							}
						} else if (
							(minutes % 5 === 0 && currentActive === 6 && seconds === 0 && isPush === false) ||
							(minutes % 15 === 0 && currentActive === 1 && seconds === 0 && isPush === false) ||
							(minutes % 30 === 0 && currentActive === 2 && seconds === 0 && isPush === false) ||
							(currentActive === 3 && minutes === 0 && seconds === 0 && isPush === false) || // 整点逻辑
							(hours % 4 === 0 && currentActive === 4 && minutes === 0 && seconds === 0 && isPush ===
								false) ||
							(currentActive === 7 && hours === 0 && minutes === 0 && seconds === 0 && isPush === false)
						) {
							isPush = true;
							seriesData.push([newValue,
								newValue,
								newValue,
								newValue,
								0
							])
							setTimeout(function() {
								isDataZoom = false
							}, 500);
							// console.log(timestampInSeconds, "-----------------整点其他")
						} else if (currentActive === 1 || currentActive === 2 || currentActive === 3 || currentActive ===
							4 || currentActive === 6 || currentActive === 7) {

							if (newValue < seriesData[lastIndex][3]) {
								seriesData[lastIndex][3] = newValue
							}
							if (newValue > seriesData[lastIndex][2]) {
								seriesData[lastIndex][2] = newValue
							}
							// console.log(timestampInSeconds, "-----------------整点其他2")
						}
						if (seconds > 0) {
							isPush = false;
						}
					}
					let markLine = {}
					if (newValue !== 0) {
						markLine = {
							yAxis: newValue
						}
					}

					let optionObj = {
						series: [{
							data: seriesData,
							markLine: {
								data: [
									newValue !== 0 ? markLine : [],
								]
							},
						}]
					}
					mainEcharts.setOption(optionObj, false);
				}
			},
			updatePriceEcharts(newValue, oldValue, ownerInstance, instance) {
				marketPrice = newValue
				this.updateTimeData(newValue);
			},
			updateInitKLine(newValue, oldValue, ownerInstance, instance) {
				let t = this;
				// this.updateTimeData(marketPrice);
				// console.log(newValue, ownerInstance, instance)
				t.updateTimeData(marketPrice);
				setTimeout(function() {
					t.initKlineData();
				}, 3000);
			},
			updateCurrentActive(newValue, oldValue, ownerInstance, instance) {
				currentActive = newValue
				isDataZoom = true
				if (newValue == 0) {
					lineElement = document.getElementById('echartsLine')
					lineEcharts = echarts.init(lineElement)
					// #ifdef APP-PLUS
					lineEcharts.on('dataZoom', function(params) {
						this.calcEndValue()
					}, this)
					// #endif
				}
			},
			onTouchStart(event, ownerInstance) {
				startX = event.touches[0].clientX
				startY = event.touches[0].clientY
			},
			calcEndValue() {
				if (mainEcharts.getOption().series[1] || assistantEcharts.getOption().series[1]) {
					let newEnd = mainEcharts.getOption().dataZoom[0].endValue
					let finalEnd
					let maxEnd = mainEcharts.getOption().series[0].data.length - 1
					if (parseInt(newEnd) >= maxEnd) {
						finalEnd = maxEnd
					} else if (parseInt(newEnd) <= 0) {
						finalEnd = 0
					} else {
						finalEnd = parseInt(newEnd)
					}
					this.$ownerInstance.callMethod('updateEndValue', finalEnd)

				}
				//updata
				let newEnd = mainEcharts.getOption().dataZoom[0].endValue
				let newStart = mainEcharts.getOption().dataZoom[0].startValue
				this.$ownerInstance.callMethod('updateDataZoom', {
					newEnd,
					newStart
				})
			},
			onTouchMove(event, ownerInstance) {
				return false;
				if (ownerInstance.$vm.currentActive == 0) {
					let deltaX = (event.touches[0].clientX - startX) / lineEcharts.getWidth() * 30
					let newStart = lineEcharts.getOption().dataZoom[0].startValue + deltaX
					let newEnd = lineEcharts.getOption().dataZoom[0].endValue + deltaX
					lineEcharts.dispatchAction({
						type: 'dataZoom',
						startValue: newStart,
						endValue: newEnd,
					})
					this.lineData.dataZoom[0].startValue = newStart
					this.lineData.dataZoom[0].endValue = newEnd
				} else {
					let deltaX = (event.touches[0].clientX - startX) / mainEcharts.getWidth() * 30
					let newStart = mainEcharts.getOption().dataZoom[0].startValue + deltaX
					let newEnd = mainEcharts.getOption().dataZoom[0].endValue + deltaX
					mainEcharts.dispatchAction({
						type: 'dataZoom',
						startValue: newStart,
						endValue: newEnd,
					})

					assistantEcharts.dispatchAction({
						type: 'dataZoom',
						startValue: newStart,
						endValue: newEnd,
					})
					this.mainData.dataZoom[0].startValue = newStart
					this.mainData.dataZoom[0].endValue = newEnd
					// console.log("onTouchMove")
					this.assistantData.dataZoom[0].startValue = newStart
					this.assistantData.dataZoom[0].endValue = newEnd
					if (this.mainIndicatorsIndex != 0 || this.assistantIndicatorsIndex != 0) {
						let finalEnd
						let maxEnd = mainEcharts.getOption().series[0].data.length - 1
						if (parseInt(newEnd) >= maxEnd) {
							finalEnd = maxEnd
						} else if (parseInt(newEnd) <= 0) {
							finalEnd = 0
						} else {
							finalEnd = parseInt(newEnd)
						}
						this.endValue = parseInt(finalEnd)
					}
				}

			},
		}
	}
</script>
<style lang="scss" scoped>
	.echarts-wrapper {
		height: calc(100vh - (150rpx + 20rpx + 30rpx * 2) - 60rpx - (120rpx + 20rpx));
		background: #fff;
	}

	.template-wrapper {
		height: 100%;
	}

	.echarts-line {
		height: 100%;
	}

	.flex-wrap {
		flex-wrap: wrap;
	}

	.indicators {
		// margin-bottom:10rpx;
		padding: 10rpx;
		height: 46rpx;
		background: #fff;
		display: flex;
		// justify-content: space-between;

		&.tow {
			border-top: 1rpx solid #F2F2F2;
			border-bottom: 1rpx solid #F2F2F2;
		}

		.flex-wrap {
			flex-wrap: wrap;
			flex: 1;
		}

		.indicators-right {
			width: 70%;
			display: flex;
			justify-content: end;

			.indicators-item {
				width: 100%;
				overflow-x: auto;
				display: flex;
				align-items: center;

				.indicators-items {
					display: flex;
					align-items: center;
					margin-right: 20rpx;
				}
			}
		}

		.indicators-left {
			display: flex;
			align-items: center;
			margin-left: 0;
			position: relative;
			// padding: 5rpx 20rpx;
			font-size: 24rpx;
			color: $baseColor;
			// background-color: #2450FF;
			border-radius: 8rpx;
			margin-right:20rpx;

			.indicators-title {
				margin-left:20rpx;
			}

			.kdj-data {
				margin-left:20rpx;
				font-size: 20rpx;
				color: #BABABA;
			}

			.icon {
				font-size: 34rpx;
				color: #000000;
			}

			&::after {}
		}

		.mr-10 {
			margin-right: 10rpx;
		}

		.icon {
			width: 20rpx;
			height: 20rpx;
			border-radius: 20rpx;
			margin-right: 8rpx;
		}

		.indicators-setting-icon {
			font-size: 32rpx;
		}

		.label {
			font-size: 24rpx;
		}

		.value {
			font-size: 24rpx;
		}
	}

	.echarts {
		height: calc(100% - (46rpx * 2) - 30%);
	}

	.assistant-echarts {
		height: 30%;
	}

	.overflow-x-auto {
		overflow-x: auto;
	}


	.popup-container {
		padding: 30rpx;

		.title {
			font-size: 24rpx;
			color: #333;
		}

		.main-item {
			margin: 0 20rpx 20rpx 0;
			padding: 5rpx 20rpx;
			font-size: 24rpx;
			color: #333;
		}

		.assistant-item {
			margin: 0 20rpx 20rpx 0;
			padding: 5rpx 20rpx;
			font-size: 24rpx;
			color: #333;
		}

		.active-item {
			background-color: $color-gray;
		}

		.mt-20 {
			margin-top: 20rpx;
		}
	}
</style>