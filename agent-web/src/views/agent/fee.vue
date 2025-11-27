<template>
    <a-drawer class="drawer-default" title="手续费设置" v-model:visible="visible" :width="700">
        <template #default>

            <a-form :model="formModel" ref="formRef" :rules="formRules">
                <a-table :loading="initLoading" :data="formModel.fees" row-key="id" isLeaf
                    :pagination="false">
                    <template #columns>
                        <a-table-column title="产品" data-index="title" :width="120">
                            <template #cell="{ record }">
                                <div>{{ record.title }}</div>
                            </template>
                        </a-table-column>
                        <a-table-column title="基数" data-index="title" :width="120">
                            <template #cell="{ record, rowIndex }">
                                <a-form-item hide-label hide-asterisk :field="`fees[${rowIndex}].jishu`">
                                    <a-input-number disabled v-model="record.jishu" :precision="2"
                                        allow-clear></a-input-number>
                                </a-form-item>
                            </template>
                        </a-table-column>
                        <a-table-column title="分配" data-index="dist" :width="120">
                            <template #cell="{ record, rowIndex }">
                                <a-form-item hide-label hide-asterisk :field="`fees[${rowIndex}].dist`">
                                    <a-input-number v-model="record.dist" :precision="2" allow-clear></a-input-number>
                                </a-form-item>
                            </template>
                        </a-table-column>
                        <a-table-column title="代理加成" data-index="title" :width="120">
                            <template #cell="{ record, rowIndex }">
                                <a-form-item hide-label hide-asterisk :field="`fees[${rowIndex}].markup`">
                                    <a-input-number v-model="record.markup" :precision="2" allow-clear></a-input-number>
                                </a-form-item>
                            </template>
                        </a-table-column>
                    </template>
                </a-table>
            </a-form>
        </template>
        <template #footer>
            <a-space>
                <a-button @click="close()">取消</a-button>
                <a-button type="primary" :loading="btnLoading" :disabled="btnLoading" @click="onSaveOk()">确定</a-button>
            </a-space>
        </template>
    </a-drawer>
</template>
<script lang="ts">
export default {
    name: "productControl",
};
</script>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";
import { useRouter } from "vue-router";
import { Result, ResultError, PageLimitType } from '@/types';
import { useSetting } from "@/hooks/useSetting";
import { getDetailAgentFeeApi, setDetailAgentFeeApi } from "@/api/agent";

const emit = defineEmits(["success"]);

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const agentId = ref<number>(0);

const agentName = ref<string>("")

const initLoading = ref<boolean>(false)

const toInit = () => {
    initLoading.value = false
    getDetailAgentFeeApi({
        id: agentId.value
    }).then((res: Result) => {
        res.data.forEach((item:any,index:number) => {
            formModel.value.fees[index] = {
                id: item.id,
                title: item.title,
                jishu:Number( item.jishu),
                dist: Number(item.dist),
                markup:  Number(item.markup),
            }
        });
        initLoading.value = false
    }).catch((err: ResultError) => {
        initLoading.value = false
        $utils.errorMsg(err.message);
    });
}

const formModel = ref<any>({
    fees: []
});



const formRules = reactive<any>({
});

const btnLoading = ref<boolean>(false);

const formRef = ref<HTMLElement>();

const onSaveOk = () => {
    proxy?.$refs['formRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;

            setDetailAgentFeeApi(
                Object.assign(
                    {
                        id: agentId.value,
                    },
                    formModel.value
                )
            )
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
    })
}
const visible = ref<boolean>(false);

const open = (id: number = 0, name: string = '') => {
    agentId.value = id;
    agentName.value = name
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    agentId.value = 0;
    agentName.value = '';
    proxy?.$refs['formRef']?.resetFields();
    formModel.value.fees = [];
    visible.value = false;
};

defineExpose({ open });
</script>