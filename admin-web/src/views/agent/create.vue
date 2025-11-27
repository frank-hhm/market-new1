<template>
    <a-modal :title="operation == 'create' ? '添加代理商' : '编辑代理商'" @BeforeOk="onCreateOk" @BeforeCancel="close" width="700px"
        :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
        render-to-body>
        <div v-loading="initLoading">

            <a-form :model="createForm" ref="createRef" :rules="createRules">
                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="代理级别" field="level">
                            <a-select :loading="initLevelLoading" v-model="createForm.level" placeholder="请选择代理级别" @change="onChangeLevel">
                                <a-option v-for="item in levels" :key="item.value" :label="item.name"
                                    :value="item.value" />
                            </a-select>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12" v-if="createForm.level > 1">
                        <a-form-item :label-col-flex="labelColFlex" label="所属上级" field="pid">
                            <a-select :loading="initAgentLoading" v-model="createForm.pid" placeholder="请选择所属上级" allow-clear>
                                <a-option v-for="item in agentSelect" :key="item.id" 
                                    :value="item.id" >{{ item.real_name }}({{item.account}})</a-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="姓名" field="real_name">
                            <a-input v-model="createForm.real_name" placeholder="请输入姓名"></a-input>
                        </a-form-item>
                        <a-form-item :label-col-flex="labelColFlex" label="账号" field="account">
                            <a-input v-model="createForm.account" placeholder="请输入账号"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="头像" field="avatar">
                            <upload-btn v-model="createForm.avatar" count="1" width="60px" height="60px"></upload-btn>
                        </a-form-item>
                    </a-col>
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="邀请码" field="invite_code">
                            <a-input v-model="createForm.invite_code" placeholder="请输入邀请码"></a-input>
                        </a-form-item>
                    </a-col>
                    <!-- <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="客户盈利抽成比例" field="ratio">
                            <a-input-number v-model="createForm.ratio" placeholder="请输入客户盈利抽成比例"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col>
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="客户手续费加成" field="ratio_fee">
                            <a-input-number v-model="createForm.ratio_fee" placeholder="请输入客户手续费加成"
                                :precision="2"></a-input-number>
                        </a-form-item>
                    </a-col> -->
                    <a-col :md="24" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
                            <a-radio-group v-model="createForm.status">
                                <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name
                                }}</a-radio>
                            </a-radio-group>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="客服微信" field="kefu_wechat">
                            <a-input v-model="createForm.kefu_wechat" placeholder="请输客服微信"
                                ></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="24">
                        <a-form-item :label-col-flex="labelColFlex" label="客服QQ" field="kefu_qq">
                            <a-input v-model="createForm.kefu_qq" placeholder="请输客服QQ"
                                ></a-input>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-row :gutter="20">
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="密码：" field="pwd">
                            <a-input v-model="createForm.pwd" type="password" placeholder="请输入密码"></a-input>
                        </a-form-item>
                    </a-col>
                    <a-col :md="12" :xs="12">
                        <a-form-item :label-col-flex="labelColFlex" label="确定密码：" field="conf_pwd">
                            <a-input v-model="createForm.conf_pwd" type="password" placeholder="请再次输入密码"></a-input>
                        </a-form-item>
                    </a-col>
                </a-row>
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
    name: "agentCreate",
};
</script>

<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { EnumItemType, Result, ResultError } from "@/types";
import { useDataStore, useEnumStore } from "@/store";
import { createAgentApi, getAgentLevelDataApi, getAgentSelectApi, getDetailAgentApi, updateAgentApi } from "@/api/agent";

const labelColFlex = ref<string>("120px");

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const statusEnum = ref<EnumItemType[]>(useEnumStore().getEnumItem("StatusEnum"));

const visible = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const toInit = () => {
    if (!operationId.value) {
        return;
    }
    initLoading.value = true;
    getDetailAgentApi({ id: operationId.value })
        .then((res: Result) => {
            createForm.value.account = res.data.account;
            createForm.value.real_name = res.data.real_name;
            createForm.value.avatar = res.data.avatar;
            createForm.value.level = res.data.level?.value || 4;
            createForm.value.status = res.data.status.value;
            createForm.value.ratio = res.data.ratio;
            createForm.value.ratio_fee = res.data.ratio_fee;
            createForm.value.invite_code = res.data.invite_code;
            createForm.value.kefu_qq = res.data.kefu_qq;
            createForm.value.kefu_wechat = res.data.kefu_wechat;
            
            if(createForm.value.level > 1){
                toInitAgentSelect(createForm.value.level,false)
            }
            createForm.value.pid = res.data.pid;
            initLoading.value = false;
        })
        .catch((err: ResultError) => {
            initLoading.value = false;
            $utils.errorMsg(err);
        });
}

const initLoading = ref<boolean>(false)

const btnLoading = ref<boolean>(false)

const createForm = ref<any>({
    level: null,
    real_name: "",
    account: "",
    avatar: "",
    ratio: 0.00,
    ratio_fee: 0.00,
    status: 1,
    pwd: "",
    conf_pwd: "",
    invite_code:"",
    kefu_qq:"",
    kefu_wechat:""
});

const createRules: any = reactive({
    level: [{ required: true, message: "请选择代理级别！", trigger: "blur" }],
    real_name: [{ required: true, message: "请输入名称！", trigger: "blur" }],
    account: [{ required: true, message: "请输入输入账号！", trigger: "blur" }],
});

const onCreateOk = () => {
    proxy?.$refs['createRef']?.validate((valid: any, fields: any) => {
        if (!valid) {
            if (btnLoading.value) {
                return;
            }
            btnLoading.value = true;
            let operationApi: any = null;
            if (operation.value == "create") {
                operationApi = createAgentApi(createForm.value);
            } else {
                operationApi = updateAgentApi(
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
                    useDataStore().initAgents();
                    close();
                    emit("success");
                    btnLoading.value = false;
                })
                .catch((err: ResultError) => {
                    $utils.errorMsg(err);
                    btnLoading.value = false;
                });
        }
    });
}

const initLevelLoading = ref<boolean>(false)

const levels = ref<EnumItemType[]>([])

const toInitLevel = () => {
    initLevelLoading.value = false
    getAgentLevelDataApi().then((res: Result) => {
        levels.value = res.data
        initLoading.value = false
    }).catch((err: ResultError) => {
        initLoading.value = false
    })
}

const onChangeLevel = () => {
    if (createForm.value.level > 1) {
        toInitAgentSelect(createForm.value.level);
    }else{
        createForm.value.pid = null;
        agentSelect.value = [];
    }
}

const initAgentLoading = ref<boolean>(false)

const agentSelect = ref<any[]>([])

const toInitAgentSelect = (pid: number,isInit: boolean = true) => {
    initAgentLoading.value = true
    getAgentSelectApi(pid).then((res: Result) => {
        if(isInit){
            createForm.value.pid = null;
        }
        agentSelect.value = res.data
        initAgentLoading.value = false
    }).catch((err: ResultError) => {
        initAgentLoading.value = false
    })
}

const open = (id: number = 0) => {
    if (id != 0) {
        operation.value = "update";
        operationId.value = id;
    } else {
        operation.value = "create";
        createRules["pwd"] = [
            { required: true, message: "请输入密码", trigger: "blur" },
        ];
        createRules["conf_pwd"] = [
            { required: true, message: "请再次密码", trigger: "blur" },
        ];
    }
    nextTick(() => {
        toInitLevel();
        toInit();
    });
    visible.value = true;
};

const close = () => {
    proxy?.$refs['createRef']?.resetFields();
    operationId.value = 0;
    createRules.pwd = [];
    createRules.conf_pwd = [];
    visible.value = false;
    return true;
};

defineExpose({ open });
</script>