<template>
  <a-modal
    title="余额充值"
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
        <a-form-item :label-col-flex="labelColFlex" label="当前账号：">
          <div class="ml5">
            <div>{{ coinDetail.member?.username }}</div>
            <div>{{ coinDetail.member?.mobile }}</div>
          </div>
        </a-form-item>
        <a-form-item :label-col-flex="labelColFlex" label="当前余额：">
          <div class="fz14 fw ml5 text-red">{{ coinDetail.balance }}</div>
        </a-form-item>
        <a-form-item
          :label-col-flex="labelColFlex"
          label="充值方式："
          field="mode"
        >
          <a-radio-group v-model="formModel.mode">
            <a-radio value="inc">增加</a-radio>
            <a-radio value="dec">减少</a-radio>
            <a-radio value="final">最终余额</a-radio>
          </a-radio-group>
        </a-form-item>
        <a-form-item
          :label-col-flex="labelColFlex"
          label="变更金额："
          field="money"
        >
          <a-input-number v-model="formModel.money">
            <template #suffix>元</template>
          </a-input-number>
        </a-form-item>
        <a-form-item
          :label-col-flex="labelColFlex"
          label="标记来源："
          field="source"
        >
          <a-select
            v-model="formModel.source"
            placeholder="请选择标记来源"
            allow-clear
          >
            <a-option
              v-for="(item, index) in sourceEnum"
              :key="index"
              :value="item.value"
            >
              {{ item.name }}
            </a-option>
          </a-select>
        </a-form-item>

        <a-form-item
          :label-col-flex="labelColFlex"
          label="备注说明："
          field="remark"
        >
          <a-textarea v-model="formModel.remark"></a-textarea>
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
import { ref, getCurrentInstance, nextTick, reactive } from "vue";
import { EnumItemType, Result, ResultError } from "@/types";
import {
  getDetailMemberCoinApi,
  setMemberRechargeBalanceApi,
} from "@/api/finance/coin";
import { useSetting } from "@/hooks/useSetting";
import { useEnumStore } from "@/store";

const labelColFlex = ref<string>("80px");

const sourceEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("finance.SourceEnum")
);

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const emit = defineEmits(["success"]);

const visible = ref<boolean>(false);

type modeType = "inc" | "dec" | "final";

const formModel = ref<any>({
  mode: "inc",
  money: null,
  remark: "",
  source: "admin",
});

const coinId = ref<number | string>(0);

const initLoading = ref<boolean>(false);

const coinDetail = ref<any>({});

const toInit = () => {
  initLoading.value = true;
  getDetailMemberCoinApi({
    id: coinId.value,
  })
    .then((res: Result) => {
      coinDetail.value = res.data;
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      initLoading.value = false;
    });
};

const moneyValid = (rule: any, callback: any) => {
  if (Number(formModel.value.money) < 0) {
    callback("金额必须大于等于0!");
  } else {
    callback();
  }
};
const formRules = reactive<any>({
  money: [
    { required: true, message: "金额不能为空！", trigger: "blur" },
    { validator: moneyValid, trigger: "blur" },
  ],
});

const btnLoading = ref<boolean>(false);

const formRef = ref<HTMLElement>();

const onSaveOk = () => {
  proxy?.$refs["formRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      setMemberRechargeBalanceApi(
        Object.assign(
          {
            id: coinId.value,
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
    }
  });
};

const open = (id: number | string) => {
  coinId.value = id;
  visible.value = true;
  toInit();
};

const close = () => {
  proxy?.$refs["formRef"]?.resetFields();
  coinId.value = 0;
  visible.value = false;
  return true;
};

defineExpose({ open, close });
</script>