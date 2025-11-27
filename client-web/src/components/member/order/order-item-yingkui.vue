<template>
    <div class="order-item">
        <div :style="{
            color: yingkuiClass
        }">
            {{ yingkuiSign }}{{ yingkuiAmount }}
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "member-order-item-yingkui",
};
</script>
<script lang="ts" setup>
import { useChargeStore, useMarketStore, useWalletStore } from "@/store";
import { ref, getCurrentInstance, nextTick, reactive, onMounted, watch, computed } from "vue";

const props = withDefaults(
    defineProps<{
        order?: Object;
    }>(),
    {
        order: () => {
            return {}
        }
    }
);

const yingkuiData = computed(() => {
    return useWalletStore().getYingKui(props.order);
});

const yingkuiAmount = computed(() => {
    return yingkuiData.value?.amount;
});

const yingkuiSign = computed(() => {
    return yingkuiData.value?.sign;
});

const yingkuiClass = computed(() => {
    return yingkuiData.value?.className;
});

</script>

<style scoped>
.order-item-market-price {}
</style>