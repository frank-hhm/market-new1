<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form layout="horizontal" :model="searchForm" ref="searchFormRef">
          <a-row :gutter="20">
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item :label-col-flex="labelColFlex" label="账号" prop="username_like">
                <a-input class="form-input-inline" v-model="searchForm.username_like" placeholder="请输入账号或手机号"
                  allow-clear />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item :label-col-flex="labelColFlex" label="状态" prop="status">
                <a-select v-model="searchForm.status" placeholder="选择状态" allow-clear>
                  <a-option v-for="(item, index) in statusEnum" :key="index" :value="item.value" :label="item.name" />
                </a-select>
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
                </a-space>
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </a-card>
      <div class="mt12"></div>
    </template>
    <template v-slot:page-header-left> 申请列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small" :disabled="initLoading"><icon-refresh /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <a-collapse v-if="countData.length > 0" :default-active-key="[]">
        <a-collapse-item v-for="(item, index) in countData" :key="index" :header="item.title">
          <template v-for="(items, idx) in item.data" :key="idx">
            <span class="text-grey" :class="Number(idx) > 0 ? 'ml20' : ''">{{ items.name }}:</span>
            <span>{{ items.value || 0 }}</span>
          </template>
        </a-collapse-item>
      </a-collapse>
      <a-table :class="countData.length > 0 ? 'mt12' : ''" :loading="initLoading" :data="lists" row-key="id" isLeaf
        :pagination="false" :scroll="{
          x: '100%',
          y: height - 39,
        }" :table-layout-fixed="true">
        <template #columns>
          <a-table-column title="账号" data-index="member_username" :width="120">
            <template #cell="{ record }">
              <member-column-detail :member="{
                username: record.member_username,
                mobile: record.member_mobile,
                id: record.member_id,
              }"></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="代理商" data-index="agent_real_name" align="left" :width="120">
            <template #cell="{ record }">
              <agent-column-detail :agent="record.agent"></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="金额" data-index="money" align="center" :width="120">
            <template #cell="{ record }">
              <div>{{ record.money }}</div>
            </template>
          </a-table-column>
          <a-table-column title="提现手续费" data-index="fee" align="center" :width="120">
            <template #cell="{ record }">
              <div>{{ record.fee }}</div>
            </template>
          </a-table-column>
          <a-table-column title="备注" data-index="remark" align="center" :width="120">
            <template #cell="{ record }">
              <div v-if="record.remark">
                {{ record.remark }}
              </div>
              <div v-else class="text-grey te2">无</div>
            </template>
          </a-table-column>
          <a-table-column title="到账金额" data-index="amount" :width="160">
            <template #cell="{ record }">
              <div>${{ record.amount }}</div>
              <div class="flex" v-if="record.pay_type.value !== 'offline_usdt' && systemInfo?.usdt_out_rate">
                <div class="text-grey">换算：</div>
                <div>
                  ￥{{
                    ( record.amount * Number(systemInfo?.usdt_out_rate)).toFixed(2)
                  }}
                </div>
              </div>
            </template>
          </a-table-column>
          <a-table-column title="类型" data-index="type" align="center" :width="120">
            <template #cell="{ record }">
              <div>{{ record.type?.name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="提现到账" data-index="pay_type" align="center" :width="150">
            <template #cell="{ record }">
              <div>{{ record.pay_type?.name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="信息" data-index="bank_name" :width="320">
            <template #cell="{ record }">
              <div class="" v-if="record.pay_type.value == 'offline_bank'">
                <div class="flex">
                  <div class="text-grey">银行卡名称：</div>
                  <div>{{ record.bank_name }}</div>
                </div>
                <div class="flex">
                  <div class="text-grey">银行卡号：</div>
                  <div>{{ record.bank_code }}</div>
                </div>
                <div class="flex">
                  <div class="text-grey">银行卡姓名：</div>
                  <div>{{ record.bank_real_name }}</div>
                </div>
                <div class="flex">
                  <div class="text-grey">支行：</div>
                  <div>{{ record.bank_child }}</div>
                </div>
              </div>
              <div v-else-if="record.pay_type.value == 'offline_usdt'">
                <div class="flex">
                  <div class="text-grey">U地址：</div>
                  <div>{{ record.usdt_card || "" }}</div>
                </div>
              </div>
              <div v-else-if="record.pay_type.value == 'offline_alipay'">
                <div class="flex">
                  <div class="text-grey">支付账号：</div>
                  <div>{{ record.alipay_card || "" }}</div>
                </div>
                <div class="flex">
                  <div class="text-grey">支付姓名：</div>
                  <div>{{ record.alipay_name || "" }}</div>
                </div>
                <div class="flex">
                  <div class="text-grey">支付收款码：</div>
                  <div>
                    <a-image :src="record.alipay_img" :height="40" :width="40"></a-image>
                  </div>
                </div>
              </div>
            </template>
          </a-table-column>
          <a-table-column title="申请时间" data-index="create_time" :width="160">
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>
          <a-table-column title="处理时间" data-index="update_time" :width="160">
            <template #cell="{ record }">
              <div class="text-grey" v-if="$utils.inArray(record.status?.value, [1, 2])">
                {{ record.update_time }}
              </div>
              <div v-else class="text-grey">未处理</div>
            </template>
          </a-table-column>
          <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
            <template #cell="{ record }">
              <div :class="`text-${record.status?.color}`">
                {{ record.status?.name }}
              </div>
            </template>
          </a-table-column>
          <a-table-column title="操作" fixed="right" align="center" :width="140">
            <template #cell="{ record }">
              <a-space>
                <template v-if="record.status?.value == 0">
                  <div v-permission="'finance-withdrawal-member-handle'">
                    <a-popconfirm content="确定审核通过吗？" @ok="onHandle(record.id, 1)">
                      <template #icon>
                        <icon-exclamation-circle-fill type="red" />
                      </template>
                      <a-button size="small" status="success">通过</a-button>
                    </a-popconfirm>
                  </div>
                  <div v-permission="'finance-withdrawal-member-handle'">
                    <a-popconfirm content="确定驳回吗？" @ok="onHandle(record.id, 2)">
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
  getFinanceMemberWithdrawalListApi,
  handleFinanceMemberWithdrawalApi,
} from "@/api/finance/withdrawal";
import { useAppStore, useEnumStore, useAdminStore } from "@/store";
import { storeToRefs } from "pinia";
const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const { systemInfo } = storeToRefs(useAppStore());

const statusEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("finance.WithdrawalStatusEnum")
);

const labelColFlex = ref<string>("50px");

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  status: any;
  agent_id: string | number | string[] | number[];
  create_time: any;
}>({
  username_like: "",
  status: null,
  agent_id: [],
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
  obj.status = searchForm.value.status;
  obj.agent_id = searchForm.value.agent_id;
  obj.create_time = searchForm.value.create_time;
  initLoading.value = true;
  getFinanceMemberWithdrawalListApi(obj)
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

const onHandle = (id: number, status: number) => {
  handleFinanceMemberWithdrawalApi({
    id: id,
    status: status,
  })
    .then((res: Result) => {
      toInit();
      $utils.successMsg(res);
    })
    .catch((err: ResultError) => {
      toInit();
      $utils.errorMsg(err);
    });
};
</script>