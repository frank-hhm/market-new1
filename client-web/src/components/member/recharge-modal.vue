<template>
    <a-modal v-model:visible="visible" title="入金" @BeforeOk="onCreateSave" @BeforeCancel="close" :width="'400px'"
        :align-center="true" title-align="start" render-to-body :mask-closable="false">
        <div class="recharge-body">
            <template v-if="step === 1">
                <div class="recharge-balance">
                    <div>
                        <div>我的余额</div>
                        <div>
                            <span>$</span>
                            <span class="ml5 text-red fw fz20">{{ memberCoin?.balance || 0 }}</span>
                        </div>
                    </div>
                    <div>
                        <div>汇率</div>
                        <div>1USDT≈{{ config?.usdt_rate || 7 }}cny</div>
                    </div>
                </div>
                <div class="mt20" v-loading="initPaymentLoading">
                    <a-form layout="vertical" :model="createForm" ref="createRef" :rules="createRules">
                        <a-form-item label="入金金额(USDT)" field="money">
                            <a-input-number v-model="createForm.money" model-event="input" @input="onChangeMoney"
                                size="large" placeholder="请输入入金金额" allow-clear mode="embed" />
                        </a-form-item>
                        <a-form-item label="支付方式" field="payment_id">
                            <a-radio-group v-model="createForm.payment_id" @change="onChangePayType">
                                <div v-for="(item, index) in paymentSelect" :key="index" class="payment-item">
                                    <a-radio :value="item.id">
                                        <template #radio="{ checked }">
                                            <div class="payment-items" :class="{ 'payment-items-checked': checked }">
                                                <div class="payment-items-yl-icon" v-if="item.cover" :style="{
                                                    backgroundImage: `url(${item.cover})`
                                                }"></div>
                                                <div class="payment-items-center">
                                                    <div class="charge-name">{{ item.name }}</div>
                                                    <div class="charge-desc" v-if="item.min || item.max">
                                                        限额{{ item.min }} - {{ item.max }}
                                                    </div>
                                                </div>
                                                <div className="payment-item-mask">
                                                    <div className="payment-item-mask-dot"  ></div>
                                                </div>
                                            </div>
                                        </template>
                                    </a-radio>
                                </div>
                            </a-radio-group>
                        </a-form-item>
                        <a-form-item hide-label>
                            <a-button class="mt10 mb20" v-if="paymentSelect.length > 0" type="primary" size="large" long
                                @click="toPay">
                                <span>确定支付</span>
                                <span v-if="createForm?.money > 0 && createForm.payment_id !== 0 && payValue">({{
                                    payValue
                                }}{{ createForm.pay_type == "offline_usdt" ? "USDT" : "CNY" }})</span>
                            </a-button>
                            <a-button v-else disabled size="large" long>暂无支付方式</a-button>
                        </a-form-item>
                    </a-form>
                </div>
                <div class="text-center text-gray">
                    <div>交易由纽约商品交易所及港交所实盘对接</div>
                    <div>合作伙伴：平安保险|金瑞期货|芝加哥商品交易所|CME集团</div>
                    <div>香港交易所|新加坡交易所|欧洲期货交易所</div>
                    <div class="flex items-center center">
                        <div>7*24小时在线客服：</div>
                        <a-button type="text" size="mini" @click="$utils.toKeFu()">联系客服</a-button>
                    </div>
                </div>
            </template>
            <template v-else-if="step === 2">
                <div>
                    <div class="recharge-pay">
                        <span>付款金额:</span>
                        <span class="text-red fw fz20 ml10">{{ payValue }}</span>
                        <span class="ml5">{{ createForm.pay_type == "offline_usdt" ? "USDT" : "CNY" }}</span>
                    </div>


                    <div class="mt20">
                        <div class="flex between">
                            <span>支付方式</span>
                            <span class="text-default fw fz14 ml5"
                                v-if="createForm.pay_type == 'offline_usdt'">USDT转账</span>
                            <span class="text-default fw fz14 ml5"
                                v-else-if="createForm.pay_type == 'offline_bank'">银联转账</span>
                            <span class="text-default fw fz14 ml5"
                                v-else-if="createForm.pay_type == 'offline_alipay'">支付宝转账</span>
                        </div>
                        <div class="payment-setting" v-if="paymentConfig.setting.length > 0">
                            <template v-for="(item, index) in paymentConfig.setting">
                                <div class="payment-setting-item"
                                    v-if="item.type === 'text' || item.type === 'textarea'">
                                    <div class="text-gray">{{ item.key }}</div>
                                    <div class="payment-setting-item-right">
                                        <div class="value">{{ item.value || '' }}</div>
                                        <div class="copy-icon" @click="$utils.copyDomText(item.value || '')">
                                            <icon-copy />
                                        </div>
                                    </div>
                                </div>
                                <div class="payment-setting-item" v-if="item.type === 'image'">
                                    <div class="text-gray">{{ item.key }}</div>

                                    <div class="payment-setting-item-right">
                                        <a-image height="30" :src="item.value"></a-image>
                                        <a-button size="mini" class="ml10"
                                            @click="onPaymentImagePreview(item.value)">查看</a-button>
                                    </div>
                                </div>
                            </template>
                            <a-image-preview v-model:visible="paymentConfigSettingVisible" infinite
                                :src="paymentConfigSettingSrc" />
                        </div>
                        <a-form class="mt20" layout="vertical" :model="createSaveForm" ref="createSaveRef"
                            :rules="createSaveRules">
                            <a-form-item :label="createForm.pay_type == 'offline_usdt' ? '交易ID' : '付款人姓名/账号'"
                                field="account">
                                <a-input v-model="createSaveForm.account" size="large"
                                    :placeholder="'请输入' + (createForm.pay_type == 'offline_usdt' ? '交易ID' : '付款人姓名/账号')"
                                    allow-clear />
                            </a-form-item>
                            <a-form-item label="备注" field="remark">
                                <a-textarea v-model="createSaveForm.remark" size="large" rows="4" placeholder="请输入备注"
                                    allow-clear />
                            </a-form-item>
                        </a-form>
                    </div>
                </div>
            </template>
        </div>

        <template #footer>
            <a-space>
                <a-button v-btn @click="close()" :disabled="btnLoading">关闭</a-button>
                <a-button v-if="step > 1" v-btn :disabled="btnLoading" @click="onStep(1)">返回上一步</a-button>
                <a-button v-if="step > 1" v-btn :loading="btnLoading" type="primary"
                    @click="onCreateSave">确定已支付,提交审核</a-button>
            </a-space>
        </template>
    </a-modal>
</template>

<script lang="ts">
export default {
    name: "member-recharge-modal",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, reactive, onMounted, watch, computed, nextTick } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { useAppStore, useMemberStore } from "@/store";
import { storeToRefs } from "pinia";
import { getPaymentConfigApi, getPaymentSelectApi } from "@/api/common";
import { Result, ResultError } from "@/types";
import { ValidatedError } from "@arco-design/web-vue";
import { rechargeTransferApi, usdtpayTransferApi } from "@/api/member";
import { Notification } from "@arco-design/web-vue";

const { member, memberCoin } = storeToRefs(useMemberStore());
const { config } = storeToRefs(useAppStore());
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const visible = ref<boolean>(false);

const btnLoading = ref<boolean>(false);

const step = ref<number>(1);

const createForm = ref<any>({
    money: null,
    pay_type: null,
    payment_id: null
});

const createRules: any = reactive({
    money: [{ required: true, message: "请输入入金金额", trigger: ["blur", "change"] }],
    payment_id: [{ required: true, message: "请选择支付方式", trigger: ["blur", "change"] }],
});

const payValue = ref<any>(null);
const initPayValue = () => {
    nextTick(() => {
        if (createForm.value.money) {
            if (createForm.value.pay_type == 'offline_usdt') {
                payValue.value = createForm.value?.money
            } else {
                payValue.value = (Number(createForm.value?.money) * Number(config?.value.usdt_rate)).toFixed(2)
            }
        }
    });
};

const onChangeMoney = (val: any) => {
    initPayValue()
};

const onChangePayType = (val: any) => {
    let index = paymentSelect.value.findIndex(item => item.id == Number(val));
    createForm.value.pay_type = paymentSelect.value[index].type?.value;
    initPayValue()
};


const initPaymentLoading = ref<boolean>(false);

const paymentSelect = ref<any[]>([]);
const initPaymentData = () => {
    initPaymentLoading.value = true;
    getPaymentSelectApi()
        .then((res: Result) => {
            paymentSelect.value = res.data;
            initPaymentLoading.value = false;
        }).catch((err: ResultError) => {
            $utils.errorMsg(err);
            close()
        })
}

const paymentConfig = ref<any>({});

const paymentConfigSettingSrc = ref<string>("");

const onPaymentImagePreview = (src: string) => {
    paymentConfigSettingSrc.value = src;
    paymentConfigSettingVisible.value = true;
}

const paymentConfigSettingVisible = ref<boolean>(false);

const createSaveForm = ref<any>({
    account: '',
    remark: '',
})

const createSaveRules: any = reactive({
    account: [{
        validator: (value: string | undefined, callback: (error?: string) => void) => {
            if (createSaveForm.value.account == '' || !createSaveForm.value.account) {
                callback(createForm.value.pay_type == 'offline_usdt' ? '交易ID' : '付款人姓名/账号')
            } else {
                callback()
            }
        },
        trigger: ['change', 'blur'],
    }],
});
const toPay = () => {
    proxy?.$refs['createRef']?.validate((error: undefined | Record<string, ValidatedError>) => {
    })
    if (createForm.value.money <= 0) {
        $utils.errorMsg('请输入正确的金额');
        return false;
    }
    if (createForm.value.payment_id == 0 || !createForm.value.payment_id) {
        $utils.errorMsg('请输入选择支付方式');
        return false;
    }
    initPaymentLoading.value = true;
    getPaymentConfigApi(createForm.value.payment_id)
        .then((res: Result) => {
            paymentConfig.value = res.data || {};
            step.value = 2
            initPaymentLoading.value = false;
        }).catch((err: ResultError) => {
            $utils.errorMsg(err);
            initPaymentLoading.value = false;
        })
}

const onStep = (_step: number) => {
    step.value = _step
}

const onCreateSave = () => {
    if (btnLoading.value == true) {
        return false;
    }
    proxy?.$refs['createSaveRef']?.validate((valid: boolean) => {
        if (!valid) {
            btnLoading.value = true;
            let operationApi: any = null;
            if (createForm.value.pay_type == "offline_usdt") {
                operationApi = usdtpayTransferApi(Object.assign({
                    usdt: createForm.value.money,
                    money: numFilter(createForm.value.money * config?.value.usdt_rate),
                    pay_type: createForm.value.pay_type,
                    payment_id: createForm.value.payment_id,
                    usdt_type: "usdt"
                }, createSaveForm.value));
            } else {
                operationApi = rechargeTransferApi(
                    Object.assign(
                        {
                            money: numFilter(createForm.value.money * config?.value.usdt_rate),
                            usdt: createForm.value.money,
                            pay_type: createForm.value.pay_type,
                            payment_id: createForm.value.payment_id,

                        },
                        createSaveForm.value
                    )
                );
            }

            operationApi.then((res: Result) => {
                Notification.success({
                    title: "提交成功!",
                    content: String(res.message || ""),
                    duration: 2000,
                    onClose: () => {

                    },
                });
                close();
                btnLoading.value = false;
            }).catch((err: ResultError) => {
                Notification.error({
                    title: "提交失败!",
                    content: String(err.data?.message || ""),
                    duration: 1500,
                    onClose: () => {
                    },
                });
                btnLoading.value = false;
            });
        }
    })
}

const numFilter = (value: number) => {
    // 截取当前数据到小数点后三位
    let tempVal = parseFloat(String(value)).toFixed(3)
    let realVal = tempVal.substring(0, tempVal.length - 1)
    return realVal
}

const open = (isSet: boolean = true) => {
    visible.value = true;
    btnLoading.value = false;
    if (isSet) {
        useMemberStore().setRechargeModal(true);
    }
    initPaymentData();
};

const close = (isSet: boolean = true) => {
    visible.value = false;
    nextTick(() => {
        setTimeout(() => {
            proxy?.$refs['createRef']?.resetFields();
            proxy?.$refs['createSaveRef']?.resetFields();
            createForm.value.money = null;
            createForm.value.payment_id = null;
            createForm.value.pay_type = null;

            createSaveForm.value.account = ''
            createSaveForm.value.remark = ''

            payValue.value = null

            step.value = 1
            if (isSet) {
                useMemberStore().setRechargeModal(false);
            }
        }, 300);
    });
    return true;
};

watch(
    () => useMemberStore().rechargeModal,
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


<style scoped>
.recharge-balance {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: calc(var(--base-padding));
    background: var(--color-secondary);
}

.payment-item {
    max-width: 100%;
    width: 380px;
    overflow: hidden;
}

.payment-items {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: calc(380px - var(--base-padding) * 2);
    user-select: none;
    background: var(--color-secondary);
    border-radius: var(--base-radius-default);
    padding: calc(var(--base-padding) / 2);
    border: 1px solid var(--color-fill-3);
    margin-bottom: calc(var(--base-padding));
}

.payment-items-yl-icon {
    width: 36px;
    height: 36px;
    background-size: 100% 100%;
}

.payment-items-center {
    margin-left: 10px;
    width: calc(320px - var(--base-padding) * 4);
}

.payment-items-center .charge-name {
    color: var(--color-text-1);
    font-weight: bold;
}

.payment-items-center .charge-desc {
    margin-top: 4px;
    font-size: var(--base-size-small);
    color: rgb(var(--red-6));
}

.payment-item-mask {
    height: 20px;
    width: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 100%;
    border: 1px solid var(--color-border-2);
    box-sizing: border-box;
}

.payment-item-mask-dot {
    width: 8px;
    height: 8px;
    border-radius: 100%;
    background: var(--color-border-2);
}

.payment-items.payment-items-checked .payment-item-mask {
    border: 1px solid rgb(var(--primary-6));
}

.payment-items.payment-items-checked .payment-item-mask-dot {
    background: rgb(var(--primary-6));
}

.payment-items.payment-items-checked {
    border-color: rgb(var(--primary-6));
}

.recharge-pay {
    padding: calc(var(--base-padding));
    background: var(--color-secondary);
}


.payment-setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 20px 0;
}

.payment-setting-item-right {
    display: flex;
    align-items: center;
}

.payment-setting-item-right .copy-icon {
    cursor: pointer;
    color: rgb(var(--primary-6));
}
</style>