<template>
  <layout-body-content pageHeader hideFooter>
    <template v-slot:page-header-left> 服务器数据缓存 </template>
    <template v-slot:page-header-right> </template>
    <template v-slot:content="{}">
      <a-row :gutter="[20, 20]">
        <a-col
          v-for="(item, index) in cacheList"
          :key="index"
          :md="12"
          :xs="24"
          :xl="12"
        >
          <a-card :style="{ width: '100%' }" :title="item.name" hoverable>
            <template #extra>
              <a-popconfirm content="确定清理吗？" @ok="onClean(item.type)">
                <template #icon>
                  <icon-exclamation-circle-fill type="red" />
                </template>
                <a-button type="primary" size="small">清理</a-button>
              </a-popconfirm>
            </template>
            <div v-for="(items, idx) in item.descList" :key="idx">
              {{ items }}
            </div>
          </a-card>
        </a-col>
      </a-row>
    </template>
  </layout-body-content>
</template>
          
<script lang="ts" setup>
import { ref, getCurrentInstance, reactive, onMounted } from "vue";

import type { Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";
import { cleanCacheApi } from "@/api/system/config";

const { isMobile } = storeToRefs(useAppStore());

const cacheList = ref<any>([
  {
    name: "用户数据缓存",
    type: "user",
    descList: ["清除后当前在线用户将全部需要重新登录"],
  },
  {
    name: "用户登录缓存",
    type: "user_login",
    descList: ["清除后当前在线用户将全部需要重新登录"],
  },
  {
    name: "代理商登录缓存",
    type: "agent_login",
    descList: ["清除后当前在线用户将全部需要重新登录"],
  },
  {
    name: "后台登录缓存",
    type: "admin_login",
    descList: ["清除后当前在线用户将全部需要重新登录"],
  },
  {
    name: "产品信息缓存",
    type: "product",
    descList: ["清除当前所有产品信息缓存"],
  },
  {
    name: "行情数据缓存",
    type: "market",
    descList: ["清除行情数据缓存"],
  },
  {
    name: "系统配置信息缓存",
    type: "system",
    descList: ["清除系统配置信息缓存"],
  },
]);

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const btnLoading = ref<boolean>(false);
const initLoading = ref<boolean>(false);

const onClean = (type: string) => {
  if (btnLoading.value) {
    return;
  }
  btnLoading.value = true;
  cleanCacheApi({
    type: type,
  })
    .then((res: Result) => {
      $utils.successMsg(res.message);
      btnLoading.value = false;
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
      btnLoading.value = false;
    });
};
</script>