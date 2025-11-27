import calculateMA from "./ma"
/**
 * 计算布林指标中的标准差
 * @param dataList
 * @param ma
 * @return {number}
 */
function getBollMd(dataList: any, ma: any) {
    var dataSize = dataList.length;
    var sum = 0;
    dataList.forEach((data: any) => {
        var closeMa = data[1] - ma;
        sum += closeMa * closeMa;
    });
    var b = sum > 0;
    sum = Math.abs(sum);
    var md = Math.sqrt(sum / dataSize);
    return b ? md : -1 * md;
}
// 中轨线=20日的移动平均线

// 上轨线=中轨线+两倍的标准差

// 下轨线=中轨线-两倍的标准差
const calc_boll = (value: any, dayCount: number,decimal:number = 2) => {
    //中轨线
    const ma20 = calculateMA(value, 20, decimal)
    //上轨线
    const upList = []
    // 下轨线
    const downList = []
    for (let i = 0; i < value.length; i++) {
        if (i < dayCount || !value[i][3] || !value[i][0] || !value[i][1] || !value[i][2]) {
            upList.push("-")
            downList.push("-")
        }
        else {
            var md = getBollMd(value.slice(i - dayCount, i + 1), parseFloat(ma20[i]));
            upList.push((parseFloat(ma20[i]) + 2 * md ).toFixed(decimal))
            downList.push((parseFloat(ma20[i]) - 2 * md).toFixed(decimal))
        }
    }


    return [ma20, upList, downList]
}
export default calc_boll
