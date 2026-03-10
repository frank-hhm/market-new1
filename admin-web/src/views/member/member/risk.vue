<template>
    <a-drawer class="drawer-default" title="配置风控滑点" v-model:visible="visible" :width="880">
        <template #default>


      <a-form :model="formModel" ref="formRef" :rules="formRules">
        <a-table
          :loading="initLoading"
          :data="formModel.risk"
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
            <a-table-column title="状态" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`risk[${rowIndex}].status`"
                >
                      <a-switch type="round" size="small" v-model="record.status" :checked-value="1" :unchecked-value="0"/>
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="触发时长(秒)" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`risk[${rowIndex}].trigger_time`"
                >
                  <a-input-number
                    v-model="record.trigger_time"
                    allow-clear
                  ></a-input-number>
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="监控时长(时)" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`risk[${rowIndex}].monitor_time`"
                >
                  <a-input-number
                    v-model="record.monitor_time"
                    allow-clear
                  ></a-input-number>
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="平仓滑点(叠加)" data-index="title" :width="120">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  hide-label
                  hide-asterisk
                  :field="`risk[${rowIndex}].price`"
                >
                  <a-input-number
                    v-model="record.price"
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
  name: "riskComponent",
};
</script>
<script lang="ts" setup>
import { onMounted, ref, getCurrentInstance, nextTick, reactive } from "vue";
import { useRouter } from "vue-router";
import { Result, ResultError, PageLimitType } from "@/types";
import { useSetting } from "@/hooks/useSetting";
import {
  getDetailMemberRiskApi,
  setDetailMemberRiskApi,
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
  getDetailMemberRiskApi({
    id: memberId.value,
  })
    .then((res: Result) => {
      res.data.forEach((item: any, index: number) => {
        formModel.value.risk[index] = {
          id: item.id,
          title: item.title,
          price: Number(item.price),
          status: Number(item.status),
          trigger_time: Number(item.trigger_time),
          monitor_time: Number(item.monitor_time),
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
  risk: [],
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

      setDetailMemberRiskApi(
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
  formModel.value.setting = [];
  visible.value = false;
};

defineExpose({ open });
</script>