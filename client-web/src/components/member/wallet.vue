<template>
    <div class="member-wallet">
        <div class="member-wallet-top">
            <div class="reward">
                <div class="reward-value" :style="[`color: ${totalProfitClass
                    }`]">
                    {{ totalProfit || 0.00 }}
                </div>
                <div class="label">持仓盈亏</div>
            </div>
            <template v-if="!isMoni">
                <a-button v-if="member?.id && member?.bind_status?.value == 1" type="primary"
                    @click="onRecharge">入金</a-button>
                <a-button v-else-if="member?.id && member?.bind_status?.value == 0" type="primary" status="danger"
                    @click="onBindCard">去认证</a-button>
                <div v-else-if="member?.id && (member?.bind_status?.value == 2 || member?.bind_status?.value == 3)">
                    <div class="fz12 text-gray" v-if="member?.bind_status?.value == 2">认证状态</div>
                    <div class="mt5" :class="`text-` + member?.bind_status?.color" type="primary">{{
                        member?.bind_status?.name }}</div>
                    <div class="mt5">
                        <a-button v-if="member?.bind_status?.value == 3" type="primary" status="danger" size="mini"
                            @click="onBindCard">重新认证</a-button>
                    </div>
                </div>
            </template>
            <a-button v-else-if="!member?.id" type="primary" @click="onRecharge">登录</a-button>
        </div>
        <div class="member-wallet-detail">
            <div class="wallet-item">
                <div class="value">{{ member?.username || "--" }}</div>
                <div class="label">账号</div>
            </div>
            <div class="wallet-item">
                <div class="value">{{ marginRatio || 0.00 }}%</div>
                <div class="label">保证金比例</div>
            </div>
            <div class="wallet-item">
                <div class="value">{{ netValue || 0.00 }}</div>
                <div class="label">净值</div>
            </div>
            <div class="wallet-item">
                <div class="value">{{ availableBalance || 0.00 }}</div>
                <div class="label">可用</div>
            </div>
            <div class="wallet-item ">
                <div class="value">{{ marginUsed || 0.00 }}</div>
                <div class="label">占用</div>
            </div>
            <div class="wallet-item">
                <div class="value">{{ balance || 0.00 }}</div>
                <div class="label">余额</div>
            </div>
        </div>
        <memberRechargeComponent></memberRechargeComponent>
    </div>
</template>
<script lang="ts">
export default {
    name: "wallet",
};
</script>
<script lang="ts" setup>
import { computed, ref, watch } from "vue";
import { useMarketStore, useMemberStore, useWalletStore } from "@/store";
import { storeToRefs } from "pinia";
import memberRechargeComponent from "@/components/member/recharge-modal.vue";

const { member, isMoni } = storeToRefs(useMemberStore());

const onRecharge = () => {
    if (!useMemberStore().isLogin) {
        useMemberStore().setLoginModal(true);
        return false;
    }
    useMemberStore().setRechargeModal(true);
}

const onBindCard = () => {
    useMemberStore().setBindCardModal(true);
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

const marginRatio = computed(() => {
    return useWalletStore().getMarginRatio();
});

const netValue = computed(() => {
    return useWalletStore().getNetValue();
});

const availableBalance = computed(() => {
    return useWalletStore().getAvailableBalance();
});

const marginUsed = computed(() => {
    return useWalletStore().getTotalMarginUsed();
});

const balance = computed(() => {
    return useMemberStore().getBalance();
});

</script>
<style scoped>
.member-wallet {
    color: var(--color-text-1);
    margin-top: calc(var(--base-padding));
    padding: calc(var(--base-padding));
    background: var(--color-bg-2);
    border-radius: var(--base-radius-default);
    border: 1px solid var(--color-border-1);
    height: calc(30% - var(--base-padding) * 2);
    min-height:200px;
    overflow-y: scroll;
}


.member-wallet-top {
    padding: 10px 10px;
    background-color: var(--color-bg-2);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.member-wallet-top .reward {
    width: calc(100% - 80px);
}

.member-wallet-top .reward .reward-value {
    font-size: var(--base-size-large-x);
    font-weight: bold;
    line-height: 40px;
}

.member-wallet-top .reward .label {
    color: var(--color-text-3);
}

.member-wallet-detail {
    /* margin-top: 20px; */
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    align-items: center;
    height: calc(100% - 96px);
}

.member-wallet-detail .wallet-item {
    width: 33.333%;
    text-align: center;
}

.member-wallet-detail .wallet-item .value {
    font-weight: bold;
    line-height: 40px;
}

.member-wallet-detail .wallet-item .label {
    line-height: 16px;
    color: var(--color-text-3);
}


@media screen and (max-height: 1060px), screen and (max-width: 1199px) {
    .member-wallet {
        margin-top: calc(var(--base-min-padding));
        width: calc(100% - var(--base-min-padding) * 4);
        padding: var(--base-min-padding) calc(var(--base-min-padding) * 2);
        height: 200px;
    }

    .member-wallet-top {
        margin-top: 10px;
        padding: var(--base-min-padding);
        background-color: var(--color-bg-2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 40px;
    }

    .member-wallet-detail {
        margin-top: var(--base-min-padding);
    }
}
</style>