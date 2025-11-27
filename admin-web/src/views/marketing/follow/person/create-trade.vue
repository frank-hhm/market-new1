<template>
    <a-drawer class="drawer-default" :title="operation == 'create' ? '添加交易单' : '编辑交易单'" v-model:visible="visible"
        :width="800">

        <div v-if="visible" v-loading="initLoading">
            <a-form :model="createForm" ref="createRef" :rules="createRules">
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="姓名" field="nickname">
                            <a-input v-model="createForm.nickname" placeholder="请输入标题"></a-input>
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex" label="个性签名" field="signature">
                            <a-textarea v-model="createForm.signature" placeholder="请输入个性签名"></a-textarea>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="头像" field="avatar">
                            <upload-btn v-model="createForm.avatar" width="80px" height="80px" count="1"></upload-btn>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月收益率" field="revenue_text">
                            <a-input-number v-model="createForm.revenue_text" placeholder="请输入" :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月胜率" field="month_win_rate_text">
                            <a-input-number v-model="createForm.month_win_rate_text" placeholder="请输入" :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月分润(usd)" field="month_profit_text">
                            <a-input-number v-model="createForm.month_profit_text" placeholder="请输入" :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="累计分润率" field="total_profit_text">
                            <a-input-number v-model="createForm.total_profit_text" placeholder="请输入" :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="交易员保证金(usd)" field="deposit_text">
                            <a-input-number v-model="createForm.deposit_text" placeholder="请输入" :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                            <a-radio-group v-model="createForm.status">
                                <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name
                                    }}</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                </a-row>
            </a-form>
        </div>
        <template #footer>
            <a-space>
                <a-button @click="close">取消</a-button>
                <a-button @click="onSave()" type="primary" :loading="btnLoading"
                    :disabled="initLoading || btnLoading">确定</a-button>
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
import { createFollowPersonApi, getDetailFollowPersonApi, updateFollowPersonApi } from "@/api/follow/person";
import { useEnumStore } from "@/store";
import { EnumItemType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";

const labelColFlex = ref<string>("120px");

const statusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false)

const btnLoading = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const toInit = () => {
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getDetailFollowPersonApi({
        id: operationId.value
    }).then((res: any) => {
        createForm.value.title = res.data.title;
        createForm.value.title_desc = res.data.title_desc;
        createForm.value.cover = res.data.cover;
        createForm.value.content = res.data.content;
        createForm.value.status = res.data.status.value;
        initLoading.value = false;
    })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
}

const createForm = ref<any>({
    nickname: "",
    avatar: "",
    signature: "",
    revenue_text: "",
    month_win_rate_text: "",
    month_profit_text: "",
    total_profit_text: "",
    deposit_text: "",
    status: 1,
});

const createRules: any = reactive({
    nickname: [{ required: true, message: "请输入姓名！", trigger: "blur" }],
    avatar: [{ required: true, message: "请选择图片！", trigger: "blur" }],
    signature: [{ required: true, message: "请输入签名！", trigger: "blur" }],
});


const onSave = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createFollowPersonApi(createForm.value);
            } else {
                operationApi = updateFollowPersonApi(
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
    })
}

const open = (id: number = 0) => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
    } else {
        operation.value = "create";
    }
    nextTick(() => {
        toInit();
    });
    visible.value = true;
};

const close = () => {
    proxy?.$refs['createRef']?.resetFields();
    operationId.value = 0;
    visible.value = false;
};

defineExpose({ open });
</script>