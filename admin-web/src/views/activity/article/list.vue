<template>
  <layout-body-content pageHeader>
    <template v-slot:page-header-left> 列表 </template>
    <template v-slot:page-header-right>
      <a-space>
        <a-tooltip content="刷新">
          <a-button @click="toInit(true)" size="small"><icon-refresh /></a-button>
        </a-tooltip>
        <a-button @click="onGather" size="small" v-permission="'article-gather'"
          :loading="gatherLoading">执行采集</a-button>
        <a-button type="primary" @click="onCreate(0)" size="small" v-permission="'article-create'">添加文章</a-button>
        <createComponent ref="createComponentRef" @success="toInit(true)"></createComponent>
      </a-space>
      <createComponent ref="createComponentRef" @success="toInit(true)"></createComponent>
    </template>
    <template v-slot:content="{ height }">
      <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
        x: '100%',
        y: height - 39,
      }" :table-layout-fixed="true">
        <template #columns>
          <a-table-column title="图片" data-index="cover" :width="80">
            <template #cell="{ record }">
              <a-image :src="record.cover" :height="40" fit="cover" :width="80" />
            </template>
          </a-table-column>
          <a-table-column title="标题" data-index="title" :width="160">
            <template #cell="{ record }">
              <div>{{ record.title }}</div>
            </template>
          </a-table-column>
          <a-table-column title="副标题" data-index="title_desc" :width="160">
            <template #cell="{ record }">
              <div>{{ record.title_desc }}</div>
            </template>
          </a-table-column>
          <a-table-column title="创建时间" data-index="create_time" align="center" :width="160">
            <template #cell="{ record }">
              <div class="text-grey">{{ record.create_time }}</div>
            </template>
          </a-table-column>
          <a-table-column title="状态" fixed="right" data-index="status" align="center" :width="80">
            <template #cell="{ record }">
              <a-switch v-model="record.status.value" v-permission-disabled="'article-status'" size="small" type="round"
                :loading="record.loading" :beforeChange="() => {
                    return (record.switch = true);
                  }
                  " @change="onStatusChange($event, record)" :checked-value="1" :unchecked-value="0" />
            </template>
          </a-table-column>
          <a-table-column title="操作" align="center" :width="140">
            <template #cell="{ record }">
              <a-space>
                <a-button @click="onCreate(record.id)" v-permission="'article-update'" size="small">编辑</a-button>
                <div v-permission="'article-delete'">
                  <a-popconfirm content="确定删除吗？" @ok="onDelete(record.id)">
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
    </template>
  </layout-body-content>
</template>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance } from "vue";
import createComponent from "./create.vue";
import {
  deleteArticleApi,
  getArticleGatherApi,
  getArticleListApi,
  updateStatusArticleApi,
} from "@/api/article";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const createComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
  proxy?.$refs["createComponentRef"]?.open(id);
};

const gatherLoading = ref<boolean>(false);

const onGather = () => {
  gatherLoading.value = true;
  getArticleGatherApi()
    .then((res: Result) => {
      $utils.successMsg(res);
      toInit(true);
      gatherLoading.value = false;
    })
    .catch((err: ResultError) => {
      gatherLoading.value = false;
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
  getArticleListApi(obj)
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

const onStatusChange = (val: any, row: any) => {
  if (row.switch === true) {
    row.loading = true;
    updateStatusArticleApi({
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
  deleteArticleApi({ id })
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
</script>