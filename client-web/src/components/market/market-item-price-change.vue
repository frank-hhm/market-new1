<template>
    <div class="item-price-change" :class="item.is_open ? '' : 'market-none-open'" :style="{ color: color }">
        <div :class="item.is_open ? '' : 'market-none-open'">{{ sign }}{{ changeValue }}%</div>
    </div>
</template>

<script lang="ts">
export default {
    name: "market-item-price-change",
};
</script>
<script lang="ts" setup>
import { useMarketStore, useMemberStore } from "@/store";
import { computed, ref, watch } from "vue";

const props = withDefaults(
    defineProps<{
        item?: any;
    }>(),
    {
        item: () => {
            return {};
        },
    }
);

const priceChange = computed(() => {
    return useMarketStore().getPriceChange(Number(props.item?.id))
});
const changeValue = computed(() => {
    return priceChange.value?.changeValue;
});
const color = computed(() => {
    const val = priceChange.value?.changeValue;
    if (val === 0) return "#000";
    return val > 0 ? useMemberStore().getUpColor() : useMemberStore().getDownColor();
});
const sign = computed(() => {
    return priceChange.value?.changeValue > 0 ? "+" : "";
});
</script>
<style scoped>
.item-price-change {
    font-size: var(--base-size-small)
}
</style>