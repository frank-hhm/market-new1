<template>
    <layout-body-tabs :tabs="headTab" v-model="tabActive" heightFil @change="changeTab" v-loading="initLoading">
        <!-- <template v-if="tabActive == 'cnshuhai'" >
            <div class="form-main">
                <a-form :model="configForm" ref="configFormRef" >
                    <div class="card-form-box">
                        <a-form-item :label-col-flex="labelColFlex">
                            <a-alert>未有账号可以
                                <a href="http://www.cnshuhai.com" class="text-red" target="_blank">点击这里</a>
                                进入第三方平台注册</a-alert>
                        </a-form-item>

                        <a-form-item :label-col-flex="labelColFlex" label="账号" field="cnshuhai_username">
                            <a-input v-model="configForm.cnshuhai_username" placeholder="请输入账号" allow-clear />
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex" label="密码" field="cnshuhai_password">
                            <a-input type="password" v-model="configForm.cnshuhai_password" placeholder="请输入密码"
                                allow-clear />
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex" label="订阅市场" field="cnshuhai_subscribe">
                            <a-input v-model="configForm.cnshuhai_subscribe" placeholder="请输入订阅市场"
                                allow-clear />
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex">
                            <a-button type="primary" @click="onSave" :loading="btnLoading"
                                :disabled="btnLoading">保存</a-button>
                        </a-form-item>
                    </div>
                </a-form>
            </div>
        </template> -->
        <template v-if="tabActive == 'itick'" >
            <div class="form-main">
                <a-form :model="configForm" ref="configFormRef" >
                    <div class="card-form-box">
                        <a-form-item :label-col-flex="labelColFlex" label="Key" field="密钥">
                            <a-input v-model="configForm.itick_key" placeholder="请输入密钥" allow-clear />
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex">
                            <a-button type="primary" @click="onSave" :loading="btnLoading"
                                :disabled="btnLoading">保存</a-button>
                        </a-form-item>
                    </div>
                </a-form>
            </div>
        </template>
    </layout-body-tabs>
</template>
<script lang="ts" setup>
import { getConfigApi, saveConfigApi } from "@/api/system/config";
import { EnumType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance } from "vue";
const labelColFlex = ref<string>("80px");

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const headTab = ref<EnumType>([
    // {
    //     name: "数海",
    //     value: 'cnshuhai',
    // },
    {
        name: "itick",
        value: 'itick',
    }
]);

const tabActive = ref<string>('itick');

const changeTab = (tab: string) => {
    tabActive.value = tab;
};

const configFormRef = ref<HTMLElement>();

const configForm = ref<any>({
    cnshuhai_username: '',
    cnshuhai_password: '',
    cnshuhai_subscribe:'',
    itick_key:""
})


const initLoading = ref<boolean>(false)

const toInit = () => {
    initLoading.value = true
    getConfigApi('market').then((res: Result) => {
        configForm.value = res.data
        configForm.value.cnshuhai_username = res.data.cnshuhai_username;
        configForm.value.cnshuhai_password = res.data.cnshuhai_password;
        configForm.value.cnshuhai_subscribe = res.data.cnshuhai_subscribe;
        configForm.value.itick_key = res.data.itick_key;
        
        initLoading.value = false;
    }, (err: ResultError) => {
        initLoading.value = false;
        $utils.errorMsg(err);
    })
}

const btnLoading = ref<boolean>(false)

const onSave = () => {
    proxy?.$refs['configFormRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            btnLoading.value = true;
            saveConfigApi(configForm.value).then((res: Result) => {
                btnLoading.value = false;
                $utils.successMsg(res.message);
            }, (err: ResultError) => {
                btnLoading.value = false;
                $utils.errorMsg(err);
            })
        }
    })
}

onMounted(() => {
    toInit();
})
</script>

<style scoped>
.form-main {
    padding-top: 40px;
    width: 600px;
}
</style>
  