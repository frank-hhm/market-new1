import { request, requestProgress } from '@/utils/request/default'
export const getCaptchaApi = () => {
    return request({
        url: 'common.publics/captcha',
        method: 'GET'
    })
}
export const initConfigApi = () => {
    return request({
        url: 'common.publics/configData',
        method: 'GET'
    })
}

export const getEnumApi = () => {
    return request({
        url: 'common.publics/enum',
        method: 'GET'
    })

}
export const getMobileCodeCaptchaApi = (data: {
    phone: string
    captcha_code: string
    captcha_uniqid: string
}) => {
    return request({
        url: 'common.publics/sendSmsByCaptcha',
        method: 'POST',
        data
    })
}

export const getAgreementApi = (
    type: string
) => {
    return request({
        url: 'common.publics/agreement',
        method: 'GET',
        params: {
            type
        }
    })
}

export const getPaymentSelectApi = (
) => {
    return request({
        url: 'common.publics/getPaymentSelect',
        method: 'GET'
    })
}


export const getPaymentConfigApi = (id:number | string
) => {
    return request({
        url: 'common.publics/getPaymentConfig',
        method: 'GET',
        params:{
            id
        }
    })
}



export const getArticleListApi = (params:any) => {
    return request({
        url: 'index/getArticleList',
        method: 'GET',
        params
    }, {
        isAllow: false,
        apiPath: 'index/'
    })
}
export const getArticleDetailApi = (params:any) => {
    return request({
        url: 'index/getArticleDetail',
        method: 'GET',
        params
    }, {
        isAllow: false,
        apiPath: 'index/'
    })
}
export const getProductMarketApi = (params:any) => {
    return request({
        url: 'index/getProductMarket',
        method: 'GET',
        params
    }, {
        isAllow: false,
        apiPath: 'index/'
    })
}