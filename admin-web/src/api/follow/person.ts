import { request,requestProgress } from '@/utils/request/default'


export const getFollowPersonListApi = (params: any) => {
    return request({
        url: `follow.person/list`,
        method: 'GET',
        params
    })
}

export const getFollowPersonSelectApi = (level:number) => {
    return request({
        url: 'follow.person/getFollowSelect',
        method: 'GET',
        params: {
            level
        }
    })
}
export const getFollowPersonSelectByPidApi = (pid:number) => {
    return request({
        url: 'follow.person/getFollowSelectByPid',
        method: 'GET',
        params: {
            pid
        }
    })
}
export const createFollowPersonApi = (data: any) => {
    return request({
        url: 'follow.person/create',
        method: 'POST',
        data
    })
}
export const updateFollowPersonApi = (data: any) => {
    return request({
        url: `follow.person/update`,
        method: 'PUT',
        data
    })
}


export const updateStatusFollowPersonApi = (data: any) => {
    return request({
        url: `follow.person/status`,
        method: 'PUT',
        data
    })
}
export const deleteFollowPersonApi = (params: { id: number | string }) => {
    return request({
        url: `follow.person/delete`,
        method: 'DELETE',
        params
    })
}
export const getDetailFollowPersonApi = (params: any) => {
    return request({
        url: 'follow.person/detail',
        method: 'GET',
        params
    })
}