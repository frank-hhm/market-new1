<template>
    <div>

        <div>
            <div>test</div>
            <a-table size="mini"></a-table>
            <a-table size="medium"></a-table>
            <a-table></a-table>
        </div>
        <a-table :data="orderHold" :loading="isInitMemberStatus" row-key="id" isLeaf :pagination="false" :scroll="{
            x: '100%',
            y: Number(useAppStore().orderBodyHeight - (isMobile ? 30 : 39))
        }" :table-layout-fixed="true" :size="isMobile ? 'small' : 'medium'">
            <template #columns>
                <a-table-column title="产品11" data-index="ptitle" :width="100">
                    <template #cell="{ record }">
                        <span>{{ record.ptitle }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="订单号" data-index="orderno" :width="160">
                    <template #cell="{ record }">
                        <span>{{ record.orderno }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="方向" data-index="ostyle" :width="80">
                    <template #cell="{ record }">
                        <span :class="`text-` + record.ostyle?.color">{{ record.ostyle?.name }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="手数" data-index="onumber" align="center" :width="60">
                    <template #cell="{ record }">
                        <span>{{ record.onumber }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="开仓价" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <span>{{ record.buyprice }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="当前价" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <span v-if="record.pid">{{ useMarketStore().getMarketPrice(record.pid) }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="盈亏" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                    </template>
                </a-table-column>
                <a-table-column title="委托标记" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <div v-if="record.mark_price && record.mark_price != '' && record.mark_price != 0">
                            <span class="mr10">触发：</span>
                            <span class="">{{ record.mark_price > record.trigger_price ? '>=' : '<=' }}{{
                                record.mark_price }}</span>
                        </div>
                        <span v-else>--</span>
                    </template>
                </a-table-column>
                <a-table-column title="止损" data-index="loss" align="center" :width="120">
                    <template #cell="{ record }">
                        <span>{{ record.loss ? record.loss : '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="止赢" data-index="surplus" align="center" :width="120">
                    <template #cell="{ record }">
                        <span>{{ record.surplus ? record.surplus : '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="开仓时间" data-index="create_time" align="center" :width="160">
                    <template #cell="{ record }">
                        <span class="text-grey">{{ record.create_time || '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="操作" fixed="right" align="right" :width="240">
                    <template #cell="{ record }">
                        <a-space>
                            <a-button size="small" :loading="record.loading" :disabled="record.loading"
                                @click="onSell(record)">平仓</a-button>
                            <a-button size="small" :disabled="record.loading"
                                @click="onSetRobot(record)">止盈止损</a-button>
                            <a-button size="small" :disabled="record.loading"
                                @click="onSelectMarketId(record)">查看行情</a-button>
                        </a-space>
                    </template>
                </a-table-column>
            </template>
        </a-table>
    </div>
</template>

<script lang="ts">
export default {
    name: "member-order-hold",
};
</script>
<script lang="ts" setup>
import { orderSellApi } from "@/api/order";
import { useChargeStore, useMarketStore, useMemberStore, useAppStore } from "@/store";
import { Result, ResultError } from "@/types";
import { storeToRefs } from "pinia";
import { ref, getCurrentInstance } from "vue";
import { initMember } from "@/utils";

const { orderHold } = storeToRefs(useChargeStore());
const { isInitMemberStatus } = storeToRefs(useMemberStore());
const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const onSell = (item: any) => {
    item.loading = true;
}

const onSelectMarketId = (item: any) => {
    useMarketStore().setMarketId(item.pid);
}


const orderSetRobotComponentRef = ref<HTMLElement>();

const onSetRobot = (item: any) => {
    proxy?.$refs["orderSetRobotComponentRef"]?.open(1, item.id)
}
</script>