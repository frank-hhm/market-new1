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
                label="方向"
                prop="ostyle"
              >
                <a-select
                  v-model="searchForm.ostyle"
                  placeholder="选择方向"
                  allow-clear
                >
                  <a-option
                    v-for="(item, index) in ostyleEnum"
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
                label="是否委托"
                prop="is_wei"
              >
                <a-select
                  v-model="searchForm.is_wei"
                  placeholder="选择状态"
                  allow-clear
                >
                  <a-option value="all" label="全部" />
                  <a-option :value="0" label="市价" />
                  <a-option :value="1" label="委托" />
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24"  :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="建仓时间"
                prop="buytime"
              >
                <shortcuts-time
                  v-model="searchForm.buytime"
                  :btnShortcuts="false"
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24"  :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="平仓时间"
                prop="selltime"
              >
                <shortcuts-time
                  v-model="searchForm.selltime"
                  :btnShortcuts="false"
                />
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
              <a-form-item :label-col-flex="labelColFlex">
                <a-space>
                  <a-button type="primary" @click="toInit()">查询</a-button>
                  <a-button plain @click="onSearchReset()">重置</a-button>
                  <a-button
                    @click="setAuto"
                    :status="isAuto ? 'success' : 'normal'"
                  >
                    <div class="flex items-center">
                      <icon-pause-circle-fill v-if="isAuto" class="mr4" />
                      <div>{{ isAuto ? "关闭" : "自动刷新" }}</div>
                    </div>
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
        <span class="">盈亏统计:</span>
        <span class="">{{
          Number(countData.money_profit || 0).toFixed(2)
        }}</span>
        <span class="ml20">交易手数:</span>
        <span class="">{{ countData.onumber }}</span>
        <span class="ml20">委托金额:</span>
        <span class="">{{ countData.money_fee }}</span>
        <span class="ml20">手续费:</span>
        <span class="">{{ countData.money_sx_fee }}</span>
        <span class="ml20">手续费代理:</span>
        <span class="">{{ countData.yong_agents_money }}</span>
      </div>
    </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
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
          <a-table-column title="订单编号" data-index="orderno" :width="160">
            <template #cell="{ record }">
              <div>{{ record.orderno }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="代理商"
            data-index="agent_id"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <agent-column-detail :agent="record.agent"></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column
            title="账号"
            data-index="member_username"
            :width="120"
          >
            <template #cell="{ record }">
              <member-column-detail
                :member="{
                  username: record.member_username,
                  mobile: record.member_mobile,
                  id: record.member_id,
                }"
              ></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="产品" data-index="ptitle" :width="120">
            <template #cell="{ record }">
              <div>{{ record.ptitle }}</div>
            </template>
          </a-table-column>
          <a-table-column title="状态" data-index="ostaus" :width="60">
            <template #cell="{ record }">
              <div>{{ record.ostaus.name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="类型" data-index="sys_ping" :width="80">
            <template #cell="{ record }">
              <div>{{ record.sys_ping.name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="方向" data-index="ostyle" :width="80">
            <template #cell="{ record }">
              <div :class="`text-${record.ostyle.color}`">
                {{ record.ostyle.name }}
              </div>
            </template>
          </a-table-column>
          <a-table-column title="建仓点位" data-index="buyprice" :width="120">
            <template #cell="{ record }">
              <div class="nowrap">{{ record.buyprice }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="止盈"
            align="center"
            data-index="surplus"
            :width="120"
          >
            <template #cell="{ record }">
              <div v-if="record.surplus">{{ record.surplus }}</div>
              <div v-else class="text-grey">无</div>
            </template>
          </a-table-column>
          <a-table-column
            title="止损"
            align="center"
            data-index="loss"
            :width="120"
          >
            <template #cell="{ record }">
              <div v-if="record.loss">{{ record.loss }}</div>
              <div v-else class="text-grey">无</div>
            </template>
          </a-table-column>
          <a-table-column title="平仓点位" data-index="sellprice" :width="120">
            <template #cell="{ record }">
              <div class="nowrap">{{ record.sellprice }}</div>
            </template>
          </a-table-column>
          <a-table-column title="委托额" data-index="fee" :width="120">
            <template #cell="{ record }">
              <div>{{ record.fee }}</div>
            </template>
          </a-table-column>
          <a-table-column title="手续费" data-index="sx_fee" :width="80">
            <template #cell="{ record }">
              <div>{{ record.sx_fee }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="手数"
            align="center"
            data-index="onumber"
            :width="60"
          >
            <template #cell="{ record }">
              <div>{{ record.onumber }}</div>
            </template>
          </a-table-column>
          <a-table-column title="实际盈亏" data-index="ploss" :width="120">
            <template #cell="{ record }">
              <div>{{ record.ploss }}</div>
            </template>
          </a-table-column>
          <a-table-column title="建仓时间" data-index="buytime" :width="160">
            <template #cell="{ record }">
              <div class="nowrap text-grey">{{ record.buytime }}</div>
            </template>
          </a-table-column>
          <a-table-column title="平仓时间" data-index="selltime" :width="160">
            <template #cell="{ record }">
              <div class="nowrap text-grey">{{ record.selltime }}</div>
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
import { getOrderMoniPinListApi } from "@/api/order/order";
import { useEnumStore, useAdminStore } from "@/store";
import { storeToRefs } from "pinia";
const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("50px");

const ostyleEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("order.OstyleEnum")
);

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  ostyle: any;
  buytime: string[];
  selltime: string[];
  agent_id: string | number | string[] | number[];
  is_wei: string | number;
}>({
  username_like: "",
  ostyle: null,
  buytime: [],
  selltime: [],
  agent_id: [],
  is_wei: "all",
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.ostyle = null;
  searchForm.value.buytime = [];
  searchForm.value.agent_id = [];
  searchForm.value.selltime = [];
  toInit(true);
};

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const countData = ref<any>({
  money_profit: 0,
  money_sx_fee: 0,
  money_fee: 0,
  onumber: 0,
  yong_agents_money: 0,
});

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.username_like = searchForm.value.username_like;
  obj.ostyle = searchForm.value.ostyle;
  obj.buytime = searchForm.value.buytime;
  obj.agent_id = searchForm.value.agent_id;
  obj.selltime = searchForm.value.selltime;
  obj.is_wei = searchForm.value.is_wei;
  initLoading.value = true;
  getOrderMoniPinListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      countData.value.money_profit = res.data.money_profit || 0.0;
      countData.value.money_sx_fee = res.data.money_sx_fee || 0.0;
      countData.value.money_fee = res.data.money_fee || 0.0;
      countData.value.onumber = res.data.onumber || 0;
      countData.value.yong_agents_money = res.data.yong_agents_money || 0.0;

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

const timer = ref<any>(null);

const isAuto = ref<boolean>(false);

const setAuto = () => {
  isAuto.value = !isAuto.value;
  if (isAuto.value) {
    timer.value = setInterval(() => {
      toInit();
    }, 5000);
  } else {
    clearInterval(timer.value);
  }
};
</script>