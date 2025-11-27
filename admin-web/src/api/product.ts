import { request,requestProgress } from '@/utils/request/default'


export const getProductListApi = (params: any) => {
    return request({
        url: `product/list`,
        method: 'GET',
        params
    })
}
export const getProductSelectApi = (params: any) => {
    return request({
        url: `product/select`,
        method: 'GET',
        params
    })
}

export const getProductSelectLabelValueDataApi = () => {
    return request({
        url: `product/getSelectLabelValueData`,
        method: 'GET'
    })
}

export const getDetailProductApi = (params: any) => {
    return request({
        url: 'product/detail',
        method: 'GET',
        params
    })
}
export const createProductApi = (data: any) => {
    return request({
        url: 'product/create',
        method: 'POST',
        data
    })
}
export const updateProductApi = (data: any) => {
    return request({
        url: `product/update`,
        method: 'PUT',
        data
    })
}

export const updateStatusProductApi = (data: any) => {
    return request({
        url: `product/status`,
        method: 'PUT',
        data
    })
}
export const updateHotProductApi = (data: any) => {
    return request({
        url: `product/hot`,
        method: 'PUT',
        data
    })
}

export const deleteProductApi = (params: { id: number | string }) => {
    return request({
        url: `product/delete`,
        method: 'DELETE',
        params
    })
}
