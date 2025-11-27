<template>
    <a-split class="console-body" :loading="allLoading" v-model:size="splitSize" @moving="onSplitMoving">
        <template #first>
            <div class="console-body-left">
                <a-split direction="vertical" :style="{ height: '100%' }" @moving="onSplitMoving">
                    <template #first>
                        <div class="console-body-market-echart" v-loading="!wsStatus">
                            <marketEchartsComponent v-if="wsStatus" ref="marketEchartsComponentRef">
                            </marketEchartsComponent>
                        </div>
                    </template>
                    <template #second>
                        <div class="console-body-order">
                            <memberOrderComponent></memberOrderComponent>
                        </div>
                    </template>
                </a-split>
            </div>
        </template>
        <template #second>
            <div class="console-body-right">
                <a-split direction="vertical" :style="{ height: '100%' }" min="240px">
                    <template #first>
                        <sectorComponent></sectorComponent>
                    </template>
                    <template #second >
                        <marketDetailComponent></marketDetailComponent>
                        <walletComponent></walletComponent>
                    </template>
                </a-split>
            </div>
        </template>
    </a-split>
</template>
<script lang="ts" setup>
import { ref, reactive, getCurrentInstance, nextTick, onMounted, computed } from "vue";
import store, { useAppStore, useWebsocketStore } from "@/store";
import { storeToRefs } from "pinia";
import { websocketCallbackFunction } from "@/utils/ws-callback-function";
import sectorComponent from "@/components/market/sector.vue"
import marketDetailComponent from "@/components/market/market-detail.vue"
import walletComponent from "@/components/member/wallet.vue"
import memberOrderComponent from "@/components/member/order.vue"
import marketEchartsComponent from "@/components/market/market-echarts.vue"

const { allLoading, isMobile } = storeToRefs(useAppStore());

const { wsStatus } = storeToRefs(useWebsocketStore());
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;


const websocketOpenCallback = () => {
    if (useWebsocketStore().ws) {
        $utils.eventsListener("websocketMessage", websocketCallbackFunction);
    }
}

onMounted(() => {
    websocketOpenCallback();
    nextTick(() => {
        setTimeout(() => {
            var fixedTable: any = document?.getElementsByClassName("console-body-order");
            let parentNodes = fixedTable[0].children;
            let consoleHeight = parentNodes[0].offsetHeight
            let otherHeight = isMobile ? 44 : (40 - 20)
            useAppStore().setOrderBodyHeight(Number(consoleHeight - otherHeight))
        }, 300);
    })
})

const splitSize = ref<number>(0.7)

const marketEchartsComponentRef = ref<HTMLElement>()
const onSplitMoving = () => {
    nextTick(() => {
    proxy?.$refs["marketEchartsComponentRef"]?.onMoving()
        setTimeout(() => {
            var fixedTable: any = document?.getElementsByClassName("console-body-order");
            let parentNodes = fixedTable[0].children;
            let consoleHeight = parentNodes[0].offsetHeight;
            let otherHeight = isMobile ? 44 : (40 - 20);
            useAppStore().setOrderBodyHeight(Number(consoleHeight - otherHeight))
        }, 300);
    })
}

</script>

<style scoped>
.console-body {
    display: flex;
    justify-content: space-between;
    position: fixed;

    width: calc(100% - (2*var(--base-padding)));
    height: calc(100% - (2*var(--base-padding)) - 51px - 51px);
    min-width: 1200px;
}

.console-body-left {
    height: 100%;
    /* width: calc(100% - 420px - var(--base-padding)); */
}

.console-body-right {
    /* width: 420px; */
    position: relative;
    height: 100%;
}

.console-body-order {
    /* height: calc(30% - var(--base-padding)); */
    height: 100%;
}

.console-body-market-echart {
    height: calc(100% - 2px);
}

@media screen and (max-height: 1060px),
screen and (max-width: 1199px) {
    .console-body {
        width: calc(100% - (2*var(--base-min-padding)));
    }

    .console-body-left {
        width: 100%;
        /* width: calc(100% - 420px - var(--base-min-padding)); */
    }

    .console-body-order {
        /* height: 30%; */
        height: 100%;
    }
}
</style>

<style>
.layout-body .arco-split-pane::-webkit-scrollbar {
    width: 0;
    height: 0;
}
</style>