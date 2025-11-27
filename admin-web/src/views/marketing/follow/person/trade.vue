<template>
    <a-drawer class="drawer-default" :title="'交易单'" v-model:visible="visible" :width="1200">
        <div v-if="visible" v-loading="initLoading">
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit(true)" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button type="primary" @click="onCreate(0)" size="small"
                    v-permission="'follow-trade-create'">添加交易单</a-button>
                <createComponent ref="createComponentRef" @success="toInit(true)"></createComponent>
            </a-space>
        </div>
        <template #footer>
            <a-space>
                <a-button @click="close">关闭</a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script lang="ts">
export default {
    name: "articleCreate",
};
</script>
<script lang="ts" setup>
import { getFollowTradeListApi } from "@/api/follow/trade";
import { useSetting } from "@/hooks/useSetting";
import { useEnumStore } from "@/store";
import { EnumItemType, PageLimitType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";
import createComponent from "./create-trade.vue"
const labelColFlex = ref<string>("80px");

const statusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false)

const operationId = ref<number>(0);


const lists = ref<any>([]);

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    initLoading.value = true;
    getFollowTradeListApi(obj)
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


const createComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string) => {
    proxy?.$refs["createComponentRef"]?.open(id);
};

const open = (id: number = 0) => {
    operationId.value = id;
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    operationId.value = 0;
    visible.value = false;
};

defineExpose({ open });
</script>