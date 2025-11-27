import { ref } from "vue"
import store from "@/store"
import { defineStore } from "pinia"
import { getToken, removeToken } from "@/utils"
import { getDefaultDetailApi } from "@/api/agent"
import { IAgent } from "@/types"

export const useAgentStore = defineStore("agent", () => {

    const token = ref<string>(getToken() || "")

    const agentDetail = ref<IAgent>({})

    const setInfo = (value: IAgent) => {
        agentDetail.value = value
    }

    const initInfo = async () => {
        const { data }: any = await getDefaultDetailApi()
        agentDetail.value = data
    }

    /** 重置 Token */
    const resetToken = () => {
        removeToken()
        token.value = ""
    }
    return { token, agentDetail,initInfo, setInfo, resetToken }
})

/** 在 setup 外使用 */
export function useAgentStoreHook() {
    return useAgentStore(store)
}


export default {
    useAgentStore,
    useAgentStoreHook
}