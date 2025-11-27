<template>
    <router-view />
</template>
    
<script lang="ts" setup>
import { onMounted, ref, watch } from "vue";
import { storeToRefs } from "pinia";
import { useAgentStore, useAppStore, useEnumStore } from "@/store";

const { systemInfo,isDark } = storeToRefs(useAppStore());


useEnumStore().initEnum();
useAppStore().setDark(isDark.value)

useAppStore().getSystemInfo();

onMounted(() => {
    if (useAgentStore().token) {
        useAgentStore().initInfo()
        // useWebsocketStore().initWebSocket();
    }
    document.title = systemInfo.value.system_name || '管理后台';
    setHeadLinks()
});

const setHeadLinks = () => {
    const link = document.createElement('link');
    link.rel = 'icon';
    link.type = 'image/x-icon';
    link.href = systemInfo.value.system_icon || '';
    document.head.appendChild(link);
}

watch(
    () => systemInfo.value,
    (val) => {
        setHeadLinks()
    },
    { deep: true }
);
</script>
    
    
    
<style>
#nprogress .bar {
    background: rgba(var(--primary-6)) !important;
}

#nprogress .peg {
    box-shadow: 0 0 10px rgba(var(--primary-6)), 0 0 5px rgba(var(--primary-6)) !important;
}
</style>
    
    
    