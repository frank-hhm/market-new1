import { request } from '@/utils/request/default'


export const getMemberDetailApi = () => {
    return request({
        url: 'member.member/detail',
        method: 'GET'
    })
}


export const bindMemberRealApi = (data:any) => {
    return request({
        url: 'member.member/bindReal',
        method: 'POST',
        data
    })
}

export const updateMemberPasswordApi = (data:any) => {
    return request({
        url: 'member.member/updatePass',
        method: 'PUT',
        data
    })
}
export const getWaterApi = (params:any) => {
    return request({
        url: 'finance.FinanceLog/list',
        method: 'GET',
        params
    })
}

export const logoutApi = () => {
    return request({
        url: 'member.member/logout',
        method: 'GET'
    })
}



export const rechargeTransferApi = (data:any) => {
    return request({
        url: 'member.member/recharge',
        method: 'POST',
        data
    })
}

export const usdtpayTransferApi = (data:any) => {
    return request({
        url: 'member.member/usdtpayTransferApi',
        method: 'POST',
        data
    })
}
export const loginqieApi = () => {
    return request({
        url: 'member.member/loginqie',
        method: 'GET'
    })
}





