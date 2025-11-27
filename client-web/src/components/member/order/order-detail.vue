<template>

    <a-modal :title="'交易详细'" @BeforeCancel="close" :width="'600px'" :top="useSetting().ModalTop" class="modal"
        v-model:visible="visible" :align-center="true" title-align="start" :mask-closable="false" render-to-body>
        <div class="order-detail" v-loading="initLoading">
            <div class="order-detail-left">
                <a-list>
                    <template #header>
                        <div class="order-detail-item">
                            <div class="text-gray">产品</div>
                            <div>{{ orderDetail.ptitle }}</div>
                        </div>
                    </template>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">平仓手数</div>
                            <div>{{ orderDetail.onumber }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">盈亏</div>
                            <div class="fw" :style="{
                                color: Number(orderDetail.ploss) > 0 ? useMemberStore().getUpColor() : useMemberStore().getDownColor()
                            }">{{ Number(orderDetail.ploss) > 0 ? '+' : '' }}{{ orderDetail.ploss }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item
                        v-if="orderDetail.mark_price && orderDetail.mark_price != 0 && orderDetail.mark_price != ''">
                        <div class="order-detail-item">
                            <div class="text-gray">标记价格</div>
                            <div>{{ orderDetail.trigger_price ? orderDetail.trigger_price : '--' }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item
                        v-if="orderDetail.mark_price && orderDetail.mark_price != 0 && orderDetail.mark_price != ''">
                        <div class="order-detail-item">
                            <div class="text-gray">触发价格</div>
                            <div>{{ orderDetail.mark_price ? orderDetail.mark_price : '--' }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">止盈价格</div>
                            <div>{{ orderDetail.surplus ? orderDetail.surplus : '--' }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">止损价格</div>
                            <div>{{ orderDetail.loss ? orderDetail.loss : '--' }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">交易费</div>
                            <div>{{ orderDetail?.fx_fee || 0 }}</div>
                        </div>
                    </a-list-item>
                </a-list>
            </div>
            <div class="order-detail-right">
                <a-list>
                    <template #header>
                        <div class="order-detail-item">
                            <div class="text-gray">开仓方向</div>
                            <div :class="`text-${orderDetail.ostyle?.color}`">{{ orderDetail.ostyle?.name }}</div>
                        </div>
                    </template>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">开仓价格</div>
                            <div>{{ orderDetail.buyprice }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">开仓时间</div>
                            <div>{{ orderDetail.buytime }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">平仓价格</div>
                            <div>{{ orderDetail.sellprice }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">平仓类型</div>
                            <div>{{ orderDetail.sell_type?.name }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">平仓时间</div>
                            <div>{{ orderDetail.selltime }}</div>
                        </div>
                    </a-list-item>
                    <a-list-item>
                        <div class="order-detail-item">
                            <div class="text-gray">订单号</div>
                            <div>{{ orderDetail.orderno }}</div>
                        </div>
                    </a-list-item>
                </a-list>
            </div>
        </div>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close()">关闭</a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "member-order-detail",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { getOrderDetailApi } from "@/api/order";
import { Result, ResultError } from "@/types";
import { useMemberStore } from "@/store";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const visible = ref<boolean>(false);

const orderId = ref<number | string>(0);

const orderDetail = ref<any>({});

const initLoading = ref<boolean>(false);

const toInit = () => {
    initLoading.value = true;
    getOrderDetailApi({
        id: orderId.value,
    }).then((res: Result) => {
        orderDetail.value = res.data || {};
        setTimeout(() => {
            initLoading.value = false;
        }, 500);
    }).catch((err: ResultError) => {
        $utils.errorMsg(err);
        close();

    });
};

const open = (id: number | string) => {
    visible.value = true;
    orderId.value = id;
    nextTick(() => {
        toInit();
    });
};

const close = () => {
    visible.value = false;
    initLoading.value = false;
    return true;
};

defineExpose({ open });
</script>

<style scoped>
.order-detail {
    display: flex;
    justify-content: space-between;
}

.order-detail-right,
.order-detail-left {
    width: calc(50% - 10px);
}

.order-detail-top {
    display: flex;
    justify-content: space-between;
}

.order-detail-item {
    display: flex;
    justify-content: space-between;
}
</style>