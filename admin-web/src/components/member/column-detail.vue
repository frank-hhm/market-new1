
<template>
  <div class="member-column-detail">
    <a-image
      v-if="member?.avatar"
      class="member-column-detail-avatar"
      :src="member?.avatar"
      :height="40"
      :width="40"
    />
    <div class="member-column-detail-right">
      <div>{{ member.username }}</div>
      <div v-if="member?.nickname">{{ member.nickname }}</div>
      <div>{{ member.email }}</div>
      <div>{{ member.mobile }}</div>
    </div>
  </div>
</template>

<script lang="ts">
export default {
  name: "memberColumnDetail",
};
</script>
<script lang="ts" setup>
import { ref } from "vue";
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";
const props = withDefaults(
  defineProps<{
    member?: any;
    isId?: boolean;
  }>(),
  {
    member: () => {
      return {};
    },
    isId: false,
  }
);
</script>

<style scoped>
.member-column-detail {
  display: flex;
  align-items: center;
}
.member-column-detail-avatar {
    margin-right: 10px;
}
</style>