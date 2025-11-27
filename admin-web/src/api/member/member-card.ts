import { request,requestProgress } from '@/utils/request/default'

export const getMemberCardListApi = (params: any) => {
    return request({
        url: `member.memberCard/list`,
        method: 'GET',
        params
    })
}

export const getMemberCardDetailApi = (params: any) => {
    return request({
        url: `member.memberCard/detail`,
        method: 'GET',
        params
    })
}

export const updateMemberCardApi = (data: any) => {
    return request({
        url: `member.memberCard/update`,
        method: 'PUT',
        data
    })
}

export const handleMemberCardApi = (data: any) => {
    return request({
        url: `member.memberCard/handle`,
        method: 'PUT',
        data
    })
}