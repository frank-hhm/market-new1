<template>
  <div>
    <a-form ref="configFormRef">
      <a-card title="当前版本信息" class="card" v-loading="initLoading">
        <template #extra>
          <a-space>
            <a-tooltip content="刷新">
              <a-button @click="toInit()" size="small"
                ><icon-refresh
              /></a-button>
            </a-tooltip>
          </a-space>
        </template>
        <a-form-item :label-col-flex="labelColFlex" label="当前版本">
          <div>
            <div>{{ versionData.version }}</div>
            <div>{{ versionData.desc }}</div>
          </div>
        </a-form-item>
        <a-form-item :label-col-flex="labelColFlex" label="当前环境">
          <a-radio-group
            type="button"
            size="large"
            v-model="versionData.env_name"
          >
            <a-radio value="dev" :disabled="versionData.env_name !== 'dev'"
              >开发环境</a-radio
            >
            <a-radio value="uat" :disabled="versionData.env_name !== 'uat'"
              >测试环境</a-radio
            >
            <a-radio value="pro" :disabled="versionData.env_name !== 'pro'"
              >线上生产环境</a-radio
            >
          </a-radio-group>
        </a-form-item>
      </a-card>
    </a-form>

    <div class="mt12"></div>

    <a-card title="版本列表" class="card">
      <a-table
        :loading="initDataLoading"
        :data="versionList"
        row-key="id"
        isLeaf
        :pagination="false"
        :scroll="{
          x: '100%',
        }"
        :table-layout-fixed="true"
      >
        <template #empty></template>
        <template #columns>
          <a-table-column
            title="版本号"
            :fixed="isMobile ? undefined : 'right'"
            data-index="version"
            align="center"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.version }}</div>
            </template>
          </a-table-column>
          <a-table-column title="描述" data-index="desc" :width="120">
            <template #cell="{ record }">
              <div>{{ record.desc }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="提交时间"
            data-index="create_time"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.create_time }}</div>
            </template>
          </a-table-column>
          <a-table-column title="状态" data-index="status.name" :width="120">
            <template #cell="{ record }">
              <div>{{ record.status.name }}</div>
            </template>
          </a-table-column>
          <a-table-column
            title="更新执行时间"
            data-index="publish_time"
            :width="120"
          >
            <template #cell="{ record }">
              <div>{{ record.publish_time || "--" }}</div>
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
                <div
                  v-if="record.status.value == 1"
                  v-permission="'version-publish'"
                >
                  <a-popconfirm
                    content="确定推送更新吗？"
                    @ok="onPublishVersion(record.id)"
                  >
                    <template #icon>
                      <icon-exclamation-circle-fill type="red" />
                    </template>
                    <a-button size="small">推送更新</a-button>
                  </a-popconfirm>
                </div>
              </a-space>
            </template>
          </a-table-column>
        </template>
      </a-table>
      <div class="mt12 flex end">
        <page :listPage="listPage" @change="pageChange"></page>
      </div>
    </a-card>
  </div>
</template>
<script lang="ts" setup>
import { getCurrentInstance, onMounted, ref } from "vue";
import {
  getVersionDataListApi,
  getVersionDefaultApi,
  publishVersionApi,
} from "@/api/version";
import { PageLimitType, Result, ResultError } from "@/types";
import { useAppStore } from "@/store";
import { useSetting } from "@/hooks/useSetting";
import { storeToRefs } from "pinia";

const { isMobile } = storeToRefs(useAppStore());

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("180px");

const versionData = ref<any>({});

const initLoading = ref<boolean>(false);
const toInit = () => {
  initLoading.value = true;
  toInitData();
  getVersionDefaultApi()
    .then((res: Result) => {
      initLoading.value = false;
      versionData.value = res.data;
    })
    .catch((err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    });
};

const versionList = ref<any>([]);

const initDataLoading = ref<boolean>(false);
const toInitData = () => {
  initDataLoading.value = true;
  let obj: any = {};
  obj.page = listPage.value.page;
  obj.limit = listPage.value.limit;
  initLoading.value = true;
  getVersionDataListApi(obj)
    .then((res: Result) => {
      versionList.value = res.data.data;
      listPage.value.total = res.data.total;

      setTimeout(() => {
        initDataLoading.value = false;
      }, 300);
    })
    .catch((error: ResultError) => {
      initDataLoading.value = false;
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

const onPublishVersion = (id: number | string) => {
  publishVersionApi({
    id,
  })
    .then((res: Result) => {
      $utils.successMsg(res.message);
      setTimeout(() => {
        toInit();
      }, 300);
    })
    .catch((error: ResultError) => {
      $utils.errorMsg(error);
    });
};
</script>