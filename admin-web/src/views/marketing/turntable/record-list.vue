<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form :model="searchForm" ref="searchFormRef">
          <a-row :gutter="20">
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item :label-col-flex="labelColFlex" label="账号" prop="username_like">
                <a-input class="form-input-inline" v-model="searchForm.username_like" placeholder="请输入账号或手机号"
                  allow-clear />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item :label-col-flex="labelColFlex" label="代理商" prop="agent_id">
                <agent-search-select v-model="searchForm.agent_id"></agent-search-select>
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
      <div class="flex items-center">
        <span class="text-grey">总计：</span>
        <span>{{ countData }}</span>
      </div>
    </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small" :disabled="initLoading"><icon-refresh /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
        x: '100%',
        y: height - 39,
      }">
        <template #columns>
          <a-table-column title="账号" data-index="member" :width="120">
            <template #cell="{ record }">
              <div class="admin-box" v-if="record.member">
                <div>{{ record.member.username }}</div>
                <div>{{ record.member.mobile }}</div>
              </div>
            </template>
          </a-table-column>
          <a-table-column title="代理商" data-index="agent_real_name" align="left" :width="120">
            <template #cell="{ record }">
              <span v-if="record.agent_real_name">
                {{ record.agent_real_name }}
                ({{ record.agent_account }})
              </span>
              <span class="text-grey" v-else>无</span>
            </template>
          </a-table-column>
          <a-table-column title="状态" data-index="status" :width="140">
            <template #cell="{ record }">
              <div class="text-green" v-if="record.status == 1">中奖</div>
              <div v-else class="text-grey">未中奖</div>
            </template>
          </a-table-column>
          <a-table-column title="中奖金额" data-index="number" align="center" :width="120">
            <template #cell="{ record }">
              <div>${{ record.number }}</div>
            </template>
          </a-table-column>
          <a-table-column title="抽奖时间" data-index="create_time" :width="160">
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>
        </template>
      </a-table>
    </template>
    <template #footer>
      <page :listPage="listPage" @change="pageChange"></page>
      <memberRechargeBalanceComponent ref="memberRechargeBalanceComponentRef" @success="toInit(true)">
      </memberRechargeBalanceComponent>
    </template>
  </layout-body-content>
</template>

<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getTurntableRecordListApi } from "@/api/marketing/turntable-record";
import { useEnumStore, useAdminStore } from "@/store";
import { storeToRefs } from "pinia";

const { adminInfo } = storeToRefs(useAdminStore());
const labelColFlex = ref<string>("50px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  agent_id: string | number;
  create_time: any;
}>({
  username_like: "",
  agent_id: "all",
  create_time: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.agent_id = "all";
  searchForm.value.create_time = [];
  toInit(true);
};

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);

const countData = ref<any>(0);

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.agent_id = searchForm.value.agent_id;
  obj.create_time = searchForm.value.create_time;
  obj.username_like = searchForm.value.username_like;
  initLoading.value = true;
  getTurntableRecordListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      countData.value = res.data.count_data;
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

onMounted(() => {
  toInit();
});
</script>