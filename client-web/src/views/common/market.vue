<template>
    <div class="market-main">
        <div class="wrap-row">
            <div class="main-title">热门实时行情</div>
            <div class="main-market">
                <template v-for="(item, index) in productList" :key="index">
                    <div v-if="index < 4" class="main-market-item">
                        <div class="pro-title">{{ item.name }}</div>
                        <div class="pro-code">{{ item.code }}</div>
                        <div class="pro-price">
                            <div class="pro-price-number" :class="[
                                `${(useMarketStore().getPriceChange(item.id)?.changeValue > 0) ? 'market-red' : 'market-green'
                                }`,
                            ]">
                                {{ marketPrices[item.id] || item?.price }}
                            </div>
                            <div class="pro-price-icon" :class="[
                                `${(useMarketStore().getPriceChange(item.id)?.changeValue > 0) ? 'market-red' : 'market-green'
                                }`,
                            ]">
                                <icon-caret-up size="16px"
                                    v-if="(useMarketStore().getPriceChange(item.id)?.changeValue > 0)" /><icon-caret-down
                                    size="16px" v-else />
                            </div>
                        </div>
                        <div class="pro-change">
                            <div class="pro-change-number1" :class="[
                                `${(useMarketStore().getPriceChange(item.id)?.changeValue >= 0) ? 'market-red' : 'market-green'}`,
                            ]">
                                {{ (useMarketStore().getPriceChange(item.id)?.changeValue) }}
                            </div>
                            <div class="pro-change-number2" :class="[
                                `${(Number(useMarketStore().getPriceChange(item.id)?.changeNum) >= 0) ? 'market-red' : 'market-green'}`,
                            ]">
                                {{ (useMarketStore().getPriceChange(item.id)?.changeNum) }}%
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</template>


<script lang="ts">
export default {
    name: "commonMarket",
};
</script>

<script lang="ts" setup>
import { getProductMarketApi } from "@/api/common";
import { useSetting } from "@/hooks/useSetting";
import router from "@/router";
import { useMarketStore } from "@/store";
import { PageLimitType, Result, ResultError } from "@/types";
import { storeToRefs } from "pinia";
import { computed, getCurrentInstance, onMounted, ref } from "vue";
const { marketPrices } = storeToRefs(useMarketStore());

const {
    proxy,
    proxy: { $utils },
} = getCurrentInstance() as any;

const initLoading = ref<boolean>(true);

const productList = ref<any>([]);

const productOldList = ref<any>([]);

const toProduct = () => {
    getProductMarketApi({}).then((res: Result) => {
        productList.value.forEach((item: any, index: number) => {
            res.data.forEach((item2: any, index2: number) => {
                if (item.id == item2.id) {
                    item2.oldPrice = item.Price;
                }
            });
        });
        productList.value = res.data;
        initLoading.value = false;
    });
};

const forGetRequest = () => {
    toProduct();
};

onMounted(() => {
    forGetRequest();
});
</script>


<style scoped>
.market-main {
    position: relative;
    width: 100%;
    z-index: 1;
    background: #f5f7fa;
    padding: 100px 0;
}

.market-main .main-bg {
    position: absolute;
    display: block;
    width: 100%;
    height: 100%;
    left: 0;
    top: 0;
    z-index: -1;
}

.market-main .main-title {
    font-size: var(--base-title-size);
    font-weight: bold;
    text-align: center;
}

.market-main .main-market {
    margin-top: 100px;
    display: flex;
    justify-content: flex-start;
}

.market-main .main-market .main-market-item {
    width: calc(25% - 20px - 80px);
    background: #fff;
    border-radius: 4px;
    color: #151515;
    flex-direction: column;
    padding: 60px 40px;
    position: relative;
    margin: 0 10px;
}

.market-main .main-market .main-market-item .pro-title {
    font-size: 24px;
    text-align: center;
    margin-bottom: 10px;
}

.market-main .main-market .main-market-item .pro-code {
    font-size: 16px;
    text-align: center;
}

.market-main .main-market .main-market-item .pro-price {
    font-size: 36px;
    color: #ff5f57;
    text-align: center;
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

.market-main .main-market .main-market-item .pro-change {
    margin-top: 30px;
    display: flex;
    color: #ff5f57;
    justify-content: space-around;
    font-size: 16px;
}

@media screen and (max-width:999px) {
    .market-main .main-market {
        /* display: block; */
        flex-wrap: wrap;
    }

    .market-main .main-market .main-market-item {
        width: calc(50% - 20px - 80px);
        margin-bottom: 20px;
    }

}

@media screen and (max-width:699px) {
    .market-main {
        padding: 30px 0;
    }

    .market-main .main-market {
        margin-top: 30px;
        display: block;
    }

    .market-main .main-market .main-market-item {
        padding: 40px 20px;
        width: calc(100% - 60px);
        margin-bottom: 20px;
    }
}
</style>
