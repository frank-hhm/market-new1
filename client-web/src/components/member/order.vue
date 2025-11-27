<template>
    <div class="member-order">
        <layout-body-tabs heightFil :tabs="tabs" v-model="tabIndex" @change="onChangeTab">
            <div class="tab-item-body" v-if="tabIndex == 'chi'">
                <memberOrderHoldComponent></memberOrderHoldComponent>
            </div>
            <div class="tab-item-body" v-if="tabIndex == 'gua'">
                <memberOrderRobotComponent></memberOrderRobotComponent>
            </div>
            <div class="tab-item-body" v-if="tabIndex == 'today'">
                <memberOrderComlateComponent ref="memberOrderComlateComponentRef"></memberOrderComlateComponent>
            </div>
            <div class="tab-item-body" v-if="tabIndex == 'history'">
                <membeHistoryRecordComponent ref="memberOrderHistoryComponentRef"></membeHistoryRecordComponent>
            </div>
            <div class="tab-item-body" v-if="tabIndex == 'water'">
                <memberWaterComponent ref="memberWaterComponentRef"></memberWaterComponent>
            </div>
        </layout-body-tabs>
    </div>
</template>


<script lang="ts" setup>
import { computed, getCurrentInstance, ref, watch, nextTick } from "vue";
import { useChargeStore, useMarketStore, useMemberStore } from "@/store";
import { storeToRefs } from "pinia";
import { EnumItemType, EnumListsType, Result, ResultError } from "@/types";
import memberOrderHoldComponent from "@/components/member/order/order-hold.vue";
import memberOrderRobotComponent from "@/components/member/order/order-robot.vue";
import memberOrderComlateComponent from "@/components/member/order/order-complate.vue";
import membeHistoryRecordComponent from "@/components/member/order/order-history-record.vue";
import memberWaterComponent from "@/components/member/water.vue";
import { initMember } from "@/utils";

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const tabs = ref<EnumItemType[]>([{
    name: "持仓",
    value: "chi"
}, {
    name: "挂单",
    value: "gua"
}, {
    name: "当日盈亏",
    value: "today"
}, {
    name: "历史盈亏",
    value: "history"
}, {
    name: "资金流水",
    value: "water"
}])


const tabIndex = ref<string | number>("chi");

const tabIndexBak = ref<string | number>("chi");

const memberOrderHoldComponentRef = ref<HTMLElement>();

const memberOrderComlateComponentRef = ref<HTMLElement>();

const memberWaterComponentRef = ref<HTMLElement>();


const memberOrderHistoryComponentRef = ref<HTMLElement>();


const onChangeTab = (tab: string) => {
    if (tabIndexBak.value == tab) {
        return false;
    }
    switch (tab) {
        case "chi":
            nextTick(() => {
                initMember()
            });
            break;
        case "gua":
            nextTick(() => {
                initMember()
            });
            break;
        case "today":
            nextTick(() => {
                proxy?.$refs["memberOrderComlateComponentRef"]?.initTodayComplateOrder();
            });
            break;
        case "history":
            nextTick(() => {
                proxy?.$refs["memberOrderHistoryComponentRef"]?.toInit();
            });
            break;
        case "water":
            nextTick(() => {
                proxy?.$refs["memberWaterComponentRef"]?.toInit(true);
            });
            break;
    }
    tabIndexBak.value = tabIndex.value;
}

</script>
<style scoped>
.member-order {
    color: var(--color-text-1);
    /* margin-top: calc(var(--base-padding)); */
    height: 100%;
}

.tab-item-body {
    height: calc(100% - var(--base-padding) * 2);
    padding: calc(var(--base-padding));
}


@media screen and (max-height: 1060px) , screen and (max-width: 1199px) {
    .member-order {

        margin-top: calc(var(--base-min-padding));
    }

    .tab-item-body {
        padding: calc(var(--base-min-padding));
        height:100% ;
    }
}
</style>