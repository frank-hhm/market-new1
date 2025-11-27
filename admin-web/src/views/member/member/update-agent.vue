<template>
  <a-drawer
    class="drawer-default"
    title="代理转移"
    v-model:visible="visible"
    :width="700"
  >
    <template #default>
      <a-table
        :loading="initLoading"
        :data="lists"
        row-key="id"
        isLeaf
        :pagination="false"
        :scroll="{
          x: '100%',
        }"
      >
        <template #columns>
          <a-table-column
            title="当前所属"
            data-index="agent-id"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <a-tag color="red" v-if="record.id == operationAgentId">当前所属</a-tag>
            </template>
          </a-table-column>
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
          <a-table-column title="账号" data-index="account" :width="120">
            <template #cell="{ record }">
              <agent-column-detail :agent="record"></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="操作" align="center" :width="180">
            <template #cell="{ record }">
              <a-space>
                <div v-permission="'member-update-agent'" v-if="record.id != operationAgentId">
                  <a-popconfirm
                    content="确定转移吗？"
                    @ok="onUpdate(record.id)"
                  >
                    <template #icon>
                      <icon-exclamation-circle-fill type="red" />
                    </template>
                    <a-button size="small">转移</a-button>
                  </a-popconfirm>
                </div>
              </a-space>
            </template>
          </a-table-column>
        </template>
      </a-table>
      <div class="mt20 flex end">
        <page :listPage="listPage" @change="pageChange"></page>
      </div>
    </template>
    <template #footer>
      <a-button @click="close">关闭</a-button>
    </template>
  </a-drawer>
</template>
<script lang="ts">
export default {
  name: "memberAgentUpdate",
};
</script>

<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance, nextTick } from "vue";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { getAgentListApi } from "@/api/agent";
import { updateMemberAgentApi } from "@/api/member/member";

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const initLoading = ref<boolean>(false);

const visible = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);
const operationAgentId = ref<number>(0);

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

const onUpdate = (agentId: number) => {
  initLoading.value = true;
  updateMemberAgentApi({
    id: operationId.value,
    agent_id: agentId,
  })
    .then((res: Result) => {
      $utils.successMsg(res.message);
      close();
      emit("success");
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
      initLoading.value = false;
    });
};

const open = (id: number = 0, agentId: number) => {
  operation.value = "update";
  operationId.value = id;
  operationAgentId.value = agentId;
  nextTick(() => {
    toInit();
  });
  visible.value = true;
};

const close = () => {
  operationId.value = 0;
  visible.value = false;
  return true;
};

defineExpose({ open });
</script>