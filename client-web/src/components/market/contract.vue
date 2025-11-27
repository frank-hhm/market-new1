<template>
    <a-modal :title="'合约属性'" @BeforeCancel="close" :width="'600px'" class="modal" v-model:visible="visible"
        :align-center="true" title-align="start" render-to-body>
        <div class="market-contract" v-loading="initLoading">
            <div class="contract-item">
                <div class="title">合约单位</div>
                <div class="desc">{{ productDetail?.heyue_danwei || '无配置' }}</div>
            </div>
            <div class="contract-item2">
                <div class="contract-items">
                    <div class="title">货币单位</div>
                    <div class="desc">{{ productDetail?.money_danwei || '无配置' }}</div>
                </div>
                <div class="contract-items">
                    <div class="title">点差</div>
                    <div class="desc">{{ productDetail?.dian_cha || '无配置' }}</div>
                </div>
                <div class="contract-items">
                    <div class="title">单笔交易数</div>
                    <div class="desc">{{ productDetail?.buy_min || '无配置' }}手~{{ productDetail?.buy_max || '无配置' }}手
                    </div>
                </div>
            </div>
            <div class="contract-item">
                <div class="title">报价小数点</div>
                <div class="desc">{{ productDetail?.decimal || '无配置' }}</div>
            </div>
            <div class="tips-text">注：例如数值为2，表示报价为2位小数点，即{{ Number(2).toFixed(productDetail?.xiaoshu) }}。</div>

            <div class="contract-item">
                <div class="title">单手保证金</div>
                <div class="desc">{{ productDetail?.pay_choose || '无配置' }}USD/手</div>
            </div>
            <div class="tips-text">注：建仓时，您所付出作为买卖双方确保履约的担保费用;该产品为固定保证金。</div>
            <div class="contract-item">
                <div class="title">强制保证金</div>
                <div class="desc">{{ productDetail?.baozhengjin_rate || '无配置' }}%</div>
            </div>
            <div class="tips-text">注：当你的保证金比例低于该数值，会平仓亏损最大的持仓订单。</div>
            <div class="contract-item">
                <div class="title">隔夜保证金</div>
                <div class="desc">{{ productDetail?.geye_baozhengjin_rate || '无配置' }}%</div>
            </div>
            <div class="tips-text">注：当时间至结算时间你的保证金比例低于该数值，会平仓亏损最大的持仓订单。</div>
            <div class="contract-item time">
                <div class="title">
                    <div class="text">交易时间</div>
                    <div class="text">GMT+0</div>
                </div>
                <div class="desc">{{ productDetail?.opentimetext || '无配置' }}</div>
            </div>
            <div class="contract-item">
                <div class="title">结算时间
                </div>
                <div class="desc">{{ productDetail?.selldate || '无配置' }}(GMT+0)</div>
            </div>
            <div class="tips-text red">注：本公司保留对以上数据可因市场情况而调整的权利</div>
        </div>

        <template #footer>
            <a-space>
                <a-button @click="close()">关闭</a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script lang="ts">
export default {
    name: "market-contract",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { useMarketStore } from "@/store";
import { getProductParamsDetailApi } from "@/api/product";
import { Result, ResultError } from "@/types";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(true);

const productDetail = ref<any>({})

const toInitProductDetail = () => {
    initLoading.value = true;
    let id = useMarketStore().selectMarketId
    getProductParamsDetailApi(id).then((res: Result) => {
        productDetail.value = res.data
        initLoading.value = false;
    }).catch((err: ResultError) => {
        $utils.errorMsg(err);
        setTimeout(() => {
            close();
        }, 1000);

    })
}

const open = () => {
    productDetail.value = {}
    toInitProductDetail();
    visible.value = true;
};

const close = () => {
    visible.value = false;
    return true;
};


defineExpose({ open });
</script>


<style scoped>
.market-contract {
    min-height: 500px;
}


.contract-item {
    margin: calc(var(--base-padding));
    width: calc(100% - var(--base-padding) * 4);
    line-height: 24px;
    background: var(--color-fill-2);
    display: flex;
    align-items: center;
    font-size: var(--base-size-default);
    border-radius: var(--base-radius-default);
    padding: calc(var(--base-padding));
}

.contract-item.time {
    align-items: normal;
    height: auto;
}

.contract-item .title {
    font-size: var(--base-size-small);
    width: 80px;
    color: rgb(var(--gray-6));
}

.contract-item .desc {
    font-size: var(--base-size-default);
    color: var(--color-text-1);
}

.contract-item2 {
    width: calc(100% - var(--base-padding) * 2);
    margin: calc(var(--base-padding));
    display: flex;
    justify-content: space-between;

}

.contract-item2 .contract-items {
    width: calc(33.33% - var(--base-padding) * 4);
    background: var(--color-fill-2);
    padding: calc(var(--base-padding));
    border-radius: var(--base-radius-default);
    text-align: center;
}

.contract-item2 .contract-items .title {
    font-size: var(--base-size-small);
    color: rgb(var(--gray-6));
}

.contract-item2 .contract-items .desc {
    font-size: var(--base-size-default);
    color: var(--color-text-1);
}

.tips-text {
    margin-left: var(--base-padding);
    color: rgb(var(--gray-6));
}

.tips-text.red {
    color: rgb(var(--red-6));
}
</style>