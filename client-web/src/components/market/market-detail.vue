<template>
    <div class="market-detail" v-loading="!marketDetail">
        <div class="market-detail-top">
            <div class="flex items-center">
                <div class="fz18">{{ marketDetail?.name }}</div>
                <div class="ml10 text-gray">{{ marketDetail?.code }}</div>
            </div>
            <div class="market-detail-top-right">
                <a-tooltip content="查看合约属性">
                    <a-button shape="circle" size="small" @click="onContract"><icon-calendar /></a-button>
                </a-tooltip>
                <marketContractComponent ref="marketContractComponentRef"></marketContractComponent>
                <a-tooltip :content="$utils.inArray(selectMarketId, member?.collects) ? '从自选中删除' : '加入自选'">
                    <a-button class="ml10" shape="circle" size="small" @click="onOptional">
                        <icon-subscribe-add v-if="!$utils.inArray(selectMarketId, member?.collects)" />
                        <icon-subscribe v-else />
                    </a-button>
                </a-tooltip>
                <!-- <icon-subscribe /> -->
            </div>
        </div>
        <div class="market-detail-price">
            <div class="market-detail-price-number" :class="marketDetail?.is_open ? '' : 'market-none-open'"
                :style="{ color: marketDetail?.price >= marketDetail?.old_price ? getUpColor : getDownColor }">{{
                    marketPrices[marketDetail?.id] || marketDetail?.price }}</div>
            <div class="ml12" :class="marketDetail?.is_open ? '' : 'market-none-open'"
                :style="{ color: marketDetail?.price >= marketDetail?.old_price ? getUpColor : getDownColor }">
                {{ changeNumSign }}{{ changeNum }}
            </div>
            <view class="ml12" :class="marketDetail?.is_open ? '' : 'market-none-open'"
                :style="{ color: Number(changeValue) >= 0 ? getUpColor : getDownColor }">
                {{ changeValueSign }}{{ changeValue }}%
            </view>
        </div>
        <div class="market-detail-today">
            <div class="market-detail-today-item">
                <div class="title">今开:</div>
                <div class="number">{{ marketDetail?.open_price }}</div>
            </div>
            <div class="market-detail-today-item">
                <div class="title">昨收:</div>
                <div class="number">{{ marketDetail?.last_close || 0 }}</div>
            </div>
            <div class="market-detail-today-item">
                <div class="title">最高:</div>
                <div class="number">{{ marketDetail?.height_price || 0 }}</div>
            </div>
            <div class="market-detail-today-item">
                <div class="title">最低:</div>
                <div class="number">{{ marketDetail?.low_price || 0 }}</div>
            </div>
        </div>
        <div class="market-defailt-bottom">
            <a-button class="sell-btn" type="primary" status="success" v-if="marketDetail?.is_open"
                @click="onCreateOrder(1)">买跌</a-button>
            <a-button class="buy-btn" type="primary" status="danger" v-if="marketDetail?.is_open"
                @click="onCreateOrder(2)">买涨</a-button>
            <a-button v-if="!marketDetail?.is_open" :disabled="true" class="market-none-open">休市</a-button>
        </div>
        <createOrderComponent ref="createOrderComponentRef" v-if="createOrderStatus && useMarketStore().selectMarketId"
            @close="onCreateOrderClose()">
        </createOrderComponent>
    </div>
</template>
<script lang="ts">
export default {
    name: "market-detail",
};
</script>
<script lang="ts" setup>
import { computed, getCurrentInstance, nextTick, ref, watch } from "vue";
import { useMarketStore, useMemberStore } from "@/store";
import { storeToRefs } from "pinia";
import marketContractComponent from "@/components/market/contract.vue"
import createOrderComponent from "@/components/member/order/create-order.vue"
import { addOptionalProductApi, delOptionalProductApi } from "@/api/product";
import { Result, ResultError } from "@/types";
import { ValidatedError, Notification } from "@arco-design/web-vue";
import { on } from "events";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const { marketPrices, selectMarketId } = storeToRefs(useMarketStore());
const { member } = storeToRefs(useMemberStore());

const changeNum = ref<number | string>(0)

const changeValue = ref<number | string>(0)

const calculateChange = () => {

    const price = parseFloat(useMarketStore().getMarketDetail()?.price);

    const openPrice = parseFloat(useMarketStore().getMarketDetail()?.open_price);

    if (isNaN(price) || isNaN(openPrice)) {
        changeValue.value = "0.00";
        return;
    }

    let liuChu = openPrice || price;
    if (liuChu === 0) {
        changeValue.value = "0.00";
        return;
    }

    let _price = price - openPrice;
    let changeZhi = _price / liuChu;

    if (changeZhi < 0) {
        const changeJuedui = Math.ceil(Math.abs(changeZhi) * 10000) / 10000;
        changeZhi = changeJuedui * -1;
    }

    changeNum.value = (_price / 100).toFixed(useMarketStore().getMarketDetail()?.decimal || 2);

    changeValue.value = (changeZhi * 100).toFixed(2);
}

watch(
    () => useMarketStore().getMarketDetail()?.price,
    (val) => {
        calculateChange();
    },
    {
        deep: true,
        immediate: true
    }
);

const marketDetail = computed(() => useMarketStore().getMarketDetail());

const getUpColor = computed(() => useMemberStore().getUpColor());
const getDownColor = computed(() => useMemberStore().getDownColor());

const changeValueSign = computed(() => parseFloat(String(changeValue.value)) > 0 ? "+" : "");
const changeNumSign = computed(() => parseFloat(String(changeNum.value)) > 0 ? "+" : "");


const marketContractComponentRef = ref<HTMLElement>()

//查看合约属性
const onContract = () => {
    proxy?.$refs["marketContractComponentRef"]?.open();
}

const createOrderComponentRef = ref<HTMLElement>()
const createOrderStatus = ref<boolean>(false)
//开仓
const onCreateOrder = (ostyle: string | number) => {
    if (!useMemberStore().isLogin) {
        useMemberStore().setLoginModal(true);
        return false;
    }
    createOrderStatus.value = true
    nextTick(() => {
        proxy?.$refs["createOrderComponentRef"]?.open(ostyle);
    })
}

const onCreateOrderClose = () => {
    createOrderStatus.value = false
}

//加入组选
const onOptional = () => {
    if (!useMemberStore().isLogin) {
        useMemberStore().setLoginModal(true);
        return false;
    }
    if ($utils.inArray(selectMarketId.value, member.value?.collects)) {
        delOptionalProductApi(selectMarketId.value).then((res: Result) => {
            // initDetail()
            $utils.successMsg(res.message || "删除自选成功")

        }).catch((err: ResultError) => {
            $utils.errorMsg(err)
        })
    } else {
        addOptionalProductApi(selectMarketId.value).then((res: Result) => {
            // initDetail()
            $utils.successMsg(res.message || "添加自选成功")
        }).catch((err: ResultError) => {
            $utils.errorMsg(err)
        })
    }
}
</script>


<style scoped>
.market-detail {
    color: var(--color-text-1);
    /* margin-top: calc(var(--base-padding)); */
    padding: calc(var(--base-padding) * 2);
    background: var(--color-bg-2);
    border-radius: var(--base-radius-default);
    border: 1px solid var(--color-border-1);
    height: 240px;
    width: calc(100% - var(--base-padding) * 4);
}

.market-detail-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.market-detail-top-right {
    display: flex;
}

.market-detail-top-right .right-btn {
    cursor: pointer;
    user-select: none;
}

.market-detail-price {
    margin-top: calc(var(--base-padding));
    display: flex;
    align-items: baseline;
    width: 100%;
}

.market-detail-price-number {
    font-weight: bold;
    font-size: 32px;
}

.market-detail-today {
    margin-top: calc(var(--base-padding));
    display: flex;
    flex-wrap: wrap;
}

.market-detail-today .market-detail-today-item {
    margin-top: calc(var(--base-padding) / 2);
    width: 50%;
    line-height: 20px;
}

.market-detail-today .market-detail-today-item .title {
    color: rgb(var(--gray-6));
    font-size: var(--base-size-small);
}

.market-defailt-bottom {
    margin-top: calc(var(--base-padding) * 2);
    display: flex;
    justify-content: space-between;
}

.market-defailt-bottom .sell-btn {
    width: calc(50% - var(--base-padding));
    height: 40px;
    line-height: 40px;
    /* background: rgb(var(--green-6)); */
    /* border-radius: var(--base-radius-default); */
    /* color: #fff; */
}

.market-defailt-bottom .buy-btn {
    width: calc(50% - var(--base-padding));
    height: 40px;
    line-height: 40px;
}

.market-defailt-bottom .market-none-open {
    height: 40px;
    width: 100%;
    line-height: 40px;
}



@media screen and (max-height: 1060px) {
    .market-detail {
        margin-top: calc(var(--base-min-padding));
        width: calc(100% - var(--base-min-padding) * 4);
        padding: calc(var(--base-min-padding) * 2) calc(var(--base-min-padding) * 2);
        height: calc(200px + var(--base-min-padding));
    }

    .market-detail-price {
        margin-top: calc(var(--base-min-padding));
    }

    .market-detail-price-number {
        font-size:26px;
    }


    .market-detail-today {
        margin-top: calc(var(--base-min-padding));
    }

    .market-defailt-bottom {
        margin-top: calc(var(--base-min-padding));
    }
}
</style>