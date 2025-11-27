<template>
    <div>
        <a-table :loading="initLoading" :data="orderComplateList" row-key="id" isLeaf :pagination="false" :scroll="{
            x: '100%',
            y: Number(useAppStore().orderBodyHeight - (isMobile ? 30 : 52))
        }" :table-layout-fixed="true" :size="isMobile ? 'small' : 'medium'">
            <template #columns>
                <a-table-column title="产品" data-index="ptitle" :width="100">
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
                <a-table-column title="平仓价格" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <span>{{ record.sellprice }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="盈亏" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <span :style="{
                            color: parseFloat(record.ploss) > 0 ? useMemberStore().getUpColor() : useMemberStore().getDownColor()
                        }">{{ record.ploss > 0 ? "+" : '' }}{{ record.ploss }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="平仓类型" data-index="sell_type" align="center" :width="80">
                    <template #cell="{ record }">
                        <span>{{ record.sell_type?.name }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="平仓时间" data-index="selltime" align="center" :width="160">
                    <template #cell="{ record }">
                        <span class="text-grey">{{ record.selltime || '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="开仓时间" data-index="create_time" align="center" :width="160">
                    <template #cell="{ record }">
                        <span class="text-grey">{{ record.create_time || '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="操作" fixed="right" align="center" :width="100">
                    <template #cell="{ record }">
                        <a-space>
                            <a-button size="small" @click="onDetail(record)">查看详细</a-button>
                        </a-space>
                    </template>
                </a-table-column>
            </template>
        </a-table>
        <memberOrderDetalModalComponent ref="memberOrderDetalModalComponentRef"></memberOrderDetalModalComponent>
    </div>
</template>

<script lang="ts">
export default {
    name: "member-order-complate",
};
</script>
<script lang="ts" setup>
import { getTodayComplateOrderSelectApi } from "@/api/order";
import { useMemberStore,useAppStore } from "@/store";
import { Result, ResultError } from "@/types";
import { ref, getCurrentInstance } from "vue";
import memberOrderDetalModalComponent from "@/components/member/order/order-detail.vue";
import { storeToRefs } from "pinia";
const { isMobile } = storeToRefs(useAppStore());


const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const orderComplateList = ref<any[]>([]);

const initLoading = ref<boolean>(false);

const memberOrderDetalModalComponentRef = ref<HTMLElement>();

const onDetail = (item: any) => {
    proxy?.$refs["memberOrderDetalModalComponentRef"]?.open(item.id)
}

const initTodayComplateOrder = () => {
    initLoading.value = true;
    getTodayComplateOrderSelectApi({}).then((res: Result) => {
        orderComplateList.value = res.data || []
        initLoading.value = false;
    }).catch((err: ResultError) => {
        $utils.errorMsg(err)
        initLoading.value = false;
    })
}


defineExpose({ initTodayComplateOrder });

</script>