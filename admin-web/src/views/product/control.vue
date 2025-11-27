<template>
    <div>
        <a-drawer class="drawer-default" title="调控" v-model:visible="visible" :width="700">
            <template #default>
                <div class="flex between items-center">
                    <div class="flex  items-center">
                        <div>当前产品：</div>
                        <div class="ml5 fw">{{ productName }}</div>
                    </div>
                    <a-space class="mt12">
                        <a-button @click="toInit()" :disabled="initLoading">
                            <icon-sync />
                        </a-button>
                        <a-button type="primary" @click="onCreate()" v-permission="'product-create'">添加</a-button>
                    </a-space>
                </div>
                <createControlProductComponent ref="createControlProductComponentRef" @success="toInit(true)">
                </createControlProductComponent>

                <a-table :loading="initLoading" class="mt20" :data="lists" row-key="id" isLeaf :pagination="false">
                    <template #columns>
                        <a-table-column title="产品" data-index="product" :width="120">
                            <template #cell="{ record }">
                                <div>{{ record.product.name || '--' }}</div>
                            </template>
                        </a-table-column>
                        <a-table-column title="价位" data-index="price" :width="80">
                            <template #cell="{ record }">
                                <div>{{ record.price }}</div>
                            </template>
                        </a-table-column>
                        <a-table-column title="开始时间" data-index="execute_time" align="center" :width="160">
                            <template #cell="{ record }">
                                <div class="text-grey">{{ record.execute_time }}</div>
                            </template>
                        </a-table-column>
                        <a-table-column title="结束时间" data-index="complete_time" align="center" :width="160">
                            <template #cell="{ record }">
                                <div class="text-grey">{{ record.complete_time }}</div>
                            </template>
                        </a-table-column>
                        <a-table-column title="操作" align="center" :width="60">
                            <template #cell="{ record }">
                                <a-space>
                                    <div v-permission="'product-delete'">
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
                <div class="mt20 flex end">
                    <page :listPage="listPage" @change="pageChange"></page>
                </div>
            </template>
            <template #footer>
                <a-space>
                    <a-button @click="close">关闭</a-button>
                </a-space>
            </template>
        </a-drawer>
    </div>
</template>


<script lang="ts">
export default {
    name: "productControl",
};
</script>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance, nextTick } from "vue";
import { useRouter } from "vue-router";
import { Result, ResultError, PageLimitType } from '@/types';
import createControlProductComponent from "./create-control.vue";
import { deleteProductPriceApi, getProductPriceListApi } from "@/api/product-price";
import { useSetting } from "@/hooks/useSetting";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const productId = ref<number>(0)

const productName = ref<string>('')

const initLoading = ref<boolean>(false)

const lists = ref<any>([])

const toInit = (isInit: boolean = false) => {
    if (isInit) {
        listPage.value.page = 1;
    }
    let obj: any = {};
    obj.page = listPage.value.page;
    obj.limit = listPage.value.limit;
    obj.pid = productId.value;
    initLoading.value = true;
    getProductPriceListApi(obj).then((res: Result) => {
        lists.value = res.data.data;
        listPage.value.total = res.data.total;
        setTimeout(() => {
            initLoading.value = false;
        }, 300);
    }).catch((err: ResultError) => {
        initLoading.value = false;
        $utils.errorMsg(err.message);
    });
}

const listPage = ref<any>({
    total: 0,
    limit: useSetting().PageLimit.value,
    page: 1,
});

const pageChange = (res: PageLimitType) => {
    listPage.value = res;
    toInit();
};

const createControlProductComponentRef = ref<HTMLElement>()

const onCreate = () => {
    proxy?.$refs["createControlProductComponentRef"]?.open(productId.value, productName.value);
};

const onDelete = (id: number) => {
    deleteProductPriceApi({ id })
        .then((res: Result) => {
            toInit();
            $utils.successMsg(res.message);
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
        });
}


const visible = ref<boolean>(false);

const open = (id: number = 0, name: string = '') => {
    productId.value = id;
    productName.value = name;
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    productId.value = 0;
    productName.value = '';
    visible.value = false;
};

defineExpose({ open });
</script>