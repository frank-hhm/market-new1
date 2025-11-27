import { request,requestProgress } from '@/utils/request/default'


export const getOrderPinListApi = (params: any) => {
    return request({
        url: `order.order/pinList`,
        method: 'GET',
        params
    })
}
export const getOrderGuaListApi = (params: any) => {
    return request({
        url: `order.orderRobot/guaList`,
        method: 'GET',
        params
    })
}

export const getOrderChiListApi = (params: any) => {
    return request({
        url: `order.order/chiList`,
        method: 'GET',
        params
    })
}


export const getOrderMoniPinListApi = (params: any) => {
    return request({
        url: `order.order/pinMoniList`,
        method: 'GET',
        params
    })
}

export const getOrderMoniChiListApi = (params: any) => {
    return request({
        url: `order.order/chiMoniList`,
        method: 'GET',
        params
    })
}