<template>
  <a-modal
    :title="operation == 'create' ? '添加渠道' : '编辑渠道'"
    @BeforeOk="onCreateOk"
    @BeforeCancel="close"
    width="600px"
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
        <a-row :gutter="20">
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="名称"
              field="name"
            >
              <a-input
                v-model="createForm.name"
                placeholder="请输入名称"
              ></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="别名(仅后台显示)"
              field="nickname"
            >
              <a-input
                v-model="createForm.nickname"
                placeholder="请输入别名"
              ></a-input>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="类型"
              prop="pay_type"
            >
              <a-select v-model="createForm.type" placeholder="选择类型">
                <a-option value="offline_usdt" label="线下usdt" />
                <a-option value="offline_bank" label="线下银行卡" />
                <a-option value="offline_alipay" label="线下支付宝" />
                <a-option value="alipay_transfer" label="支付宝转账" />
                <a-option value="wechat_qrcode" label="微信扫码" />
              </a-select>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item :label-col-flex="labelColFlex">
              <div style="width: 100%">
                <a-button size="mini" @click="onAddSetting"
                  >添加配置项</a-button
                >
                <div class="setting-box">
                  <template v-for="(item, index) in settingData" :key="index">
                    <div class="setting-item">
                      <div class="setting-item-top">
                        第 {{ index + 1 }} 项配置
                      </div>
                      <div
                        class="setting-item-del"
                        @click="onDelSetting(index)"
                      >
                        删除
                      </div>
                      <div class="setting-item-main">
                        <div class="setting-item-left">
                          <a-input
                            size="small"
                            v-model="item.key"
                            placeholder="请输入配置标识"
                          ></a-input>
                        </div>
                        <div class="setting-item-center">
                          <a-select
                            size="small"
                            v-model="item.type"
                            placeholder="请选择字段类型"
                          >
                            <a-option label="文本" value="text"></a-option>
                            <a-option label="图片" value="image"></a-option>
                            <a-option
                              label="多行文本"
                              value="textarea"
                            ></a-option>
                          </a-select>
                        </div>
                        <div class="setting-item-right">
                          <a-input
                            v-if="item.type === 'text' || !item.type"
                            size="small"
                            v-model="item.value"
                            placeholder="请输入配置值"
                          ></a-input>
                          <upload-btn
                            v-else-if="item.type === 'image'"
                            v-model="item.value"
                            width="80px"
                            height="80px"
                            count="1"
                          ></upload-btn>
                          <a-textarea
                            v-else-if="item.type === 'textarea'"
                            size="small"
                            v-model="item.value"
                            placeholder="请输入配置值"
                            allow-clear
                          />
                        </div>
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24" v-if="createForm.type === 'offline_alipay'">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="是否大图扫码"
              field="is_qrcode"
            >
              <a-radio-group v-model="createForm.is_qrcode">
                <a-radio
                  :value="0"
                  >否</a-radio
                >
                <a-radio
                  :value="1"
                  >是</a-radio
                >
              </a-radio-group>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="图标"
              field="cover"
            >
              <upload-btn
                v-model="createForm.cover"
                width="80px"
                height="80px"
                count="1"
              ></upload-btn>
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-row :gutter="20">
              <a-col :md="12" :xs="24">
                <a-form-item
                  :label-col-flex="labelColFlex"
                  label="最小限制(0为不限制)"
                  field="min"
                >
                  <a-input-number
                    v-model="createForm.min"
                    :min="0"
                    :max="10000000000"
                  />
                </a-form-item>
              </a-col>
              <a-col :md="12" :xs="24">
                <a-form-item
                  :label-col-flex="labelColFlex"
                  label="最大限制(0为不限制)"
                  field="max"
                >
                  <a-input-number
                    v-model="createForm.max"
                    :min="0"
                    :max="10000000000"
                  />
                </a-form-item>
              </a-col>
            </a-row>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="排序"
              field="sort"
            >
              <a-input-number v-model="createForm.sort" :min="0" :max="10000" />
            </a-form-item>
          </a-col>
          <a-col :md="24" :xs="24">
            <a-form-item
              :label-col-flex="labelColFlex"
              label="状态"
              field="status"
            >
              <a-radio-group v-model="createForm.status">
                <a-radio
                  v-for="item in statusEnum"
                  :value="item.value"
                  :key="item.value"
                  >{{ item.name }}</a-radio
                >
              </a-radio-group>
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
  name: "PaymentCreate",
};
</script>
<script lang="ts" setup>
import { ref, getCurrentInstance, nextTick, reactive, onMounted } from "vue";
import { useSetting } from "@/hooks/useSetting";
import {
  createPaymentApi,
  updatePaymentApi,
  getDetailPaymentApi,
} from "@/api/payment";
import { EnumItemType, Result, ResultError } from "@/types";
import { useEnumStore } from "@/store";

const labelColFlex = ref<string>("80px");

const emit = defineEmits(["success"]);

const statusEnum = ref<EnumItemType[]>(
  useEnumStore().getEnumItem("StatusEnum")
);

const operation = ref<string>("create");

const operationId = ref<number>(0);

const {
  proxy,
  proxy: { $utils },
} = getCurrentInstance() as any;

const visible = ref<boolean>(false);

const initLoading = ref<boolean>(false);

const btnLoading = ref<boolean>(false);

const onCreateOk = () => {
  proxy?.$refs["createRef"]?.validate((valid: any, fields: any) => {
    if (!valid) {
      if (btnLoading.value) {
        return;
      }
      btnLoading.value = true;
      let operationApi: any = null;
      createForm.value.setting = settingData.value;
      if (operation.value == "create") {
        operationApi = createPaymentApi(createForm.value);
      } else {
        operationApi = updatePaymentApi(
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

const toInit = () => {
  if (!operationId.value) {
    return;
  }
  initLoading.value = true;
  getDetailPaymentApi({
    id: operationId.value,
  })
    .then((res: any) => {
      createForm.value.name = res.data.name;
      createForm.value.nickname = res.data.nickname;
      createForm.value.cover = res.data.cover;
      createForm.value.setting = res.data.setting;
      settingData.value = res.data.setting;
      createForm.value.min = Number(res.data.min);
      createForm.value.max = Number(res.data.max);
      createForm.value.type = res.data.type.value;
      createForm.value.sort = Number(res.data.sort);
      createForm.value.status = Number(res.data.status.value);
      createForm.value.is_qrcode = Number(res.data.is_qrcode);
      
      initLoading.value = false;
    })
    .catch((err: ResultError) => {
      $utils.errorMsg(err);
      initLoading.value = false;
    });
};

const createForm = ref<any>({
  cover: "",
  nickname: "",
  name: "",
  min: 0,
  max: 0,
  sort: 0,
  status: 1,
  type: "offline_bank",
  is_qrcode:0
});

const createRules: any = reactive({
  name: [{ required: true, message: "请选择名称！", trigger: "blur" }],
  nickname: [{ required: true, message: "请输入别名！", trigger: "blur" }],
  cover: [{ required: true, message: "请选择图标！", trigger: "blur" }],
});

const settingData = ref<any>([]);

const onAddSetting = () => {
  settingData.value.push({
    key: "",
    type: "text",
    value: "",
  });
};

const onDelSetting = (idx: number) => {
  settingData.value.splice(idx, 1);
};

const open = (id: number = 0) => {
  if (id != 0) {
    operation.value = "update";
    operationId.value = id;
  } else {
    operation.value = "create";
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
  settingData.value = [];
  return true;
};

defineExpose({ open });
</script>
<style scoped>
.setting-box {
  width: 100%;
  font-size: 12px;
  max-height: 240px;
  overflow-y: scroll;
}

.setting-item {
  padding: 10px;
  margin-top: 10px;
  border: 1px solid #f0f0f0;
  position: relative;
  border-radius: var(--base-radius);
  position: relative;
}

.setting-item-top {
  line-height: 16px;
}

.setting-item-main {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.setting-item-left {
  width: 160px;
}
.setting-item-center {
  width: 160px;
}
.setting-item-right {
  width: calc(100% - 320px - 20px);
}

.setting-item-del {
  position: absolute;
  top: 5px;
  right: 5px;
  cursor: pointer;
  user-select: none;
  color: rgba(var(--gray-6));
}

.setting-item-del:hover {
  color: var(--base-default);
}
</style>