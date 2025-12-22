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
                  placeholder="请输入账号/手机号/邮箱"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="身份信息"
                prop="card_like"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.card_like"
                  placeholder="请输入身份信息"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="代理商"
                prop="agent_id"
              >
              <agent-search-select v-model="searchForm.agent_id"></agent-search-select>
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="创建时间"
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
    <template v-slot:page-header-left> 会员列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
        </a-tooltip>
        <a-button
          type="primary"
          @click="onCreate(0)"
          v-permission="'member-create'"
          size="small"
          >添加会员</a-button
        >
      </a-space>
      <createMemberComponent
        ref="createMemberComponentRef"
        @success="toInit(true)"
      ></createMemberComponent>
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
          <a-table-column
            title="ID"
            align="center"
            data-index="id"
            :width="80"
          >
            <template #cell="{ record }">
              <span>{{ record.id }}</span>
            </template>
          </a-table-column>
          <a-table-column
            title="代理商"
            data-index="agent"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <agent-column-detail :agent="record.agent"></agent-column-detail>
            </template>
          </a-table-column>

          <a-table-column title="账号" data-index="username" :width="120">
            <template #cell="{ record }">
              <member-column-detail :member="record" isId></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="身份信息" data-index="is_bind" :width="160">
            <template #cell="{ record }">
              <div v-if="record.bind_status?.value == 1">
                <div>{{ record.real_name }}</div>
                <div>{{ record.card_number }}</div>
              </div>
              <div v-else class="text-grey">{{ record.bind_status?.name }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="邀请码"
            data-index="invite_code"
            align="center"
            :width="100"
          >
            <template #cell="{ record }">
              <div @click="$utils.copyDomText(record.invite_code)">
                {{ record.invite_code }}
              </div>
            </template>
          </a-table-column>
          <a-table-column
            title="余额"
            data-index="balance"
            align="center"
            :width="100"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div>{{ record.balance }}</div>
            </template>
          </a-table-column>

          <a-table-column
            title="累计盈亏"
            align="center"
            data-index="yingkui_total"
            :width="120"
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
          <a-table-column
            title="返佣%/累计"
            data-index="fee_cash_commission_total"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>
                {{ record.fee_cash || 0 }}%/{{
                  record.fee_cash_commission_total || 0
                }}
              </div>
            </template>
          </a-table-column>

          <a-table-column
            title="抽奖次数"
            data-index="turntable"
            align="center"
            :width="100"
          >
            <template #cell="{ record }">
              <div>{{ record.turntable }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="状态"
            data-index="status"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <a-switch
                v-model="record.status.value"
                v-permission-disabled="'member-status'"
                size="small"
                type="round"
                :loading="record.loading"
                :beforeChange="
                  () => {
                    return (record.switch = true);
                  }
                "
                @change="onStatusChange($event, record)"
                :checked-value="1"
                :unchecked-value="0"
              />
            </template>
          </a-table-column>
          
          <a-table-column
            title="最后一次登录时间"
            data-index="last_time"
            align="center"
            :width="160"
            :sortable="{
              sortDirections: ['ascend', 'descend'],
              sorter: true,
            }"
          >
            <template #cell="{ record }">
              <div class="text-grey">{{ record.last_time }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="最后一次登录IP"
            data-index="last_ip"
            align="center"
            :width="140"
          >
            <template #cell="{ record }">
              <div class="fz12">{{ record.last_ip?.value }}</div>
              <div class="text-grey" v-if="record.last_ip?.text">
                {{ record.last_ip?.text }}
              </div>
            </template>
          </a-table-column>
          
          <a-table-column
            title="注册时间"
            data-index="create_time"
            :width="140"
          >
            <template #cell="{ record }">
              <div class="text-grey">
                {{ record.create_time }}
              </div>
            </template>
          </a-table-column>
          
          

          <a-table-column title="操作" align="center" :width="180">
            <template #cell="{ record }">
              <a-space>
                <!-- <a-button
                  @click="onSetSlippage(record.id)"
                  v-permission="'member-update'"
                  size="small"
                  >配置滑点</a-button
                > -->
                <a-button
                  @click="onCreate(record.id)"
                  v-permission="'member-update'"
                  size="small"
                  >编辑</a-button
                >
                <!-- <template #icon>
                  <icon-exclamation-circle-fill type="red" />
                </template> -->
                <!-- <a-button size="small">删除</a-button> -->
                <a-dropdown @select="onHandleSelect">
                  <a-button size="small">更多</a-button>
                  <template #content>
                    <div v-permission="'member-agent-update'">
                      <a-doption
                        :value="{
                          type: 'agent-update',
                          id: record.id,
                          agent_id: record.agent_id,
                        }"
                        >代理转移</a-doption
                      >
                    </div>
                    <div v-permission="'member-update'">
                      <a-doption
                        :value="{
                          type: 'bank',
                          id: record.id,
                        }"
                        >编辑银行卡</a-doption
                      >
                    </div>
                    <div v-permission="'member-update'">
                      <a-doption
                        :value="{
                          type: 'slippage',
                          id: record.id,
                        }"
                        >配置滑点</a-doption
                      >
                    </div>
                    <div v-permission="'member-delete'">
                      <a-doption
                        :value="{
                          type: 'delete',
                          id: record.id,
                        }"
                        >删除</a-doption
                      >
                    </div>
                  </template>
                </a-dropdown>
              </a-space>
            </template>
          </a-table-column>
        </template>
      </a-table>
    </template>
    <template #footer>
      <page :listPage="listPage" @change="pageChange"></page>
      <slippageComponent
        ref="slippageComponentRef"
        @success="toInit()"
      ></slippageComponent>
      <updateBindComponent
        ref="updateBindComponentRef"
        @success="toInit()"
      ></updateBindComponent>
      <agentUpdateComponent
        ref="agentUpdateComponentRef"
        @success="toInit()"
      ></agentUpdateComponent>
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import createMemberComponent from "./create.vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  getMemberListApi,
  updateStatusMemberApi,
  deleteMemberApi,
} from "@/api/member/member";
import { storeToRefs } from "pinia";
import { useAdminStore } from "@/store/modules";
import slippageComponent from "./slippage.vue";
import { Modal } from "@arco-design/web-vue";
import updateBindComponent from "./update-bind.vue";
import agentUpdateComponent from "./update-agent.vue";

const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const createMemberComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
  proxy?.$refs["createMemberComponentRef"]?.open(id);
};

const slippageComponentRef = ref<HTMLElement>();
const onSetSlippage = (id: number | string) => {
  proxy?.$refs["slippageComponentRef"].open(id);
};

const labelColFlex = ref<string>("50px");

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  username_like: string;
  card_like: string;
  agent_id: string | number | string[] | number[];
  create_time: any;
}>({
  username_like: "",
  card_like: "",
  agent_id: [],
  create_time: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.agent_id = [];
  searchForm.value.create_time = [];
  toInit(true);
};

const initLoading = ref<boolean>(false);

const lists = ref<any[]>([]);

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  obj.card_like = searchForm.value.card_like;
  obj.username_like = searchForm.value.username_like;
  obj.agent_id = searchForm.value.agent_id;
  obj.create_time = searchForm.value.create_time;
  if (sortField.value && sortFields.value) {
    obj.table_sorter = {
      [sortField.value]: sortFieldValue.value,
    };
  }
  initLoading.value = true;
  getMemberListApi(obj)
    .then((res: Result) => {
      initLoading.value = false;
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
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

const onDelete = (id: number | string) => {
  deleteMemberApi({ id })
    .then((res: Result) => {
      toInit();
      $utils.successMsg(res.message);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};

const onStatusChange = (val: any, row: any) => {
  if (row.switch === true) {
    row.loading = true;
    updateStatusMemberApi({
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

const updateBindComponentRef = ref<HTMLElement>();

const onBindUpdate = (id: number) => {
  proxy?.$refs["updateBindComponentRef"]?.open(id);
};

const onHandleSelect = (res: any) => {
  if (res.type && res.id) {
    switch (res.type) {
      case "delete":
        Modal.confirm({
          title: "提示",
          content: "确定删除吗？",
          okText: "确定",
          cancelText: "取消",
          alignCenter: true,
          onOk() {
            onDelete(res.id);
          },
          onCancel() {
            return true;
          },
        });
        break;
      case "slippage":
        onSetSlippage(res.id);
        break;
      case "bank":
        onBindUpdate(res.id);
        break;
      case "agent-update":
        onAgentUpdate(res.id, res.agent_id || 0);
        break;
      default:
        onCreate(res.id);
        break;
    }
  }
};

const agentUpdateComponentRef = ref<HTMLElement>();

const onAgentUpdate = (id: number, agentId: number) => {
  proxy?.$refs["agentUpdateComponentRef"]?.open(id, agentId);
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
  toInit(true);
};

</script>