
import { getProductConfigApi } from "@/api/product"
import store from "@/store"
import { Result, ResultError } from "@/types"
import { getCacheSelectMarketId, setCacheSelectMarketId } from "@/utils"
import { defineStore } from "pinia"
import { ref } from "vue"
export const useMarketStore = defineStore("market", () => {

    const markets = ref<any[]>([])

    const sectors = ref<any[]>([])

    //当前选择的板块
    const selectSectorId = ref<number | string>(0)

    //当前选择的行情
    const selectMarketId = ref<number | string>(getCacheSelectMarketId())

    //默认自选
    const defaultCollects = ref<number[] | string[]>([])

    const marketPrices = ref<any>({})
    const setMarkets = (data: any[]) => {
        markets.value = data
    }

    const setSector = (data: any[]) => {
        sectors.value = data
    }

    const setSectorId = (id: number | string) => {
        selectSectorId.value = id
    }

    const setMarketId = (id: number | string) => {
        selectMarketId.value = id
        setCacheSelectMarketId(id)
    }

    //更新价格
    const setMarketItem = (_market: any) => {
        marketPrices.value[_market?.id] = _market?.p;
        const index = markets.value.findIndex(item => item.id === _market?.id);
        if (index !== -1) {
            const item = markets.value[index];
            // 判断涨跌状态
            const up_status = parseFloat(_market.p) > parseFloat(item.price);
            markets.value[index]!.up_status = up_status
            markets.value[index]!.old_price = item.price
            markets.value[index]!.price = _market.p
        }
    }

    const getMarketPrice = (id: number | string) => {
        return Number(marketPrices.value[id]) || getMarketDefaultPrice(id);
    }

    const getMarketDetail = () => {
        const _marketItem = markets.value.find(item => item.id == selectMarketId.value);
        if (!_marketItem) {
            return markets.value[0];
        }
        return _marketItem
    }

    const getMarketById = (id: number | string) => {
        return markets.value.find(item => item.id == id);
    }

    const getMarketDefaultPrice = (id: number | string) => {
        const _marketItem = markets.value.find(item => item.id == id);
        if (!_marketItem) {
            return 0;
        }
        return _marketItem.price || 0
    }

    const initMarkets = () => {
        getProductConfigApi().then((res: Result) => {
            markets.value = res.data?.list || []
            sectors.value = res.data?.sector || []
            defaultCollects.value = res.data?.collects || []
            if (!selectMarketId.value) {
                selectMarketId.value = markets.value[0]?.id
            }
        }).catch((err: ResultError) => {

        })
    }

    const getPriceChange = (id: number | string) => {
        const market = markets.value.find(item => item.id === id);
        if (!market || !market.price || !market.open_price) return {
            changeValue: 0,
            changeNum: 0
        };

        const price = parseFloat(market.price);
        const openPrice = parseFloat(market.open_price);

        if (isNaN(price) || isNaN(openPrice)) return {
            changeValue: 0,
            changeNum: 0
        };

        let liuChu = openPrice || price;
        if (liuChu === 0) return {
            changeValue: 0,
            changeNum: 0
        };

        let _price = price - openPrice;
        let changeZhi = _price / liuChu;

        if (changeZhi < 0) {
            let changeJuedui = Math.ceil(Math.abs(changeZhi) * 10000) / 10000;
            changeZhi = parseFloat(String(Number(changeJuedui) * -1));
        }

        return {
            changeValue: parseFloat((changeZhi * 100).toFixed(2)),
            changeNum: _price.toFixed(2)
        };
    }
    return { markets, sectors, selectMarketId, setMarketId, setSectorId, getMarketDetail, selectSectorId, marketPrices, defaultCollects, setMarkets, setSector, initMarkets, setMarketItem, getMarketPrice, getMarketDefaultPrice, getPriceChange, getMarketById }
})
/** 在 setup 外使用 */
export function useMarketStoreHook() {
    return useMarketStore(store)
}


export default {
    useMarketStore,
    useMarketStoreHook
}