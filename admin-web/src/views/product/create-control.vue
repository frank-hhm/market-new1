<template>
  <a-modal
    title="添加调控"
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
    <div v-loading="initLoading">
      <a-form :model="formModel" ref="formRef" :rules="formRules">
        <a-form-item :label-col-flex="labelColFlex" label="当前产品：">
          <div class="ml5">
            <div>{{ productName }}</div>
          </div>
        </a-form-item>
        <a-form-item :label-col-flex="labelColFlex" label="当前价格：">
          <div class="ml5">
            <div class="text-red fw">{{ mklinePrice }}</div>
          </div>
        </a-form-item>

        <a-form-item :label-col-flex="labelColFlex" label="类型" field="type">
          <a-radio-group type="button" v-model="formModel.type">
            <a-radio :value="1">时间段</a-radio>
            <a-radio :value="2">插针</a-radio>
          </a-radio-group>
        </a-form-item>
        <a-form-item
          :label-col-flex="labelColFlex"
          label="价位："
          field="price"
        >
          <a-input-number v-model="formModel.price" :precision="2" allow-clear>
            <template #suffix>元</template>
          </a-input-number>
        </a-form-item>
        <a-form-item
          v-if="formModel.type == 1"
          :label-col-flex="labelColFlex"
          label="开始时间："
          field="execute_time"
        >
          <a-date-picker
            style="width: 100%"
            v-model="formModel.execute_time"
            allow-clear
            show-time
            format="YYYY-MM-DD HH:mm:ss"
          />
        </a-form-item>
        <a-form-item
          v-if="formModel.type == 1"
          :label-col-flex="labelColFlex"
          label="结束时间："
          field="complete_time"
        >
          <a-date-picker
            style="width: 100%"
            v-model="formModel.complete_time"
            allow-clear
            show-time
            format="YYYY-MM-DD HH:mm:ss"
          />
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
  name: "memberRechargeBalance",
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
import { createProductPriceApi } from "@/api/product-price";
import { getProductMklinePriceApi } from "@/api/product-price";

import { useSetting } from "@/hooks/useSetting";
import { Modal } from "@arco-design/web-vue";

const labelColFlex = ref<string>("80px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

const productId = ref<number>(0);

const productName = ref<string>("");

const formModel = ref<any>({
  price: null,
  type: 1,
  execute_time: "",
  complete_time: "",
});

const initLoading = ref<boolean>(false);

const toInit = () => {
  //   initLoading.value = false;
};

const formRules = reactive<any>({
  price: [{ required: true, message: "价位不能为空！", trigger: "blur" }],
});

const btnLoading = ref<boolean>(false);

const formRef = ref<HTMLElement>();

const onSaveOk = () => {
  proxy?.$refs["formRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      if (
        formModel.value.price > mklinePrice.value &&
        formModel.value.price - mklinePrice.value * (5 / 100) >
          mklinePrice.value
      ) {
        Modal.confirm({
          title: "提示",
          content:
            "当前价格大于实时价格(5%):" +
            (formModel.value.price - mklinePrice.value).toFixed(2) +
            "，是否继续？",
          okText: "确定继续",
          cancelText: "取消",
          alignCenter: true,
          onOk() {
            btnLoading.value = true;
            toCreateApi();
          },
          onCancel() {
            return true;
          },
        });
      } else if (
        formModel.value.price < mklinePrice.value &&
        formModel.value.price + mklinePrice.value * (5 / 100) <
          mklinePrice.value
      ) {
        Modal.confirm({
          title: "提示",
          content:
            "当前价格小于实时价格(5%):" +
            (mklinePrice.value - formModel.value.price).toFixed(2) +
            "，是否继续？",
          okText: "确定继续",
          cancelText: "取消",
          alignCenter: true,
          onOk() {
            btnLoading.value = true;
            toCreateApi();
          },
          onCancel() {
            return true;
          },
        });
      } else {
        btnLoading.value = true;
        toCreateApi();
      }
    }
  });
};

const toCreateApi = () => {
  createProductPriceApi(
    Object.assign(
      {
        pid: productId.value,
      },
      formModel.value
    )
  )
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
};

const mklinePrice = ref<number | string | any>(0.0);

const toPrice = () => {
  getProductMklinePriceApi({
    pid: productId.value,
  }).then((res: Result) => {
    mklinePrice.value = res.data.price || 0.0;
    initLoading.value = false;
  });
};

const priceTimer = ref<any>(null);

const clearPriceTimer = () => {
  if (priceTimer.value) {
    clearInterval(priceTimer.value);
    priceTimer.value = null;
  }
};

const forGetPrice = () => {
  clearPriceTimer();
  toPrice();
  priceTimer.value = setInterval(() => {
    toPrice();
  }, 2000);
};

const open = (id: number, name: string) => {
  initLoading.value = true;
  productId.value = id;
  productName.value = name;
  visible.value = true;
  forGetPrice();
  setTimeout(() => {
    toInit();
  }, 500);
};

const close = () => {
  proxy?.$refs["formRef"]?.resetFields();
  productId.value = 0;
  productName.value = "";
  visible.value = false;
  clearPriceTimer();
  return true;
};

onBeforeUnmount(() => {
  clearPriceTimer();
});

defineExpose({ open, close });
</script>