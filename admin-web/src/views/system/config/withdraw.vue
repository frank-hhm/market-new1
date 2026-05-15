<template>
    <div  v-loading="initLoading">
        <a-form :model="configForm" ref="configFormRef">
            <a-card title="代理提现" class="card">
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="代理提现手续费" field="agent_withdrawal_rate">
                        <a-input-number v-model="configForm.agent_withdrawal_rate" placeholder="比例" allow-clear/>
                        <template #help>
                            <span>百分比 * 代理提现金额 进行计算</span>
                        </template>
                    </a-form-item>
                    <a-form-item class="mt20" :label-col-flex="labelColFlex">
                        <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                    </a-form-item>
                </div>
            </a-card>
            <a-card title="用户提现" class="card mt12">
                <div class="mt20 card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="其他提现手续费" field="member_withdrawal_rate">
                        <a-input-number v-model="configForm.member_withdrawal_rate" placeholder="比例" allow-clear/>
                        <template #help>
                            <span>百分比 * 提现金额 进行计算</span>
                        </template>
                    </a-form-item>
                    <a-form-item  class="mt20" :label-col-flex="labelColFlex">
                        <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                    </a-form-item>
                </div>
            </a-card>
            <a-card title="U提现" class="card mt12">
                <!-- <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="U提现手续费" field="member_usdt_withdrawal_rate">
                        <a-input-number v-model="configForm.member_usdt_withdrawal_rate" placeholder="比例" allow-clear/>
                        <template #help>
                            <span>百分比 * 提现金额 进行计算</span>
                        </template>
                    </a-form-item>
                </div> -->
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="U提现手续费" field="member_usdt_withdrawal_money">
                        <a-input-number v-model="configForm.member_usdt_withdrawal_money" placeholder="U提现手续费" allow-clear/>
                    </a-form-item>
                </div>
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="状态" field="member_usdt_withdrawal_status">
                        <a-switch v-model="configForm.member_usdt_withdrawal_status" :checked-value="1"
                            :unchecked-value="0" />
                    </a-form-item>
                </div>
                <a-form-item :label-col-flex="labelColFlex">
                    <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                </a-form-item>
            </a-card>
            <a-card title="支付提现" class="card mt12">
                <div class="card-form-box">
                    <a-form-item :label-col-flex="labelColFlex" label="状态" field="member_alipay_withdrawal_status">
                        <a-switch v-model="configForm.member_alipay_withdrawal_status" :checked-value="1"
                            :unchecked-value="0" />
                    </a-form-item>
                </div>
                <a-form-item :label-col-flex="labelColFlex">
                    <a-button type="primary" @click="onSave" :loading="btnLoading" :disabled="btnLoading">保存</a-button>
                </a-form-item>
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
    agent_withdrawal_rate:0,
    member_withdrawal_rate:0,
    member_usdt_withdrawal_rate:0,
    member_alipay_withdrawal_status: 0,
    member_usdt_withdrawal_status: 0,
    member_usdt_withdrawal_money:0
})

const initLoading = ref<boolean>(false)

const toInit = () => {
    initLoading.value = true
    getConfigApi('agent').then((res: Result) => {
        configForm.value = res.data
        configForm.value.member_usdt_withdrawal_money = Number(res.data.member_usdt_withdrawal_money);
        configForm.value.agent_withdrawal_rate = Number(res.data.agent_withdrawal_rate);
        configForm.value.member_withdrawal_rate = Number(res.data.member_withdrawal_rate);
        configForm.value.member_usdt_withdrawal_rate = Number(res.data.member_usdt_withdrawal_rate);
        configForm.value.member_usdt_withdrawal_status = Number(res.data.member_usdt_withdrawal_status);
        configForm.value.member_alipay_withdrawal_status = Number(res.data.member_alipay_withdrawal_status);
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
    width:600px;
}
</style>