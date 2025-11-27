<template>
    <a-modal :title="operation == 'create' ? '添加轮播图' : '编辑轮播图'" @BeforeOk="onCreateOk" @BeforeCancel="close" width="500px"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
        render-to-body>

        <div v-loading="initLoading">
            <a-form :model="createForm" ref="createRef" :rules="createRules">
                <a-form-item :label-col-flex="labelColFlex" label="图片" field="cover">
                    <upload-btn v-model="createForm.cover" width="240px" height="120px" count="1"></upload-btn>
                </a-form-item>

                <a-form-item :label-col-flex="labelColFlex" label="链接" field="link">
                    <a-input v-model="createForm.link" placeholder="请输入链接"></a-input>
                </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="排序" field="sort">
                <a-input-number v-model="createForm.sort" :min="0" :max="10000" />
            </a-form-item>
                <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                    <a-radio-group v-model="createForm.status">
                        <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name
                        }}</a-radio>
                    </a-radio-group>
                </a-form-item>
            </a-form>
        </div>
        <template #footer>
            <a-space>
                <a-button @click="close()">取消</a-button>
                <a-button type="primary" @click="onCreateOk()" :loading="btnLoading" :disabled="btnLoading">确定</a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "bannerCreate",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { createBannerApi, updateBannerApi, getDetailBannerApi } from "@/api/banner";
import { EnumItemType, Result, ResultError } from "@/types";
import { useEnumStore } from "@/store"

const labelColFlex = ref<string>("80px");

const emit = defineEmits(["success"]);


const statusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));

const operation = ref<string>("create");

const operationId = ref<number>(0);

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false)

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
                operationApi = createBannerApi(createForm.value);
            } else {
                operationApi = updateBannerApi(
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

const toInit = () => {
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getDetailBannerApi({
        id: operationId.value
    }).then((res: any) => {
        createForm.value.cover = res.data.cover;
        createForm.value.link = res.data.link;
        createForm.value.sort = res.data.sort;
        createForm.value.status = res.data.status.value;
        initLoading.value = false;
    })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
}


const createForm = ref<any>({
    cover: "",
    link: "",
    sort:0,
    status: 1,
});

const createRules: any = reactive({
    cover: [{ required: true, message: "请选择图片！", trigger: "blur" }],
});


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
    return true;
};

defineExpose({ open });
</script>