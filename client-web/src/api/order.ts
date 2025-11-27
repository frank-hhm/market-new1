import { request } from '@/utils/request/default'

export const orderSellApi = (data: any) => {
    return request({
        url: 'order/sell',
        method: 'POST',
        data
    })
}
export const getTodayComplateOrderSelectApi = (params: any) => {
    return request({
        url: 'order/getTodayComplateOrderSelect',
        method: 'GET',
        params
    })
}
export const cancelOrderRobotApi = (data: any) => {
    return request({
        url: 'order.robot/cancel',
        method: 'POST',
        data
    })
}

export const historyRecordApi = (data: any) => {
    return request({
        url: 'order/chengList',
        method: 'POST',
        data
    })
}
export const getOrderDetailApi = (params: any) => {
    return request({
        url: 'order/getOrderDetail',
        method: 'GET',
        params
    })
}

export const getOrderRobotDetailApi = (params: any) => {
    return request({
        url: 'order.robot/getOrderDetail',
        method: 'GET',
        params
    })
}


export const createOrderApi = (data: any) => {
    return request({
        url: 'order/create',
        method: 'POST',
        data
    })
}
export const penddingOrderApi = (data: any) => {
    return request({
        url: 'order.robot/createEntrust',
        method: 'POST',
        data
    })
}

export const peddingModifyApi = (data: any) => {
    return request({
        url: 'order.robot/upYsWei',
        method: 'POST',
        data
    })
}

export const holdModifyApi = (data: any) => {
    return request({
        url: 'order/upYs',
        method: 'POST',
        data
    })
}




