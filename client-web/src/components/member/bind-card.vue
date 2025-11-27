<template>
    <a-modal :title="'绑定身份证'" @BeforeCancel="close" :width="'400px'" :top="useSetting().ModalTop" class="modal"
        v-model:visible="visible" :align-center="true" title-align="start" :mask-closable="false" render-to-body>
        <div class="member-bind-card-body">
            <a-form class="login-body-form" :model="cardFrom" layout="vertical" ref="cardRef" :rules="cardRules">
                <a-form-item field="real_name" label="姓名">
                    <a-input v-model="cardFrom.real_name" placeholder="请输入姓名" size="large">
                        <template #prefix>
                            <icon-user />
                        </template>
                    </a-input>
                </a-form-item>
                <a-form-item field="card_number" label="证件号码">
                    <a-input v-model="cardFrom.card_number" placeholder="请输入证件号码" allow-clear size="large">
                        <template #prefix>
                            <icon-safe />
                        </template>
                    </a-input>
                </a-form-item>
            </a-form>
        </div>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close()">取消</a-button>
                <a-button type="primary" @click="onSaveOk()" :loading="btnLoading" :disabled="btnLoading">提交</a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script lang="ts">
export default {
    name: "member-bind-card",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { Result, ResultError } from "@/types";
import { getAgreementApi } from "@/api/common";
import { useMemberStore } from "@/store";
import { ValidatedError } from "@arco-design/web-vue";
import { bindMemberRealApi } from "@/api/member";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const visible = ref<boolean>(false);

const btnLoading = ref<boolean>(false);

const cardFrom = reactive({
    real_name: "",
    card_number: ""

});
const cardRules = {
    real_name: [{ required: true, message: "请填写您的姓名", trigger: ['change', 'blur'] }],
    card_number: [
        { required: true, message: "请填写您的证件号码", trigger: ['change', 'blur'] },
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                if (!$utils.idCard(value)) {
                    callback('证件号码有误')
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
};


const onSaveOk = () => {
    proxy?.$refs['cardRef']?.validate((error: undefined | Record<string, ValidatedError>) => {
        if (error === undefined) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            bindMemberRealApi(cardFrom).then((res: Result) => {
                $utils.successMsg(res.message);
                $utils.initMember();
                useMemberStore().setBindCardModal(false);
            }).catch((err: ResultError) => {
                $utils.errorMsg(err);
                btnLoading.value = false;
            })
        }
    })
}
const open = (isSet: boolean = true) => {
    visible.value = true;
    btnLoading.value = false;
    if (isSet) {
        useMemberStore().setBindCardModal(true);
    }
};

const close = (isSet: boolean = true) => {
    visible.value = false;
    
    proxy?.$refs['cardRef']?.resetFields();
    if (isSet) {
        useMemberStore().setBindCardModal(false);
    }
    return true;
};

watch(
    () => useMemberStore().bindCardModal,
    (val) => {
        if (val === true) {
            open(false);
        } else {
            close(false);
        }
    },
    {
        deep: true,
        immediate: true
    }
);

defineExpose({ open });
</script>