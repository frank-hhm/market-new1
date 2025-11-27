<template>
  <a-modal
    :title="'编辑'"
    @BeforeOk="onCreateOk"
    @BeforeCancel="close"
    width="400px"
    :top="useSetting().ModalTop"
    class="modal"
    v-model:visible="visible"
    :align-center="false"
    title-align="start"
    render-to-body
  >
    <div v-loading="initLoading">
      <a-form
        layout="vertical"
        :model="createForm"
        ref="createRef"
        :rules="createRules"
      >
        <a-form-item label="姓名" field="real_name">
          <a-input
            v-model="createForm.real_name"
            placeholder="请输入姓名"
          ></a-input>
        </a-form-item>
        <a-form-item label="身份证" field="card_number">
          <a-input
            v-model="createForm.card_number"
            placeholder="请输入身份证"
          ></a-input>
        </a-form-item>
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
  name: "memberBindUpdate",
};
</script>

<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import { Result, ResultError } from "@/types";
import {
  getMemberCardDetailApi,
  updateMemberCardApi,
} from "@/api/member/member-card";

const labelColFlex = ref<string>("120px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const initLoading = ref<boolean>(true);

const btnLoading = ref<boolean>(false);

const createForm = ref<any>({
  real_name: "",
  card_number: "",
});

const createRules: any = reactive({
  real_name: [{ required: true, message: "请输入姓名！", trigger: "blur" }],
  card_number: [{ required: true, message: "请输入身份证！", trigger: "blur" }],
});

const toInit = () => {
  if (!operationId.value) {
    return;
  }
  initLoading.value = true;
  getMemberCardDetailApi({ id: operationId.value })
    .then((res: Result) => {
      createForm.value.real_name = res.data.real_name;
      createForm.value.card_number = res.data.card_number;
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    });
};
const onCreateOk = () => {
  proxy?.$refs["createRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      let operationApi: any = null;
      operationApi = updateMemberCardApi(
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
  if (id != 0) {
    operation.value = "update";
    operationId.value = id;
  }
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