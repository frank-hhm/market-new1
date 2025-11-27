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
                label="充值账号"
                prop="account"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.account"
                  placeholder="请输入充值账号"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="状态"
                prop="status"
              >
                <a-select
                  v-model="searchForm.status"
                  placeholder="选择状态"
                  allow-clear
                >
                  <a-option
                    v-for="(item, index) in statusEnum"
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
                label="渠道"
                prop="payment_id"
              >
                <a-select
                  v-model="searchForm.payment_id"
                  placeholder="选择渠道"
                  allow-clear
                  :loading="initPaymentLoading"
                >
                  <a-option value="all" label="全部" />
                  <a-option
                    v-for="(item, index) in paymentSelect"
                    :key="index"
                    :value="item.id"
                    >{{ item.name }}({{ item.nickname }})</a-option
                  >
                </a-select>
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
      <div class="mt12"></div
    ></template>
    <template v-slot:page-header-left> 申请列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small"  :disabled="initLoading"
            ><icon-refresh
          /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <a-collapse v-if="countData.length > 0" :default-active-key="[]">
        <a-collapse-item
          v-for="(item, index) in countData"
          :key="index"
          :header="item.title"
        >
          <template v-for="(items, idx) in item.data" :key="idx">
            <span class="text-grey" :class="idx > 0 ? 'ml20' : ''"
              >{{ items.name }}:</span
            >
            <span>{{ items.value || 0 }}</span>
          </template>
        </a-collapse-item>
      </a-collapse>
      <a-table
        :class="countData.length > 0 ? 'mt12' : ''"
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
          <!-- <a-table-column title="头像" data-index="member_avatar" :width="60">
                        <template #cell="{ record }">
                            <a-image :src="record.member_avatar" :height="40" :width="40" />
                        </template>
</a-table-column> -->
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
            title="金额(any)"
            data-index="money"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.money }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="金额(usdt)"
            data-index="usdt"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.usdt }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="充值账号"
            data-index="account"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <span v-if="record.account">
                {{ record.account }}
              </span>
              <span v-else class="text-grey">无</span>
            </template>
          </a-table-column>
          <a-table-column title="凭证" data-index="voucher_url" align="center" :width="150">
            <template #cell="{ record }">
              <a-image v-if="record.voucher_url" :src="record.voucher_url" :width="100" :height="60" s show-loader></a-image>
              <span v-else class="text-grey">无凭证</span>
            </template>
          </a-table-column>
          <a-table-column
            title="备注"
            data-index="remark"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div v-if="record.remark">
                {{ record.remark }}
              </div>
              <div v-else class="text-grey te2">无</div>
            </template>
          </a-table-column>
          <a-table-column
            title="类型"
            data-index="pay_type"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.pay_type?.name }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="渠道"
            data-index="payment_id"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div v-if="record.payment_id === -1">
                {{ record.payment1_name || "支付宝在线" }}
              </div>
              <div v-if="record.payment_id > 0">
                {{ record.payment_name || "" }}
              </div>
              <div v-if="record.payment_id > 0">
                {{ record.payment_nickname || "" }}
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
            title="赠送抽奖次数"
            data-index="turntable"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.turntable || 0 }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="申请时间"
            data-index="create_time"
            :width="160"
          >
            <template #cell="{ record }">
              <div class="text-grey">
                {{ record.create_time }}
              </div>
            </template>
          </a-table-column>
          <a-table-column
            title="处理时间"
            data-index="update_time"
            :width="160"
          >
            <template #cell="{ record }">
              <div
                class="text-grey"
                v-if="$utils.inArray(record.status?.value, [1, 2])"
              >
                {{ record.update_time }}
              </div>
              <div v-else class="text-grey">未处理</div>
            </template>
          </a-table-column>
          <a-table-column
            title="状态"
            data-index="status"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <div :class="`text-${record.status?.color}`">
                {{ record.status?.name }}
              </div>
            </template>
          </a-table-column>
          <a-table-column title="操作" align="center" :width="140">
            <template #cell="{ record }">
              <a-space>
                <template v-if="record.status?.value == 0">
                  <div v-permission="'finance-recharge-handle'">
                    <a-popconfirm
                      content="确定审核通过吗？"
                      @ok="onHandle(record.id, 1)"
                    >
                      <template #icon>
                        <icon-exclamation-circle-fill type="red" />
                      </template>
                      <a-button size="small" status="success">通过</a-button>
                    </a-popconfirm>
                  </div>
                  <div v-permission="'finance-recharge-handle'">
                    <a-popconfirm
                      content="确定驳回吗？"
                      @ok="onHandle(record.id, 2)"
                    >
                      <template #icon>
                        <icon-exclamation-circle-fill type="red" />
                      </template>
                      <a-button size="small">驳回</a-button>
                    </a-popconfirm>
                  </div>
                </template>
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
import {
  getFinanceRechargeListApi,
  handleFinanceRechargeApi,
} from "@/api/finance/recharge";
import { useEnumStore, useAdminStore } from "@/store";
import { storeToRefs } from "pinia";
import { getPaymentSelectApi } from "@/api/payment";
const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const statusEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("finance.StatusEnum")
);

const labelColFlex = ref<string>("50px");

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  account: string;
  status: any;
  agent_id: string | number | string[] | number[];
  payment_id: string | number;
  create_time: any;
}>({
  username_like: "",
  account: "",
  status: null,
  agent_id: [],
  payment_id: "all",
  create_time: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.status = null;
  searchForm.value.agent_id = [];
  searchForm.value.create_time = [];
  toInit(true);
};

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);

const countData = ref<any>([]);

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.username_like = searchForm.value.username_like;
  obj.account = searchForm.value.account;
  obj.status = searchForm.value.status;
  obj.agent_id = searchForm.value.agent_id;
  obj.payment_id = searchForm.value.payment_id;

  obj.create_time = searchForm.value.create_time;
  initLoading.value = true;
  getFinanceRechargeListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      countData.value = res.data.count_data || 0;
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


const paymentSelect = ref<any[]>([]);
const initPaymentLoading = ref<boolean>(false);
const toInitPaymentSelect = () => {
  initPaymentLoading.value = true;
  getPaymentSelectApi({})
    .then((res: Result) => {
      paymentSelect.value = res.data;
      initPaymentLoading.value = false;
    })
    .catch((err: ResultError) => {
      initPaymentLoading.value = false;
    });
};

onMounted(() => {
  toInitPaymentSelect();
  toInit();
});

const handleLoading = ref<boolean>(false);

const onHandle = (id: number, status: number) => {
  if (handleLoading.value === true) {
    $utils.errorMsg("正在处理中，请勿重复操作！");
    return;
  }
  handleLoading.value = true;
  handleFinanceRechargeApi({
    id: id,
    status: status,
  })
    .then((res: Result) => {
      toInit();
      handleLoading.value = false;
      $utils.successMsg(res);
    })
    .catch((err: ResultError) => {
      toInit();
      handleLoading.value = false;
      $utils.errorMsg(err);
    });
};
</script>