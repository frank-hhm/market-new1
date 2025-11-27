import { request } from '@/utils/request/default'


export const getProductConfigApi = () => {
    return request({
        url: 'product/getProductConfig',
        method: 'GET'
    })
}

export const getProductParamsDetailApi = (id: number | string) => {
    return request({
        url: 'product/getProductParamsDetail',
        method: 'GET',
        params: {
            id
        }
    })
}
export const delOptionalProductApi = (id: number | string) => {
    return request({
        url: 'product/delOptional',
        method: 'PUT',
        data: {
            id
        }
    })
}
export const addOptionalProductApi = (id: number | string) => {
    return request({
        url: 'product/addOptional',
        method: 'POST',
        data: {
            id
        }
    })
}

export const getKDataApi = (params: any) => {
    return request({
        url: 'marketKLine/kLine',
        method: 'GET',
        params
    })
}
