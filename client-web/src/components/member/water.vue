<template>
    <div class="member-water-body">
        <div :style="`height:${useAppStore().orderBodyHeight}px`">
            <a-table :data="lists" :loading="initLoading" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: Number(useAppStore().orderBodyHeight - (isMobile ? (32 + 30) : (40 + 46)))
            }" :table-layout-fixed="true" :size="isMobile ? 'small' : 'medium'">
                <template #columns>
                    <a-table-column title="编号" data-index="id" :width="160">
                        <template #cell="{ record }">
                            <span>#{{ record.id }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="账户类型" data-index="pay_type" align="center" :width="120">
                        <template #cell="{ record }">
                            <span>{{ record.pay_type?.name || "--" }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="增减" data-index="type" align="center" :width="80">
                        <template #cell="{ record }">
                            <div :class="`text-${record.type.color}`">
                                {{ record.type?.name || "--" }}
                            </div>
                        </template>
                    </a-table-column>
                    <a-table-column title="金额" align="center" data-index="money" :width="80">
                        <template #cell="{ record }">
                            <span>{{ record.money }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="来源" data-index="source" align="center" :width="120">
                        <template #cell="{ record }">
                            <div>{{ record.source?.name }}</div>
                        </template>
                    </a-table-column>
                    <!-- <a-table-column title="描述" data-index="describe" :width="200">
                        <template #cell="{ record }">
                            <span v-if="record.describe">
                                {{ record.describe }}
                            </span>
                            <span v-else class="text-grey">无</span>
                        </template>
                    </a-table-column> -->
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <span class="text-grey">{{ record.create_time || '--' }}</span>
                        </template>
                    </a-table-column>
                </template>
            </a-table>
        </div>
        <div class="member-watch-footer-page">
            <page :listPage="listPage" @change="pageChange"></page>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "water",
};
</script>
<script lang="ts" setup>
import { getWaterApi } from "@/api/member";
import { useSetting } from "@/hooks/useSetting";
import { PageLimitType, Result, ResultError } from "@/types";
import { computed, getCurrentInstance, ref, watch } from "vue";
import { useAppStore } from "@/store"
import { storeToRefs } from "pinia";

const { isMobile } = storeToRefs(useAppStore())



const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const lists = ref<any[]>([]);

const initLoading = ref<boolean>(false);

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    initLoading.value = true;
    let obj: {
        page: number;
        limit: number;
    } = {
        page: listPage.value.page,
        limit: Number(listPage.value.limit || useSetting().PageLimit.value),
    };
    getWaterApi(obj).then((res: Result) => {
        lists.value = res.data.data || []
        listPage.value.total = res.data.total;
        setTimeout(() => {
            initLoading.value = false;
        }, 300);
    }).catch((err: ResultError) => {
        $utils.errorMsg(err)
        initLoading.value = false;
    })
}


const listPage = ref<PageLimitType>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

defineExpose({ toInit });



</script>

<style scoped>
.member-water-body {
    position: relative;
    height: 100%;
}

.member-watch-footer-page {
    position: absolute;
    display: flex;
    justify-content: end;
    align-items: center;
    right: 1px;
    bottom: -10px;
    width: calc(100% - 2px);
    background-color: var(--color-bg-1);
    height: 42px;

}


@media screen and (max-height: 1060px),
screen and (max-width: 1199px) {
    .member-watch-footer-page {
        height: 32px;
        bottom: -4px;
    }
}
</style>