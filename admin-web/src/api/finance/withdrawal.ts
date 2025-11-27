import { request,requestProgress } from '@/utils/request/default'

export const getFinanceAgentWithdrawalListApi = (params: any) => {
    return request({
        url: `finance.agentWithdrawal/list`,
        method: 'GET',
        params
    })
}
export const handleFinanceAgentWithdrawalApi = (data: any) => {
    return request({
        url: `finance.agentWithdrawal/handle`,
        method: 'PUT',
        data
    })
}
export const getFinanceMemberWithdrawalListApi = (params: any) => {
    return request({
        url: `finance.memberWithdrawal/list`,
        method: 'GET',
        params
    })
}
export const handleFinanceMemberWithdrawalApi = (data: any) => {
    return request({
        url: `finance.memberWithdrawal/handle`,
        method: 'PUT',
        data
    })
}