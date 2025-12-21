<template>
    <layout-body-content pageHeader header>
        <template #header>
            <a-card class="card-form">
                <a-form :model="searchForm" ref="searchFormRef">
                    <a-row :gutter="20">
                        <a-col :md="12" :xs="24" :xl="6">
                            <a-form-item :label-col-flex="labelColFlex" label="代理商" prop="agent_id">
                                <agent-search-select v-model="searchForm.agent_id"></agent-search-select>
                            </a-form-item>
                        </a-col>
                        <a-col :md="12" :xs="24" :xl="6">
                            <a-form-item :label-col-flex="labelColFlex" label="用户账号" prop="username_like">
                                <a-input class="form-input-inline" v-model="searchForm.username_like"
                                    placeholder="请输入用户账号或手机号" allow-clear />
                            </a-form-item>
                        </a-col>
                        <a-col :md="12" :xs="24" :xl="6">
                            <a-form-item :label-col-flex="labelColFlex" label="跟单员" prop="person_like">
                                <a-input class="form-input-inline" v-model="searchForm.person_like"
                                    placeholder="请输入跟单员账号" allow-clear />
                            </a-form-item>
                        </a-col>
                        <a-col :md="12" :xs="24" :xl="6">
                            <a-form-item :label-col-flex="labelColFlex" label="状态" prop="status">
                                <a-select v-model="searchForm.status" placeholder="选择状态" allow-clear>
                                    <a-option value="all">全部</a-option>
                                    <a-option v-for="(item, index) in OrderStatusEnum" :key="index" :value="item.value"
                                        :label="item.name" />
                                </a-select>
                            </a-form-item>
                        </a-col>
                        <a-col :md="12" :xs="24" :xl="6">
                            <a-form-item :label-col-flex="labelColFlex" label="时间" prop="create_time">
                                <shortcuts-time v-model="searchForm.create_time" :btnShortcuts="false" />
                            </a-form-item>
                        </a-col>
                    </a-row>
                    <a-row :gutter="20">
                        <a-col>
                            <a-form-item :label-col-flex="labelColFlex">
                                <a-space>
                                    <a-button type="primary" @click="toInit()">查询</a-button>
                                    <a-button plain @click="onSearchReset()">重置</a-button>
                                    <a-button @click="toInit()" :disabled="initLoading">
                                        <icon-sync />
                                    </a-button>
                                </a-space>
                            </a-form-item>
                        </a-col>
                    </a-row>
                </a-form>
            </a-card>
            <div class="mt12"></div>
        </template>
        <template v-slot:page-header-left>
            <div>
                <span class="">跟单总金额:</span>
                <span class="">{{
                    Number(countData.money_count || 0).toFixed(2)
                    }}</span>
                <span class="ml20">累计收益:</span>
                <span class="">{{
                    Number(countData.total_revenue || 0).toFixed(2)
                    }}</span>

            </div>
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit(true)" size="small"><icon-refresh /></a-button>
                </a-tooltip>
            </a-space>
        </template>
        <template v-slot:content="{ height }">
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                x: '100%',
                y: height - 39,
            }" :table-layout-fixed="true">
                <template #columns>
                    <a-table-column title="用户账号" data-index="member" :width="120">
                        <template #cell="{ record }">
                            <member-column-detail :member="{
                                username: record.member.username,
                                mobile: record.member.mobile,
                                id: record.member_id,
                            }"></member-column-detail>
                        </template>
                    </a-table-column>
                    <a-table-column title="交易员" data-index="person" :width="240">
                        <template #cell="{ record }">
                            <member-column-detail :member="{
                                avatar: record.person?.avatar,
                                username: record.person?.nickname,
                                mobile: record.person?.mobile,
                                id: record.person?.id,
                            }"></member-column-detail>
                        </template>
                    </a-table-column>
                    <a-table-column title="跟单时间累计" data-index="create_day" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.create_day }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="收益累计" data-index="total_revenue" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.total_revenue }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="跟单金额" data-index="money" :width="160">
                        <template #cell="{ record }">
                            <div>{{ record.money }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
                        <template #cell="{ record }">
                            <div :class="`text-${record.status.color}`">{{ record.status.name }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="跟单开始时间" data-index="create_time" align="center" :width="160">
                        <template #cell="{ record }">
                            <div class="text-grey">{{ record.create_time }}</div>
                        </template>
                    </a-table-column>
                    <a-table-column title="操作" fixed="right" align="center" :width="320">
                        <template #cell="{ record }">
                            <a-space>
                                <div v-permission="'follow-order-endStatus'" v-if="record.status.value == 1">
                                    <a-popconfirm content="确定结束该订单吗？" @ok="onEnd(record.id)">
                                        <template #icon>
                                            <icon-exclamation-circle-fill type="red" />
                                        </template>
                                        <a-button size="small">结束</a-button>
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
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { endFollowOrderApi, getFollowOrderListApi } from "@/api/follow/order";
import { useEnumStore } from "@/store";

const labelColFlex = ref<string>("50px");


const OrderStatusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("follow.OrderStatusEnum"));

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
    username_like: string;
    person_like: string;
    create_time: any;
    status: any,
    agent_id: string | number | string[] | number[];
}>({
    username_like: "",
    person_like: "",
    create_time: [],
    status: "all",
    agent_id: [],
});

const onSearchReset = () => {
    proxy?.$refs["searchFormRef"]?.resetFields();
    searchForm.value.create_time = [];
  searchForm.value.agent_id = [];
    toInit(true);
};

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const countData = ref<any>({
    money_count: 0,
    total_revenue: 0
});

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    obj.create_time = searchForm.value.create_time;
    obj.person_like = searchForm.value.person_like;
    obj.username_like = searchForm.value.username_like;
  obj.agent_id = searchForm.value.agent_id;
    obj.status = searchForm.value.status;
    initLoading.value = true;
    getFollowOrderListApi(obj)
        .then((res: Result) => {
            initLoading.value = false;
            lists.value = res.data.data;
            listPage.value.total = res.data.total;
            countData.value.money_count = res.data.money_count || 0.0;
            countData.value.total_revenue = res.data.total_revenue || 0.0;
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

const onEnd = (id: number | string) => {
    endFollowOrderApi({ id })
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