import { request,requestProgress } from '@/utils/request/default'

export const getPaymentSelectApi = (params:any) => {
    return request({
        url: 'payment/select',
        method: 'GET',
        params
    })
}