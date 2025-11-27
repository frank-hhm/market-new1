<template>
    <div>
        <a-modal :title="operation == 'create' ? '添加板块' : '编辑板块'" @BeforeOk="onCreateOk" @BeforeCancel="close"
            :width="isMobile ? 'calc(100% - 20px)' : '400px'" :top="useSetting().ModalTop" class="modal"
            v-model:visible="visible" :align-center="false" title-align="start" render-to-body>
            <a-form layout="vertical" :model="createForm" ref="createRef" :rules="createRules">
                <a-form-item label="板块名称" field="sector_name">
                    <a-input v-model="createForm.sector_name" placeholder="请输入板块名称"></a-input>
                </a-form-item>
                <a-form-item label="排序" field="sort">
                    <a-input-number v-model="createForm.sort" :min="0" :max="10000" />
                </a-form-item>
            </a-form>
            <template #footer>
                <a-space>
                    <a-button v-btn @click="close">取消</a-button>
                    <a-button v-btn type="primary" :disabled="btnLoading" :loading="btnLoading"
                        @click="onCreateOk">确定</a-button>
                </a-space>
            </template>
        </a-modal>
    </div>
</template>

<script lang="ts">
export default {
    name: 'productSectorCreate'
}
</script>

<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive } from "vue";
import { createProductSectorApi, updateProductSectorApi, getDetailProductSectorApi } from "@/api/product-sector";
import type { Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { storeToRefs } from "pinia";
import { useAppStore } from "@/store";

const { isMobile } = storeToRefs(useAppStore());
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const visible = ref<boolean>(false);

const createRef = ref<HTMLElement>();

const createForm = ref<any>({
    sector_name: "",
    sort: null
});

const createRules: any = reactive({
    sector_name: [{ required: true, message: "板块名称不能为空！" }],
});

const btnLoading = ref<boolean>(false);

const onCreateOk = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createProductSectorApi(createForm.value);
            } else {
                operationApi = updateProductSectorApi(
                    Object.assign(
                        {
                            id: operationId.value,
                        },
                        createForm.value
                    )
                );
            }
            operationApi
                .then((res: Result) => {
                    $utils.successMsg(res.message);
                    close();
                    emit("success");
                    btnLoading.value = false;
                })
                .catch((err: ResultError) => {
                    $utils.errorMsg(err);
                    btnLoading.value = false;
                });
        }
    });
};
const open = (id = 0,sort:number = 0, name: string = '') => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
        createForm.value.sector_name = name;
        createForm.value.sort = sort;

    } else {
        operation.value = "create";
    }
    visible.value = true;
};

const close = () => {
    proxy?.$refs['createRef']?.resetFields();
    operationId.value = 0;
    visible.value = false;
    return true
};

defineExpose({ open });
</script>
