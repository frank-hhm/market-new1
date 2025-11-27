<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-alert type="warning"
          >代理商的登录地址：{{ $utils.getHostUrl() + "/agent/"}}</a-alert
        >
        <div class="mt12"></div>
      </a-card>
    </template>
    <template v-slot:page-header-left> 代理商列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small"
            ><icon-refresh
          /></a-button>
        </a-tooltip>
        <a-button
          type="primary"
          @click="onCreate(0)"
          size="small"
          v-permission="'agent-create'"
          >添加代理商</a-button
        >
      </a-space>
      <createAgentComponent
        ref="createAgentComponentRef"
        @success="toInit(true)"
      ></createAgentComponent>
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
            title="级别"
            data-index="level"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <a-tag color="purple">{{ record.level?.name }}</a-tag>
            </template>
          </a-table-column>
          <a-table-column
            title="上级"
            data-index="p_nickname"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <span v-if="record.p_nickname">
                {{ record.p_nickname }}
              </span>
              <span class="text-grey" v-else>无</span>
            </template>
          </a-table-column>
          <a-table-column title="账号" data-index="account" :width="140">
            <template #cell="{ record }">
              <agent-column-detail :agent="record"></agent-column-detail>
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
          >
            <template #cell="{ record }">
              <div>{{ record.balance }}</div>
            </template>
          </a-table-column>
          <a-table-column title="登录次数" align="center" :width="100">
            <template #cell="{ record }">
              <div>{{ record.login_count }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="最后一次登录时间"
            data-index="last_time"
            align="center"
            :width="160"
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
            title="状态"
            data-index="status"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <a-switch
                v-model="record.status.value"
                :disabled="record.level < 1"
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
          <a-table-column title="操作" align="center" :width="180">
            <template #cell="{ record }">
              <a-space>
                <a-button
                  @click="onFee(record.id, record.account)"
                  v-permission="'agent-fee'"
                  size="small"
                  status="warning"
                  ><icon-mind-mapping />手续费</a-button
                >
                <a-button
                  @click="onCreate(record.id)"
                  v-permission="'agent-update'"
                  size="small"
                  >编辑</a-button
                >
                <div v-permission="'agent-delete'">
                  <a-popconfirm
                    content="确定删除吗？"
                    @ok="onDelete(record.id)"
                  >
                    <template #icon>
                      <icon-exclamation-circle-fill type="red" />
                    </template>
                    <a-button size="small">删除</a-button>
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
      <feeComponent ref="feeComponentRef"></feeComponent>
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import createAgentComponent from "./create.vue";
import feeComponent from "./fee.vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  deleteAgentApi,
  getAgentListApi,
  updateStatusAgentApi,
} from "@/api/agent";

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const createAgentComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
  proxy?.$refs["createAgentComponentRef"]?.open(id);
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
  initLoading.value = true;
  getAgentListApi(obj)
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
  deleteAgentApi({ id })
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
    updateStatusAgentApi({
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

const feeComponentRef = ref<HTMLElement>();

const onFee = (id: number, name: string) => {
  proxy?.$refs["feeComponentRef"].open(id, name);
};
</script>