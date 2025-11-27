<template>
    <div>

        <a-table :data="orderRobot" :loading="isInitMemberStatus" row-key="id" isLeaf :pagination="false" :scroll="{
            x: '100%',
            y: Number(useAppStore().orderBodyHeight - (isMobile ? 32 : 52))
        }" :table-layout-fixed="true" :size="isMobile ? 'small' : 'medium'">
            <template #columns>
                <a-table-column title="产品" data-index="ptitle" :width="100">
                    <template #cell="{ record }">
                        <span>{{ record.ptitle }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="编号" data-index="id" :width="160">
                    <template #cell="{ record }">
                        <span>#{{ record.id }}</span>
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
                <a-table-column title="当前价" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">
                        <span v-if="record.pid">{{ useMarketStore().getMarketPrice(record.pid) }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="挂单价" data-index="buyprice" align="center" :width="120">
                    <template #cell="{ record }">

                        <span>{{ record.buyprice || '--' }}</span>
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
                <a-table-column title="挂单创建时间" data-index="create_time" align="center" :width="160">
                    <template #cell="{ record }">
                        <span class="text-grey">{{ record.create_time || '--' }}</span>
                    </template>
                </a-table-column>
                <a-table-column title="操作" fixed="right" align="right" :width="240">
                    <template #cell="{ record }">
                        <a-space>
                            <a-button size="small" :loading="record.loading" :disabled="record.loading"
                                @click="onClose(record)">撤单</a-button>
                            <a-button size="small" :disabled="record.loading" @click="onSetRobot(record)">改单</a-button>
                            <a-button size="small" :disabled="record.loading"
                                @click="onSelectMarketId(record)">查看行情</a-button>
                        </a-space>
                    </template>
                </a-table-column>
            </template>
        </a-table>
        <orderSetRobotComponent ref="orderSetRobotComponentRef"></orderSetRobotComponent>
    </div>
</template>

<script lang="ts">
export default {
    name: "member-order-hold",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch } from "vue";
import { cancelOrderRobotApi } from "@/api/order";
import { useChargeStore, useMarketStore, useMemberStore, useAppStore } from "@/store";
import { Result, ResultError } from "@/types";
import { storeToRefs } from "pinia";
import { Notification } from "@arco-design/web-vue";
import orderSetRobotComponent from "./order-set-robot.vue";

const { orderRobot } = storeToRefs(useChargeStore());
const { isInitMemberStatus } = storeToRefs(useMemberStore());
const { isMobile } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const onClose = (item: any) => {
    item.loading = true;
    cancelOrderRobotApi({
        id: item.id
    }).then((res: Result) => {
        Notification.success({
            title: res.message,
            content: "",
            duration: 1500,
            onClose: () => {
            },
        });
        // $utils.successMsg(res.message);
    }).catch((err: ResultError) => {

        Notification.error({
            title: "撤单失败!",
            content: String(err.data?.message || ""),
            duration: 1500,
            onClose: () => {
            },
        });
        item.loading = false;
    });
}

const onSelectMarketId = (item: any) => {
    useMarketStore().setMarketId(item.pid);
}



const orderSetRobotComponentRef = ref<HTMLElement>();

const onSetRobot = (item: any) => {
    proxy?.$refs["orderSetRobotComponentRef"]?.open(2, item.id)
}
</script>