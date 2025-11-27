import { request,requestProgress } from '@/utils/request/default'

export const getFinanceRechargeListApi = (params: any) => {
    return request({
        url: `finance.recharge/list`,
        method: 'GET',
        params
    })
}