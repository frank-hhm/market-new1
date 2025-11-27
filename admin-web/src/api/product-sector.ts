import { request } from '@/utils/request/default'

export const getProductSectorSelectApi = () => {
    return request({
        url: `productSector/select`,
        method: 'GET'
    })
}

export const createProductSectorApi = (data: any) => {
    return request({
        url: 'productSector/create',
        method: 'POST',
        data
    })
}
export const updateProductSectorApi = (data: any) => {
    return request({
        url: 'productSector/update',
        method: 'POST',
        data
    })
}
export const deleteProductSectorApi = (params: { id: number | string }) => {
    return request({
        url: `productSector/delete`,
        method: 'DELETE',
        params
    })
}


export const getDetailProductSectorApi = (params: any) => {
    return request({
        url: 'productSector/detail',
        method: 'GET',
        params
    })
}
