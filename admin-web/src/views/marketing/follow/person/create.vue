<template>
    <a-drawer class="drawer-default" :title="operation == 'create' ? '添加交易员' : '编辑交易员'" v-model:visible="visible"
        :width="800">

        <div v-if="visible" v-loading="initLoading">
            <a-form :model="createForm" ref="createRef" :rules="createRules">
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="姓名" field="nickname">
                            <a-input v-model="createForm.nickname" placeholder="请输入标题"></a-input>
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex" label="个性签名" field="signature">
                            <a-textarea v-model="createForm.signature" placeholder="请输入个性签名"></a-textarea>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="头像" field="avatar">
                            <upload-btn v-model="createForm.avatar" width="80px" height="80px" count="1"></upload-btn>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="关联测试账号" field="member_id">
                            <a-select v-model="createForm.member_id" placeholder="选择测试账号" allow-clear
                                :loading="initMemberLoading">
                                <a-option v-for="(item, index) in members" :key="index" :value="item.id">{{
                                    item.real_name }}({{ item.email || item.mobile }} {{ item.username ? "/" +
                                        item.username : "" }})</a-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="收益类型" field="revenue_type">
                            <a-radio-group v-model="createForm.revenue_type">
                                <a-radio v-for="item in revenueTypeEnum" :value="item.value" :key="item.value">{{
                                    item.name
                                    }}</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20" v-if="createForm.revenue_type === 'lock'">
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="每日收益(固定)" field="revenue_lock">
                             <a-input-number v-model="createForm.revenue_lock" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20" v-if="createForm.revenue_type === 'auto'">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="每日收益(最小值)" field="revenue_min">
                             <a-input-number v-model="createForm.revenue_min" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="每日收益(最大值)" field="revenue_max">
                             <a-input-number v-model="createForm.revenue_max" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20" >
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="一级分佣比例" field="commission1">
                             <a-input-number v-model="createForm.commission1" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="二级分佣比例" field="commission2">
                             <a-input-number v-model="createForm.commission2" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                </a-row>
                
                <a-row :gutter="20">
                    
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="跟单人数" field="follow_count_text">
                            <a-input-number v-model="createForm.follow_count_text" placeholder="请输入"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月收益率" field="revenue_text">
                            <a-input-number v-model="createForm.revenue_text" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月胜率" field="month_win_rate_text">
                            <a-input-number v-model="createForm.month_win_rate_text" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="近1月分润(usd)" field="month_profit_text">
                            <a-input-number v-model="createForm.month_profit_text" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="累计分润率" field="total_profit_text">
                            <a-input-number v-model="createForm.total_profit_text" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="交易员保证金(usd)" field="deposit_text">
                            <a-input-number v-model="createForm.deposit_text" placeholder="请输入"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="默认进入平台天数" field="default_create_day">
                            <a-input-number v-model="createForm.default_create_day" placeholder="请输入"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="是否显示持单" field="is_show_order">
                            <a-radio-group v-model="createForm.is_show_order">
                                <a-radio  :value="1" :key="1">显示</a-radio>
                                <a-radio  :value="0" :key="0">隐藏</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                            <a-radio-group v-model="createForm.status">
                                <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name
                                }}</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                </a-row>
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
    name: "articleCreate",
};
</script>
<script lang="ts" setup>
import { createFollowPersonApi, getDetailFollowPersonApi, updateFollowPersonApi } from "@/api/follow/person";
import { getMemberTestSelectApi } from "@/api/member/member";
import { useEnumStore } from "@/store";
import { EnumItemType, Result, ResultError } from "@/types";
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";

const labelColFlex = ref<string>("120px");

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

const revenueTypeEnum = ref<EnumItemType[]>(
    useEnumStore().getEnumItem("follow.RevenueTypeEnum")
);

const toInit = () => {
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getDetailFollowPersonApi({
        id: operationId.value
    }).then((res: any) => {
        createForm.value.member_id = Number(res.data.member_id);
        createForm.value.nickname = res.data.nickname;
        createForm.value.avatar = res.data.avatar;
        createForm.value.signature = res.data.signature;
        createForm.value.revenue_text = Number(res.data.revenue_text);
        createForm.value.month_win_rate_text = Number(res.data.month_win_rate_text);
        createForm.value.month_profit_text = Number(res.data.month_profit_text);
        createForm.value.total_profit_text = Number(res.data.total_profit_text);
        createForm.value.deposit_text = Number(res.data.deposit_text);
        createForm.value.revenue_type = res.data.revenue_type;
        createForm.value.revenue_lock = Number(res.data.revenue_lock);
        createForm.value.revenue_min = Number(res.data.revenue_min);
        
        createForm.value.revenue_max = Number(res.data.revenue_max);
        createForm.value.commission1 = Number(res.data.commission1);
        createForm.value.commission2 = Number(res.data.commission2);
        createForm.value.follow_count_text = Number(res.data.follow_count_text);
        createForm.value.is_show_order = Number(res.data.is_show_order);
        createForm.value.default_create_day = Number(res.data.default_create_day);
        
        createForm.value.status = res.data.status.value;
        initLoading.value = false;
    })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initLoading.value = false;
        });
}

const createForm = ref<any>({
    nickname: "",
    avatar: "",
    signature: "",
    revenue_text: "",
    month_win_rate_text: "",
    month_profit_text: "",
    total_profit_text: "",
    deposit_text: "",
    member_id: null,
    status: 1,
    revenue_type:"lock",
    revenue_lock:null,
    revenue_min:null,
    revenue_max:null,
    commission1:null,
    commission2:null,
    follow_count_text:null,
    is_show_order:1,
    default_create_day:null
});

const createRules: any = reactive({
    nickname: [{ required: true, message: "请输入姓名！", trigger: "blur" }],
    avatar: [{ required: true, message: "请选择图片！", trigger: "blur" }],
    signature: [{ required: true, message: "请输入签名！", trigger: "blur" }],
    member_id: [{ required: true, message: "请选择测试账号", trigger: "blur" }],
    revenue_lock: [{ required: true, message: "请输入收益！", trigger: "blur" }],
    revenue_min: [{ required: true, message: "请输入随机收益最小数！", trigger: "blur" }],
    revenue_max: [{ required: true, message: "请输入随机收益最大数！", trigger: "blur" }],
    commission1: [{ required: true, message: "请输入一级分佣比例", trigger: "blur" }],
    commission2: [{ required: true, message: "请输入二级分佣比例", trigger: "blur" }],
    follow_count_text: [{ required: true, message: "请输入跟单人数", trigger: "blur" }],
});


const onSave = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createFollowPersonApi(createForm.value);
            } else {
                operationApi = updateFollowPersonApi(
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

const members = ref<any[]>([])

const initMemberLoading = ref<boolean>(false)

const initTestMember = () => {
    initMemberLoading.value = true
    getMemberTestSelectApi({})
        .then((res: Result) => {
            members.value = res.data
            initMemberLoading.value = false;
        })
        .catch((err: ResultError) => {
            $utils.errorMsg(err);
            initMemberLoading.value = false;
        });
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
        initTestMember();
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