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
                label="产品"
                prop="pid"
              >
                <a-select
                  v-model="searchForm.pid"
                  placeholder="选择产品"
                  allow-clear
                  multiple
                >
    <a-option v-for="(item, index) in productSelect" :key="index" :value="item.id"
      >{{ item.name }}({{ item.code }})</a-option
    >
                </a-select>
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
            <a-col :md="12" :xs="24" :xl="6">
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
                  <!-- <a-button type="primary" >新增持仓</a-button> -->
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
        <span class="">浮动盈亏:</span>
        <span class="">{{ Number(countData.ying_kui || 0).toFixed(2) }}</span>
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
          <!-- <a-table-column title="类型" data-index="sys_ping" :width="80">

                        <template #cell="{ record }">
                            <div>{{ record.sys_ping.name }}</div>
                        </template>
                    </a-table-column> -->
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
          <a-table-column title="盈亏" data-index="yinkui" :width="120">
            <template #cell="{ record }">
              <div>{{ record.yinkui }}</div>
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
          <a-table-column title="触发标记" data-index="mark_price" :width="180">
            <template #cell="{ record }">
              <div
                v-if="
                  record.mark_price &&
                  record.mark_price != '' &&
                  record.mark_price != 0
                "
              >
                <div>
                  <span class="text-grey">标记:</span>{{ record.trigger_price }}
                </div>
                <div>
                  <span class="text-grey">触发:</span
                  >{{ record.mark_price > record.trigger_price ? ">=" : "<="
                  }}{{ record.mark_price }}
                </div>
              </div>
              <div v-else class="text-grey">无</div>
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
          <a-table-column title="建仓时间" data-index="buytime" :width="160">
            <template #cell="{ record }">
              <div class="nowrap text-grey">{{ record.buytime }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="操作"
            fixed="right"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <a-space>
                <div v-permission="'product-order-sell'">
                  <a-popconfirm
                    content="确定要平仓吗？"
                    @ok="onPingSell(record.id, record.member_id)"
                  >
                    <a-button size="small">平仓</a-button>
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
import { getOrderChiListApi, pingSellOrderApi } from "@/api/order/order";
import { useEnumStore, useAdminStore } from "@/store";
import { storeToRefs } from "pinia";
import { getProductSelectApi } from "@/api/product";
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
  type: any;
  agent_id: string | number | string[] | number[];
  is_wei: string | number;
  pid: any;
}>({
  username_like: "",
  ostyle: "",
  buytime: [],
  type: null,
  agent_id: [],
  is_wei: "all",
  pid:null
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.ostyle = null;
  searchForm.value.agent_id = [];
  searchForm.value.buytime = [];
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
  ying_kui: 0,
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
  obj.type = searchForm.value.type;
  obj.agent_id = searchForm.value.agent_id;
  obj.is_wei = searchForm.value.is_wei;
  obj.pid = searchForm.value.pid;

  initLoading.value = true;
  getOrderChiListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      countData.value.money_profit = res.data.money_profit || 0.0;
      countData.value.money_sx_fee = res.data.money_sx_fee || 0.0;
      countData.value.money_fee = res.data.money_fee || 0.0;
      countData.value.onumber = res.data.onumber || 0;
      countData.value.yong_agents_money = res.data.yong_agents_money || 0.0;
      countData.value.ying_kui = res.data.ying_kui || 0.0;
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
  toInitProductSelect();
});

const productSelect = ref<any>([]);

const toInitProductSelect = () => {
  getProductSelectApi({})
    .then((res: Result) => {
      productSelect.value = res.data;
    })
    .catch((error: ResultError) => {
})
}

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

const onPingSell = (id: number, memberId: number) => {
  pingSellOrderApi({
    oid: id,
    member_id: memberId,
  })
    .then((res: Result) => {
      toInit();
      $utils.successMsg(res.message);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};
</script>