import { request,requestProgress } from '@/utils/request/default'

export const getNoticeGatherApi = () => {
    return request({
        url: 'notice/gather',
        method: 'GET'
    })
}
export const getNoticeListApi = (params:any) => {
    return request({
        url: 'notice/list',
        method: 'GET',
        params
    })
}
export const getDetailNoticeApi = (params: any) => {
    return request({
        url: 'notice/detail',
        method: 'GET',
        params
    })
}
export const createNoticeApi = (data:any) => {
    return request({
        url: `notice/create`,
        method: 'POST',
        data
    })
}
export const updateNoticeApi  = (data:any) => {
    return request({
        url: `notice/update`,
        method: 'PUT',
        data
    })
}

export const updateStatusNoticeApi = (data: any) => {
    return request({
        url: `notice/status`,
        method: 'PUT',
        data
    })
}

export const deleteNoticeApi = (params: { id: number | string }) => {
    return request({
        url: `notice/delete`,
        method: 'DELETE',
        params
    })
}