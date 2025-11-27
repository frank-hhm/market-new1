<template>
    <a-modal title="跟单配置" @BeforeOk="onSave" @BeforeCancel="close" width="700px" :top="useSetting().ModalTop"
        class="modal" v-model:visible="visible" :align-center="false" title-align="start" render-to-body>
        <div v-loading="initLoading">
            <a-form :model="configForm" ref="configFormRef">
                <a-form-item :label-col-flex="labelColFlex" label="跟单收益比例" field="follow_revenue" tooltip="单位%">
                    <a-input-number v-model="configForm.follow_revenue" placeholder="请输入" :precision="2"></a-input-number>
                </a-form-item>
                <a-form-item :label-col-flex="labelColFlex" label="一级返佣比例" field="follow_one_rate" tooltip="单位%">
                    <a-input-number v-model="configForm.follow_one_rate" placeholder="请输入" :precision="2"></a-input-number>
                </a-form-item>
                <a-form-item :label-col-flex="labelColFlex" label="二级返佣比例" field="follow_two_rate" tooltip="单位%">
                    <a-input-number v-model="configForm.follow_two_rate" placeholder="请输入" :precision="2"></a-input-number>
                </a-form-item>
            </a-form>
        </div>
        <template #footer>
            <a-space>
                <a-button @click="close()">取消</a-button>
                <a-button type="primary" @click="onSave()" :loading="btnLoading" :disabled="btnLoading">确定</a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "memberCreate",
};
</script>

<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { EnumItemType, Result, ResultError } from "@/types";
import { useAppStore, useEnumStore } from "@/store";
import { getConfigApi, saveConfigApi } from "@/api/system/config";

const labelColFlex = ref<string>("120px");

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const statusEnum = ref<EnumItemType[]>(
    useEnumStore().getEnumItem("StatusEnum")
);

const visible = ref<boolean>(false);


const initLoading = ref<boolean>(false);

const btnLoading = ref<boolean>(false);

const configFormRef = ref<HTMLElement>();

const configForm = ref<any>({
    follow_revenue: null,
    follow_one_rate:null,
    follow_two_rate: null,
});
const toInit = () => {
    initLoading.value = true;
    getConfigApi("follow").then(
        (res: Result) => {
            configForm.value = res.data;
            configForm.value.follow_revenue = Number(res.data.follow_revenue)
            configForm.value.follow_one_rate = Number(res.data.follow_one_rate)
            configForm.value.follow_two_rate = Number(res.data.follow_two_rate)

            initLoading.value = false;
        },
        (err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        }
    );
};

const onSave = () => {
    proxy?.$refs["configFormRef"]?.validate((valid: any, fields: any) => {
        if (!valid) {
            btnLoading.value = true;
            saveConfigApi(configForm.value).then(
                (res: Result) => {
                    btnLoading.value = false;
                    useAppStore().getSystemInfo();
                    $utils.successMsg(res.message);
                    close()
                },
                (err: ResultError) => {
                    btnLoading.value = false;
                    $utils.errorMsg(err);
                }
            );
        }
    });
};

const open = () => {
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    proxy?.$refs["createRef"]?.resetFields();
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>