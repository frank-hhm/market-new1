<template>
  <div>
    <a-form :model="configForm" ref="configFormRef">
      <a-card title="短信账号配置" class="card" v-loading="initLoading">
        <div class="card-form-box">
          <a-form-item
            :label-col-flex="labelColFlex"
            label="账号"
            field="smsbao_username"
          >
            <a-input
              v-model="configForm.smsbao_username"
              placeholder="请输入账号"
              allow-clear
            />
            
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="密码"
            field="smsbao_password"
          >
            <a-input
              v-model="configForm.smsbao_password"
              placeholder="请输入密码"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="模板签名"
            field="temp_sign"
          >
            <a-input
              v-model="configForm.temp_sign"
              placeholder="请输入模板签名"
              allow-clear
            />
          </a-form-item>
          <a-form-item :label-col-flex="labelColFlex">
            <a-button
              type="primary"
              @click="onSave"
              :loading="btnLoading"
              :disabled="btnLoading"
              >保存</a-button
            >
          </a-form-item>
        </div>
      </a-card>
      <a-card title="短信模板" class="card mt20" v-loading="initLoading">
        <template #extra>
          <a-button size="small" @click="onCreate">新增</a-button>
        </template>
        <a-table
          :loading="initLoading"
          :data="configForm.sms_temps"
          row-key="temp_id"
          isLeaf
          :pagination="false"
        >
          <template #columns>
            <a-table-column title="模板" data-index="temp_id">
              <template #cell="{ record, rowIndex }">
                <a-form-item
                  row-class="mb0"
                  hide-asterisk
                  hide-label
                  :field="`sms_temps[${rowIndex}].temp_id`"
                >
                  <a-input
                    v-model="record.temp_id"
                    placeholder="请输入模板签名"
                    allow-clear
                  />
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="模板名称" data-index="temp_name">
              <template #cell="{ record,rowIndex }">
                <a-form-item
                  row-class="mb0"
                  hide-asterisk
                  hide-label
                  :field="`sms_temps[${rowIndex}].temp_name`"
                >
                  <a-input
                    v-model="record.temp_name"
                    placeholder="请输入模板名称"
                    allow-clear
                  />
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="标识" data-index="temp_code">
              <template #cell="{ record,rowIndex }">
                <a-form-item
                  row-class="mb0"
                  hide-asterisk
                  hide-label
                  :field="`sms_temps[${rowIndex}].temp_code`"
                >
                  <a-input
                    v-model="record.temp_code"
                    placeholder="请输入模板标识"
                    allow-clear
                  />
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="模板参数" data-index="temp_param">
              <template #cell="{ record,rowIndex }">
                <a-form-item
                  row-class="mb0"
                  hide-asterisk
                  hide-label
                  :field="`sms_temps[${rowIndex}].temp_param`"
                >
                  <a-input
                    v-model="record.temp_param"
                    placeholder="请输入模板参数"
                    allow-clear
                  />
                </a-form-item>
              </template>
            </a-table-column>
            <a-table-column title="模板内容" data-index="temp_content">
              <template #cell="{ record,rowIndex }">
                <a-form-item
                  row-class="mb0"
                  hide-asterisk
                  hide-label
                  :field="`sms_temps[${rowIndex}].temp_content`"
                >
                  <a-input
                    v-model="record.temp_content"
                    placeholder="请输入模板内容"
                    allow-clear
                  />
                </a-form-item>
              </template>
            </a-table-column>
          </template>
        </a-table>
      </a-card>
    </a-form>
  </div>
</template>

<script lang="ts" setup>
import { getCurrentInstance, onMounted, ref } from "vue";
import { getConfigApi, saveConfigApi } from "@/api/system/config";
import { Result, ResultError } from "@/types";
import { config } from "process";

const labelColFlex = ref<string>("120px");

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const initLoading = ref<boolean>(true);

const configFormRef = ref<HTMLElement>();

const configForm = ref<any>({
  smsbao_username: "",
  smsbao_password: "",
  temp_sign: "",
  sms_temps: [],
});

const toInit = () => {
  initLoading.value = true;
  getConfigApi("addon_sms").then(
    (res: Result) => {
      configForm.value.smsbao_username =
        res.data?.addon_sms?.smsbao_username || "";
      configForm.value.smsbao_password =
        res.data?.addon_sms?.smsbao_password || "";
      configForm.value.temp_sign = res.data?.addon_sms?.temp_sign || "";
      configForm.value.sms_temps = res.data?.addon_sms?.sms_temps || [];
      initLoading.value = false;
    },
    (err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    }
  );
};

const onCreate = () => {
  configForm.value.sms_temps.push({
    temp_id: "",
    temp_name: "",
    temp_code: "",
    temp_param: "",
    temp_content: "",
  });
};

const btnLoading = ref<boolean>(false);

const onSave = () => {
  proxy?.$refs["configFormRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      btnLoading.value = true;
      saveConfigApi({
        addon_sms: configForm.value,
      }).then(
        (res: Result) => {
          btnLoading.value = false;
          $utils.successMsg(res.message);
          toInit();
        },
        (err: ResultError) => {
          btnLoading.value = false;
          $utils.errorMsg(err);
        }
      );
    }
  });
};
onMounted(() => {
  toInit();
});
</script>

<style scoped>
.card-form-box {
  width: 400px;
}
</style>