import { request,requestProgress } from '@/utils/request/default'

export const getPaymentListApi = (params:any) => {
    return request({
        url: 'payment/list',
        method: 'GET',
        params
    })
}
export const getPaymentSelectApi = (params:any) => {
    return request({
        url: 'payment/select',
        method: 'GET',
        params
    })
}
export const getDetailPaymentApi = (params: any) => {
    return request({
        url: 'payment/detail',
        method: 'GET',
        params
    })
}
export const createPaymentApi = (data:any) => {
    return request({
        url: `payment/create`,
        method: 'POST',
        data
    })
}
export const updatePaymentApi  = (data:any) => {
    return request({
        url: `payment/update`,
        method: 'PUT',
        data
    })
}

export const updateStatusPaymentApi = (data: any) => {
    return request({
        url: `payment/status`,
        method: 'PUT',
        data
    })
}

export const deletePaymentApi = (params: { id: number | string }) => {
    return request({
        url: `payment/delete`,
        method: 'DELETE',
        params
    })
}
export const updatePaymentAgentApi = (data: any) => {
    return request({
        url: 'payment/updateAgent',
        method: 'POST',
        data
    })
}