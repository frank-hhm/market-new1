<template>
    <layout-body-content pageHeader>
        <template v-slot:page-header-left> 跟单员列表 </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit(true)" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button type="primary" @click="onCreate(0)" size="small"
                    v-permission="'follow-person-create'">添加交易员</a-button>
            </a-space>
            <createComponent ref="createComponentRef" @success="toInit(true)"></createComponent>
        </template>
        <template v-slot:content="{ height }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: height - 39,
            }" :table-layout-fixed="true">
                <template #columns>
                    <a-table-column title="关联账号" data-index="avatar" :width="140">
                        <template #cell="{ record }">
                           <member-column-detail v-if="record.member" :member="record.member" isId></member-column-detail>
                           <div  v-else class="text-red">未绑定</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="头像" data-index="avatar" :width="60">
                        <template #cell="{ record }">
                            <a-image :src="record.avatar" :height="40" fit="cover" :width="40" />
                        </template>
                    </a-table-column>
                    <a-table-column title="姓名" data-index="nickname" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.nickname }}</div>
                            <div class="text-grey">{{ record.signature }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="当前跟单人数" data-index="member_count" :width="160" align="center">
                        <template #cell="{ record }">
                            <div>{{ record.member_count | 0 }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="近1月收益率" data-index="revenue_text" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.revenue_text }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="近1月胜率" data-index="month_win_rate_text" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.month_win_rate_text }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="近1月分润(usd)" data-index="month_profit_text" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.month_profit_text }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="累计分润率" data-index="total_profit_text" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.total_profit_text }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="交易员保证金(usd)" data-index="deposit_text" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.deposit_text }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
                        <template #cell="{ record }">
                            <a-switch v-model="record.status.value" v-permission-disabled="'banner-status'" size="small"
                                type="round" :loading="record.loading" :beforeChange="() => {
                                    return (record.switch = true);
                                }
                                    " @change="onStatusChange($event, record)" :checked-value="1"
                                :unchecked-value="0" />
                        </template>
                    </a-table-column>
                    <a-table-column title="操作" fixed="right" align="center" :width="320">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click="onCreate(record.id)" v-permission="'follow-person-update'"
                                    size="small">编辑</a-button>
                                <div v-permission="'follow-person-delete'">
                                    <a-popconfirm content="确定删除吗？" @ok="onDelete(record.id)">
                                        <template #icon>
                                            <icon-exclamation-circle-fill type="red" />
                                        </template>
                                        <a-button size="small">删除</a-button>
                                    </a-popconfirm>
                                </div>
                            </a-space>
                        </template>
                    </a-table-column>
                </template>
            </a-table>
        </template>
        <template #footer>
            <page :listPage="listPage" @change="pageChange"></page>
        </template>
    </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import createComponent from "./create.vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
    getFollowPersonListApi,
    updateStatusFollowPersonApi,
    deleteFollowPersonApi,
} from "@/api/follow/person";
import tradeComponent from "./trade.vue";



const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const createComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
    proxy?.$refs["createComponentRef"]?.open(id);
};

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    initLoading.value = true;
    getFollowPersonListApi(obj)
        .then((res: Result) => {
            initLoading.value = false;
            lists.value = res.data.data;
            listPage.value.total = res.data.total;
            setTimeout(() => {
                initLoading.value = false;
            }, 300);
        })
        .catch((error: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(error);
        });
};

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const onStatusChange = (val: any, row: any) => {
    if (row.switch === true) {
        row.loading = true;
        updateStatusFollowPersonApi({
            id: row.id,
            status: val,
        })
            .then((res: Result) => {
                row.loading = false;
                toInit();
                $utils.successMsg(res);
            })
            .catch((err: ResultError) => {
                row.loading = false;
                toInit();
                $utils.errorMsg(err);
            });
    }
};

const onDelete = (id: number | string) => {
    deleteFollowPersonApi({ id })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
};

onMounted(() => {
    toInit();
});

const tradeComponentRef = ref<HTMLElement>()

const onTrade = (id: number | string) => {
    proxy?.$refs["tradeComponentRef"]?.open(id)
}

</script>