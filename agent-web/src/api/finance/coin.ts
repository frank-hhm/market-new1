import { request,requestProgress } from '@/utils/request/default'

export const getFinanceCoinListApi = (params: any) => {
    return request({
        url: `finance.coin/list`,
        method: 'GET',
        params
    })
}