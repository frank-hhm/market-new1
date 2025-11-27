
<template>
  <div class="agent-column-detail">
    <a-image
      v-if="agent && agent?.avatar"
      class="agent-column-detail-avatar"
      :src="agent?.avatar"
      :height="40"
      :width="40"
    />
    <div v-if="agent" class="agent-column-detail-right">
      <div>{{ agent?.account }}</div>
      <div>{{ agent?.real_name }}</div>
    </div>
    <div class="agent-column-detail-right text-grey" v-else>无</div>
  </div>
</template>

<script lang="ts">
export default {
  name: "agentColumnDetail",
};
</script>
<script lang="ts" setup>
import { ref } from "vue";
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";
const props = withDefaults(
  defineProps<{
    agent?: any;
  }>(),
  {
    agent: () => {
      return {};
    },
  }
);
</script>

<style scoped>
.agent-column-detail{
    display: flex;
    align-items: center;
}

.agent-column-detail-avatar {
    margin-right: 10px;
}
</style>