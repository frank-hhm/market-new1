<template>
  <a-modal
    :title="'编辑绑定信息'"
    @BeforeOk="onCreateOk"
    @BeforeCancel="close"
    width="700px"
    :top="useSetting().ModalTop"
    class="modal"
    v-model:visible="visible"
    :align-center="false"
    title-align="start"
    render-to-body
  >
    <div v-loading="initLoading">
      <a-form :model="createForm" ref="createRef" :rules="createRules">
        <a-row :gutter="20">
          <!-- <a-col :md="12" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="开户支行"
              field="bank_child"
            >
              <a-input-number
                v-model="createForm.bank_child"
                placeholder="请输入"
              ></a-input-number>
            </a-form-item>
          </a-col> -->
          <a-col :md="12" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="开户行"
              field="bank_name"
            >
              <a-input
                v-model="createForm.bank_name"
                placeholder="请输入"
              ></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="12" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="银行卡号"
              field="bank_code"
            >
              <a-input
                v-model="createForm.bank_code"
                placeholder="请输入"
              ></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="12" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="持卡人"
              field="bank_real_name"
            >
              <a-input
                v-model="createForm.bank_real_name"
                placeholder="请输入"
              ></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="12" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="USDT地址"
              field="usdt_card"
            >
              <a-input
                v-model="createForm.usdt_card"
                placeholder="请输入USDT地址"
              ></a-input>
            </a-form-item>
          </a-col>
        </a-row>
      </a-form>
    </div>
    <template #footer>
      <a-space>
        <a-button @click="close()">取消</a-button>
        <a-button
          type="primary"
          @click="onCreateOk()"
          :loading="btnLoading"
          :disabled="btnLoading"
          >确定</a-button
        >
      </a-space>
    </template>
  </a-modal>
</template>
<script lang="ts">
export default {
  name: "memberUpdateBind",
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
  updateBindMemberApi,
} from "@/api/member/member";

const labelColFlex = ref<string>("60px");

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
      // createForm.value.bank_child = res.data.bank_child;
      createForm.value.bank_code = res.data.bank_code;
      createForm.value.bank_name = res.data.bank_name;
      createForm.value.bank_real_name = res.data.bank_real_name;
      createForm.value.usdt_card = res.data.usdt_card;
      
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
  //   bank_child: "",
  bank_code: "",
  bank_name: "",
  bank_real_name: "",
  usdt_card:""
});

const createRules: any = reactive({});

const onCreateOk = () => {
  proxy?.$refs["createRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      let operationApi: any = null;
      operationApi = updateBindMemberApi(
        Object.assign(
          {
            id: operationId.value,
          },
          createForm.value
        )
      );
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

const open = (id: number = 0) => {
  operation.value = "update";
  operationId.value = id;
  nextTick(() => {
    toInit();
  });
  visible.value = true;
};

const close = () => {
  proxy?.$refs["createRef"]?.resetFields();
  operationId.value = 0;
  visible.value = false;
  return true;
};

defineExpose({ open });
</script>