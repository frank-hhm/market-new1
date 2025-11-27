
import store from "@/store"
import { Result, ResultError } from "@/types"
import { defineStore } from "pinia"
import { ref } from "vue"
import { useAppStore } from "./app"
import { getCacheAssistantIndicatorsIndex, getCacheMainIndicatorsIndex, setCacheMainIndicatorsIndex,setCacheAssistantIndicatorsIndex } from "@/utils/cache/storage"
export const useEchartsStore = defineStore("echarts", () => {

    const mainIndicatorsIndex = ref<number>(getCacheMainIndicatorsIndex() || 0)

    const assistantIndicatorsIndex = ref<number>(getCacheAssistantIndicatorsIndex() ? getCacheAssistantIndicatorsIndex() :  2)

    const setMarnIndicatorsIndex = (index: number) => {
        mainIndicatorsIndex.value = index
        setCacheMainIndicatorsIndex(index)
    }

    const setAssistantIndicatorsIndex = (index: number) => {
        assistantIndicatorsIndex.value = index
        setCacheAssistantIndicatorsIndex(index)
    }
    return { mainIndicatorsIndex, assistantIndicatorsIndex, setMarnIndicatorsIndex, setAssistantIndicatorsIndex }
})
/** 在 setup 外使用 */
export function useEchartsStoreHook() {
    return useEchartsStore(store)
}


export default {
    useEchartsStore,
    useEchartsStoreHook
}