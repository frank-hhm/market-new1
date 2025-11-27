import store from "@/store"
import { useMarketStore, useMarketStoreHook } from "@/store/modules/market";
import Vue from 'vue'
import { initMember } from "./tools";
export const websocketCallbackFunction = (res: any) => {
    let _res = res.detail;
    switch (_res.type) {
        case "market":
            //实时行情
            if (_res.data) {
                useMarketStoreHook().setMarketItem(_res.data)
            }
            break;
        case "productCache":
            useMarketStore().initMarkets();
            break;
        case "memberDetail":
            initMember();
            break;
        case "createOrder":
            initMember();
            break;
        case "updateWeiTuo":
            initMember();
            break;
        case "upYsOrder":
            initMember();
            break;

        case "pingCang":
            initMember();
            break;
        case "closeOrder":
            initMember();
            break;



    }
}