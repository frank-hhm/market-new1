<template>

    <div class="member-coin">
        <div class="member-coin-item">
            <div class="number">
                <div class="unit">$</div>
                <div class="money">{{ netValue || 0.00 }}</div>
            </div>
            <div class="label"
                @click.stop="onShowTips('净值', '随持仓商品订单的盈亏而变化的实际余额（包括未平仓商品订单的盈亏和先行赠金信用额）账户净值有时效性，实际净值请登录交易平台查看')">
                <div class="text">净值</div>
            </div>
        </div>
        <div class="member-coin-item">
            <div class="number">
                <div class="unit">$</div>
                <div class="money">{{ availableBalance || 0.00 }}</div>
            </div>
            <div class="label" @click.stop="onShowTips('可用保证金', '可用于开立新仓的交易保证金（包含先行赠金信用额）')">
                <div class="text">可用保证金</div>
            </div>
        </div>
        <div class="member-coin-item">
            <div class="number">
                <div class="unit"></div>
                <div class="money" :style="[`color: ${totalProfitClass
                    }`]">{{ totalProfit || 0.00 }}</div>
            </div>
            <div class="label" @click.stop="onShowTips('浮动盈亏', '交易品种行情变动造成的当前持仓的盈亏情况')">
                <div class="text">浮动盈亏</div>
            </div>
        </div>
    </div>
</template>
<script lang="ts">
export default {
    name: "member-nav-coin",
};
</script>
<script setup lang="ts">
import {
    getCurrentInstance,
    ref,
    onMounted,
    watch,
    onBeforeUnmount,
    computed,
} from "vue";
import { storeToRefs } from "pinia";
import { useMemberStore, useWalletStore } from "@/store";




const onShowTips = (title: string, content: string) => {

}

const totalProfit = computed(() => {
    const profit = useWalletStore().getTotalProfit() || 0;
    return `${Number(profit) >= 0 ? '+' : ''}${parseFloat(String(Number(profit))).toFixed(2)}`;
});

const totalProfitClass = computed(() => {
    if (Number(useWalletStore().getTotalProfit()) > 0) return useMemberStore().getUpColor();
    if (Number(useWalletStore().getTotalProfit()) < 0) return useMemberStore().getDownColor();
    return '';
});


const netValue = computed(() => {
    return useWalletStore().getNetValue();
});


const availableBalance = computed(() => {
    return useWalletStore().getAvailableBalance();
});
</script>


<style scoped>
.member-coin {
    margin-top: 20px;
    margin-bottom: 20px;
    display: flex;
}

.member-coin .member-coin-item {
    width: 33.34%;
    text-align: center;
    color: var(--color-text-1);
    position: relative;
    z-index: 999;
}

.member-coin .member-coin-item .number {
    display: flex;
    justify-content: center;
    align-items: center;
}

.member-coin .member-coin-item .unit {
    margin-right: 2px;
}

.member-coin .member-coin-item .money {
    font-size: var(--base-size-default);
    font-weight: bold;
}


.member-coin .member-coin-item .label {
    margin-top: 5px;
    display: flex;
    font-size: var(--base-size-small);
    justify-content: center;
    align-items: center;
    color: rgb(var(--gray-6));
}
</style>