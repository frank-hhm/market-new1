<template>
    <a-modal :title="!isResult ? ('开仓:' + productDetail?.name) : '开仓结果'" @BeforeCancel="close" :width="'600px'"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="true" title-align="start"
        :mask-closable="false" render-to-body>
        <div class="create-order-body" v-loading="btnLoading">
            <template v-if="!isResult">
                <a-form class="create-order-form" :model="orderForm" layout="horizontal" ref="orderRef"
                    :rules="orderRules">
                    <!-- <div class="create-order-form-item">
                    <div>交易类型</div>
                    <div class="create-order-form-item-right">
                        <a-radio-group class="create-order-form-item-right" type="button" v-model="orderForm.robot">
                            <a-radio :value="0">市价</a-radio>
                            <a-radio :value="1">限价</a-radio>
                        </a-radio-group>
                    </div>
                </div> -->
                    <div class="order-popup-type">
                        <div class="sell-popup-btn" :class="orderForm.ostyle == 1 ? 'active' : ''"
                            @click="onSelectOrderType(1)" :style="{
                                color: useMemberStore().getUpColor(),
                                borderColor: orderForm.ostyle == 1 ? useMemberStore().getUpColor() : '',
                                background: orderForm.ostyle == 1 ? '#fff' : ''
                            }">
                            <div class="btn-text">买跌</div>
                            <div class="btn-price">{{ fall }}</div>
                            <div class="btn-icon" v-show="orderForm.ostyle == 1"><icon-check-circle size="20" /></div>
                        </div>
                        <div class="buy-popup-btn" :class="orderForm.ostyle == 2 ? 'active' : ''"
                            @click="onSelectOrderType(2)" :style="{
                                color: useMemberStore().getDownColor(),
                                borderColor: orderForm.ostyle == 2 ? useMemberStore().getDownColor() : '',
                                background: orderForm.ostyle == 2 ? '#fff' : ''
                            }">
                            <div class="btn-text">买涨</div>
                            <div class="btn-price">{{ rise }}</div>
                            <div class="btn-icon" v-show="orderForm.ostyle == 2"><icon-check-circle size="20" /></div>
                        </div>
                    </div>

                    <a-form-item class="create-order-form-item" hide-asterisk label="交易类型" field="robot">
                        <div class="create-order-form-item-right">
                            <a-radio-group type="button" v-model="orderForm.robot">
                                <a-radio :value="0">市价</a-radio>
                                <a-radio :value="1">限价</a-radio>
                            </a-radio-group>
                        </div>
                    </a-form-item>
                    <a-form-item v-if="orderForm.robot == 1" class="create-order-form-item" hide-asterisk label="标记价格"
                        field="limitPrice">
                        <div class="create-order-form-item-right">
                            <a-input-number style="width:120px;" v-model="orderForm.limitPrice" model-event="input"
                                size="large" placeholder="标记价格" allow-clear mode="embed" />
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item" hide-asterisk label="手数" field="onumber">
                        <div class="create-order-form-item-right">
                            <a-input-number style="width:120px;" v-model="orderForm.onumber" model-event="input"
                                size="large" placeholder="开仓手数" allow-clear mode="embed"
                                :min="Number(productDetail?.buy_min)" />
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item" hide-asterisk label="止损止盈" v-if="orderForm.robot === 0">
                        <div class="create-order-form-item-right">
                            <a-switch type="round" v-model="isProfitAndLoss" />
                        </div>
                    </a-form-item>
                    <template v-if="orderForm.robot === 0 && isProfitAndLoss">
                        <a-form-item class="create-order-form-item noerror" hide-asterisk label="设置止损" field="loss">
                            <div class="create-order-form-item-right">
                                <a-input-number style="width:120px;" v-model="orderForm.loss" model-event="input"
                                    size="large" placeholder="输入价格" allow-clear mode="embed" />
                            </div>
                        </a-form-item>
                        <div class="preview-error" v-if="orderForm.loss && (
                            (orderForm.ostyle == 1 && Number(orderForm.loss) < Number(orderForm.robot === 1 ? orderForm.limitPrice : useMarketStore().getMarketPrice(productId)))
                            || (orderForm.ostyle == 2 && Number(orderForm.loss) > Number(orderForm.robot === 1 ? orderForm.limitPrice : useMarketStore().getMarketPrice(productId)))
                        )">
                            触发价格必须{{ orderForm.ostyle == 2 ? '低于' : '高于' }}开仓价格</div>
                        <div class="preview-tips">
                            <div>当标记价格触达<span class="text-orange">{{ orderForm.loss || '--'
                            }}</span>时，将会触发市价止损委托平仓当前仓位。预期盈亏为:<span
                                    :class="Number(c_loseCloseDelta) > 0 ? 'text-green' : 'text-red'">{{
                                        orderForm.loss &&
                                            orderForm.loss != 0 && Number(c_loseCloseDelta) > 0 ? '+' : '' }}{{ orderForm.loss !=
                                        0 ? c_loseCloseDelta : "--" }}</span>
                            </div>
                        </div>
                        <a-form-item class="create-order-form-item noerror" hide-asterisk label="设置止盈" field="surplus">
                            <div class="create-order-form-item-right">
                                <a-input-number style="width:120px;" v-model="orderForm.surplus" model-event="input"
                                    size="large" placeholder="输入价格" allow-clear mode="embed" />
                            </div>
                        </a-form-item>
                        <div class="preview-error"
                            v-if="orderForm.surplus && ((orderForm.ostyle == 1 && Number(orderForm.surplus) > Number(orderForm.robot === 1 ? orderForm.limitPrice : useMarketStore().getMarketPrice(productId))) || (orderForm.ostyle == 2 && Number(orderForm.surplus) < Number(orderForm.robot === 1 ? orderForm.limitPrice : useMarketStore().getMarketPrice(productId))))">
                            触发价格必须{{ orderForm.ostyle == 1 ? '低于' : '高于' }}开仓价格</div>
                        <div class="preview-tips">
                            <div>当标记价格触达<span class="text-orange">{{ orderForm.surplus ||
                                '--' }}</span>时，将会触发市价止盈委托平仓当前仓位。预期盈亏为:<span
                                    :class="Number(c_earnCloseDelta) > 0 ? 'text-green' : 'text-red'">{{ orderForm.surplus &&
                                        orderForm.surplus != 0 && Number(c_earnCloseDelta) > 0 ? '+' : '' }}{{ orderForm.surplus !=
                                        0 ? c_earnCloseDelta : "--" }}</span>
                            </div>
                        </div>
                    </template>
                </a-form>



                <div class="member-coin">
                    <template v-if="productDetail?.pay_choose">
                        <span class="money-label">保证金</span>
                        <span class="money-text">${{ parseFloat(String(Number(productDetail?.pay_choose) * Number(
                            orderForm.onumber || 1))).toFixed(2)
                        }}</span>
                        <span class="money-label">/{{
                            orderForm.onumber || 1 }}手</span>
                    </template>
                    <span class="money-label ml10">可用资金</span>
                    <span class="money-text">${{ availableBalance || 0 }}</span>
                    <span class="money-label"></span>
                </div>
            </template>
            <template v-else>
                <div class="order-result-body">
                    <div class="order-result-body-success-icon">
                        <icon-check-circle-fill size="50" />
                    </div>

                    <div class="order-result-body-success-text">{{ resultTitle }}</div>
                    <div class="order-result-body-item" v-if="resultData.ptitle">
                        <div class="label">名称</div>
                        <div class="value">{{ resultData.ptitle }}</div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.ostyle">
                        <div class="label">方向</div>
                        <div class="value">
                            <div :class="resultData.ostyle == 1 ? 'text-red' : 'text-green'">{{ resultData.ostyle ==
                                1 ? '买跌' : '买涨' }}</div>
                        </div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.onumber">
                        <div class="label">手数</div>
                        <div class="value">{{ resultData.onumber }}手</div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.orderno">
                        <div class="label">订单号</div>
                        <div class="value">#{{ resultData.orderno }}</div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.buyprice">
                        <div class="label">{{ Number(resultData.robot) === 1 ? '标记价格' : '开仓价格' }}</div>
                        <div class="value">{{ resultData.buyprice }}</div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.buytime_text">
                        <div class="label">开仓时间</div>
                        <div class="value">{{ resultData.buytime_text }}</div>
                    </div>
                    <div class="order-result-body-item" v-if="resultData.create_time_text">
                        <div class="label">{{ Number(resultData.robot) === 1 ? '提交时间' : '成交时间' }}</div>
                        <div class="value">{{ resultData.create_time_text }}</div>
                    </div>
                </div>
            </template>
        </div>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close()">{{ isResult ? '关闭' : '取消' }}</a-button>
                <a-button v-if="!isResult && productDetail?.is_open" type="primary" @click="confirmOrder()"
                    :loading="btnLoading" :disabled="btnLoading">确认开仓</a-button>
                <a-button v-else-if="!isResult" :disabled="true">休市</a-button>
                <a-button v-else-if="isResult" type="primary" @click="onContinue">继续交易</a-button>
            </a-space>
        </template>
    </a-modal>
</template>
<script lang="ts">
export default {
    name: "create-order-modal",
};
</script>
<script lang="ts" setup>
import { getCurrentInstance, ref, nextTick, computed, watch } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { useMarketStore, useMemberStore, useWalletStore } from "@/store";
import { use } from "echarts";
import { ValidatedError } from "@arco-design/web-vue";
import { storeToRefs } from "pinia";
import { createOrderApi, penddingOrderApi } from "@/api/order";
import { Result, ResultError } from "@/types";
import { Notification } from "@arco-design/web-vue";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["close"]);

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false);

const isResult = ref<boolean>(false)

const resultData = ref<any>({})

const resultTitle = ref<string>("开仓成功")

const productId = ref<number | string>(useMarketStore().selectMarketId);

const productDetail = ref<any>(useMarketStore().getMarketDetail());

const fall = computed(() => {
    let marketPrice = useMarketStore().getMarketPrice(useMarketStore().selectMarketId);
    console.log(marketPrice)
    let spread = productDetail.value?.spread || 0
    let decimal = productDetail.value?.decimal || 2
    return (parseFloat(marketPrice) - parseFloat(spread)).toFixed(decimal)
});
const rise = computed(() => {
    let marketPrice = useMarketStore().getMarketPrice(useMarketStore().selectMarketId);
    let spread = productDetail.value?.spread || 0
    let decimal = productDetail.value?.decimal || 2
    return (parseFloat(marketPrice) + parseFloat(spread)).toFixed(decimal)
});


const availableBalance = computed(() => {
    return useWalletStore().getAvailableBalance();
});


const isProfitAndLoss = ref<boolean>(false)

const onSelectOrderType = (val: number) => {
    orderForm.value.ostyle = val
};

const orderForm = ref<any>({
    ostyle: 1,
    robot: 0,
    onumber: null,
    loss: null,
    surplus: null,
    limitPrice: null
});

const orderRules = ref<any>({
    onumber: [{ required: true, message: "请输入开仓手数", trigger: "blur" }],
    limitPrice: [
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                if (orderForm.value.robot == 1 && !orderForm.value.limitPrice) {
                    callback('请输入限价标记价格')
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
    loss: [
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                if (orderForm.value.loss && (
                    (orderForm.value.ostyle == 1 && Number(orderForm.value.loss) < Number(orderForm.value.robot === 1 ? orderForm.value.limitPrice : useMarketStore().getMarketPrice(useMarketStore().selectMarketId)))
                    || (orderForm.value.ostyle == 2 && Number(orderForm.value.loss) > Number(orderForm.value.robot === 1 ? orderForm.value.limitPrice : useMarketStore().getMarketPrice(useMarketStore().selectMarketId)))
                )) {
                    callback("触发价格必须" + (orderForm.value.ostyle == 2 ? '低于' : '高于') + "开仓价格")
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
    surplus: [
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                if (orderForm.value.surplus && ((orderForm.value.ostyle == 1 && Number(orderForm.value.surplus) > Number(orderForm.value.robot === 1 ? orderForm.value.limitPrice : useMarketStore().getMarketPrice(useMarketStore().selectMarketId))) || (orderForm.value.ostyle == 2 && Number(orderForm.value.surplus) < Number(orderForm.value.robot === 1 ? orderForm.value.limitPrice : useMarketStore().getMarketPrice(useMarketStore().selectMarketId))))) {
                    callback("触发价格必须" + (orderForm.value.ostyle == 1 ? '低于' : '高于') + "开仓价格")
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
});

const c_loseCloseDelta = computed(() => {
    if (!orderForm.value.loss) {
        return '--'
    }
    let value
    let _money = parseFloat(productDetail.value.money)
    let marketPrice = useMarketStore().getMarketPrice(useMarketStore().selectMarketId);
    let orderPrice = parseFloat(String(marketPrice));
    let spread = productDetail.value?.spread || 0
    if (orderForm.value.robot === 1) {
        orderPrice = orderForm.value.limitPrice
    }
    //买跌
    if (orderForm.value.ostyle == 1) {
        value = (orderPrice - parseFloat(spread) - orderForm.value.loss) / parseFloat(productDetail.value?.number) * parseFloat(orderForm.value.onumber) * _money
    } else {
        value = ((orderPrice + parseFloat(spread) - orderForm.value.loss) / parseFloat(productDetail.value?.number) * parseFloat(orderForm.value.onumber) * _money) * (-1)
    }
    if (isNaN(value)) {
        return '--'
    }
    return value.toFixed(2)
});
const c_earnCloseDelta = computed(() => {

    if (!orderForm.value.surplus) {
        return "-"
    }
    let value
    let _money = parseFloat(productDetail.value.money)
    let marketPrice = useMarketStore().getMarketPrice(useMarketStore().selectMarketId);
    let orderPrice = parseFloat(String(marketPrice));
    let spread = productDetail.value?.spread || 0
    if (orderForm.value.robot === 1) {
        orderPrice = orderForm.value.limitPrice
    }
    //买跌
    if (orderForm.value.ostyle == 1) {
        value = (orderPrice - parseFloat(spread) - orderForm.value.surplus) / parseFloat(productDetail.value?.number) * parseFloat(orderForm.value.onumber) * _money
    } else {
        value = ((orderPrice + parseFloat(spread) - orderForm.value.surplus) / parseFloat(productDetail.value?.number) * parseFloat(orderForm.value.onumber) * _money) * (-1)
    }
    if (isNaN(value)) {
        return '--'
    }
    return value.toFixed(2)
});


const btnLoading = ref<boolean>(false);

const confirmOrder = () => {
    proxy?.$refs['orderRef']?.validate((error: undefined | Record<string, ValidatedError>) => {
        if (error === undefined) {
            if (btnLoading.value) {
                return;
            }
            let newPrice = 0;
            //市价
            if (orderForm.value.robot === 0) {
                newPrice = useMarketStore().getMarketPrice(useMarketStore().selectMarketId)
            } else {
                newPrice = orderForm.value.limitPrice
            }
            let _productDetail = useMarketStore().getMarketDetail()

            let params = {
                order_type: orderForm.value.ostyle,
                order_pid: useMarketStore().selectMarketId,
                order_price: _productDetail?.pay_choose,
                buy_price: newPrice,
                onumber: orderForm.value.onumber,
                surplus: orderForm.value.surplus,
                loss: orderForm.value.loss,
            }
            btnLoading.value = true;
            if (orderForm.value.robot === 0) {
                createOrderApi(params).then((res: Result) => {
                    resultData.value = res.data
                    resultData.value.robot = 0
                    resultTitle.value = "开仓成功"
                    Notification.success({
                        title: "开仓成功!",
                        content: () => {
                            let contentString = "产品:" + res.data.ptitle + "\n";
                            contentString += "手数:" + res.data.onumber + "\n";
                            contentString += "开仓价格:" + res.data.buyprice + "\n";
                            contentString += "开仓时间:" + res.data.create_time + "\n";
                            contentString += "编号:" + res.data.orderno + "\n";
                            return contentString;
                        },
                        duration: 2000,
                        onClose: () => {
                        },
                    });
                    isResult.value = true
                    btnLoading.value = false;
                }).catch((err: ResultError) => {
                    resultTitle.value = "开仓失败"
                    Notification.error({
                        title: "开仓失败!",
                        content: String(err.data?.message || ""),
                        duration: 1500,
                        onClose: () => {
                        },
                    });
                    btnLoading.value = false;
                })
            } else {
                penddingOrderApi(params).then((res: Result) => {
                    resultData.value = res.data
                    resultData.value.robot = 1
                    resultTitle.value = "挂单成功"
                    Notification.success({
                        title: "挂单成功!",
                        content: () => {
                            let contentString = "产品:" + res.data.ptitle + "\n";
                            contentString += "手数:" + res.data.onumber + "\n";
                            contentString += "标记价格:" + res.data.buyprice + "\n";
                            contentString += "提交时间:" + res.data.create_time_text + "\n";
                            contentString += "编号:" + res.data.orderno + "\n";
                            return contentString;
                        },
                        duration: 2000,
                        onClose: () => {
                        },
                    });
                    isResult.value = true
                    btnLoading.value = false;
                }).catch((err: ResultError) => {
                    resultTitle.value = "挂单失败"
                    Notification.error({
                        title: "挂单失败!",
                        content: String(err.data?.message || ""),
                        duration: 1500,
                        onClose: () => {
                        },
                    });
                    btnLoading.value = false;
                })
            }
        }
    })
}

const onContinue = () => {
    isResult.value = false
    resultTitle.value = ""
    resultData.value = {}
}

const open = (ostyle: number | string) => {
    if (!productId.value) {
        if (!useMarketStore().selectMarketId) {
            $utils.errorMsg("请选择商品");
            return false;
        }
        productId.value = useMarketStore().selectMarketId;
    }
    orderForm.value.ostyle = ostyle
    nextTick(() => {
        productDetail.value = useMarketStore().getMarketDetail();
    })
    visible.value = true;
};

const close = () => {
    visible.value = false;
    nextTick(() => {
        initLoading.value = false;
        isProfitAndLoss.value = false
        proxy?.$refs['orderRef']?.resetFields();
        orderForm.value = {
            ostyle: 1,
            robot: 0,
            onumber: null,
            loss: null,
            surplus: null,
            limitPrice: null
        }
        onContinue();
    })
    emit("close")
    return true;
};

watch(
    () => useMarketStore().markets,
    (val) => {
        // console.log(val)
        productDetail.value = useMarketStore().getMarketDetail();
    });

defineExpose({ open });
</script>



<style scoped>
.create-order-body{
    position: relative;
}
.order-popup-type {
    display: flex;
    justify-content: center;
    justify-content: space-between;

}

.sell-popup-btn,
.buy-popup-btn {
    width: calc(50% - 20px);
    height: 60px;
    border-radius: var(--base-radius-default);
    line-height: 60px;
    border: 2px solid var(--color-border-1);
    position: relative;
    display: flex;
    align-items: baseline;
    justify-content: center;
    user-select: none;
    cursor: pointer;
    background: var(--color-secondary);
}

.sell-popup-btn .btn-text,
.buy-popup-btn .btn-text {
    margin-right: 10px;
}

.sell-popup-btn .btn-price,
.buy-popup-btn .btn-price {
    font-weight: bold;
    font-size: var(--base-size-large-x);
}

.buy-popup-btn .btn-price {
    /* color: rgb(var(--green-6)); */
}

.sell-popup-btn .btn-price {
    /* color: rgb(var(--red-6)); */
}
.sell-popup-btn:hover,
.buy-popup-btn:hover{
    border-color: rgba(var(--primary-6));
}

.order-popup-type .btn-icon {
    position: absolute;
    right: 10px;
    height: 62px;
    line-height: 62px;
    top: 0;
}

.sell-popup-btn.active {
    /* border: 1px solid rgb(var(--red-6));
    background: rgb(var(--red-1));
    color: rgb(var(--red-6)); */
}

.sell-popup-btn.active .btn-icon {
    /* color: rgb(var(--red-6)); */
}

.buy-popup-btn.active {
    /* border: 1px solid rgb(var(--green-6));
    background: rgb(var(--green-1)); */
}

.buy-popup-btn.active .btn-icon {
    /* color: rgb(var(--green-6)); */
}

.create-order-form {

    width: calc(100% - 30px);
    margin: 15px;
}

.create-order-form-item {
    margin-top: 15px;
    margin-bottom: 0;
    width: 100%;
    display: flex;
    justify-content: space-between;
}

.create-order-form-item-right {
    width: 100%;
    display: flex;
    justify-content: end;
}

.preview-error {
    text-align: right;
    color: rgb(var(--red-6));
}

.preview-tips {
    text-align: right;
    color: rgb(var(--gray-6));
}


.member-coin {
    width: 100%;
    margin-top: 20px;
    color: var(--color-text-1);
    font-size: var(--base-size-small);
    letter-spacing: 0;
    text-align: center;
}

.member-coin .money-label {}

.member-coin .money-text {
    color: rgb(var(--red-6));
}

.order-result-body {}

.order-result-body .order-result-body-success-icon {
    margin: 10px auto;
    width: 60px;
    height: 60px;
    color: rgb(var(--green-6));
    display: flex;
    justify-content: center;
    align-items: center;
}

.order-result-body .order-result-body-success-text {
    margin-bottom: 30px;
    color: var(--color-text-1);
    font-size: var(--base-size-large);
    text-align: center;
    width: 100%;
}

.order-result-body-item {
    width: 70%;
    margin: 0 auto 20px;
    display: flex;
    justify-content: space-between;
}

.order-result-body-item .label {
    color: rgb(var(--gray-6));
}
</style>

<style>
.create-order-form-item .arco-form-item-label-col {
    justify-content: flex-start;
}

.create-order-form-item .arco-form-item-message {
    width: 100%;
    text-align: right;
}

.create-order-form-item.noerror .arco-form-item-message {
    display: none;
}
</style>