import { request,requestProgress } from '@/utils/request/default'

export const getArticleGatherApi = () => {
    return request({
        url: 'article/gather',
        method: 'GET'
    })
}
export const getArticleListApi = (params:any) => {
    return request({
        url: 'article/list',
        method: 'GET',
        params
    })
}
export const getDetailArticleApi = (params: any) => {
    return request({
        url: 'article/detail',
        method: 'GET',
        params
    })
}
export const createArticleApi = (data:any) => {
    return request({
        url: `article/create`,
        method: 'POST',
        data
    })
}
export const updateArticleApi  = (data:any) => {
    return request({
        url: `article/update`,
        method: 'PUT',
        data
    })
}

export const updateStatusArticleApi = (data: any) => {
    return request({
        url: `article/status`,
        method: 'PUT',
        data
    })
}

export const deleteArticleApi = (params: { id: number | string }) => {
    return request({
        url: `article/delete`,
        method: 'DELETE',
        params
    })
}