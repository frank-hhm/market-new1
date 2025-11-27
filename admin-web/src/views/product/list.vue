<template>
  <layout-body-content pageHeader header>
    <template #header>
      <a-card class="card-form">
        <a-form layout="horizontal" :model="searchForm" ref="searchFormRef">
          <a-row :gutter="20">
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="名称"
                prop="ptitle"
              >
                <a-input
                  class="form-input-inline"
                  v-model="searchForm.ptitle"
                  placeholder="请输入名称"
                  allow-clear
                />
              </a-form-item>
            </a-col>
            <a-col :md="12" :xs="24" :xl="6">
              <a-form-item
                :label-col-flex="labelColFlex"
                label="板块"
                field="sector_id"
              >
                <a-select
                  v-model="searchForm.sector_id"
                  placeholder="请选择板块"
                  allow-clear
                  :loading="sectorInitLoading"
                >
                  <a-option
                    v-for="(item, index) in sectorData"
                    :key="index"
                    :value="item.id"
                  >
                    {{ item.sector_name }}
                  </a-option>
                </a-select>
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
              <a-form-item :label-col-flex="labelColFlex">
                <a-space>
                  <a-button type="primary" @click="toInit()">查询</a-button>
                  <a-button plain @click="onSearchReset()">重置</a-button>
                  <a-button @click="toInit()" :disabled="initLoading">
                    <icon-sync />
                  </a-button>
                </a-space>
              </a-form-item>
            </a-col>
          </a-row>
        </a-form>
      </a-card>
    </template>
    <template v-slot:page-header-left> 产品管理 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-button
          size="small"
          @click="onSetDefault"
          v-permission="'product-default'"
        >
          设置默认自选
        </a-button>
        <a-tooltip content="刷新">
          <a-button @click="toInit()" size="small"><icon-refresh /></a-button>
        </a-tooltip>
        <a-button
          type="primary"
          size="small"
          @click="onCreate(0)"
          v-permission="'product-create'"
        >
          添加产品
        </a-button>
      </a-space>
      <productCreate ref="createComponentRef" @success="toInit"></productCreate>
      <setDefaultComponent ref="setDefaultComponentRef"></setDefaultComponent>
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
      >
        <template #columns>
          <a-table-column title="排序" data-index="sort" :width="80">
            <template #cell="{ record }">
              <div>{{ record.sort }}</div>
            </template>
          </a-table-column>
          <a-table-column title="ID" align="center" data-index="id" :width="80">
            <template #cell="{ record }">
              <span>{{ record.id }}</span>
            </template>
          </a-table-column>
          <a-table-column
            title="分类"
            align="center"
            data-index="type"
            :width="80"
          >
            <template #cell="{ record }">
              <a-tag>{{ record.type?.name || "无" }}</a-tag>
            </template>
          </a-table-column>
          <a-table-column
            title="板块"
            align="center"
            data-index="sector_name"
            :width="80"
          >
            <template #cell="{ record }">
              <a-tag v-if="record.sector_name">{{
                record.sector_name || "无"
              }}</a-tag>
            </template>
          </a-table-column>

          <a-table-column title="名称" data-index="name" :width="160">
            <template #cell="{ record }">
              <div>{{ record.name }}</div>
            </template>
          </a-table-column>
          <a-table-column title="显示代码" data-index="xcode" :width="100">
            <template #cell="{ record }">
              <div>{{ record.code }}</div>
            </template>
          </a-table-column>
          <a-table-column title="真实代码" data-index="real_code" :width="120">
            <template #cell="{ record }">
              <div>{{ record.real_code }}</div>
            </template>
          </a-table-column>
          <a-table-column title="手续费" data-index="fee_buy" :width="120">
            <template #cell="{ record }">
              <div>{{ record.fee_buy }}</div>
            </template>
          </a-table-column>
          <a-table-column title="保证金" data-index="pay_choose" :width="120">
            <template #cell="{ record }">
              <div>{{ record.pay_choose }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="创建时间"
            data-index="create_time"
            align="center"
            :width="160"
          >
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>

          <a-table-column
            title="热门"
            data-index="is_hot"
            align="center"
            :width="80"
          >
            <template #cell="{ record }">
              <a-switch
                v-model="record.is_hot"
                v-permission-disabled="'product-update'"
                size="small"
                type="round"
                :loading="record.loading"
                :beforeChange="
                  () => {
                    return (record.switch = true);
                  }
                "
                @change="onHotChange($event, record)"
                checked-value="1"
                unchecked-value="0"
              />
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
                v-permission-disabled="'product-status'"
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
            title="操作"
            align="right"
            :fixed="!isMobile ? 'right' : undefined"
            :width="180"
          >
            <template #cell="{ record }">
              <a-space>
                <a-button
                  v-if="record.market_source.value == 'auto'"
                  @click="onControl(record.id, record.name)"
                  v-permission="'product-control'"
                  size="small"
                  status="warning"
                  ><icon-mind-mapping />调控</a-button
                >
                <a-button
                  @click="onCreate(record.id)"
                  v-permission="'product-update'"
                  size="small"
                  >编辑</a-button
                >
                <div v-permission="'product-delete'">
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
            <controlProductComponent ref="controlProductComponentRef"></controlProductComponent>
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { ref, onMounted, getCurrentInstance } from "vue";

import { storeToRefs } from "pinia";
import { useAppStore, useEnumStore } from "@/store";
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  deleteProductApi,
  getProductListApi,
  updateHotProductApi,
  updateStatusProductApi,
} from "@/api/product";
import productCreate from "./create.vue";
import { getProductSectorSelectApi } from "@/api/product-sector";
import controlProductComponent from "./control.vue";
import setDefaultComponent from "./set-default.vue";


const labelColFlex = ref<string>("50px");

const { isMobile } = storeToRefs(useAppStore());
const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const statusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));
const onCreate = (id: number = 0) => {
  proxy?.$refs["createComponentRef"].open(id);
};

const searchFormRef = ref<HTMLElement>();

const searchForm = ref<{
  ptitle: string;
  status: any;
  sector_id: any;
}>({
  ptitle: "",
  status: null,
  sector_id: null,
});

const onSearchReset = () => {
  proxy?.$refs["searchFormRef"]?.resetFields();
  searchForm.value.status = null;
  toInit(true);
};

const sectorData = ref<any[]>([]);

const sectorInitLoading = ref<boolean>(true);

const toSectorInit = () => {
  sectorInitLoading.value = true;
  getProductSectorSelectApi()
    .then((res: Result) => {
      sectorData.value = res.data;
      sectorInitLoading.value = false;
    })
    .catch((err: ResultError) => {
      sectorInitLoading.value = false;
      $utils.errorMsg(err);
    });
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
  getProductListApi(obj)
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
  toSectorInit();
  toInit();
});

const onStatusChange = (val: any, row: any) => {
  if (row.switch === true) {
    row.loading = true;
    updateStatusProductApi({
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

const onHotChange = (val: any, row: any) => {
  if (row.switch === true) {
    row.loading = true;
    updateHotProductApi({
      id: row.id,
      is_hot: val,
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
  deleteProductApi({ id })
    .then((res: Result) => {
      toInit();
      $utils.successMsg(res.message);
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
    });
};
const controlProductComponentRef = ref<HTMLElement>()

const onControl = (id: number, name: string) => {
    proxy?.$refs["controlProductComponentRef"]?.open(id, name);
}

const setDefaultComponentRef = ref<HTMLElement>()

const onSetDefault = () => {
    proxy?.$refs["setDefaultComponentRef"]?.open();
}
</script>