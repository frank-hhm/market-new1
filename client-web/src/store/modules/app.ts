import { reactive, ref } from "vue"
import store from "@/store"
import { initConfigApi } from "@/api/common";
import { Result } from "@/types"
import { defineStore } from "pinia"
import { setCacheConfig, getCacheConfig, getCacheTemplateDark, setCacheTemplateDark } from "@/utils";
export const useAppStore = defineStore("app", () => {


    const redColor = ref<string>("#F36267")
    const greenColor = ref<string>("#02B373")

    const isMobile = ref<boolean>(false)

    const isDark = ref<boolean>(getCacheTemplateDark() || false)

    const config = ref<any>(getCacheConfig() || {})

    const allLoading = ref<boolean>(false)

    const setAllLoading = (value: any) => {
        allLoading.value = value
    }
    /** 获取系统信息 */
    const initConfig = async () => {
        const { data }: Result = await initConfigApi()
        setCacheConfig(data);
        config.value = data
    }

    const setDark = (val: boolean) => {
        isDark.value = val
        // if (isDark.value) {
        //     document.body.setAttribute('arco-theme', 'dark')
        // } else {
        //     document.body.removeAttribute('arco-theme');
        // }
        // setCacheTemplateDark(isDark.value)
    }

    const setMobile = (value: boolean) => {
        isMobile.value = value
    }
    const clientWidth = ref<number>(document.body.clientWidth)

    const setClientWidth = () => {
        clientWidth.value = document.body.clientWidth
    }

    const orderBodyHeight = ref<number>(0)

    const setOrderBodyHeight = (value:number) => { 
        orderBodyHeight.value = value
    }

    return { redColor, greenColor, isDark, setDark, config, initConfig, setMobile, isMobile,setAllLoading,allLoading,clientWidth,setClientWidth,orderBodyHeight,setOrderBodyHeight }
})

/** 在 setup 外使用 */
export function useAppStoreHook() {
    return useAppStore(store)
}

export default {
    useAppStore,
    useAppStoreHook
}