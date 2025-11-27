import { request,requestProgress } from '@/utils/request/default'


export const getFollowTradeListApi = (params: any) => {
    return request({
        url: `follow.trade/list`,
        method: 'GET',
        params
    })
}
export const createFollowTradeApi = (data: any) => {
    return request({
        url: 'follow.trade/create',
        method: 'POST',
        data
    })
}
export const updateFollowTradeApi = (data: any) => {
    return request({
        url: `follow.trade/update`,
        method: 'PUT',
        data
    })
}

export const deleteFollowTradeApi = (params: { id: number | string }) => {
    return request({
        url: `follow.trade/delete`,
        method: 'DELETE',
        params
    })
}
export const getDetailFollowTradeApi = (params: any) => {
    return request({
        url: 'follow.trade/detail',
        method: 'GET',
        params
    })
}
