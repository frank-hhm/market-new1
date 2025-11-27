import { request,requestProgress } from '@/utils/request/default'

export const getAgentLevelDataApi = () => {
    return request({
        url: 'agent.agent/getLevelData',
        method: 'GET'
    })
}

export const getAgentListApi = (params: any) => {
    return request({
        url: `agent.agent/list`,
        method: 'GET',
        params
    })
}
export const getAgentCascaderApi = () => {
    return request({
        url: 'agent.agent/getCascader',
        method: 'GET'
    })
}
export const getAgentSelectApi = (level:number) => {
    return request({
        url: 'agent.agent/getAgentSelect',
        method: 'GET',
        params: {
            level
        }
    })
}
export const getAgentSelectByPidApi = (pid:number) => {
    return request({
        url: 'agent.agent/getAgentSelectByPid',
        method: 'GET',
        params: {
            pid
        }
    })
}
export const createAgentApi = (data: any) => {
    return request({
        url: 'agent.agent/create',
        method: 'POST',
        data
    })
}
export const updateAgentApi = (data: any) => {
    return request({
        url: `agent.agent/update`,
        method: 'PUT',
        data
    })
}


export const updateStatusAgentApi = (data: any) => {
    return request({
        url: `agent.agent/status`,
        method: 'PUT',
        data
    })
}
export const updatePassAgentApi = (data: any) => {
    return request({
        url: `agent.agent/updatePass`,
        method: 'PUT',
        data
    })
}

export const deleteAgentApi = (params: { id: number | string }) => {
    return request({
        url: `agent.agent/delete`,
        method: 'DELETE',
        params
    })
}
export const getDetailAgentApi = (params: any) => {
    return request({
        url: 'agent.agent/detail',
        method: 'GET',
        params
    })
}
export const getDetailAgentFeeApi = (params: any) => {
    return request({
        url: 'agent.agent/getFee',
        method: 'GET',
        params
    })
}
export const setDetailAgentFeeApi = (data: any) => {
    return request({
        url: 'agent.agent/setFee',
        method: 'POST',
        data
    })
}
export const setAgentWithdrawalApi = (data:any) => {
    return request({
        url: `agentWithdrawal/create`,
        method: 'POST',
        data
    })
}