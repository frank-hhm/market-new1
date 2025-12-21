import { request,requestProgress } from '@/utils/request/default'


export const getFollowOrderListApi = (params: any) => {
    return request({
        url: `follow.order/list`,
        method: 'GET',
        params
    })
}