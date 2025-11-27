import { ref } from "vue"
import store from "@/store"
import { defineStore } from "pinia"
import { getAgentSelectByPidApi } from "@/api/agent"
import { Result } from "@/types"
export const useDataStore = defineStore("data", () => {

    const agents = ref<any[]>([])

    const setAgents = (data: any[]) => {
        agents.value = data
    }
    const initAgents = async () => {
        const { data }: Result = await getAgentSelectByPidApi(0)
        setAgents(data);
    }

    return { agents, setAgents,initAgents }
})

/** 在 setup 外使用 */
export function useDataStoreHook() {
    return useDataStore(store)
}


export default {
    useDataStore,
    useDataStoreHook
}