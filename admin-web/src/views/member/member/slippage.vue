<template>
  <a-drawer
    class="drawer-default"
    title="滑点设置"
    v-model:visible="visible"
    :width="700"
  >
    <template #default>
      <a-form :model="formModel" ref="formRef" :rules="formRules">
        <a-table
          :loading="initLoading"
          class="mt20"
          :data="formModel.slippage"
          row-key="id"
          isLeaf
          :pagination="false"
        >
          <template #columns>
            <a-table-column title="产品" data-index="title" :width="120">
              <template #cell="{ record }">
                <div>{{ record.title }}</div>
              </template>
            </a-table-column>
            <a-table-column title="开仓滑点" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`slippage[${rowIndex}].buy`"
                >
                  <a-input-number
                    v-model="record.buy"
                    allow-clear
                  ></a-input-number>
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="出仓滑点" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`slippage[${rowIndex}].sell`"
                >
                  <a-input-number
                    v-model="record.sell"
                    allow-clear
                  ></a-input-number>
                </a-form-item>
              </template>
            </a-table-column>
          </template>
        </a-table>
      </a-form>
    </template>
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
  </a-drawer>
</template>
<script lang="ts">
export default {
  name: "productControl",
};
</script>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";
import { useRouter } from "vue-router";
import { Result, ResultError, PageLimitType } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  getDetailMemberSlippageApi,
  setDetailMemberSlippageApi,
} from "@/api/member/member";

const emit = defineEmits(["success"]);

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const memberId = ref<number>(0);

const initLoading = ref<boolean>(false);

const toInit = () => {
  initLoading.value = false;
  getDetailMemberSlippageApi({
    id: memberId.value,
  })
    .then((res: Result) => {
      res.data.forEach((item: any, index: number) => {
        formModel.value.slippage[index] = {
          id: item.id,
          title: item.title,
          buy: Number(item.buy),
          sell: Number(item.sell),
        };
      });
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err.message);
    });
};

const formModel = ref<any>({
  slippage: [],
});

const formRules = reactive<any>({});

const btnLoading = ref<boolean>(false);

const formRef = ref<HTMLElement>();

const onSaveOk = () => {
  proxy?.$refs["formRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;

      setDetailMemberSlippageApi(
        Object.assign(
          {
            id: memberId.value,
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
const visible = ref<boolean>(false);

const open = (id: number = 0) => {
  memberId.value = id;
  nextTick(() => {
    toInit();
  });
  visible.value = true;
};

const close = () => {   
  memberId.value = 0;
  proxy?.$refs["formRef"]?.resetFields();
  formModel.value.slippage = [];
  visible.value = false;
};

defineExpose({ open });
</script>