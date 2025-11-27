<template>
    <div class="market-echarts">
        <div class="market-echarts-time-tabs">
            <a-radio-group type="button" v-model="timeTabIndex" @change="onChangeKlineType"
                :disabled="initKLineLoading">
                <template v-for="(item, index) in timeTabs" :key="index">
                    <a-radio class="market-echarts-time-tab" :value="index">{{ item.name }}</a-radio>
                </template>
            </a-radio-group>
            <div v-if="timeTabIndex !== 0">
                <a-popover trigger="hover">
                    <a-button size="small"><icon-settings size="16" /></a-button>
                    <template #content>
                        <div class="market-echarts-quota">
                            <div class="market-echarts-quota-item">
                                <div class="market-echarts-quota-title">主图指标</div>
                                <div class="market-echarts-quota-list">
                                    <div class="market-echarts-quota-items"
                                        :class="useEchartsStore().mainIndicatorsIndex == index ? 'active' : ''"
                                        v-for="(item, index) in mainIndicators" :key="index"
                                        @click="onSelectMainIndicator(index)">
                                        {{ item }}
                                    </div>
                                </div>
                            </div>
                            <div class="market-echarts-quota assistant">
                                <div class="market-echarts-quota-title">副图指标</div>
                                <div class="market-echarts-quota-list">
                                    <div class="market-echarts-quota-items"
                                        :class="useEchartsStore().assistantIndicatorsIndex == index ? 'active' : ''"
                                        v-for="(item, index) in assistantIndicators" :key="index"
                                        @click="onSelectAssistantIndicator(index)">
                                        {{ item }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </a-popover>
            </div>
        </div>
        <div class="market-echarts-main" v-loading="initKLineLoading">
            <div class="indicators-top" v-if="timeTabIndex != 0">
                <div class="indicators-title" v-if="useEchartsStore().mainIndicatorsIndex !== 0">
                    {{ mainIndicators[useEchartsStore().mainIndicatorsIndex] }}</div>
                <div class="kdj-data">
                    <div v-if="useEchartsStore().mainIndicatorsIndex == 1">(5,10,15)</div>
                    <div v-else-if="useEchartsStore().mainIndicatorsIndex == 2">(5,10,20)</div>
                    <div v-else-if="useEchartsStore().mainIndicatorsIndex == 3">(3,6,12,24)</div>
                    <div v-else-if="useEchartsStore().mainIndicatorsIndex == 4">(12)</div>
                    <div v-else-if="useEchartsStore().mainIndicatorsIndex == 5">(20,2)</div>
                </div>
                <div class="kdj-right">
                    <div class="kdj-item" v-if="useEchartsStore().mainIndicatorsIndex == 1">
                        <div class="kdj-items" v-for="(item, index) in c_MA" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-else-if="useEchartsStore().mainIndicatorsIndex == 2">
                        <div class="kdj-items" v-for="(item, index) in c_EMA" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-else-if="useEchartsStore().mainIndicatorsIndex == 3">
                        <div class="kdj-items">
                            <div class="icon" :style="{ background: c_BBI.color }"></div>
                            <div class="label" :style="{ color: c_BBI.color }">BBI:</div>
                            <div class="value" :style="{ color: c_BBI.color }">{{ c_BBI.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-else-if="useEchartsStore().mainIndicatorsIndex == 4">
                        <div class="kdj-items" v-for="(item, index) in c_MIKE" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-else-if="useEchartsStore().mainIndicatorsIndex == 5">
                        <div class="kdj-items" v-for="(item, index) in c_boll" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>

                </div>
            </div>
            <div ref="echartRef" v-if="echartVisible && timeTabIndex !== 0" class="market-echarts-minute-main">
            </div>

            <div class="indicators-bottom" v-if="timeTabIndex != 0">
                <div class="indicators-title" v-if="useEchartsStore().assistantIndicatorsIndex !== 0">
                    {{ assistantIndicators[useEchartsStore().assistantIndicatorsIndex] }}</div>
                <div class="kdj-data">
                    <div v-if="useEchartsStore().assistantIndicatorsIndex == 1">(9,3,3)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 2">(26,12,9)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 3">(26)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 4">(14)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 5">(6,12,24)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 6">(9,3)</div>
                    <div v-else-if="useEchartsStore().assistantIndicatorsIndex == 7">(14)</div>
                </div>
                <div class="kdj-right">
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 1">
                        <div class="kdj-items" v-for="(item, index) in c_kdj" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 2">

                        <div class="kdj-items" v-for="(item, index) in c_macd" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 3">
                        <div class="kdj-items" v-for="(item, index) in c_arbr" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 4">
                        <div class="kdj-items">
                            <div class="icon" :style="{ background: c_atr.color }"></div>
                            <div class="label" :style="{ color: c_atr.color }">ATR:</div>
                            <div class="value" :style="{ color: c_atr.color }">{{ c_atr.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 5">
                        <div class="kdj-items" v-for="(item, index) in c_bias" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 6">
                        <div class="kdj-items" v-for="(item, index) in c_kd" :key="item.label">
                            <div class="icon" :style="{ background: item.color }"></div>
                            <div class="label" :style="{ color: item.color }">{{ item.label }}:</div>
                            <div class="value" :style="{ color: item.color }">{{ item.value }}</div>
                        </div>
                    </div>
                    <div class="kdj-item" v-if="useEchartsStore().assistantIndicatorsIndex == 7">
                        <div class="kdj-items">
                            <div class="icon" :style="{ background: c_atr.color }"></div>
                            <div class="label" :style="{ color: c_atr.color }">WR:</div>
                            <div class="value" :style="{ color: c_atr.color }">{{ c_atr.value }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div ref="assistantEchartsRef" v-if="echartVisible && timeTabIndex !== 0"
                class="market-echarts-assistant-main">
            </div>
            <div ref="echartsLineRef" v-if="echartVisible && timeTabIndex === 0" class="market-echarts-minute-main"
                :style="{ height: useEchartsStore().assistantIndicatorsIndex == 0 ? '100%' : 'calc(100% - 140px)' }">
            </div>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "market-echarts",
};
</script>
<script lang="ts" setup>
import { computed, getCurrentInstance, markRaw, nextTick, onBeforeUnmount, onMounted, ref, watch } from "vue";
import * as echarts from 'echarts';
import { useMarketStore } from "@/store";
import { Result, ResultError } from "@/types";
import { getKDataApi } from "@/api/product";
import { getPriceMinuteRefreshOption, getPriceLineRefreshOption, parseData, getDateText } from "@/utils/echarts/common";
import candlestickOption from "@/utils/echarts/candlestickOption";
import barOption from "@/utils/echarts/barOptions";
import lineOption from "@/utils/echarts/lineOption";
import { useEchartsStore } from "@/store/modules/echarts";
import { BollOptions, dColor, downColor, jColor, kColor, MA10Color, MA15Color, MA30Color, MA45Color, MA5Color, MA60Color, MAOptions, MIKEOptions } from "@/utils/echarts/constant";
import { calculateMA } from "@/utils/echarts/ma";
import calculateEMA from "@/utils/echarts/ema";
import calc_BBI from "@/utils/echarts/bbi";
import calc_MIKE from "@/utils/echarts/mike";
import calc_BOLL from "@/utils/echarts/boll";
import calc_KDJ from "@/utils/echarts/kdj";
import kdjOption from "@/utils/echarts/kdjOption";
import calc_MACD from "@/utils/echarts/macd";
import macdOption from "@/utils/echarts/macdOption";
import arbrOption from "@/utils/echarts/arbrOption";
import calc_ARBR from "@/utils/echarts/arbr";
import calc_atr from "@/utils/echarts/atr";
import calc_BIAS from "@/utils/echarts/bias";
import wroOption from "@/utils/echarts/wroOption";
import calc_WR from "@/utils/echarts/wr";
import kdOption from "@/utils/echarts/kdOption";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const timeTabIndex = ref<number>(5);

const timeTabs = ref<any>([
    {
        name: "分时",
        value: 1,
        timer: 1
    }, {
        name: "M15",
        value: 15,
        timer: 15
    }, {
        name: "M30",
        value: 30,
        timer: 30
    }, {
        name: "H1",
        value: 60,
        timer: 62
    }, {
        name: "H4",
        value: 240,
        timer: 242
    }, {
        name: "M1",
        value: 1,
        timer: 1
    }, {
        name: "M5",
        value: 5,
        timer: 5
    }, {
        name: "日K线",
        value: 60 * 24,
        timer: 60 * 24
    }
]);

const onChangeKlineType = () => {
    toInitKLine(true)
}

const echartVisible = ref<boolean>(false)

const chartInstance = ref<any>(null)
const chartassistantInstance = ref<any>(null)

const lineChartInstance = ref<any>(null)

const marketKLineData = ref<any[]>([])

const initKLineLoading = ref<boolean>(false)

const marketId = ref<number | string>(useMarketStore().selectMarketId)

//主指标列表
const mainIndicators = ref<string[]>(["不显示", "MA", "EMA", "BBI", "MIKE", "BOLL"])


const assistantIndicators = ref<string[]>(['成交量', "KDJ", "MACD", "ARBR", "ATR", "BIAS", "KD", "W&R"])


const mainOption = ref<any>({})
const assistantOption = ref<any>({})

const endValue = ref<number>(0)

const c_MA = computed(() => {
    if (mainOption.value?.series === undefined || !mainOption.value?.series[1]) {
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

        let MA5 = parseFloat(mainOption.value.series[1]?.data[endValue.value])
        let MA10 = parseFloat(mainOption.value.series[2]?.data[endValue.value])
        let MA15 = parseFloat(mainOption.value.series[3]?.data[endValue.value])
        // let MA30 = parseFloat(mainOption.value.series[4].data[productDetail.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "MA5",
            value: MA5 ? MA5.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }, {
            label: "MA10",
            value: MA10 ? MA10.toFixed(productDetail.decimal) : "-",
            color: MA10Color
        }, {
            label: "MA15",
            value: MA15 ? MA15.toFixed(productDetail.decimal) : "-",
            color: MA15Color
        }]
    }

})

const c_EMA = computed(() => {
    if (mainOption.value?.series === undefined || !mainOption.value?.series[1]) {
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
        let EMA5 = parseFloat(mainOption.value.series[1]?.data[endValue.value])
        let EMA10 = parseFloat(mainOption.value.series[2]?.data[endValue.value])
        let EMA20 = parseFloat(mainOption.value.series[3]?.data[endValue.value])
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "EMA5",
            value: EMA5 ? EMA5.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }, {
            label: "EMA10",
            value: EMA10 ? EMA10.toFixed(productDetail.decimal) : "-",
            color: MA10Color
        }, {
            label: "EMA20",
            value: EMA20 ? EMA20.toFixed(productDetail.decimal) : "-",
            color: MA15Color
        }]
    }
})

const c_BBI = computed(() => {

    if (mainOption.value?.series === undefined || !mainOption.value?.series[1]) {
        return {
            value: "-",
            color: MA5Color
        }
    } else {
        let BBI = parseFloat(mainOption.value?.series[1]?.data[endValue.value])
        let productDetail = useMarketStore().getMarketDetail();
        return {
            value: BBI ? BBI.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }
    }
})

const c_MIKE = computed(() => {
    if (mainOption.value?.series === undefined || !mainOption.value?.series[1]) {
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
        let WR = parseFloat(mainOption.value?.series[1]?.data[endValue.value])
        let MR = parseFloat(mainOption.value?.series[2]?.data[endValue.value])
        let SR = parseFloat(mainOption.value?.series[3]?.data[endValue.value])
        let WS = parseFloat(mainOption.value?.series[1]?.data[endValue.value])
        let MS = parseFloat(mainOption.value?.series[2]?.data[endValue.value])
        let SS = parseFloat(mainOption.value?.series[3]?.data[endValue.value])
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "WR",
            value: WR ? WR.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }, {
            label: "MR",
            value: MR ? MR.toFixed(productDetail.decimal) : "-",
            color: MA10Color
        }, {
            label: "SR",
            value: SR ? SR.toFixed(productDetail.decimal) : "-",
            color: MA15Color
        }, {
            label: "WS",
            value: WS ? WS.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }, {
            label: "MS",
            value: MS ? MS.toFixed(productDetail.decimal) : "-",
            color: MA10Color
        }, {
            label: "SS",
            value: SS ? SS.toFixed(productDetail.decimal) : "-",
            color: MA15Color
        }]
    }
})

const c_boll = computed(() => {
    if (mainOption.value?.series === undefined || !mainOption.value?.series[1]) {
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
        let kData = parseFloat(mainOption.value?.series[1]?.data[endValue.value])
        let dData = parseFloat(mainOption.value?.series[2]?.data[endValue.value])
        let jData = parseFloat(mainOption.value?.series[3]?.data[endValue.value])
        // let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "MID",
            value: kData ? kData.toFixed(productDetail.decimal) : "-",
            color: dColor
        }, {
            label: "UP",
            value: dData ? dData.toFixed(productDetail.decimal) : "-",
            color: jColor
        }, {
            label: "LOW",
            value: jData ? jData.toFixed(productDetail.decimal) : "-",
            color: downColor
        }]
    }
})


const c_kdj = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[0]) {
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
        let kData = parseFloat(assistantOption.value?.series[0]?.data[endValue.value])
        let dData = parseFloat(assistantOption.value?.series[1]?.data[endValue.value])
        let jData = parseFloat(assistantOption.value?.series[2]?.data[endValue.value])
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "K",
            value: kData ? kData.toFixed(productDetail.decimal) : "-",
            color: kColor
        }, {
            label: "D",
            value: dData ? dData.toFixed(productDetail.decimal) : "-",
            color: dColor
        }, {
            label: "J",
            value: jData ? jData.toFixed(productDetail.decimal) : "-",
            color: jColor
        }]
    }
})
const c_macd = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[1]) {
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
        let MACD = parseFloat(assistantOption.value?.series[0]?.data[endValue.value][1])
        let DEA = parseFloat(assistantOption.value?.series[2]?.data[endValue.value])
        let DIF = parseFloat(assistantOption.value?.series[1]?.data[endValue.value])
        // let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "MACD",
            value: MACD ? MACD.toFixed(productDetail.decimal) : "-",
            color: kColor
        }, {
            label: "DEA",
            value: DEA ? DEA.toFixed(productDetail.decimal) : "-",
            color: dColor
        }, {
            label: "DIF",
            value: DIF ? DIF.toFixed(productDetail.decimal) : "-",
            color: jColor
        }]
    }
})


const c_arbr = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[1]) {
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
        let AR = parseFloat(assistantOption.value?.series[0]?.data[endValue.value])
        let BR = parseFloat(assistantOption.value?.series[1]?.data[endValue.value])
        // let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "AR",
            value: AR ? AR.toFixed(productDetail.decimal) : "-",
            color: kColor
        }, {
            label: "BR",
            value: BR ? BR.toFixed(productDetail.decimal) : "-",
            color: dColor
        }]
    }
})

const c_atr: any = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[0]) {
        return {
            value: "-",
            color: MA5Color
        }
    } else {
        let ATR = parseFloat(assistantOption.value?.series[0]?.data[endValue.value])
        // let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return {
            value: ATR ? ATR.toFixed(productDetail.decimal) : "-",
            color: MA5Color
        }
    }
})

const c_bias = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[1]) {
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
        let BIAS1 = parseFloat(assistantOption.value?.series[0]?.data[endValue.value])
        let BIAS2 = parseFloat(assistantOption.value?.series[1]?.data[endValue.value])
        let BIAS3 = parseFloat(assistantOption.value?.series[2]?.data[endValue.value])
        // let MA30 = parseFloat(this.mainData.series[4]?.data[this.endValue]).toFixed(this.decimal)
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "BIAS1",
            value: BIAS1 ? BIAS1.toFixed(productDetail.decimal) : "-",
            color: kColor
        }, {
            label: "BIAS2",
            value: BIAS2 ? BIAS2.toFixed(productDetail.decimal) : "-",
            color: dColor
        }, {
            label: "BIAS3",
            value: BIAS3 ? BIAS3.toFixed(productDetail.decimal) : "-",
            color: jColor
        }]
    }
})

const c_kd = computed(() => {
    if (assistantOption.value?.series === undefined || !assistantOption.value?.series[1]) {
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
        // let MA30 = parseFloat(this.mainData.series[4].data[this.endValue]).toFixed(this.decimal)

        let kData = parseFloat(assistantOption.value?.series[0]?.data[endValue.value])
        let dData = parseFloat(assistantOption.value?.series[1]?.data[endValue.value])
        let productDetail = useMarketStore().getMarketDetail();
        return [{
            label: "K",
            value: kData ? kData.toFixed(productDetail.decimal) : "-",
            color: kColor
        }, {
            label: "D",
            value: dData ? dData.toFixed(productDetail.decimal) : "-",
            color: dColor
        }]
    }
})



const onSelectMainIndicator = (index: number) => {

    useEchartsStore().setMarnIndicatorsIndex(index)
    // echartVisible.value = false;

    nextTick(() => {
        // echartVisible.value = true;

        toInitKLine(true)
        // echartVisible.value = true;
    })
}

const onSelectAssistantIndicator = (index: number) => {
    useEchartsStore().setAssistantIndicatorsIndex(index)
    nextTick(() => {
        toInitKLine(true)
    })
}
const toInitKLine = (isInit: boolean = true) => {
    if (isInit) {
        initKLineLoading.value = true;
        echartVisible.value = false;
    }
    getKDataApi({
        pid: marketId.value,
        interval: timeTabs.value[timeTabIndex.value].value,
        num: 1000,
    }).then((res: Result) => {
        if (isInit) {
            echartVisible.value = true;
        }
        marketKLineData.value = res.data.items
        setTimeout(() => {
            initKLineChartData(isInit)
        }, 300);
        setTimeout(() => {
            if (isInit) {
                initKLineLoading.value = false;
            }
        }, 800);
    }).catch((err: ResultError) => {
        if (isInit) {
            initKLineLoading.value = false;
            echartVisible.value = false;
        }
    })
}

const isDataZoom = ref<boolean>(false)


const initKLineChartData = (isInit: boolean = false) => {
    nextTick(() => {
        let productDetail = useMarketStore().getMarketDetail();
        let options: any = {};
        switch (timeTabIndex.value) {
            case 0:
                if (lineChartInstance.value == null || isInit) {
                    lineChartInstance.value = markRaw(echarts.init(proxy?.$refs['echartsLineRef']));
                    options = lineOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    lineChartInstance.value?.setOption(options);
                    updatePollingInitKLine(false);
                    window.addEventListener("resize", chartInstanceResizeFunc)
                } else if (lineChartInstance.value !== undefined) {
                    const option = lineChartInstance.value.getOption();
                    options = lineOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    if (isDataZoom.value === false && option !== undefined) {
                        delete options?.dataZoom
                    }
                    lineChartInstance.value.setOption(options, false)
                }
                break;
            default:
                // console.log(echartVisible.value)
                if (chartInstance.value == null || isInit) {
                    chartInstance.value = markRaw(echarts.init(proxy?.$refs['echartRef']));
                    mainOption.value = candlestickOption(marketKLineData.value, productDetail.decimal || 2, "test");

                    chartInstance.value?.setOption(mainOption.value);
                    updatePollingInitKLine(false);
                    window.addEventListener("resize", chartInstanceResizeFunc)
                    chartInstance.value?.on('dataZoom', (params: any) => {
                        calcEndValue()
                    })
                } else if (chartInstance.value !== undefined) {
                    mainOption.value = candlestickOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    if (isDataZoom.value === false && chartInstance.value?.getOption() !== undefined) {
                        delete mainOption.value?.dataZoom
                    }
                    chartInstance.value.setOption(mainOption.value, false)
                }

                if (chartassistantInstance.value == null || isInit) {
                    chartassistantInstance.value = markRaw(echarts.init(proxy?.$refs['assistantEchartsRef']));
                    assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    chartassistantInstance.value?.setOption(assistantOption.value);
                    updatePollingInitKLine(false);
                    window.addEventListener("resize", chartInstanceResizeFunc)
                    chartassistantInstance.value?.on('dataZoom', (params: any) => {
                        calcEndValue()
                    })
                } else if (chartassistantInstance.value !== undefined) {
                    assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    if (isDataZoom.value === false && chartassistantInstance.value?.getOption() !== undefined) {
                        delete assistantOption.value?.dataZoom
                    }
                    chartassistantInstance.value.setOption(assistantOption.value, false)
                }
                echarts.connect([chartInstance.value!, chartassistantInstance.value!])
                initMainEchartQute()
                break;
        }
    })
}



const initMainEchartQute = () => {
    let productDetail = useMarketStore().getMarketDetail();
    if (timeTabIndex.value !== 0) {
        switch (useEchartsStore().mainIndicatorsIndex) {
            case 1:
                //均线MA
                nextTick(() => {
                    mainOption.value.series.push(...MAOptions)
                    let data = marketKLineData.value.map((item) => [item[1], item[4], item[3], item[2]])
                    mainOption.value.series[1].data = calculateMA(data, 5, productDetail.decimal || 2)
                    mainOption.value.series[2].data = calculateMA(data, 10, productDetail.decimal || 2)
                    mainOption.value.series[3].data = calculateMA(data, 15, productDetail.decimal || 2)

                    chartInstance.value.setOption(mainOption.value, false)
                });
                break;
            case 2:
                //EMA
                nextTick(() => {
                    mainOption.value.series.push(...MAOptions)
                    let data = marketKLineData.value.map((item) => [item[1], item[4], item[3], item[2]])
                    mainOption.value.series[1].data = calculateEMA(data, 5, productDetail.decimal || 2)
                    mainOption.value.series[2].data = calculateEMA(data, 10, productDetail.decimal || 2)
                    mainOption.value.series[3].data = calculateEMA(data, 20, productDetail.decimal || 2)

                    mainOption.value.series[1].name = "EMA5"
                    mainOption.value.series[2].name = "EMA10"
                    mainOption.value.series[3].name = "EMA20"
                    chartInstance.value.setOption(mainOption.value, false)
                });
                break;
            case 3:
                //EMA
                nextTick(() => {
                    mainOption.value.series.push(MAOptions[0])
                    let data = marketKLineData.value.map((item) => [item[1], item[4], item[3], item[2]])
                    mainOption.value.series[1].data = calc_BBI(data, productDetail.decimal || 2)
                    mainOption.value.series[1].name = "BBI"
                    chartInstance.value.setOption(mainOption.value, false)
                });
            case 4:
                //Mike
                nextTick(() => {
                    mainOption.value.series.push(...MIKEOptions)
                    let data = marketKLineData.value.map((item) => [item[1], item[4], item[3], item[2]])
                    let mikeList = calc_MIKE(data, 12, productDetail.decimal || 2)
                    mainOption.value.series[1].data = mikeList[0]
                    mainOption.value.series[2].data = mikeList[1]
                    mainOption.value.series[3].data = mikeList[2]
                    mainOption.value.series[4].data = mikeList[3]
                    mainOption.value.series[5].data = mikeList[4]
                    mainOption.value.series[6].data = mikeList[5]

                    mainOption.value.series[1].name = "WR"
                    mainOption.value.series[2].name = "MR"
                    mainOption.value.series[3].name = "SR"
                    mainOption.value.series[4].name = "WS"
                    mainOption.value.series[5].name = "MS"
                    mainOption.value.series[6].name = "SS"
                    chartInstance.value.setOption(mainOption.value, false)
                });
                break;
            case 5:
                //BOLL
                nextTick(() => {
                    // console.log(bollOption)
                    // mainOption.value = JSON.parse(JSON.stringify(bollOption))
                    mainOption.value.series.push(...BollOptions)
                    let data = marketKLineData.value.map((item) => [item[1], item[4], item[3], item[2]])
                    const [ma20, upList, downList] = calc_BOLL(data, 20, productDetail.decimal || 2)
                    mainOption.value.series[1].data = ma20
                    mainOption.value.series[2].data = upList
                    mainOption.value.series[3].data = downList
                    chartInstance.value.setOption(mainOption.value, false)
                });
                break;
        }
        switch (useEchartsStore().assistantIndicatorsIndex) {
            case 0:
                //成交量
                break;
            case 1:
                //kdj
                nextTick(() => {

                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = JSON.parse(JSON.stringify(kdjOption(productDetail.decimal)));
                    let kjdList = calc_KDJ(marketKLineData.value, productDetail.decimal)

                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    assistantOption.value.series[0].data = kjdList[0]
                    assistantOption.value.series[1].data = kjdList[1]
                    assistantOption.value.series[2].data = kjdList[2]
                    chartassistantInstance.value?.setOption(assistantOption.value, false)
                });
                break;
            case 2:
                //MACD
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = macdOption(productDetail.decimal);
                    let data = marketKLineData.value.map((item: any) => [item[1], item[4], item[3], item[2]])
                    let macd = calc_MACD(data, productDetail.decimal)
                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    assistantOption.value.series[0].data = macd[2].map((item: any, index: any) => ([index, item, item >= 0 ? 1 : -1]))
                    assistantOption.value.series[1].data = macd[0]
                    assistantOption.value.series[2].data = macd[1]
                    chartassistantInstance.value?.setOption(assistantOption.value, false)
                });
                break;
            case 3:
                //ARBRO
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = JSON.parse(JSON.stringify(arbrOption(productDetail.decimal)));

                    let data = marketKLineData.value.map((item: any) => [item[1], item[4], item[3], item[2]])
                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    let arbrList = calc_ARBR(data, 26, productDetail.decimal)
                    assistantOption.value.series[0].data = arbrList[0]
                    assistantOption.value.series[1].data = arbrList[1]
                    chartassistantInstance.value.setOption(assistantOption.value, false)
                });
                break;

            case 4:
                //ATR
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = JSON.parse(JSON.stringify(arbrOption(productDetail.decimal)));

                    let data = marketKLineData.value.map((item: any) => [item[1], item[4], item[3], item[2]])
                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    assistantOption.value.series[0].data = calc_atr(data, 14)
                    chartassistantInstance.value.setOption(assistantOption.value, false)
                });
                break;
            case 5:
                //BIAS
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = JSON.parse(JSON.stringify(kdjOption(productDetail.decimal)));

                    let data = marketKLineData.value.map((item: any) => [item[1], item[4], item[3], item[2]])
                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date

                    let biasList_6 = calc_BIAS(data, 6)
                    let biasList_12 = calc_BIAS(data, 12)
                    let biasList_24 = calc_BIAS(data, 24)

                    assistantOption.value.series[0].data = biasList_6
                    assistantOption.value.series[1].data = biasList_12
                    assistantOption.value.series[2].data = biasList_24

                    chartassistantInstance.value.setOption(assistantOption.value, false)
                });
                break;
            case 6:
                //KDO
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = kdOption(productDetail.decimal);

                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    let kjdList = calc_KDJ(marketKLineData.value)
                    assistantOption.value.series[0].data = kjdList[0]
                    assistantOption.value.series[1].data = kjdList[1]
                    chartassistantInstance.value.setOption(assistantOption.value, false)
                });
                break;
            case 7:
                //WRO
                nextTick(() => {
                    let productDetail = useMarketStore().getMarketDetail();
                    // assistantOption.value = barOption(marketKLineData.value, productDetail.decimal || 2, "test");
                    assistantOption.value = JSON.parse(JSON.stringify(wroOption()));
                    let data = marketKLineData.value.map((item: any) => [item[1], item[4], item[3], item[2]])

                    let date = marketKLineData.value.map((item) => {
                        return getDateText(item[0])
                    });
                    assistantOption.value.xAxis[0].data = date
                    assistantOption.value.series[0].data = calc_WR(data, 14)

                    chartassistantInstance.value.setOption(assistantOption.value, false)
                });
                break;
        }
    }
}

















const calcEndValue = () => {
    if (chartInstance.value?.getOption().series[1]) {
        let newEnd = chartInstance.value?.getOption().dataZoom[0].endValue
        let finalEnd
        let maxEnd = chartInstance.value?.getOption().series[0].data.length - 1
        if (parseInt(newEnd) >= maxEnd) {
            finalEnd = maxEnd
        } else if (parseInt(newEnd) <= 0) {
            finalEnd = 0
        } else {
            finalEnd = parseInt(newEnd)
        }
        updateEndValue(finalEnd)
    }
}

const updateEndValue = (newValue: any) => {
    endValue.value = newValue
}

const chartInstanceResizeFunc = () => {
    chartInstance.value?.resize();
    chartassistantInstance.value?.resize();
    lineChartInstance.value?.resize();
}

const removeCharts = () => {
    window.removeEventListener("resize", chartInstanceResizeFunc)
    chartInstance.value = null;
    lineChartInstance.value = null;
    chartassistantInstance.value = null;
    if (!chartInstance.value || !lineChartInstance.value) {
        return;
    }
    chartInstance.value?.dispost();
    lineChartInstance.value?.dispost();
    chartassistantInstance.value?.dispost();
    echartVisible.value = false;
}

const pollingTimer = ref<any>(null);

const pollingTime = () => {
    pollingTimer.value = setInterval(() => {
        const now = new Date(); // 获取当前时间
        const seconds = now.getSeconds(); // 获取当前秒数
        const minutes = now.getMinutes();
        const hours = now.getHours();
        if (seconds === 0) {
            updatePollingInitKLine(true);
        }

        // updatePollingInitKLine(true);
    }, 1000)
}

const marketPrice = ref<number | string>(0);

const updatePriceEcharts = (price: number | string) => {
    marketPrice.value = price
    updateTimeData(price)
}

const updatePollingInitKLine = (isInit: boolean = false) => {
    updateTimeData(marketPrice.value);
    if (isInit === true) {
        setTimeout(() => {
            toInitKLine(false)
        }, 2000);
    }
}

const updateTimeData = (price: number | string) => {
    switch (timeTabIndex.value) {
        case 0:
            lineChartInstance.value?.setOption(getPriceLineRefreshOption(lineChartInstance.value, price, timeTabIndex.value), false);
            break;
        default:
            chartInstance.value?.setOption(getPriceMinuteRefreshOption(chartInstance.value, price, timeTabIndex.value), false);
            break;
    }
}

watch(
    () => useMarketStore().getMarketPrice(marketId.value),
    (val) => {
        updatePriceEcharts(val)
    },
    {
        deep: true,
        immediate: true
    }
);

watch(
    () => useMarketStore().selectMarketId,
    (val) => {
        marketId.value = val;
        nextTick(() => {
            toInitKLine(true);
        });
    },
    {
        deep: true,
        immediate: true
    }
);

watch(
    () => echartVisible.value,
    (val) => {
        if (val === false) {
            removeCharts();
        }
    },
    {
        deep: true,
        immediate: true
    }
);

onMounted(() => {
    pollingTime();
})
onBeforeUnmount(() => {
    removeCharts();
})

const onMoving = () => {
chartInstanceResizeFunc();
}
defineExpose({ onMoving });
</script>
<style scoped>
.market-echarts {
    padding: calc(var(--base-padding));
    background: var(--color-bg-2);
    border-radius: var(--base-radius-default);
    border: 1px solid var(--color-border-1);
    height: calc(100% - var(--base-padding) * 2);
}

.market-echarts-time-tabs {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.market-echarts-main {
    margin-top: 6px;
    width: 100%;
    height: calc(100% - 40px - var(--base-padding));
    position: relative;
}


.market-echarts-minute-main {
    width: 100%;
    /* height: 100%; */
    height: calc(100% - 140px);
}

.market-echarts-assistant-main {
    width: 100%;
    height: 120px;
}

.market-echarts-time-tab {
    user-select: none;
}

.market-echarts-quota-item.assistant {
    margin-top: 20px;
}

.market-echarts-quota-list {
    margin-top: 10px;
    width: 320px;
    display: flex;
    flex-wrap: wrap;
}

.market-echarts-quota-items {
    padding: 3px 10px;
    background-color: var(--color-fill-2);
    margin-right: 10px;
    margin-bottom: 10px;
    border-radius: var(--base-radius-default);
    cursor: pointer;
    user-select: none;
}

.market-echarts-quota-items:hover {
    color: rgb(var(--primary-6));
}

.market-echarts-quota-items.active {
    background-color: rgb(var(--primary-6));
    color: #fff;
}

.market-echarts-main .indicators-top {
    position: absolute;
    top: 0;
    display: flex;
    align-items: center;
    color: rgb(var(--gray-6));
    z-index: 99999;
    /* font-size: 14px; */
}

.market-echarts-main .indicators-bottom {
    display: flex;
    align-items: center;
    color: rgb(var(--gray-6));
    height: 24px;
}

.market-echarts-main .indicators-bottom .kdj-data {
    margin-left: 6px;
}


.indicators-top .kdj-right,
.indicators-bottom .kdj-right {
    display: flex;
    justify-content: end;
    margin-left: 10px;
}

.indicators-top .kdj-right .kdj-item,
.indicators-bottom .kdj-right .kdj-item {
    width: 100%;
    overflow-x: auto;
    display: flex;
    align-items: center;
}

.indicators-top .kdj-right .kdj-items,
.indicators-bottom .kdj-right .kdj-items {
    display: flex;
    align-items: center;
    margin-right: 10px;
}
</style>