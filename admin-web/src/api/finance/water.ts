import { request,requestProgress } from '@/utils/request/default'

export const getFinanceWaterListApi = (params: any) => {
    return request({
        url: `finance.water/list`,
        method: 'GET',
        params
    })
}
export const getFinanceGgentWaterListApi = (params: any) => {
    return request({
        url: `finance.water/agentList`,
        method: 'GET',
        params
    })
}

export const handleFinanceWaterApi = (data: any) => {
    return request({
        url: `finance.water/handle`,
        method: 'PUT',
        data
    })
}