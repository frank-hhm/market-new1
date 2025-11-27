<template>
    <div class="order-history-record" v-loading="initLoading">
        <a-form layout="horizontal" :model="searchForm" ref="searchFormRef">
            <div class="form-row">
                <div class="form-row-left">
                    <div class="form-item">
                        <a-form-item hide-label prop="create_time">
                            <shortcuts-time v-model="searchForm.time" :btnShortcuts="false" ref="searchTimeRef"
                                format="YYYY-MM-DD" :default="[-1, 1, 3, 7, 30]" :isShowTime="false"
                                @change="onChangeTime()" :max="31" />
                        </a-form-item>
                    </div>
                    <!-- <div class="form-item">
                        <a-form-item hide-label>
                            <a-space>
                                <a-button size="small" type="primary" @click="toInit()">查询</a-button>
                                <a-button size="small" plain @click="onSearchReset()">重置</a-button>
                            </a-space>
                        </a-form-item>
                    </div> -->
                </div>
                <div class="form-row-right">
                    <div class="form-row-right-item">
                        <div class="text-gray">平仓总手数:</div>
                        <div>{{ Number(totalCount).toFixed(1) }}手</div>
                    </div>
                    <div class="form-row-right-item ml10">
                        <div class="text-gray">总盈亏:</div>
                        <div :style="{
                            color: Number(totalPluss) > 0 ? useMemberStore().getUpColor() : useMemberStore().getDownColor()
                        }">{{ Number(totalPluss) > 0 ? '+' : '' }}${{ Number(totalPluss).toFixed(2) }}</div>
                    </div>
                </div>
            </div>
        </a-form>
        <div>
            <a-table :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: Number(useAppStore().orderBodyHeight - (isMobile ? (30 + 40) : (40 + 56)))
            }" :table-layout-fixed="true" :size="isMobile ? 'small' : 'medium'">
                <template #columns>
                    <a-table-column title="产品" data-index="ptitle" :width="100">
                        <template #cell="{ record }">
                            <span>{{ record.ptitle }}</span>
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
        </div>
        <memberOrderDetalModalComponent ref="memberOrderDetalModalComponentRef"></memberOrderDetalModalComponent>
    </div>
</template>
<script lang="ts">
export default {
    name: "member-order-history-record",
};
</script>
<script lang="ts" setup>
import { getCurrentInstance, nextTick, ref } from 'vue';
import { Result, ResultError } from '@/types';
import { historyRecordApi } from '@/api/order';
import { useMemberStore, useAppStore } from "@/store";
import memberOrderDetalModalComponent from "@/components/member/order/order-detail.vue";
import { storeToRefs } from 'pinia';
const { isMobile } = storeToRefs(useAppStore());


const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const searchFormRef = ref<HTMLElement>();

const getToday = () => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return [String(`${year}-${month}-${day}`), String(`${year}-${month}-${day}`)];
}


const searchForm = ref<{
    time: string[];
}>({
    time: getToday(),
});

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);

const totalCount = ref<number | string>(0.0);
const totalPluss = ref<number | string>(0.00);

const onChangeTime = () => {
    nextTick(() => {
        toInit();
    })
};
const toInit = () => {
    initLoading.value = true;
    let obj: any = {};
    if (searchForm.value.time !== undefined && searchForm.value.time[0] && searchForm.value.time[1]) {

        obj.start_date = searchForm.value.time[0];
        obj.end_date = searchForm.value.time[1];
    } else {
        let today = getToday();
        obj.start_date = today[0];
        obj.end_date = today[1];
        nextTick(() => {
            searchForm.value.time = today;
        });
    }
    historyRecordApi(obj).then((res: Result) => {
        lists.value = res.data.list || []
        totalCount.value = res.data.onumber_sum || 0
        totalPluss.value = res.data.ploss_sum || 0.00
        initLoading.value = false;
    }).catch((err: ResultError) => {
        $utils.errorMsg(err)
        initLoading.value = false;
    })
}

const searchTimeRef = ref<HTMLElement>();
const onSearchReset = () => {
    proxy?.$refs["searchFormRef"]?.resetFields()
    searchForm.value.time = []
    nextTick(() => {
        proxy?.$refs["searchTimeRef"]?.onClear()
    })
    toInit();
}

const memberOrderDetalModalComponentRef = ref<HTMLElement>();

const onDetail = (item: any) => {
    proxy?.$refs["memberOrderDetalModalComponentRef"]?.open(item.id)
}


defineExpose({ toInit });
</script>

<style scoped>
.form-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-row-left {
    width: calc(80% - 340px);
    display: flex;
}

.form-item {
    margin-right: 20px;
}

.form-row-right {
    width: 320px;
    display: flex;
    align-items: center;
    justify-content: end;
}

.form-row-right-item {
    display: flex;
}
</style>

<style>
@media screen and (max-height: 1060px) {
    .order-history-record .form-row .form-row-left .arco-form-item {
        margin-bottom: var(--base-min-padding);
    }
}
</style>