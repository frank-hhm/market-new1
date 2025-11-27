<template>
  <a-modal
    title="设置默认自选"
    @BeforeOk="onSaveOk"
    @BeforeCancel="close"
    width="500px"
    :top="useSetting().ModalTop"
    class="modal"
    v-model:visible="visible"
    :align-center="false"
    title-align="start"
    render-to-body
  >
    <div v-loading="initLoading || initProductsLoading">
      <a-form :model="formModel" ref="formRef" :rules="formRules">
        <a-form-item :label-col-flex="labelColFlex" hide-label>
          <a-transfer
            :data="products"
            :title="['所有产品', '默认自选']"
            v-model="formModel.member_default_product"
          >
            <template #item="{ label }">
              {{ label }}
            </template>
          </a-transfer>
        </a-form-item>
      </a-form>
    </div>

    <template #footer>
      <a-space>
        <a-button @click="close()">取消</a-button>
        <a-button
          type="primary"
          :loading="btnLoading"
          :disabled="btnLoading"
          @click="onSaveOk()"
          >确定</a-button
        >
      </a-space>
    </template>
  </a-modal>
</template>

<script lang="ts">
export default {
  name: "setDefault",
};
</script>

<script lang="ts" setup>
import {
  ref,
  getCurrentInstance,
  nextTick,
  reactive,
  onBeforeUnmount,
} from "vue";
import { Result, ResultError } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import { Modal } from "@arco-design/web-vue";
import { getConfigApi, saveConfigApi } from "@/api/system/config";
import { getProductSelectLabelValueDataApi } from "@/api/product";

const labelColFlex = ref<string>("80px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const defaultProduct = ref<string[] | number[]>([]);

const formModel = ref<any>({
  member_default_product: [],
});

const initLoading = ref<boolean>(false);

const toInit = () => {
  initLoading.value = true;
  getConfigApi("member").then(
    (res: Result) => {
      formModel.value.member_default_product = res.data.member_default_product || [];
      initLoading.value = false;
    },
    (err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    }
  );
};

const initProductsLoading = ref<boolean>(false);

const products = ref<any>([]);

const toInitProducts = () => {
  initProductsLoading.value = true;
  getProductSelectLabelValueDataApi().then(
    (res: Result) => {
      products.value = res.data;
      initProductsLoading.value = false;
    },
    (err: ResultError) => {
      initProductsLoading.value = false;
      $utils.errorMsg(err);
    }
  );
};

const onSaveOk = () => {
  proxy?.$refs["formRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      saveConfigApi(formModel.value).then(
        (res: Result) => {
          btnLoading.value = false;
          $utils.successMsg(res.message);
          close();
        },
        (err: ResultError) => {
          btnLoading.value = false;
          $utils.errorMsg(err);
        }
      );
    }
  });
};

const formRules = reactive<any>({});

const btnLoading = ref<boolean>(false);

const formRef = ref<HTMLElement>();

const open = () => {
  initLoading.value = true;
  visible.value = true;
  toInitProducts();
  setTimeout(() => {
    toInit();
  }, 500);
};

const close = () => {
  proxy?.$refs["formRef"]?.resetFields();
  visible.value = false;
  return true;
};

onBeforeUnmount(() => {});

defineExpose({ open, close });
</script>