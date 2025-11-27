
<template>
  <a-select
    v-model="agentId"
    placeholder="选择代理商"
    allow-clear
    :loading="initAgentLoading"
    multiple
    @change="onChange"
  >
    <!-- <a-option value="all" label="全部" /> -->
    <a-option v-for="(item, index) in agents" :key="index" :value="item.id"
      >{{ item.real_name }}({{ item.account }})</a-option
    >
  </a-select>
</template>

<script lang="ts">
export default {
  name: "searchSelect",
};
</script>
<script lang="ts" setup>
import { ref, onMounted } from "vue";
import { useAppStore, useDataStore } from "@/store";
import { storeToRefs } from "pinia";
import { getAgentSelectByPidApi } from "@/api/agent";
import { ResultError, Result } from "@/types";

const { agents } = storeToRefs(useDataStore());

const emit = defineEmits(["change", "update:modelValue"]);

const props = withDefaults(
  defineProps<{
    modelValue?: string | number | string[] | number[] | any;
  }>(),
  {
    modelValue: "",
  }
);

const agentId = ref<string | number | string[] | number[]>(props.modelValue);

const initAgentLoading = ref<boolean>(false);
const toInitAgentSelect = () => {
  initAgentLoading.value = true;
  getAgentSelectByPidApi(0)
    .then((res: Result) => {
      useDataStore().setAgents(res.data);
      initAgentLoading.value = false;
    })
    .catch((err: ResultError) => {
      initAgentLoading.value = false;
    });
};

onMounted(() => {
  console.log(agents.value);
  if (agents.value.length == 0) {
    toInitAgentSelect();
  }
});
const onChange = (value: any) => {
  emit("update:modelValue", value);
};
</script>

<style scoped>
</style>