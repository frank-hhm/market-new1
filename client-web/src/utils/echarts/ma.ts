
export const calculateMA = (klines: any[], dayCount: number, decimal: number = 2) => {
    var result: any = [];
    for (var i = 0, len = klines.length; i < len; i++) {
        if (i < dayCount) {
            result.push('-');
            continue;
        }
        var sum = 0;
        for (var j = 0; j < dayCount; j++) {
            sum += Number(klines[i - j][1]);
        }
        result.push((sum / dayCount).toFixed(decimal));
    }
    return result;
}

export default calculateMA