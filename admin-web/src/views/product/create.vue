<template>
    <a-drawer class="drawer-default" :title="operation == 'create' ? '添加产品' : '编辑产品'" v-model:visible="visible"
        :width="840" @BeforeClose="close()" @BeforeOk="onSave">

        <div v-if="visible" v-loading="initLoading">
            <a-form :model="createForm" layout="vertical" ref="createRef" :rules="createRules">
                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="产品名称" field="name">
                            <a-input v-model="createForm.name" placeholder="请输入产品名称"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="产品板块" field="sector_id">
                            <a-select v-model="createForm.sector_id" placeholder="请选择产品板块" allow-clear
                                :loading="sectorInitLoading">
                                <a-option v-for="(item, index) in sectorData" :key="index" :value="item.id">
                                    {{ item.sector_name }}
                                </a-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="显示代码" field="code">
                            <a-input v-model="createForm.code" placeholder="请输入显示代码"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="排序" field="sort">
                            <a-input-number v-model="createForm.sort" placeholder="请输入排序"></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-divider orientation="left"><span class="text-default fw">交易配置</span></a-divider>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="保证金" field="pay_choose">
                            <a-input-number v-model="createForm.pay_choose" :min="0" placeholder="请输入保证金"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="手续费" field="fee_buy">
                            <a-input-number v-model="createForm.fee_buy" :min="0" placeholder="请输入手续费"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>

                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="单数" tooltip="涨跌盈亏基数" field="number">
                            <a-input-number v-model="createForm.number" :min="0" placeholder="请输入单数"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="单价" tooltip="涨跌盈亏基数金额" field="money">
                            <a-input-number v-model="createForm.money" placeholder="请输入单价" :precision="5"
                                :min="0.01"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="点差" field="spread">
                            <a-input-number v-model="createForm.spread" placeholder="请输入点差"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="杠杆" field="leverage">
                            <a-input-number v-model="createForm.leverage" placeholder="请输入杠杆"></a-input-number>
                        </a-form-item>
                    </a-col>

                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="单笔最小交易数" field="buy_min">
                            <a-input-number v-model="createForm.buy_min" placeholder="请输入单笔最小交易数"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="单笔最大交易手数" field="buy_max">
                            <a-input-number v-model="createForm.buy_max" placeholder="请输入单笔最大交易手数"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="开仓滑点" field="buy_slippage">
                            <a-input-number v-model="createForm.buy_slippage" placeholder="请输入开仓滑点"
                                ></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="出仓滑点" field="sell_slippage">
                            <a-input-number v-model="createForm.sell_slippage" placeholder="请输入出仓滑点"
                                ></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>

                <a-divider orientation="left"><span class="text-default fw">行情配置</span></a-divider>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="数据来源" field="market_source">
                            <a-select v-model="createForm.market_source" placeholder="请选择数据来源" allow-clear>
                                <a-option v-for="(item, index) in marketSourceEnum" :key="index" :value="item.value">
                                    {{ item.name }}
                                </a-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="真实代码" field="real_code">
                            <a-input v-model="createForm.real_code" placeholder="请输入真实代码"></a-input>
                        </a-form-item>
                    </a-col>

                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="小数位数" field="decimal">
                            <a-input-number v-model="createForm.decimal" placeholder="请输入小数位数"
                                :min="0"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12"  v-if="createForm.market_source == 'auto'">
                        <a-form-item :label-col-flex="labelColFlex" label="最小波动" field="point_min"  tooltip="自主生成单次最小跳动数（1秒2次）">
                            <a-input-number v-model="createForm.point_min" placeholder="请输入最小波动" :precision="5"
                                :step="0.00001" :min="0"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12"  v-if="createForm.market_source == 'auto'">
                        <a-form-item :label-col-flex="labelColFlex" label="最大波动" field="point_max" tooltip="自主生成单次最大跳动数（1秒2次）">
                            <a-input-number v-model="createForm.point_max" placeholder="请输入最小波动" :precision="5"
                                :step="0.00001" :min="0"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12" v-if="createForm.market_source == 'auto'">
                        <a-form-item :label-col-flex="labelColFlex" label="初始价位" field="init_price">
                            <a-input-number v-model="createForm.init_price" placeholder="请输入价位"
                                :precision="4"></a-input-number>
                            <template #help>
                                <div class="mt10">
                                    <div>注意：不输入或为0则保持价位，如变更会初始[上次初始值为：{{ initPrice }}]</div>
                                </div>
                            </template>
                        </a-form-item>
                    </a-col>
                    <!-- <a-col :md="12" :xs="12"  v-if="createForm.market_source !== 'auto'">
                        <a-form-item :label-col-flex="labelColFlex" label="接口随机波动"
                            tooltip="获取接口值后，会加上+-此处的值。如5，则在接口获取的数据中加上-5~5之间的随机数" field="point_rand">
                            <a-input-number v-model="createForm.point_rand" placeholder="请输入随机波动" :precision="5"
                                :step="0.00001" :min="0"></a-input-number>
                        </a-form-item>
                    </a-col> -->
                </a-row>
                <a-divider orientation="left"><span class="text-default fw">时间配置</span></a-divider>

                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="开市时间" field="open_time">
                            <week-time v-model="createForm.open_time"></week-time>
                        </a-form-item>
                    </a-col>
                </a-row>

                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="隔夜平仓" field="yesterday_close"
                            tooltip="下单第二天强制平仓前天订单">
                            <a-switch v-model="createForm.yesterday_close" size="small" type="round" :checked-value="1"
                                :unchecked-value="0" />
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12" v-if="createForm.yesterday_close === 1">
                        <a-form-item :label-col-flex="labelColFlex" label="隔夜平仓时间" field="yesterday_close_time">
                            <a-time-picker format="HH:mm" v-model="createForm.yesterday_close_time"/>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-divider orientation="left"><span class="text-default fw">合约属性</span>
                            <span class="fw">(仅在合约属性展示)</span></a-divider>

                 <a-row  class="mt20" :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="合约单位" field="product_params.heyue_danwei">
                            <a-input v-model="createForm.product_params.heyue_danwei" placeholder="请输入合约单位"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="货币单位" field="product_params.money_danwei">

                            <a-input v-model="createForm.product_params.money_danwei" placeholder="请输入货币单位"></a-input>
                        </a-form-item>
                    </a-col>
                    <!-- <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="点差" field="product_params.dian_cha">
                            <a-input v-model="createForm.product_params.dian_cha" placeholder="请输入点差"></a-input>
                        </a-form-item>
                    </a-col> -->
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="强制保证金"
                            field="product_params.baozhengjin_rate">

                            <a-input v-model="createForm.product_params.baozhengjin_rate"
                                placeholder="请输入强制保证金"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="隔夜保证金"
                            field="product_params.geye_baozhengjin_rate">

                            <a-input v-model="createForm.product_params.geye_baozhengjin_rate"
                                placeholder="请输入隔夜保证金"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="开市时间"
                            field="product_params.open_time">

                            <a-textarea v-model="createForm.product_params.open_time"
                                placeholder="请输入开市时间"></a-textarea>
                        </a-form-item>
                    </a-col>
                    
                </a-row>
            </a-form>
        </div>
        <template #footer>
            <a-space>
                <a-button v-btn @click="close">取消</a-button>
                <a-button v-btn type="primary" :disabled="btnLoading" :loading="btnLoading"
                    @click="onSave">保存</a-button>
            </a-space>
        </template>
    </a-drawer>
</template>

<script lang="ts">
export default {
    name: "productCreate",
};
</script>
<script lang="ts" setup>
import { createProductApi, getDetailProductApi, updateProductApi } from "@/api/product";
import { getProductSectorSelectApi } from "@/api/product-sector";
import { useEnumStore } from "@/store";
import { EnumItemType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";

const labelColFlex = ref<string>("100px");

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const marketSourceEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("product.MarketSourceEnum"));


const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false)

const btnLoading = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);


const createForm = ref<any>({
    name: "",
    sector_id: null,
    code: "",
    real_code: "",
    market_source:'auto',
    yesterday_close: 0,
    yesterday_close_time:"",
    open_time: [],
    pay_choose: null,
    fee_buy: null,
    number: null,
    money: null,
    spread:null,
    leverage:null,
    decimal: 0,
    point_min: null,
    point_max: null,
    point_rand: null,
    init_price:0,
    sort: 0,
    buy_slippage:null,
    sell_slippage:null,

    product_params: {
        heyue_danwei: "",
        money_danwei: "",
        dian_cha: "",
        baozhengjin_rate: "",
        geye_baozhengjin_rate: "",
        open_time:""
    }
});

const createRules: any = reactive({
    name: [{ required: true, message: "请输入标题！", trigger: "blur" }],
    code: [{ required: true, message: "请输入显示代码！", trigger: "blur" }],
    real_code: [{ required: true, message: "请输入真实代码！", trigger: "blur" }],
});

const onSave = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true; let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createProductApi(createForm.value);
            } else {
                operationApi = updateProductApi(
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

const initPrice = ref<number>(0)

const toInit = () => {
    if(operationId.value === 0){
        return false;
    }
    initLoading.value = true;
    getDetailProductApi({
        id: operationId.value
    }).then((res: Result) => {
        let product = res.data.product, product_params = res.data.product_params;
        createForm.value.name = product.name;
        createForm.value.sector_id = Number(product.sector_id) || null;
        createForm.value.code = product.code;
        createForm.value.real_code = product.real_code;
        createForm.value.market_source = product.market_source.value;
        createForm.value.yesterday_close = Number(product.yesterday_close);
        createForm.value.yesterday_close_time = product.yesterday_close_time || "";
        createForm.value.open_time = product.open_time  || [];
        createForm.value.pay_choose = Number(product.pay_choose);
        createForm.value.fee_buy = Number(product.fee_buy);

        createForm.value.number = Number(product.number);
        createForm.value.money = Number(product.money);
        createForm.value.decimal = Number(product.decimal);
        createForm.value.spread = Number(product.spread || 0);
        createForm.value.leverage = Number(product.leverage || 0);
        
        createForm.value.point_min = Number(product.point_min);
        createForm.value.point_max = Number(product.point_max);
        createForm.value.point_rand = Number(product.point_rand);
        createForm.value.buy_min = Number(product.buy_min);
        createForm.value.buy_max = Number(product.buy_max);
        initPrice.value = Number(product.price || 0);
        createForm.value.sort = Number(product.sort);
        createForm.value.buy_slippage = Number(product.buy_slippage);
        createForm.value.sell_slippage = Number(product.sell_slippage);

        createForm.value.product_params = product_params
        initLoading.value = false;
    }).catch((err: ResultError) => {
        $utils.errorMsg(err);
        initLoading.value = false;
    });
};

const sectorData = ref<Array<{
    id: number,
    sector_name: string
}>>([]);

const toTypeInit = () => {
    sectorInitLoading.value = true;
    getProductSectorSelectApi()
        .then((res: Result) => {
            sectorData.value = res.data;
            sectorInitLoading.value = false;
        })
        .catch((error: ResultError) => {
            sectorInitLoading.value = false;
            $utils.errorMsg(error);
        });

};
const sectorInitLoading = ref<boolean>(false)

const open = (id: number = 0) => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
    } else {
        operation.value = "create";
    }
    nextTick(() => {
        toTypeInit();
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
</script>@/api/product-sector