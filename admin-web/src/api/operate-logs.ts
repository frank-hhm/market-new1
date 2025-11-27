import { request,requestProgress } from '@/utils/request/default'

export const getOperateLogsListApi = (params:any) => {
    return request({
        url: 'operateLogs/list',
        method: 'GET',
        params
    })
}
