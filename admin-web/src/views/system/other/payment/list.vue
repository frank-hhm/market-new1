<template>
  <layout-body-content pageHeader>
    <template v-slot:page-header-left> 渠道列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small"
            ><icon-refresh
          /></a-button>
        </a-tooltip>
        <a-button
          type="primary"
          size="small"
          @click="onCreate(0)"
          v-permission="'payment-create'"
          >添加渠道</a-button
        >
      </a-space>
      <createComponent
        ref="createComponentRef"
        @success="toInit(true)"
      ></createComponent>
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
            title="图标"
            align="center"
            data-index="cover"
            :width="80"
          >
            <template #cell="{ record }">
              <a-image
                :src="record.cover"
                :height="40"
                fit="cover"
                :width="80"
              />
            </template>
          </a-table-column>
          <a-table-column title="名称" data-index="name" :width="160">
            <template #cell="{ record }">
              <div>{{ record.name }}</div>
              <div>{{ record.nickname }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="代理"
            align="left"
            data-index="agent_id"
            :width="100"
          >
            <template #cell="{ record }">
              <template v-if="record.agent_id > 0">
                <agent-column-detail
                  :agent="record.agent"
                ></agent-column-detail>
              </template>
              <div v-else class="text-grey">未关联</div>
            </template>
          </a-table-column>
          <a-table-column
            title="类型"
            align="center"
            data-index="type"
            :width="100"
          >
            <template #cell="{ record }">
              <div>{{ record.type.name }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="配置项"
            align="center"
            data-index="setting"
            :width="200"
          >
            <template #cell="{ record }">
              <template v-if="record.setting.length > 0">
                <template v-for="(item, index) in record.setting" :key="index">
                  <div class="mb2">
                    <span class="text-grey">{{ item.key }}</span
                    >：
                    <span v-if="item.type === 'text'">{{ item.value }}</span>
                    <span v-else-if="item.type === 'textarea'">---</span>
                    <span v-else-if="item.type === 'image'"
                      ><a-image
                        :src="item.value"
                        :height="30"
                        fit="cover"
                        :width="30"
                    /></span>
                  </div>
                </template>
              </template>
              <div v-else class="text-grey">无</div>
            </template>
          </a-table-column>
          <a-table-column
            title="最小"
            align="center"
            data-index="min"
            :width="100"
          >
            <template #cell="{ record }">
              <div>{{ record.min }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="最大"
            align="center"
            data-index="max"
            :width="100"
          >
            <template #cell="{ record }">
              <div>{{ record.max }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="创建时间"
            data-index="create_time"
            align="center"
            :width="200"
          >
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
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
                v-permission-disabled="'payment-status'"
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
          <a-table-column title="操作" align="center" :width="200">
            <template #cell="{ record }">
              <a-space>
                <a-button
                  @click="onAgentUpdate(record.id, record.agent_id)"
                  v-permission="'payment-update'"
                  size="small"
                  >关联代理</a-button
                >
                <a-button
                  @click="onCreate(record.id)"
                  v-permission="'payment-update'"
                  size="small"
                  >编辑</a-button
                >
                <div v-permission="'payment-delete'">
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
      <agentUpdateComponent
        ref="agentUpdateComponentRef"
        @success="toInit()"
      ></agentUpdateComponent>
    </template>
    <template #footer>
      <page :listPage="listPage" @change="pageChange"></page>
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import createComponent from "./create.vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import agentUpdateComponent from "./update-agent.vue";
import {
  getPaymentListApi,
  updateStatusPaymentApi,
  deletePaymentApi,
} from "@/api/payment";
const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const createComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
  proxy?.$refs["createComponentRef"]?.open(id);
};

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const toInit = (isInit: boolean = false) => {
  if (isInit) {
    listPage.value.page = 1;
  }
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  initLoading.value = true;
  getPaymentListApi(obj)
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

const onStatusChange = (val: any, row: any) => {
  if (row.switch === true) {
    row.loading = true;
    updateStatusPaymentApi({
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

const onDelete = (id: number | string) => {
  deletePaymentApi({ id })
    .then((res: Result) => {
      toInit();
      $utils.successMsg(res.message);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};

onMounted(() => {
  toInit();
});

const agentUpdateComponentRef = ref<HTMLElement>();

const onAgentUpdate = (id: number, agentId: number) => {
  proxy?.$refs["agentUpdateComponentRef"]?.open(id, agentId);
};
</script>