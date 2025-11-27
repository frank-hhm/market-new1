
export const calculateEMA = (klines: any[], dayCount: number, decimal: number = 2) => {
    let EMAList = []
    for (let i = 0; i < klines.length; i++) {
        if (i < dayCount - 1) {
            EMAList.push("-")
        }
        else {
            let newData = klines.slice(i - dayCount + 1, i + 1)
            let EMA = exec_calcEMA(newData, dayCount)
            EMAList.push(parseFloat(EMA).toFixed(decimal))
        }
    }
    return EMAList
}

const exec_calcEMA: any = (data: any[], dayCount: number) => {
    if (dayCount == 1) {
        return (2 * parseFloat(data[dayCount - 1][1])) / (dayCount + 1)
    }
    return (2 * parseFloat(data[dayCount - 1][1]) + (dayCount - 1) * exec_calcEMA(data, dayCount - 1)) / (dayCount + 1)
}

export default calculateEMA