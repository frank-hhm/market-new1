<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form layout="horizontal" :model="searchForm" ref="searchFormRef">
          <a-row :gutter="20">
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="账号"
                prop="username_like"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.username_like"
                  placeholder="请输入账号或手机号"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="模拟类型"
                prop="moni"
              >
                <a-select
                  v-model="searchForm.moni"
                  placeholder="选择类型"
                  allow-clear
                >
                  <a-option value="all" label="全部" />
                  <a-option value="0" label="实盘" />
                  <a-option value="1" label="模拟" />
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="代理商"
                prop="agent_id"
              >
                <agent-search-select
                  v-model="searchForm.agent_id"
                ></agent-search-select>
              </a-form-item>
            </a-col>
          </a-row>
          <a-row :gutter="20">
            <a-col>
              <a-form-item :label-col-flex="labelColFlex">
                <a-space>
                  <a-button type="primary" @click="toInit()">查询</a-button>
                  <a-button plain @click="onSearchReset()">重置</a-button>
                </a-space>
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </a-card>
      <div class="mt12"></div>
    </template>
    <template v-slot:page-header-left>
      <div class="flex items-center">
        <span class="text-grey">余额:</span>
        <span>{{ countData.balance || 0 }}</span>
        <span class="ml20 text-grey">可用：</span>
        <span>{{ countData.keyong || 0 }}</span>
        <span class="ml20 text-grey">盈亏：</span>
        <span>{{ countData.yingkui || 0 }}</span>
      </div>
    </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small" :disabled="initLoading"
            ><icon-refresh
          /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <a-table
        :loading="initLoading"
        :data="lists"
        row-key="id"
        isLeaf
        :pagination="false"
        :scroll="{
          x: '100%',
          y: height - 39,
        }"
        :table-layout-fixed="true"
        @sorter-change="onTableSorterChange"
      >
        <template #columns>
          <a-table-column title="ID" data-index="member" :width="50">
            <template #cell="{ record }">
              {{ record.member_id }}
            </template>
          </a-table-column>
          <a-table-column
            title="账号类型"
            data-index="member"
            :width="60"
            align="center"
          >
            <template #cell="{ record }">
              <a-tag v-if="record.member?.moni == 1" color="red" size="small"
                >模拟</a-tag
              >
              <a-tag v-else color="green" size="small">实</a-tag>
            </template>
          </a-table-column>
          <a-table-column title="账号" data-index="member" :width="120">
            <template #cell="{ record }">
              <member-column-detail
                :member="record.member"
              ></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column
            title="代理商"
            data-index="member"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <agent-column-detail
                :agent="record.member.agent"
              ></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column
            title="总余额"
            data-index="balance"
            align="center"
            :width="120"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.balance || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="当前可用"
            data-index="keyong"
            :width="120"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.keyong || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="跟单资产"
            data-index="follow_balance"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.follow_balance || 0 }}</div>
            </template>
          </a-table-column>
          
          <a-table-column
            title="盈亏"
            data-index="yingkui_total"
            :width="180"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.yingkui_total || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="当前净值"
            data-index="jingzhi"
            align="center"
            :width="120"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.jingzhi }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="佣金余额"
            data-index="commission_balance"
            align="center"
            :width="120"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.commission_balance || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="佣金累计"
            data-index="commission_total"
            align="center"
            :width="120"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.commission_total || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column title="操作" fixed="right" align="center" :width="70">
            <template #cell="{ record }">
              <a-space>
                <a-button
                  size="small"
                  v-permission="'finance-member_coin-recharge'"
                  @click="onRecharge(record.id)"
                  >充值</a-button
                >
              </a-space>
            </template>
          </a-table-column>
        </template>
      </a-table>
    </template>
    <template #footer>
      <page :listPage="listPage" @change="pageChange"></page>
      <memberRechargeBalanceComponent
        ref="memberRechargeBalanceComponentRef"
        @success="toInit(true)"
      ></memberRechargeBalanceComponent>
    </template>
  </layout-body-content>
</template>

<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getFinanceCoinListApi } from "@/api/finance/coin";
import memberRechargeBalanceComponent from "./recharge-balance.vue";
import { useAdminStore } from "@/store/modules/admin";
import { storeToRefs } from "pinia";
const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("50px");

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  moni: string;
  agent_id: string | number | string[] | number[];
}>({
  username_like: "",
  moni: "all",
  agent_id: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.agent_id = [];
  sortFields.value = {};
  sortField.value = "";
  sortFieldValue.value = "";
  toInit(true);
};

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);

const countData = ref<any>({
  balance: 0.0,
  futures_balance: 0.0,
  shares_balance: 0.0,
  futures_keyong: 0.0,
  futures_yingkui: 0.0,
  shares_yingkui: 0.0,
  shares_keyong: 0.0,
});

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.username_like = searchForm.value.username_like;
  obj.moni = searchForm.value.moni;
  obj.agent_id = searchForm.value.agent_id;
  if (sortField.value && sortFields.value) {
    obj.table_sorter = {
      [sortField.value]: sortFieldValue.value,
    };
  }
  initLoading.value = true;
  getFinanceCoinListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      countData.value.count = res.data.count;
      countData.value.balance = res.data.balance;
      countData.value.keyong = res.data.keyong;
      countData.value.yingkui = res.data.yingkui;
      countData.value.jingzhi = res.data.jingzhi;
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

const sortFields = ref<any>({});
const sortField = ref<string>("");
const sortFieldValue = ref<string>("");

const onTableSorterChange = (dataIndex: string, direction: string) => {
  sortField.value = dataIndex;
  //   sortFieldValue.value = direction === "ascend" ? "asc" : "desc";
  if (direction) {
    sortFieldValue.value = direction === "ascend" ? "asc" : "desc";
  } else {
    sortFieldValue.value = "";
  }
  //   const newSort = { ...sortFields.value };
  //   if (direction) {
  //     newSort[dataIndex] = direction === 'ascend' ? 'asc' : 'desc';
  //   } else {
  //     delete newSort[dataIndex];
  //   }
  //   sortFields.value = newSort;
  toInit(true);
};

onMounted(() => {
  toInit();
});

const memberRechargeBalanceComponentRef = ref<HTMLElement>();

const onRecharge = (id: number) => {
  proxy?.$refs["memberRechargeBalanceComponentRef"]?.open(id);
};
</script>