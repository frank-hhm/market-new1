import { request,requestProgress } from '@/utils/request/default'


export const getVersionDefaultApi = () => {
    return request({
        url: `versionData/versionDefault`,
        method: 'GET'
    })
}
export const createVersionDefaultApi = (data:any) => {
    return request({
        url: `versionData/createVersionDefault`,
        method: 'POST',
        data
    })
}
export const getVersionDataListApi = (params:any) => {
    return request({
        url: `versionData/getList`,
        method: 'GET',
        params
    })
}
export const publishVersionApi = (data:any) => {
    return request({
        url: `versionData/publish`,
        method: 'POST',
        data
    })
}