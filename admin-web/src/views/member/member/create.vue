<template>
  <a-modal :title="operation == 'create' ? '添加用户' : '编辑用户'" @BeforeOk="onCreateOk" @BeforeCancel="close" width="700px"
    :top="useSetting().ModalTop" class="modal" v-model:visible="visible" :align-center="false" title-align="start"
    render-to-body>
    <div v-loading="initLoading">
      <a-form :model="createForm" ref="createRef" :rules="createRules">
        <a-row :gutter="20">
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="用户名" field="username">
              <a-input v-model="createForm.username" :disabled="operation == 'create' ? false : true"
                placeholder="请输入用户名"></a-input>
            </a-form-item>
            <a-form-item :label-col-flex="labelColFlex" label="手机" field="mobile">
              <a-input v-model="createForm.mobile" placeholder="请输入手机"></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="头像" field="avatar">
              <upload-btn v-model="createForm.avatar" count="1" width="60px" height="60px"></upload-btn>
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="20">
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="返佣" field="fee_cash" tooltip="单位%">
              <a-input-number v-model="createForm.fee_cash" placeholder="请输入" :precision="2"></a-input-number>
            </a-form-item>
          </a-col>
          <!-- <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="点差返现" field="spread_cash" tooltip="单位%">
              <a-input-number v-model="createForm.spread_cash" placeholder="请输入" :precision="2"></a-input-number>
            </a-form-item>
          </a-col> -->
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="手续费返现" field="fee_cash" tooltip="单位%">
              <a-input-number v-model="createForm.fee_cash" placeholder="请输入" :precision="2"></a-input-number>
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="20">
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="平仓时间差" field="order_pin_time" tooltip="单位秒">
              <a-input-number v-model="createForm.order_pin_time" placeholder="请输入"></a-input-number>
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="20">
          <a-col :md="12" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="代理商" field="agent_id">
              <a-cascader v-model="createForm.agent_id" :options="agentSelect" allow-search allow-clear
                class="form-select-fil" check-strictly placeholder="请选择代理商" />
            </a-form-item>
          </a-col>
        </a-row>
        <a-row :gutter="20">
          <a-col :md="24" :xs="24">
            <a-form-item :label-col-flex="labelColFlex" label="状态" field="status">
              <a-radio-group v-model="createForm.status">
                <a-radio v-for="item in statusEnum" :value="item.value" :key="item.value">{{ item.name }}</a-radio>
              </a-radio-group>
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
  name: "memberCreate",
};
</script>

<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { EnumItemType, Result, ResultError } from "@/types";
import { useEnumStore } from "@/store";
import {
  createMemberApi,
  getDetailMemberApi,
  updateMemberApi,
} from "@/api/member/member";
import {
  getAgentCascaderApi,
  getAgentListApi,
  getAgentSelectApi,
} from "@/api/agent";

const labelColFlex = ref<string>("120px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const statusEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("StatusEnum")
);

const visible = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const toInit = () => {
  if (!operationId.value) {
    return;
  }
  initLoading.value = true;
  getDetailMemberApi({ id: operationId.value })
    .then((res: Result) => {
      createForm.value.username = res.data.username;
      createForm.value.agent_id = res.data.agent_id;

      createForm.value.mobile = res.data.mobile;
      createForm.value.avatar = res.data.avatar;
      createForm.value.fee_cash = Number(res.data.fee_cash);
      createForm.value.spread_cash = Number(res.data.spread_cash);
      createForm.value.order_pin_time = Number(res.data.order_pin_time);

      createForm.value.status = res.data.status.value;
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    });
};

const initLoading = ref<boolean>(false);

const btnLoading = ref<boolean>(false);

const createForm = ref<any>({
  mobile: "",
  username: "",
  avatar: "",
  status: 1,
  pwd: "",
  conf_pwd: "",
  fee_cash: null,
  order_pin_time: null,
  agent_id: null,
  spread_cash: null
});

const createRules: any = reactive({
  username: [{ required: true, message: "请输入用户名！", trigger: "blur" }],
  mobile: [{ required: true, message: "请输入手机号！", trigger: "blur" }],
});

const onCreateOk = () => {
  proxy?.$refs["createRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      let operationApi: any = null;
      if (operation.value == "create") {
        operationApi = createMemberApi(createForm.value);
      } else {
        operationApi = updateMemberApi(
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
  });
};

const initAgentLoading = ref<boolean>(false);

const agentSelect = ref<any[]>([]);

const toInitAgentSelect = () => {
  initAgentLoading.value = true;
  getAgentCascaderApi()
    .then((res: Result) => {
      agentSelect.value = res.data;
      initAgentLoading.value = false;
    })
    .catch((err: ResultError) => {
      initAgentLoading.value = false;
    });
};

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
    toInitAgentSelect();
    toInit();
  });
  visible.value = true;
};

const close = () => {
  proxy?.$refs["createRef"]?.resetFields();
  operationId.value = 0;
  createRules.pwd = [];
  createRules.conf_pwd = [];
  visible.value = false;
  return true;
};

defineExpose({ open });
</script>