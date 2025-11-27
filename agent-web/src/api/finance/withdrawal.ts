import { request,requestProgress } from '@/utils/request/default'

export const getFinanceMemberWithdrawalListApi = (params: any) => {
    return request({
        url: `finance.memberWithdrawal/list`,
        method: 'GET',
        params
    })
}