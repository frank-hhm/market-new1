<template>
    <div v-loading="initLoading">
        <a-form :model="configForm" ref="configFormRef">
            <a-card title="其他配置" class="card">
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="平仓时间差" field="order_ping_time">
                        <a-input-number v-model="configForm.order_ping_time" placeholder="时间" allow-clear>
                            <template #suffix>
                                秒
                            </template>
                        </a-input-number>
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="usdt入金汇率" field="usdt_rate">
                        <a-input-number v-model="configForm.usdt_rate" placeholder="usdt入金汇率" allow-clear :precision="2" />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="usdt出金汇率" field="usdt_out_rate">
                        <a-input-number v-model="configForm.usdt_out_rate" placeholder="usdt出金汇率" allow-clear :precision="2" />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="单笔最低下单金额" field="order_min_price">
                        <a-input-number v-model="configForm.order_min_price" placeholder="金额" allow-clear :precision="2" />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="单笔最高下单金额" field="order_max_price">
                        <a-input-number v-model="configForm.order_max_price" placeholder="金额" allow-clear :precision="2" />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex">
                        <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                    </a-form-item>
                </div>
            </a-card>
            <a-card title="支付宝在线配置" class="card mt12">
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="状态" field="payment_alipays_status">
                        <a-switch v-model="configForm.payment_alipays_status" :checked-value="1" :unchecked-value="0" />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="支付宝Appid" field="payment_alipay_appid">
                        <a-input v-model="configForm.payment_alipay_appid" placeholder="支付宝appid" allow-clear />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="支付宝公钥(public)" field="payment_alipay_public_key">
                        <a-textarea v-model="configForm.payment_alipay_public_key" placeholder="请输入支付宝公钥" allow-clear />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="支付宝私钥(private)" field="payment_alipay_private_key">
                        <a-textarea v-model="configForm.payment_alipay_private_key" placeholder="请输入支付宝私钥" allow-clear />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="应用公钥(app_cert)" field="payment_alipay_app_cert">
                        <a-textarea v-model="configForm.payment_alipay_app_cert" placeholder="请输入应用公钥" allow-clear />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex" label="支付宝根证书(root)" field="payment_alipay_root_cert">
                        <a-textarea v-model="configForm.payment_alipay_root_cert" placeholder="请输入支付宝根证书" allow-clear />
                    </a-form-item>
                    <a-form-item :label-col-flex="labelColFlex">
                        <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                    </a-form-item>
                </div>
            </a-card>
        </a-form>
    </div>
</template>

<script lang="ts" setup>
import { getCurrentInstance, onMounted, ref } from "vue";
import { getConfigApi, saveConfigApi } from "@/api/system/config";
import { Result, ResultError } from "@/types";
import { useAppStore } from "@/store";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("160px");

const configFormRef = ref<HTMLElement>();

const configForm = ref<any>({
    usdt_rate: null,
    usdt_out_rate: null,
    order_max_price: 10000,
    order_min_price: 10,
    payment_alipays_status: 0,
    payment_alipay_appid: "",
    payment_alipay_public_key: "",
    payment_alipay_private_key: "",
    payment_alipay_app_cert: "",
    payment_alipay_root_cert: "",
    order_ping_time:0
})

const initLoading = ref<boolean>(false)

const toInit = () => {
    initLoading.value = true
    getConfigApi('trade').then((res: Result) => {
        configForm.value = res.data
        configForm.value.order_ping_time = Number(res.data.order_ping_time);
        configForm.value.usdt_rate = Number(res.data.usdt_rate);
        configForm.value.order_max_price = Number(res.data.order_max_price);
        configForm.value.order_min_price = Number(res.data.order_min_price);
        configForm.value.payment_alipays_status = Number(res.data.payment_alipays_status);
        configForm.value.payment_alipay_appid = String(res.data.payment_alipay_appid);
        configForm.value.payment_alipay_public_key = String(res.data.payment_alipay_public_key);
        configForm.value.payment_alipay_private_key = String(res.data.payment_alipay_private_key);
        configForm.value.payment_alipay_root_cert = String(res.data.payment_alipay_root_cert);
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
                useAppStore().getSystemInfo();
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
.card-form-box {
    width: 600px;
}
</style>