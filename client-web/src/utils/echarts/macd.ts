import calculateEMA from "./ema"
const calc_MACD = (data: any, decimal: number = 2) => {
    let dif: any = exec_DIF(data)
    //第二句是dea线等于dif线的9日均值。顾名思义连续9天的dif值除以9就是dea的当日值。
    let dea: any = []
    for (let i = 0; i < dif.length; i++) {
        if (i < 8) {
            dea.push("-")
            continue
        }
        else {
            let sum: any = 0
            for (let j = i - 8; j < i; j++) {
                if (dif[j] == "-" || isNaN(dif[j])) {
                    continue
                }
                sum += dif[j]
            }
            let _dea = sum / 9
            dea.push(_dea)
        }
    }
    //第三句macd等于dif线值减去dea线值再乘以2，并且以红绿色柱子来表示。
    let macd = dif.map((item: any, index: any) => {
        if (dif[index] == "-" || dea[index] == "-" || isNaN(dif[index])) {
            return "-"
        }
        else {
            return ((dif[index] - dea[index]) * 2)
        }
    })
    return [dif, dea, macd]
}

//第一句dif线的计算公式是12日均值减去26日均值得出的那个值就是dif值。
const exec_DIF = (data: any, decimal: number = 2) => {
    let ema_12 = calculateEMA(data, 12)
    let ema_26 = calculateEMA(data, 26)
    let dif: any = ema_12.map((item, index) => {
        if (ema_12[index] == "-" || ema_26[index] == "-") {
            return "-"
        }
        else {
            return (parseFloat(ema_12[index]) - parseFloat(ema_26[index]))
        }
    })
    return dif
}
export default calc_MACD
