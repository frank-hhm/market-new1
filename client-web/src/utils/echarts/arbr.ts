// AR指标计算公式为：N日AR=(N日内（H－O）之和）/(N日内（O－L）之和)*100。

// BR计算公式为：N日BR=（N日内（H－YC）之和）/N日内（YC－L）之和）*100。

// 其中，O 为当日开盘价，H 为当日最高价，L 为当日最低价，YC 为前一交易日的收盘价，N 为设定的时间参数，一般原始参数日设定为 26 日
const calc_ARBR = (value: any, dayCount: number, decimal: number = 2) => {
    let arList = []
    let brList = []

    for (let i = 0; i < value.length; i++) {
        if (i < dayCount || !value[i][3] || !value[i][0] || !value[i][1] || !value[i][2]) {
            arList.push("-")
            brList.push("-")
        }
        else {
            let ar_sum_1 = 0
            let ar_sum_2 = 0
            let br_sum_1 = 0
            let br_sum_2 = 0
            for (let j = i - dayCount + 1; j < i; j++) {
                //[开盘值, 收盘值, 最低值, 最高值]
                ar_sum_1 += parseFloat(value[j][3]) - parseFloat(value[j][0])
                ar_sum_2 += parseFloat(value[j][0]) - parseFloat(value[j][2])

                br_sum_1 += parseFloat(value[j][3]) - parseFloat(value[j - 1][1])
                br_sum_2 += parseFloat(value[j - 1][1]) - parseFloat(value[j][2])
            }
            if (!ar_sum_2 || !br_sum_2) {
                arList.push("-")
                brList.push("-")
                continue
            }
            let ar = ar_sum_1 / ar_sum_2 * 100
            let br = br_sum_1 / br_sum_2 * 100
            arList.push(ar)
            brList.push(br)
        }
    }
    return [arList, brList]
}
export default calc_ARBR