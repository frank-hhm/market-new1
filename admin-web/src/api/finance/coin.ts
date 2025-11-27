import { request,requestProgress } from '@/utils/request/default'

export const getFinanceCoinListApi = (params: any) => {
    return request({
        url: `finance.coin/list`,
        method: 'GET',
        params
    })
}


export const getDetailMemberCoinApi = (params: any) => {
    return request({
        url: 'finance.coin/detail',
        method: 'GET',
        params
    })
}
// 余额充值
export const setMemberRechargeBalanceApi = (data: any) => {
    return request({
        url: 'finance.coin/rechargeBalance',
        method: 'PUT',
        data
    })
}
