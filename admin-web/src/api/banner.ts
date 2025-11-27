import { request,requestProgress } from '@/utils/request/default'

export const getBannerListApi = (params:any) => {
    return request({
        url: 'banner/list',
        method: 'GET',
        params
    })
}
export const getDetailBannerApi = (params: any) => {
    return request({
        url: 'banner/detail',
        method: 'GET',
        params
    })
}
export const createBannerApi = (data:any) => {
    return request({
        url: `banner/create`,
        method: 'POST',
        data
    })
}
export const updateBannerApi  = (data:any) => {
    return request({
        url: `banner/update`,
        method: 'PUT',
        data
    })
}

export const updateStatusBannerApi = (data: any) => {
    return request({
        url: `banner/status`,
        method: 'PUT',
        data
    })
}

export const deleteBannerApi = (params: { id: number | string }) => {
    return request({
        url: `banner/delete`,
        method: 'DELETE',
        params
    })
}