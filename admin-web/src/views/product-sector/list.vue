<template>
    <layout-body-content pageHeader hideFooter>
        <template v-slot:page-header-left>
            产品板块管理
        </template>
        <template v-slot:page-header-right>
            <a-space>
                <a-tooltip content="刷新">
                    <a-button @click="toInit" size="small"><icon-refresh /></a-button>
                </a-tooltip>
                <a-button type="primary" size="small" @click="onCreate(0)" v-permission="'product-type-create'">
                    添加板块
                </a-button>
            </a-space>
            <productSectorCreate ref="createComponentRef" @success=" toInit"></productSectorCreate>
        </template>
        <template v-slot:content="{
                        height
                    }">
            <!-- 列表 -->
            <a-table :loading="initLoading" :data="lists" row-key="id" isLeaf :pagination="false" :scroll="{
                        x: '100%',
                        y: height - 39
                    }">
                <template #columns>
                    <a-table-column title="排序"  align="center" data-index="sort" :width="120">
                        <template #cell="{ record }">
                            <span>{{ record.sort }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="板块名称" data-index="sector_name" :minWidth="160">
                        <template #cell="{ record }">
                            <span>{{ record.sector_name }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="创建时间" data-index="create_time" :width="160">
                        <template #cell="{ record }">
                            <span class="text-grey">{{ record.create_time }}</span>
                        </template>
                    </a-table-column>
                    <a-table-column title="操作" align="right" :fixed="!isMobile ? 'right' : undefined" :width="180">
                        <template #cell="{ record }">
                            <a-space>
                                <a-button @click=" onCreate(record.id,record.sort,record.type_name)" v-permission="'product-type-update'"
                                    size="small">编辑</a-button>
                                <div v-permission="'product-type-delete'">
                                    <a-popconfirm content="确定删除吗？" @ok=" onDelete(record.id)">
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
    </layout-body-content>
</template>

<script lang="ts" setup>
import { ref, onMounted, getCurrentInstance } from "vue";
import productSectorCreate from "./create.vue";
import { getProductSectorSelectApi, deleteProductSectorApi } from "@/api/product-sector";
import { PageLimitType, Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";

import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";

const { isMobile } = storeToRefs(useAppStore());
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const initLoading = ref<boolean>(true);

const lists = ref<any>([]);

const createComponentRef = ref<HTMLElement>();

const onCreate = (id: number | string,sort:number = 0 ,name :string = '') => {
    proxy?.$refs["createComponentRef"].open(id,sort,name);
};

const toInit = () => {
    initLoading.value = true;
    getProductSectorSelectApi().then((res: Result) => {
            lists.value = res.data;
            setTimeout(() => {
                initLoading.value = false;
            }, 300);
        })
        .catch((err: ResultError) => {
                initLoading.value = false;
            $utils.errorMsg(err);
        });
};

const onDelete = (id: number) => {
    deleteProductSectorApi({
        id,
    })
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
</script>@/api/product-sector