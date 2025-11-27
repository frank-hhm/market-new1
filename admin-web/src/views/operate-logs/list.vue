<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form
          :layout="isMobile ? 'vertical' : 'horizontal'"
          :model="searchForm"
          ref="searchFormRef"
        >
          <a-row :gutter="20">
            <a-col :md="8" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="账号"
                prop="account_like"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.account_like"
                  placeholder="请输入账号"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="8" :xs="24" :xl="8">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="时间"
                prop="create_time"
              >
                <shortcuts-time
                  v-model="searchForm.time"
                  :btnShortcuts="false"
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="8">
              <a-form-item :label-col-flex="labelColFlex" hide-label>
                <a-space>
                  <a-button @click="toInit()">查询</a-button>
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
      <a-radio-group type="button" v-model="source" @change="changeType">
        <a-radio
          v-for="(item, index) in sourceTabs"
          :key="index"
          :disabled="initLoading"
          :value="item.value"
          >{{ item.name }}</a-radio
        >
      </a-radio-group>
    </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small"
            ><icon-refresh
          /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <!-- 列表 -->
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
      >
        <template #columns>
          <a-table-column
            title="账号"
            data-index="source"
            :width="isMobile ? 160 : undefined"
          >
            <template #cell="{ record }">
              <member-column-detail
                v-if="record.source.value === 'member'"
                :member="{
                  username: record.source_username,
                  nickname: record.source_nickname,
                  mobile: record.source_mobile,
                }"
              ></member-column-detail>
              <agent-column-detail
                v-else-if="record.source.value === 'agent'"
                :agent="{
                  account: record.source_username,
                  real_name: record.source_nickname,
                }"
              ></agent-column-detail>
              <div v-else-if="record.source_id === 0">
                <div class="text-grey">未知</div>
              </div>
              <div class="admin-box" v-else>
                <div>{{ record.source_username }}</div>
                <div class="text-grey">{{ record.source_nickname }}</div>
              </div>
            </template>
          </a-table-column>
          <a-table-column
            title="来源"
            align="center"
            data-index="source"
            :width="isMobile ? 80 : undefined"
          >
            <template #cell="{ record }">
              <span>{{ record.source.name || "--" }}</span>
            </template>
          </a-table-column>
          <a-table-column
            title="操作"
            align="center"
            data-index="type"
            :width="isMobile ? 80 : undefined"
          >
            <template #cell="{ record }">
              <span>{{ record.type.name || "--" }}</span>
            </template>
          </a-table-column>
          <a-table-column
            title="IP"
            data-index="ip"
            :width="isMobile ? 160 : undefined"
          >
            <template #cell="{ record }">
              <div>{{ record.ip.value || "--" }}</div>
              <div class="text-grey">{{ record.ip.text || "--" }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="设备系统"
            data-index="agent_default"
            :minWidth="160"
          >
            <template #cell="{ record }">
              <div class="nowrap te1">
                <span class="text-grey">类型:</span>
                <span class="text-blue">{{
                  record.agent_default?.type?.name || "-"
                }}</span>
              </div>
              <template v-if="record.agent_default?.is_bot === true">
                <div class="nowrap te1">
                  <span class="text-grey">名称:</span>
                  <span>{{ record.agent_default?.bot_name || "--" }}</span>
                </div>
              </template>
              <template v-else>
                <div class="nowrap te1">
                  <span class="text-grey">设备:</span>
                  <a-tooltip
                    :content="
                      record.agent_default?.device?.name +
                      ' ' +
                      record.agent_default?.device?.version
                    "
                    position="top"
                  >
                    <span>{{ record.agent_default?.device?.name || "" }}</span>
                  </a-tooltip>
                </div>
                <div class="nowrap te1">
                  <span class="text-grey">系统:</span>
                  <a-tooltip
                    :content="
                      record.agent_default?.platform?.name +
                      ' ' +
                      record.agent_default?.platform?.version
                    "
                    position="top"
                  >
                    <span class="fz12"
                      >{{ record.agent_default?.platform?.name || "" }}
                      {{ record.agent_default?.platform?.version }}</span
                    >
                  </a-tooltip>
                </div>
              </template>
            </template>
          </a-table-column>
          <a-table-column
            title="请求方式"
            align="center"
            data-index="method"
            :width="isMobile ? 80 : undefined"
          >
            <template #cell="{ record }">
              <span>{{ record.method || "--" }}</span>
            </template>
          </a-table-column>

          <a-table-column title="时间" data-index="create_time" :width="160">
            <template #cell="{ record }">
              <span class="text-grey">{{ record.create_time }}</span>
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
import { ref, onMounted, getCurrentInstance } from "vue";
import { getOperateLogsListApi } from "@/api/operate-logs";
import { EnumType, PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";

import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";

const { isMobile } = storeToRefs(useAppStore());


const labelColFlex = ref<string>("50px");

const { proxy } = getCurrentInstance() as any;
const {
  proxy: { $utils },
} = getCurrentInstance() as any;

const initLoading = ref<boolean>(true);

const sourceTabs = ref<EnumType>([
  {
    name: "系统",
    value: "admin",
  },
  {
    name: "代理",
    value: "agent",
  },
  {
    name: "用户",
    value: "member",
  },
]);

const source = ref<string>("admin");

const changeType = (val: string | number | boolean) => {
  source.value = String(val);
  toInit();
};

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  account_like: string;
  time: string[];
}>({
  account_like: "",
  time: [],
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.time = [];
  toInit(true);
};

const lists = ref<any[]>([]);

const listPage = ref<PageLimitType>({
  total: 0,
  limit: useSetting().PageLimit.value,
  page: 1,
});

const pageChange = (res: PageLimitType) => {
  listPage.value = res;
  toInit();
};

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  initLoading.value = true;
  let obj: any = {
    page: listPage.value.page,
    limit: Number(listPage.value.limit || useSetting().PageLimit.value),
    source: source.value,
  };
  obj.account_like = searchForm.value.account_like;
  obj.time = searchForm.value.time;
  getOperateLogsListApi(obj)
    .then((res: Result) => {
      lists.value = res.data.data;
      listPage.value.total = res.data.total;
      setTimeout(() => {
        initLoading.value = false;
      }, 300);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};

onMounted(() => {
  toInit();
});
</script>