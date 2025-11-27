
import store, { useChargeStore, useChargeStoreHook, useMarketStore, useMemberStore } from "@/store"
import { Result, ResultError } from "@/types"
import { defineStore } from "pinia"
import { ref } from "vue"
import { getCacheMemberDetaulColor, removeToken, setCacheMemberDetaulColor } from "@/utils";
import { useAppStore } from "./app"

// 内部封装：计算单个订单的保证金
function _calcOrderMargin(order:any) {
    return parseFloat(order.fee) || 0
    // const price = parseFloat(order.buyprice);
    // const onumber = parseFloat(order.onumber); // 手数
    // const money = parseFloat(order.product_money); // 定价
    // const number = parseFloat(order.product_number); // 倍数
    // const leverage = 1; // 杠杆（可选）

    // // 保证金 = (买入价 * 手数 * 定价 * 倍数) / 杠杆
    // return ((price * onumber * money * number) / leverage);
}
export const useWalletStore = defineStore("wallet", () => {


    const getYingKui = (order: any) => {
        const marketPrice = useMarketStore().getMarketPrice(order.pid);
        if (!marketPrice || isNaN(marketPrice)) return {
            amount: '0.00',
            sign: '',
            className: ''
        };
        const buyPrice = parseFloat(order.buyprice);
        const currentPrice = parseFloat(marketPrice);
        const onumber = parseFloat(order.onumber); // 手数
        const money = parseFloat(order.product_money); // 定价
        const number = parseFloat(order.product_number); // 倍数
        const ostyle = parseInt(order.ostyle.value); // 1=买跌 2=买涨

        // console.log(currentPrice - buyPrice, "buyPrice:" + buyPrice, "currentPrice:" + currentPrice,
        // 	"onumber:" + onumber, "money:" + money, "number:" + number, "ostyle:" + ostyle)
        let profit = (currentPrice - buyPrice) / number * money * onumber;

        if (ostyle === 1) {
            profit = (buyPrice - currentPrice) / number * money * onumber;
        }
        let className = '';
        let sign = '';

        if (profit > 0) {
            className = useMemberStore().getUpColor();
            sign = '+';
        } else if (profit < 0) {
            className = useMemberStore().getDownColor();
            sign = '';
        } else {
            className = '';
            sign = '';
        }

        return {
            amount: profit.toFixed(2), // 只保留绝对值
            sign,
            className
        };
    }
    const getTotalProfit = () => {
        const totalProfit = useChargeStore().orderHold.reduce((sum, order) => {
            const profit = parseFloat(getYingKui(order).amount) || 0;
            return (parseFloat(sum) + profit).toFixed(2);
        }, 0);
        return parseFloat(totalProfit).toFixed(2);
    }

    //净值
    const getNetValue = () => {
        const balance = parseFloat(String(useMemberStore().getBalance())) || 0;
        const totalProfit = parseFloat(getTotalProfit()) || 0;
        return (balance + totalProfit).toFixed(2);
    }
    //保证金比例
    const getMarginRatio = () => {
        //保证比例 = 净值 / 占用的保证金 * 100
        const marginUsed = parseFloat(getTotalMarginUsed()) || 0;
        const netValue = parseFloat(getNetValue()) || 0;

        if (netValue === 0 || marginUsed === 0) return '0.00';
        return ((netValue / marginUsed * 100)).toFixed(2);
    }
    //占用的保证金
    const getTotalMarginUsed = () => {
        return useChargeStore().orderHold.reduce((sum, order) => {
            const margin = _calcOrderMargin(order); // 封装为内部函数
            return sum + margin;
        }, 0).toFixed(2);
    }

    //可用
    const getAvailableBalance = () => {
        const balance = parseFloat(String(useMemberStore().getBalance())) || 0;
        const marginUsed = parseFloat(getTotalMarginUsed()) || 0;
        const totalProfit = parseFloat(getTotalProfit()) || 0;
        return (balance - marginUsed + totalProfit).toFixed(2);
    }
    return {
        getTotalProfit,
        getYingKui,
        getMarginRatio,
        getTotalMarginUsed,
        getNetValue,
        getAvailableBalance
    }
})
/** 在 setup 外使用 */
export function useWalletStoreHook() {
    return useWalletStore(store)
}


export default {
    useWalletStore,
    useWalletStoreHook
}