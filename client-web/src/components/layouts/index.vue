<template>
  <div v-loading="allLoading">
    <LayoutNav></LayoutNav>
    <div class="layout-body">
      <router-view></router-view>
    </div>
    <div class="layout-footer">
      <copyright></copyright>
    </div>
  </div>
</template>
<script lang="ts" setup>
import { useAppStore } from "@/store";
import { storeToRefs } from "pinia";
import LayoutNav from "@/components/layouts/LayoutNav.vue";

const { allLoading } = storeToRefs(useAppStore());

</script>

<style scoped>
.layout-body {
  margin: calc(var(--base-padding));
  /* position: fixed; */
  width: calc(100%  - (2*var(--base-padding)));
  height: calc(100% - (2*var(--base-padding)) - 51px - 51px);
  /* min-width: 1200px; */
}

.layout-footer {
  width: 100%;
  text-align: center;
  position: fixed;
  bottom: 0;
  height: 50px;
  background: var(--color-bg-1);
  border-top: 1px solid var(--color-border-1);
  line-height: 40px;
  z-index: -1;
}

.layout-footer img {
  margin: 10px auto;
  height: 30px;
}

html.dark .layout-body {
  background: var(--color-bg-1);
}
</style>