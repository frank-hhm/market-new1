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
                <agent-search-select
                  v-model="searchForm.agent_id"
                ></agent-search-select>
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
    <template v-slot:page-header-left> 列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
        </a-tooltip>
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
      >
        <template #columns>
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
          <a-table-column title="真实姓名" data-index="is_bind" :width="100">
            <template #cell="{ record }">
              <div>{{ record.real_name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="身份证号" data-index="is_bind" :width="160">
            <template #cell="{ record }">
              <div>{{ record.card_number }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="处理时间"
            data-index="handle_time"
            :width="160"
          >
            <template #cell="{ record }">
              <div
                class="text-grey"
                v-if="$utils.inArray(record.bind_status?.value, [1, 3])"
              >
                {{ record.handle_time }}
              </div>
              <div v-else class="text-grey">未处理</div>
            </template>
          </a-table-column>
          <a-table-column
            title="申请时间"
            data-index="create_time"
            :width="160"
          >
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="认证状态"
            data-index="bind_status"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <div :class="`text-${record.bind_status?.color}`">
                {{ record.bind_status?.name }}
              </div>
            </template>
          </a-table-column>
          <a-table-column title="操作" align="center" :width="140">
            <template #cell="{ record }">
              <a-space>
                <a-button
                  v-permission="'member-card-update'"
                  size="small"
                  @click="onUpdate(record.id)"
                  >编辑</a-button
                >
                <template v-if="record.bind_status?.value == 2">
                  <div v-permission="'member-bind-handle'">
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
                  <div v-permission="'member-card-handle'">
                    <a-popconfirm
                      content="确定驳回吗？"
                      @ok="onHandle(record.id, 3)"
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
      <updateMemberCardComponent
        ref="updateMemberCardComponentRef"
        @success="toInit(true)"
      ></updateMemberCardComponent>
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  getMemberCardListApi,
  handleMemberCardApi,
} from "@/api/member/member-card";
import updateMemberCardComponent from "./update.vue";

import { storeToRefs } from "pinia";
import { useAdminStore } from "@/store/modules";

const { adminInfo } = storeToRefs(useAdminStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

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
  initLoading.value = true;
  getMemberCardListApi(obj)
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

onMounted(() => {
  toInit();
});

const onHandle = (id: number, status: number) => {
  handleMemberCardApi({
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

const updateMemberCardComponentRef = ref<HTMLElement>();

const onUpdate = (id: number) => {
  proxy?.$refs["updateMemberCardComponentRef"].open(id);
};
</script>