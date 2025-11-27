<template>
  <div>
    <a-form :model="configForm" ref="configFormRef">
      <a-card title="其他公共配置" class="card" v-loading="initLoading">
        <template #extra>
          <a-button
            type="primary"
            @click="onSave"
            :loading="btnLoading"
            :disabled="btnLoading"
            >保存</a-button
          >
        </template>
        <div class="card-form-box">
          <a-form-item
            :label-col-flex="labelColFlex"
            label="模拟风云榜时间提示"
            field="moni_activity_time_tips"
          >
            <a-input
              v-model="configForm.moni_activity_time_tips"
              placeholder="模拟风云榜时间提示"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="客服QQ"
            field="kefu_qq"
          >
            <a-input
              v-model="configForm.kefu_qq"
              placeholder="请输入客服QQ"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="微信客服"
            field="kefu_wechat"
          >
            <a-input
              v-model="configForm.kefu_wechat"
              placeholder="请输入微信号"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="ios下载地址"
            field="ios_download_url"
          >
            <a-input
              v-model="configForm.ios_download_url"
              placeholder="请输入ios下载地址"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="安卓下载地址"
            field="android_download_url"
          >
            <a-input
              v-model="configForm.android_download_url"
              placeholder="请输入安卓下载地址"
              allow-clear
            />
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="登录Banner"
            field="login_banner"
          >
            <upload-btn
              v-model="configForm.login_banner"
              count="1"
              width="300px"
              height="140px"
            ></upload-btn>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="公司介绍"
            field="about_content"
          >
            <editor
              ref="editorAboutRef"
              height="400px"
              v-model="configForm.about_content"
            ></editor>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="用户需知"
            field="about_content_know"
          >
            <editor
              ref="editorAboutKnowRef"
              height="400px"
              v-model="configForm.about_content_know"
            ></editor>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="交易规则"
            field="about_role"
          >
            <editor
              ref="editorRoleRef"
              height="400px"
              v-model="configForm.about_role"
            ></editor>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="跟单规则"
            field="about_content_follow"
          >
            <editor
              ref="editorFollowRef"
              height="400px"
              v-model="configForm.about_content_follow"
            ></editor>
          </a-form-item>
          
          <!-- <a-form-item :label-col-flex="labelColFlex" label="关于我们" field="about_bg">
                        <upload-btn v-model="configForm.about_bg" count="1" width="200px" height="300px"></upload-btn>
                    </a-form-item> -->
          <a-form-item
            :label-col-flex="labelColFlex"
            label="客户协议"
            field="agreement_customer"
          >
            <editor
              ref="editorCustomerRef"
              height="400px"
              v-model="configForm.agreement_customer"
            ></editor>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="风险披露"
            field="agreement_risk"
          >
            <editor
              ref="editorRiskRef"
              height="400px"
              v-model="configForm.agreement_risk"
            ></editor>
          </a-form-item>
          <a-form-item
            :label-col-flex="labelColFlex"
            label="免责声明"
            field="agreement_disclaimers"
          >
            <editor
              ref="editorDisclaimersRef"
              height="400px"
              v-model="configForm.agreement_disclaimers"
            ></editor>
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
    </a-form>
  </div>
</template>


<script lang="ts" setup>
import { getCurrentInstance, onMounted, ref } from "vue";
import { getConfigApi, saveConfigApi } from "@/api/system/config";
import { Result, ResultError } from "@/types";
import { useAppStore } from "@/store";

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const labelColFlex = ref<string>("180px");

const configFormRef = ref<HTMLElement>();

const configForm = ref<any>({
  about_bg: "",
  kefu_qq: "",
  kefu_wechat: "",
  about_content: "",
  about_content_know: "",
  about_role: "",
  agreement_customer: "",
  agreement_risk: "",
  agreement_disclaimers: "",
  login_banner: "",
  moni_activity_time_tips: "",
  ios_download_url: "",
  android_download_url: "",
  about_content_follow:""
});

const initLoading = ref<boolean>(false);

const editorRiskRef = ref<HTMLElement>();

const editorCustomerRef = ref<HTMLElement>();

const editorDisclaimersRef = ref<HTMLElement>();

const editorAboutRef = ref<HTMLElement>();

const editorRoleRef = ref<HTMLElement>();

const editorAboutKnowRef = ref<HTMLElement>();
const editorFollowRef = ref<HTMLElement>();

const toInit = () => {
  initLoading.value = true;
  getConfigApi("other").then(
    (res: Result) => {
      configForm.value = res.data;
      configForm.value.about_bg = res.data.about_bg;
      configForm.value.kefu_qq = String(res.data.kefu_qq);
      configForm.value.kefu_wechat = String(res.data.kefu_wechat);
      configForm.value.login_banner = String(res.data.login_banner);
      configForm.value.moni_activity_time_tips = String(
        res.data.moni_activity_time_tips
      );
      configForm.value.ios_download_url = String(res.data.ios_download_url);
      configForm.value.android_download_url = String(
        res.data.android_download_url
      );

      proxy?.$refs["editorAboutRef"]?.setContent(res.data.about_content || "");
      proxy?.$refs["editorRoleRef"]?.setContent(res.data.about_role || "");
      proxy?.$refs["editorRiskRef"]?.setContent(res.data.agreement_risk || "");
      proxy?.$refs["editorCustomerRef"]?.setContent(
        res.data.agreement_customer || ""
      );
      proxy?.$refs["editorDisclaimersRef"]?.setContent(
        res.data.agreement_disclaimers || ""
      );
      proxy?.$refs["editorAboutKnowRef"]?.setContent(
        res.data.about_content_know || ""
      );
      proxy?.$refs["editorFollowRef"]?.setContent(
        res.data.about_content_follow || ""
      );

      initLoading.value = false;
    },
    (err: ResultError) => {
      initLoading.value = false;
      $utils.errorMsg(err);
    }
  );
};

const btnLoading = ref<boolean>(false);

const onSave = () => {
  proxy?.$refs["configFormRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      btnLoading.value = true;
      saveConfigApi(configForm.value).then(
        (res: Result) => {
          btnLoading.value = false;
          useAppStore().getSystemInfo();
          $utils.successMsg(res.message);
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
  width: 600px;
}
</style>