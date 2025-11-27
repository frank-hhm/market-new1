<template>
    <a-drawer class="drawer-default" :title="operation == 'create' ? '添加文章' : '编辑文章'" v-model:visible="visible" :width="800">

        <div v-if="visible" v-loading="initLoading">
            <a-form :model="createForm" ref="createRef" :rules="createRules">
                <a-form-item :label-col-flex="labelColFlex" label="标题" field="title">
                    <a-input v-model="createForm.title" placeholder="请输入标题"></a-input>
                </a-form-item>
                <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                    <a-radio-group v-model="createForm.status">
                        <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name
                        }}</a-radio>
                    </a-radio-group>
                </a-form-item>
                <a-form-item :label-col-flex="labelColFlex" label="内容" field="content">
                    <editor ref="editorRef" height="600px" v-model="createForm.content"></editor>
                </a-form-item>
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
    name: "noticeCreate",
};
</script>
<script lang="ts" setup>
import { createNoticeApi, getDetailNoticeApi, updateNoticeApi } from "@/api/notice";
import { useEnumStore } from "@/store";
import { EnumItemType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";

const labelColFlex = ref<string>("80px");

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
    getDetailNoticeApi({
        id: operationId.value
    }).then((res: any) => {
        createForm.value.title = res.data.title;
        createForm.value.content = res.data.content;
        proxy?.$refs["editorRef"]?.setContent(res.data.content);
        createForm.value.status = res.data.status.value;
        initLoading.value = false;
    })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
}

const createForm = ref<any>({
    title: "",
    content: "",
    status: 1,
});

const createRules: any = reactive({
    title: [{ required: true, message: "请输入标题！", trigger: "blur" }],
});

const editorRef = ref<HTMLElement>()

const onSave = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createNoticeApi(createForm.value);
            } else {
                operationApi = updateNoticeApi(
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