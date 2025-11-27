import { request,requestProgress } from '@/utils/request/default'
export const getTurntableRecordListApi = (params: any) => {
    return request({
        url: `activity.turntableRecordLog/list`,
        method: 'GET',
        params
    })
}
