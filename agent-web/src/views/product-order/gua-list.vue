<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form layout="horizontal" :model="searchForm" ref="searchFormRef">
          <a-row :gutter="20">
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
                label="账号类型"
                prop="moni"
              >
                <a-select
                  v-model="searchForm.moni"
                  placeholder="选择类型"
                  allow-clear
                >
                  <a-option value="all" label="全部" />
                  <a-option value="0" label="实盘" />
                  <a-option value="1" label="模拟" />
                </a-select>
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item :label-col-flex="labelColFlex">
                <a-space>
                  <a-button type="primary" @click="toInit()">查询</a-button>
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
    <template v-slot:page-header-left> 列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
        </a-tooltip>
      </a-space>
    </template>
    <template v-slot:content="{ height }">
      <a-table
        class=""
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
            data-index="member"
            align="left"
            :width="120"
          >
            <template #cell="{ record }">
              <agent-column-detail :agent="record.agent"></agent-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="账号" data-index="member" :width="120">
            <template #cell="{ record }">
              <member-column-detail
                :member="record.member"
              ></member-column-detail>
            </template>
          </a-table-column>
          <a-table-column title="产品" data-index="pro" :width="120">
            <template #cell="{ record }">
              <div>{{ record.ptitle }}</div>
            </template>
          </a-table-column>
          <a-table-column title="方向" data-index="ostyle" :width="80">
            <template #cell="{ record }">
              <div :class="`text-${record.ostyle.color}`">
                {{ record.ostyle.name }}
              </div>
            </template>
          </a-table-column>

          <a-table-column title="当时价格" data-index="mark_price" :width="120">
            <template #cell="{ record }">
              <div class="nowrap">{{ record.mark_price }}</div>
            </template>
          </a-table-column>

          <a-table-column title="标记价格" data-index="buyprice" :width="120">
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
          <a-table-column title="委托额" data-index="order_price" :width="120">
            <template #cell="{ record }">
              <div>{{ record.order_price }}</div>
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
          <a-table-column
            title="提交时间"
            data-index="create_time"
            :width="160"
          >
            <template #cell="{ record }">
              <div class="nowrap text-grey">{{ record.create_time }}</div>
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
import { getOrderGuaListApi } from "@/api/order/order";
import { useEnumStore } from "@/store";
import { storeToRefs } from "pinia";
import { Modal } from "@arco-design/web-vue";

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
  agent_id: string | number | string[] | number[];
  moni: number | string;
}>({
  agent_id: [],
  moni: "all",
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  toInit(true);
};

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const toInit = (isInit: boolean = false) => {
  let obj: any = {};
  obj.moni = searchForm.value.moni;
  obj.agent_id = searchForm.value.agent_id;
  initLoading.value = true;
  getOrderGuaListApi(obj)
    .then((res: Result) => {
      listPage.value.total = res.data.total;
      lists.value = res.data.data;
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