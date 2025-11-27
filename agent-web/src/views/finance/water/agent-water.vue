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
                label="描述"
                prop="desc"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.desc"
                  placeholder="请输入描述"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="类型"
                prop="pay_type"
              >
                <a-select
                  v-model="searchForm.pay_type"
                  placeholder="选择类型"
                  allow-clear
                >
                  <a-option
                    v-for="(item, index) in rechargePayTypeEnum"
                    :key="index"
                    :value="item.value"
                    :label="item.name"
                  />
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
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="时间"
                prop="create_time"
              >
                <shortcuts-time
                  v-model="searchForm.create_time"
                  :btnShortcuts="false"
                />
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
        <template v-for="(item, index) in countData" :key="index">
          <span class="text-grey" :class="index > 0 ? 'ml20' : ''"
            >{{ item.name }}:</span
          >
          <span>{{ item.value || 0 }}</span>
        </template>
      </div>
    </template>
    <template v-slot:page-header-right
      ><a-space>
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
      >
        <template #columns>
            
          <a-table-column
            title="用户"
            data-index="member"
            :width="120"
          >
            <template #cell="{ record }">
              <member-column-detail
                :member="record.member"
              ></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column
            title="代理商"
            data-index="agent_real_name"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <agent-column-detail :agent="record.agent"></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column
            title="金额"
            data-index="money"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.money }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="账户类型"
            data-index="pay_type"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.pay_type?.name || "--" }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="操作类型"
            data-index="type"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <div :class="`text-${record.type.color}`">
                {{ record.type?.name || "--" }}
              </div>
            </template>
          </a-table-column>
          <a-table-column
            title="来源"
            data-index="source"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.source?.name }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="改前数据"
            data-index="member_balance"
            align="center"
            :width="100"
          >
            <template #cell="{ record }">
              <span>
                {{ record.member_balance }}
              </span>
            </template>
          </a-table-column>
          <a-table-column
            title="改后数据"
            data-index="balance"
            align="center"
            :width="100"
          >
            <template #cell="{ record }">
              <span>
                {{ record.balance }}
              </span>
            </template>
          </a-table-column>
          <a-table-column title="时间" data-index="create_time" :width="160">
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>
          <a-table-column title="描述" data-index="describe" :width="200">
            <template #cell="{ record }">
              <span v-if="record.describe">
                {{ record.describe }}
              </span>
              <span v-else class="text-grey">无</span>
            </template>
          </a-table-column>
          <a-table-column title="备注" data-index="remark" :width="120">
            <template #cell="{ record }">
              <div v-if="record.remark">
                {{ record.remark }}
              </div>
              <div v-else class="text-grey te2">无</div>
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
import { getFinanceGgentWaterListApi } from "@/api/finance/water";
import { useEnumStore } from "@/store";
const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("50px");

const sourceEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("finance.SourceEnum")
);
const rechargePayTypeEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("finance.RechargePayTypeEnum")
);

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  desc: string;
  pay_type: any;
  agent_id: string | number | string[] | number[];
  create_time: any;
}>({
  username_like: "",
  desc: "",
  pay_type: null,
  agent_id: [],
  create_time: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.pay_type = null;
  toInit(true);
};

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);


const countData = ref<any[]>([]);

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.username_like = searchForm.value.username_like;
  obj.desc = searchForm.value.desc;
  obj.pay_type = searchForm.value.pay_type;
  obj.agent_id = searchForm.value.agent_id;
  obj.create_time = searchForm.value.create_time;
  initLoading.value = true;
  getFinanceGgentWaterListApi(obj)
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