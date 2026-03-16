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


export const getDetailMemberRiskApi = (params: any) => {
    return request({
        url: 'member.member/getRisk',
        method: 'GET',
        params
    })
}
export const setDetailMemberRiskApi = (data: any) => {
    return request({
        url: 'member.member/setRisk',
        method: 'POST',
        data
    })
}
