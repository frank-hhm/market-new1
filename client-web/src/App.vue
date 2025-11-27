<template>
    <router-view />
</template>

<script lang="ts" setup>
import { getCurrentInstance, onMounted, watch, onBeforeUnmount } from "vue";
import { storeToRefs } from "pinia";
import { useAppStore, useWebsocketStore } from "@/store";
import { useMarketStore } from "./store/modules/market";

const { config, isDark } = storeToRefs(useAppStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

useAppStore().initConfig();
useWebsocketStore().initWebSocket();

useAppStore().setDark(isDark.value)

useMarketStore().initMarkets();
const listenResizeFn = (e: any) => {
    useAppStore().setMobile($utils.isMobileOrSmallScreen())
}

onMounted(() => {
    $utils.eventsListener('resize', listenResizeFn, true);
    setHeadLinks()
    $utils.initMember();
    useAppStore().setClientWidth();
     useAppStore().setMobile($utils.isMobileOrSmallScreen())
    window.onresize = () => {
        return (() => {
            useAppStore().setClientWidth();
        })();
    };
});
onBeforeUnmount(() => {
    $utils.dispatchEvents('resize', listenResizeFn);
})

const setHeadLinks = () => {
    const link = document.createElement('link');
    link.rel = 'icon';
    link.type = 'image/x-icon';
    link.href = config.value?.system_icon || '';
    document.head.appendChild(link);
}

watch(
    () => config.value,
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