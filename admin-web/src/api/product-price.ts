import { request,requestProgress } from '@/utils/request/default'


export const getProductPriceListApi = (params: any) => {
    return request({
        url: `productPrice/list`,
        method: 'GET',
        params
    })
}
export const createProductPriceApi = (data: any) => {
    return request({
        url: 'productPrice/create',
        method: 'POST',
        data
    })
}
export const deleteProductPriceApi = (params: { id: number | string }) => {
    return request({
        url: `productPrice/delete`,
        method: 'DELETE',
        params
    })
}

export const getProductMklinePriceApi = (params: any) => {
    return request({
        url: 'productPrice/getMklinePrice',
        method: 'GET',
        params
    })
}