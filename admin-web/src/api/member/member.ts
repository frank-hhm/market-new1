import { request,requestProgress } from '@/utils/request/default'

export const getMemberListApi = (params: any) => {
    return request({
        url: `member.member/list`,
        method: 'GET',
        params
    })
}
export const getDetailMemberApi = (params: any) => {
    return request({
        url: 'member.member/detail',
        method: 'GET',
        params
    })
}
export const createMemberApi = (data: any) => {
    return request({
        url: 'member.member/create',
        method: 'POST',
        data
    })
}
export const updateMemberApi = (data: any) => {
    return request({
        url: `member.member/update`,
        method: 'PUT',
        data
    })
}
export const updateBindMemberApi = (data: any) => {
    return request({
        url: `member.member/updateBind`,
        method: 'PUT',
        data
    })
}
export const updateStatusMemberApi = (data: any) => {
    return request({
        url: `member.member/status`,
        method: 'PUT',
        data
    })
}
export const deleteMemberApi = (params: { id: number | string }) => {
    return request({
        url: `member.member/delete`,
        method: 'DELETE',
        params
    })
}

export const getDetailMemberSlippageApi = (params: any) => {
    return request({
        url: 'member.member/getSlippage',
        method: 'GET',
        params
    })
}
export const setDetailMemberSlippageApi = (data: any) => {
    return request({
        url: 'member.member/setSlippage',
        method: 'POST',
        data
    })
}
export const updateMemberAgentApi = (data: any) => {
    return request({
        url: 'member.member/updateAgent',
        method: 'POST',
        data
    })
}


export const getMemberTestSelectApi = (params: any) => {
    return request({
        url: `member.member/getTestSelect`,
        method: 'GET',
        params
    })
}