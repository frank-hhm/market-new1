<template>
    <a-modal :title="robotType == 1 ? '设置委托' : '委托改单'" @BeforeCancel="close" :width="'400px'"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="true" title-align="start"
        :mask-closable="false" render-to-body>

        <div class="order-set-robot" v-loading="initLoading">

            <a-form v-if="orderDetail" class="order-set-robot-form" :model="orderForm" layout="horizontal"
                ref="orderRef" :rules="orderRules">
                <div class="order-set-robot-tab-list">
                    <div class="order-set-robot-tab-item" :class="tabActive === 0 ? 'active' : ''" @click="onSetTab(0)">
                        {{ robotType == 1 ? '触发止损/止盈' : '修改订单' }}
                    </div>
                    <div class="order-set-robot-tab-item" :class="tabActive === 1 ? 'active' : ''" @click="onSetTab(1)">
                        设置止损止赢
                    </div>
                </div>

                <div class="order-set-robot-detail">
                    <a-form-item class="create-order-form-item noerror" v-if="robotType == 1 && orderDetail?.orderno"
                        hide-asterisk hide-label>
                        <div class="create-order-form-item-right">
                            <div class="value">#{{ orderDetail.orderno }}</div>
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item noerror" v-else-if="robotType == 2 && orderDetail?.id"
                        hide-asterisk hide-label>
                        <div class="create-order-form-item-right">
                            <div class="value">#{{ orderDetail.id }}</div>
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item noerror" v-if="orderDetail?.ostyle" hide-asterisk
                        :label="orderDetail.ptitle">
                        <div class="create-order-form-item-right">
                            <span :class="orderOstyle == 2 ? 'text-red' : 'text-green'">{{
                                orderDetail.ostyle?.name
                                }}</span>
                            <span>/{{ orderDetail?.onumber }}手</span>
                        </div>
                    </a-form-item>

                    <a-form-item class="create-order-form-item noerror" v-if="robotType == 1" hide-asterisk
                        label="开仓价格">
                        <div class="create-order-form-item-right">
                            <div class="price">{{ orderDetail.buyprice }}</div>
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item noerror" hide-asterisk label="标记价格">
                        <div class="create-order-form-item-right">
                            <div class="price">{{ useMarketStore().getMarketPrice(orderDetail.pid) }}</div>
                        </div>
                    </a-form-item>
                    <a-form-item class="create-order-form-item " hide-asterisk label="触发价格" field="limitPrice"
                        v-if="robotType == 2">
                        <div class="create-order-form-item-right">
                            <a-input-number v-if="tabActive === 0" style="width:120px;" v-model="orderForm.limitPrice"
                                model-event="input" size="large" placeholder="输入价格" allow-clear mode="embed" />
                            <div v-if="tabActive === 1" class="price">{{ orderForm.limitPrice }}</div>
                        </div>
                    </a-form-item>

                    <template v-if="robotType != 2 && tabActive === 0">

                        <a-form-item class="create-order-form-item " hide-asterisk label="触发价格" field="markPrice">
                            <div class="create-order-form-item-right">
                                <a-input-number style="width:120px;" v-model="orderForm.markPrice" model-event="input"
                                    size="large" placeholder="输入价格" allow-clear mode="embed" />
                            </div>
                        </a-form-item>

                        <div class="preview-tips">预计盈亏 <span
                                :class="Number(c_markPrice) > 0 ? 'text-red' : 'text-green'">{{
                                    orderForm.markPrice &&
                                        orderForm.markPrice != 0 && Number(c_markPrice) > 0 ? '+' : '' }}{{ c_markPrice || '--'
                                }}</span>
                        </div>
                    </template>


                    <template v-if="tabActive === 1">
                        <a-form-item class="create-order-form-item noerror" hide-asterisk label="设置止损" field="loss">
                            <div class="create-order-form-item-right">
                                <a-input-number style="width:120px;" v-model="orderForm.loss" model-event="input"
                                    size="large" placeholder="输入价格" allow-clear mode="embed" />
                            </div>
                        </a-form-item>
                        <div class="preview-error" v-if="orderForm.loss && (
                            (orderOstyle == 1 && parseFloat(orderForm.loss) < parseFloat(orderDetail.buyprice))
                            || (orderOstyle == 2 && parseFloat(orderForm.loss) > parseFloat(orderDetail.buyprice))
                        )">
                            触发价格必须{{ orderOstyle == 2 ? '低于' : '高于' }}开仓价格</div>

                        <div class="preview-tips">
                            <div>当标记价格触达<text class="text-orange">{{ orderForm.loss || '--' }}</text>时，将会触发市价止损委托平仓当前仓位。
                            </div>
                            <div>预期盈亏为 <text :class="Number(c_loseCloseDelta) > 0 ? 'text-red' : 'text-green'">{{ orderForm.loss
                                && orderForm.loss != 0 && Number(c_loseCloseDelta) > 0 ? '+' : '' }}{{ orderForm.loss ?
                                        c_loseCloseDelta : "--" }}</text>。
                            </div>
                        </div>
                    </template>


                    <template v-if="tabActive === 1">
                        <a-form-item class="create-order-form-item noerror" hide-asterisk label="设置止盈" field="surplus">
                            <div class="create-order-form-item-right">
                                <a-input-number style="width:120px;" v-model="orderForm.surplus" model-event="input"
                                    size="large" placeholder="输入价格" allow-clear mode="embed" />
                            </div>
                        </a-form-item>

                        <div class="preview-error" v-if="orderForm.surplus && (
                            (orderOstyle == 1 && parseFloat(orderForm.surplus) > parseFloat(orderDetail.buyprice))
                            || (orderOstyle == 2 && parseFloat(orderForm.surplus) < parseFloat(orderDetail.buyprice))
                        )">触发价格必须{{ orderOstyle == 1 ? '低于' : '高于' }}开仓价格</div>

                        <div class="preview-tips">
                            <div>当标记价格触达<text class="text-orange">{{ orderForm.surplus || '--'
                            }}</text>时，将会触发市价止盈委托平仓当前仓位。</div>
                            <div>预期盈亏为 <text :class="Number(c_earnCloseDelta) > 0 ? 'text-red' : 'text-green'">{{
                                orderForm.surplus &&
                                    orderForm.surplus != 0 && Number(c_earnCloseDelta) > 0 ? '+' : '' }}{{
                                        orderForm.surplus ? c_earnCloseDelta : "--" }}</text>。
                            </div>
                        </div>
                    </template>
                </div>
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
    name: "member-order-set-robot",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch, computed } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { Result, ResultError } from "@/types";
import { Notification, ValidatedError } from "@arco-design/web-vue";
import { getOrderDetailApi, getOrderRobotDetailApi, holdModifyApi, peddingModifyApi } from "@/api/order";
import { useMarketStore } from "@/store";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false);

const robotType = ref<number | string>(1);

const orderId = ref<number | string>(0);

const tabActive = ref<number>(0);

const onSetTab = (val: number) => {
    tabActive.value = val;
}

const orderDetail = ref<any>({});

const orderOstyle = ref<string | number>(0);

const buyPrice = ref<number | string>(0);

const orderForm = ref<any>({
    limitPrice: null,
    markPrice: null,
    loss: null,
    surplus: null
});

const orderRules = ref<any>({
    limitPrice: [
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                console.log(robotType.value, tabActive.value, orderForm.value.limitPrice)
                if (robotType.value == 2 && tabActive.value == 0 && !orderForm.value.limitPrice) {
                    callback("触发价格不能为空")
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
    markPrice: [
        {
            validator: (value: string | undefined, callback: (error?: string) => void) => {
                if (robotType.value != 2 && tabActive.value == 0 && !orderForm.value.markPrice) {
                    callback("触发价格不能为空")
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
                    (orderOstyle.value == 1 && parseFloat(orderForm.value.loss) < parseFloat(orderDetail.value.buyprice))
                    || (orderOstyle.value == 2 && parseFloat(orderForm.value.loss) > parseFloat(orderDetail.value.buyprice))
                )) {
                    callback("触发价格必须" + (orderOstyle.value == 2 ? '低于' : '高于') + "开仓价格")
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
                if (
                    orderForm.value.surplus && (
                        (orderOstyle.value == 1 && parseFloat(orderForm.value.surplus) > parseFloat(orderDetail.value.buyprice))
                        || (orderOstyle.value == 2 && parseFloat(orderForm.value.surplus) < parseFloat(orderDetail.value.buyprice))
                    )
                ) {
                    callback("触发价格必须" + (orderOstyle.value == 1 ? '低于' : '高于') + "开仓价格")
                } else {
                    callback()
                }
            },
            trigger: ['change', 'blur'],
        }
    ],
})

const toInit = () => {
    initLoading.value = true;
    if (robotType.value == 2) {
        getOrderRobotDetailApi({
            id: orderId.value
        }).then((res: Result) => {
            orderDetail.value = res.data
            orderOstyle.value = res.data.ostyle.value
            orderForm.value.limitPrice = Number(res.data.buyprice)
            initLoading.value = false;
        }).catch((err: ResultError) => {
            Notification.error({
                title: "获取失败!",
                content: (err.data?.message || ''),
                duration: 2000,
                onClose: () => {
                },
            });
            initLoading.value = false;
        })
    } else if (robotType.value == 1) {
        getOrderDetailApi({
            id: orderId.value
        }).then((res: Result) => {
            orderDetail.value = res.data
            orderOstyle.value = res.data.ostyle.value
            buyPrice.value = res.data.buyprice
            initLoading.value = false;
        }).catch((err: ResultError) => {
            Notification.error({
                title: "获取失败!",
                content: (err.data?.message || ''),
                duration: 2000,
                onClose: () => {
                },
            });
            initLoading.value = false;
        })
    }
}


const btnLoading = ref<boolean>(false);

const onSaveOk = () => {
    proxy?.$refs['orderRef']?.validate((error: undefined | Record<string, ValidatedError>) => {
        if (error === undefined) {
            if (btnLoading.value) {
                return;
            }

            btnLoading.value = true
            let params = {
                surplus: orderForm.value.surplus,
                loss: orderForm.value.loss,
                oid: orderDetail.value.id,
            }

            if (robotType.value == 1) {
                if (orderForm.value.markPrice) {
                    params = Object.assign({}, params, {
                        mark_price: orderForm.value.markPrice,
                        trigger_price: useMarketStore().getMarketPrice(orderDetail.value.pid),
                    })
                }
                holdModifyApi(params).then((res: Result) => {
                    $utils.initMember()
                    Notification.success({
                        title: "提交成功!",
                        content: String(res?.message || ""),
                        duration: 1500,
                        onClose: () => {
                        },
                    });
                    btnLoading.value = false;
                    close();
                }).catch((err: ResultError) => {
                    Notification.error({
                        title: "提交失败!",
                        content: String(err.data?.message || ""),
                        duration: 1500,
                        onClose: () => {
                        },
                    });
                    btnLoading.value = false;
                })
            } else {

                params = Object.assign({}, params, {
                    order_pid: orderDetail.value.id,
                    newprice: parseFloat(String(orderForm.value.limitPrice)),
                })

                peddingModifyApi(params).then((res: Result) => {
                    $utils.initMember()
                    Notification.success({
                        title: "提交成功!",
                        content: String(res?.message || ""),
                        duration: 1500,
                        onClose: () => {
                        },
                    });
                    btnLoading.value = false;
                    close();
                }).catch((err: ResultError) => {

                    Notification.error({
                        title: "提交失败!",
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

const c_markPrice = computed(() => {

    if (!orderOstyle.value || !orderForm.value.markPrice) {
        return '--'
    }
    let _price = 0;
    let productDetail = useMarketStore().getMarketById(orderDetail.value?.pid);
    let _money = parseFloat(productDetail?.money)
    // 计算盈亏
    if (orderOstyle.value == 2) {
        // 买涨
        _price = ((orderForm.value.markPrice - orderDetail.value?.buyprice) / parseFloat(productDetail?.number) *
            parseFloat(orderDetail.value?.onumber)) * _money
    } else {
        // 买跌
        _price = (orderDetail.value?.buyprice - orderForm.value.markPrice) / parseFloat(productDetail?.number) *
            parseFloat(orderDetail.value?.onumber) * _money
    }
    return _price.toFixed(2)
})

//止损 预计盈亏
const c_loseCloseDelta = computed(() => {

    if (!orderOstyle.value || !orderDetail.value.buyprice) {
        return "--"
    }
    let value

    let productDetail = useMarketStore().getMarketById(orderDetail.value?.pid);
    let _money = parseFloat(productDetail?.money)
    let orderPrice
    if (robotType.value == 1) {
        orderPrice = orderDetail.value.buyprice
    } else {
        orderPrice = parseFloat(String(orderForm.value.limitPrice));
    }
    //卖出
    if (orderOstyle.value == 1) {
        value = (orderPrice - orderForm.value.loss) / parseFloat(productDetail?.number) * parseFloat(orderDetail.value?.onumber) * _money
    } else {
        value = ((orderPrice - orderForm.value.loss) / parseFloat(productDetail?.number) * parseFloat(orderDetail.value?.onumber) * _money) * -1
    }
    return value.toFixed(2)
})

//止盈 预计盈亏
const c_earnCloseDelta = computed(() => {
    if (!orderOstyle.value || !orderDetail.value.buyprice) {
        return "--"
    }
    let value
    let productDetail = useMarketStore().getMarketById(orderDetail.value?.pid);
    let _money = parseFloat(productDetail?.money)
    let orderPrice
    if (robotType.value == 1) {
        orderPrice = orderDetail.value.buyprice
    } else {
        orderPrice = parseFloat(String(orderForm.value.limitPrice));
    }
    //卖出
    if (orderOstyle.value == 1) {
        value = (orderPrice - orderForm.value.surplus) / parseFloat(productDetail?.number) * parseFloat(orderDetail.value.onumber) * _money
    } else {
        value = ((orderPrice - orderForm.value.surplus) / parseFloat(productDetail?.number) * parseFloat(orderDetail.value.onumber) * _money) * -1
    }
    return value.toFixed(2)
})

const open = (type: number | string, id: number | string) => {
    robotType.value = type
    orderId.value = id
    visible.value = true;
    toInit();
};

const close = () => {
    visible.value = false;
    nextTick(() => {
        initLoading.value = false;
        proxy?.$refs['orderRef']?.resetFields();
        tabActive.value = 0
        robotType.value = 1
        orderId.value = 0
        orderDetail.value = null
        orderForm.value = {
            limitPrice: null,
            markPrice: null,
            loss: null,
            surplus: null
        }
    })
    return true;
};

defineExpose({ open });
</script>

<style scoped>
.order-set-robot {
    min-height: 460px;
}

.order-set-robot-tab-list {
    height: 60px;
    line-height: 60px;
    display: flex;
    justify-content: space-between;
}

.order-set-robot-tab-item {
    width: calc(100% - var(--base-padding) * 2);
    margin: 0 var(--base-padding);
    text-align: center;
    background-color: var(--color-secondary);
    color: var(--color-text-1);
    border-radius: var(--base-radius-default);
    border: 2px solid var(--color-secondary);
    user-select: none;
    cursor: pointer;
    font-size: var(--base-size-default);
}

.order-set-robot-tab-item:hover {
    color: rgba(var(--primary-6));
}

.order-set-robot-tab-item.active {
    background-color: rgba(var(--primary-1));
    border-color: rgba(var(--primary-6));
    color: rgba(var(--primary-6));
    font-weight: bold;
}

.order-set-robot-detail {
    margin: 30px var(--base-padding) 0;
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
    font-size: var(--base-size-default);
}

.preview-tips {
    text-align: right;
    color: rgb(var(--gray-6));
}

.preview-error {
    text-align: right;
    color: rgb(var(--red-6));
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