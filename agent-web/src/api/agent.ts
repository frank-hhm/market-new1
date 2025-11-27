
import { request, requestProgress } from '@/utils/request/default'

export const getAgentListApi = (params: any) => {
    return request({
        url: `agent/list`,
        method: 'GET',
        params
    })
}
export const getAgentLevelDataApi = () => {
    return request({
        url: 'agent/getLevelData',
        method: 'GET'
    })
}
export const getDefaultDetailApi = () => {
    return request({
        url: 'agent/defaultDetail',
        method: 'GET'
    })
}
export const uploadAvatarApi = (data: any) => {
    return requestProgress({
        url: `agent/updateAvatar`,
        method: 'POST',
        data
    })
}

export function getListLoginLogsByIdApi(id:number|string,params: any,source:string = 'admin') {
    return request({
        url: 'agent/list',
        method: 'GET',
        params:{
            ...params,
            id:id,
            source:source
        }
    })
}

export const getAgentSelectByPidApi = (pid:number) => {
    return request({
        url: 'agent/getAgentSelectByPid',
        method: 'GET',
        params: {
            pid
        }
    })
}
export const getAgentSelectApi = (level:number) => {
    return request({
        url: 'agent/getAgentSelect',
        method: 'GET',
        params: {
            level
        }
    })
}
export const createAgentApi = (data: any) => {
    return request({
        url: 'agent/create',
        method: 'POST',
        data
    })
}
export const updateAgentApi = (data: any) => {
    return request({
        url: `agent/update`,
        method: 'PUT',
        data
    })
}


export const updateStatusAgentApi = (data: any) => {
    return request({
        url: `agent/status`,
        method: 'PUT',
        data
    })
}
export const updatePassAgentApi = (data: any) => {
    return request({
        url: `agent/updatePass`,
        method: 'PUT',
        data
    })
}

export const deleteAgentApi = (params: { id: number | string }) => {
    return request({
        url: `agent/delete`,
        method: 'DELETE',
        params
    })
}
export const getDetailAgentApi = (params: any) => {
    return request({
        url: 'agent/detail',
        method: 'GET',
        params
    })
}
export const getDetailAgentFeeApi = (params: any) => {
    return request({
        url: 'agent/getFee',
        method: 'GET',
        params
    })
}
export const setDetailAgentFeeApi = (data: any) => {
    return request({
        url: 'agent/setFee',
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