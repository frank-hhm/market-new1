
import store from "@/store"
import { Result, ResultError } from "@/types"
import { defineStore } from "pinia"
import { ref } from "vue"
import { useAppStore } from "./app"
export const useChargeStore = defineStore("charge", () => {

    const orderHold = ref<any[]>([])
    const orderRobot = ref<any[]>([])
    const setOrderHold = (data: any[]) => {
        orderHold.value = data
    }
    const setOrderRobot = (data: any[]) => {
        orderRobot.value = data
    }
    return { orderHold, orderRobot, setOrderHold, setOrderRobot }
})
/** 在 setup 外使用 */
export function useChargeStoreHook() {
    return useChargeStore(store)
}


export default {
    useChargeStore,
    useChargeStoreHook
}