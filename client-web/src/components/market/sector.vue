<template>
    <div class="market-sector" v-loading="sectors.length == 0 || markets.length == 0">
        <div class="market-sector-tag">
            <a-radio-group type="button" v-model="selectSectorId" @change="onChangeSelectSector">
                <a-radio :value="0">我的自选</a-radio>
                <a-radio :value="item.id" v-for="(item, index) in sectors" :key="index">{{ item.sector_name
                    }}</a-radio>
            </a-radio-group>
        </div>
        <div class="market-sector-list">
            <template v-for="(item, index) in markets" :key="index">
                <div class="market-sector-item" :class="[selectMarketId === item.id ? 'active' : '']"
                    v-if="item.sector_id == selectSectorId || (selectSectorId == 0 && $utils.inArray(String(item.id), member?.collects || useMarketStore().defaultCollects))"
                    @click.stop="onChangeSelectMarket(item.id)">
                    <div class="market-sector-item-left">
                        <div class="market-name">{{ item.name }}</div>
                        <div class="market-code mt5">{{ item.code }}</div>
                    </div>
                    <div class="market-sector-item-center " :class="item.is_open ? '' : 'market-none-open'"
                        :style="{ color: item?.price >= item?.old_price ? getUpColor : getDownColor }">
                        <div class="fw">
                            {{ marketPrices[item.id] || item.price }}</div>
                        <div class="mt4 fz12 text-gray">
                            最低：{{ item.low_price || '--' }}
                        </div>
                    </div>
                    <div class="market-sector-item-right">

                        <marketItemPriceChangeComponent :item="item"></marketItemPriceChangeComponent>
                        <div class="mt4 fz12 text-gray">
                            最高：{{ item.height_price || '--' }}
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "srctor",
};
</script>
<script lang="ts" setup>
import { computed, getCurrentInstance, ref, watch } from "vue";
import { useMarketStore, useMemberStore } from "@/store";
import { storeToRefs } from "pinia";
import marketItemPriceChangeComponent from "./market-item-price-change.vue";
const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const { markets, sectors, selectSectorId, selectMarketId, marketPrices } = storeToRefs(useMarketStore());
const { member } = storeToRefs(useMemberStore());
const onChangeSelectSector = (id: any) => {
    useMarketStore().setSectorId(id)
}
const onChangeSelectMarket = (id: any) => {
    useMarketStore().setMarketId(id)
}
const getUpColor = computed(() => useMemberStore().getUpColor());
const getDownColor = computed(() => useMemberStore().getDownColor());

</script>
<style scoped>
.market-sector {
    color: var(--color-text-1);
    /* width: 420px;
    height: calc(100% - 240px - 30% - 40px - var(--base-padding) * 7); */
    width: 100%;
    height:calc( 100% - 44px);
    position: relative;
    padding-top: 42px;
    background: var(--color-bg-2);
    border-radius: var(--base-radius-default);
    border: 1px solid var(--color-border-1);
}

.market-sector .market-sector-tag {
    position: absolute;
    left: 0;
    width: calc(100% - var(--base-padding) * 2);
    margin: calc(var(--base-padding));
    height: 32px;
    top: 0;
    background-color: var(--color-bg-2);
}

.market-sector-list {
    margin-top: 10px;
    height: calc(100% - var(--base-padding) - 42px);
    overflow-y: scroll;
}

.market-sector-item {
    padding: var(--base-padding) calc(var(--base-padding));
    display: flex;
    align-items: center;
    justify-content: space-between;
    border: 1px solid #fff;
    border-top: 1px solid var(--color-border-1);
    cursor: pointer;
    user-select: none;
}

.market-sector-item .market-code {
    margin-top: var(--base-padding / 2);
    color: var(--color-text-3);
    font-size: var(--base-size-small);
}

.market-sector-item:last-child {
    border-bottom: 1px solid var(--color-border-1);
}

.market-sector-item:hover {
    background: var(--base-grey-btn-bg);
    border: 1px solid var(--base-grey-btn-bg);
}

.market-sector-item.active {
    background: var(--color-primary-light-1);
    border: 1px solid rgba(var(--primary-6));
}

.market-sector-item-left,
.market-sector-item-center,
.market-sector-item-right {
    width: 33%;
}

.market-sector-item-center {
    text-align: left;
    font-size: var(--base-size-default)
}
@media screen and (max-height: 1060px) {
    .market-sector {
        
        height:calc( 100% - 44px);
        /* height: calc(100% - (200px + var(--base-min-padding)) - (200px + var(--base-min-padding) * 2) -  32px - var(--base-min-padding) * 4 - ( var(--base-padding) * 2)); */
    }
    .market-sector .market-sector-tag{
         margin: calc(var(--base-min-padding));
         width: calc(100% - var(--base-min-padding) * 2);
    }
    .market-sector-list{
        margin-top: 0;
        height: calc(100% - var(--base-min-padding) * 2);
    }
}
</style>