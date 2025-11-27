

var labelColor = "#666"; //文字颜色

var defaultColor = "#473bf0";

var borderColor = "#bebebe"; // 图标边框色，会影响坐标轴上悬浮框的背景色

var upColor = '#f53f3f';
var upBorderColor = '#f53f3f';
var downColor = '#00b42a';
var downBorderColor = '#00b42a';

var labelColor = "#ffffff";
var labelBg = "#000000";

var tagColor = "#ffffff";

var formatterBg = 'rgba(0,0,0,0.7)';

var axisLabelColor = "#B0AFAF";

//背景分割线
var splitLineColor = "#f2f2f2";

export const getColor = (key: string) => {
    let colorArr: any = {
        upColor: upColor,
        downColor: downColor,
        borderColor: borderColor,
        upBorderColor: upBorderColor,
        downBorder: downBorderColor,
        labelColor: labelColor,
        labelBg: labelBg,
        defaultColor: defaultColor,
        tagColor: tagColor,
        axisLabelColor: axisLabelColor,
        splitLineColor: splitLineColor,
        formatterBg: formatterBg
    }
    return colorArr[key];
};

export const parseData = (data: any) => {
    let times: any[] = [], klines: any[] = [], volumes: any[] = [];
    //接口
    // 0-交易日
    // 1-开盘价
    // 2-最低价
    // 3-最高价
    // 4-收盘价
    // 5-手
    for (let i = 0; i < data.length; i++) {
        let item: any = data[i];
        let _date = getDateText(item[0]);
        times.push(_date);
        // 0-开盘价
        // 1-收盘价
        // 2-最高价
        // 3-最低价
        klines.push([item[1], item[4], item[3], item[2], item[5]]);
        volumes.push(item[5]);
    }
    return { times: times, klines: klines, volumes: volumes };
}

export const parseBarData = (data: any) => {
    let times: any[] = [];
    for (let i = 0; i < data.length; i++) {
        let _date = getDateText(data[i][0]);
        times.push(_date);
    }
    let number: any = data.map((item: any, index: number) => [
        index,
        item[5],
        item[4] < item[1] ? 1 : item[4] == item[1] ? 0 : -1,
    ])
    return { times: times, number: number };
}

export const getDateText = (dateTime: number) => {
    const date = new Date(dateTime * 1000);
    const year = date.getFullYear();
    const month = date.getMonth() + 1;
    const day = date.getDate();
    const hour = date.getHours();
    const minus = date.getMinutes();
    const seconds = date.getSeconds();
    return `${month}-${day} ${hour}:${minus}`;
}


export const getDecimal = (kone: any) => {
    let decimal = 2;
    for (let i = 0; i < kone.length; i++) {
        let price = kone[i];
        let _deg = (price + '').split(".");
        if (_deg.length == 2 && _deg[1].length > decimal) {
            decimal = _deg[1].length;
        }
    }
    return decimal;
}


let isPush: boolean = false

export const getPriceMinuteRefreshOption = (chartInstance: any, price: number | string, currentActive: number = 0) => {
    // 获取当前图表的配置
    let seriesData: any[] = [];
    if (chartInstance !== undefined) {
        const option = chartInstance.getOption();
        // 确保数据存在
        if (option !== undefined && option.series && option.series[0] && option.series[0].data) {
            // 获取第一个系列的数据数组
            seriesData = option.series[0].data;
            const lastIndex = seriesData.length - 1;
            if (seriesData.length > 0 && seriesData[lastIndex][1]) {
                seriesData[lastIndex][1] = price;
                const now = new Date(); // 获取当前时间
                const seconds = now.getSeconds(); // 获取当前秒数
                const minutes = now.getMinutes();
                const hours = now.getHours();
                const timestampInSeconds = Math.floor(Date.now() / 1000);
                if (currentActive == 5) {
                    // console.log(Number(checkKtime) > Number(seriesData[lastIndex][5]) && Number(seriesData[lastIndex]
                    // 		[5]) + 60 > Number(checkKtime))

                    // 0-开盘价
                    // 1-收盘价
                    // 2-最高价
                    // 3-最低价
                    if (seconds === 0 && isPush === false) {
                        isPush = true;
                        seriesData.push([price,
                            price,
                            price,
                            price,
                            0
                        ])
                    } else {
                        if (price < seriesData[lastIndex][3]) {
                            seriesData[lastIndex][3] = price
                        }
                        if (price > seriesData[lastIndex][2]) {
                            seriesData[lastIndex][2] = price
                        }
                    }
                    if (seconds > 0) {
                        isPush = false;
                    }
                } else if (
                    (minutes % 5 === 0 && currentActive === 6 && seconds === 0 && isPush === false) ||
                    (minutes % 15 === 0 && currentActive === 1 && seconds === 0 && isPush === false) ||
                    (minutes % 30 === 0 && currentActive === 2 && seconds === 0 && isPush === false) ||
                    (currentActive === 3 && minutes === 0 && seconds === 0 && isPush === false) || // 整点逻辑
                    (hours % 4 === 0 && currentActive === 4 && minutes === 0 && seconds === 0 && isPush ===
                        false) ||
                    (currentActive === 7 && hours === 0 && minutes === 0 && seconds === 0 && isPush === false)
                ) {
                    isPush = true;
                    seriesData.push([price,
                        price,
                        price,
                        price,
                        0
                    ])
                } else if (currentActive === 1 || currentActive === 2 || currentActive === 3 || currentActive ===
                    4 || currentActive === 6 || currentActive === 7) {

                    if (price < seriesData[lastIndex][3]) {
                        seriesData[lastIndex][3] = price
                    }
                    if (price > seriesData[lastIndex][2]) {
                        seriesData[lastIndex][2] = price
                    }
                }
                if (seconds > 0) {
                    isPush = false;
                }
            }
        }
    }
    let optionObj = {
        series: [{
            data: seriesData,
            markLine: {
                data: [
                    {
                        yAxis: price
                    }
                ]
            },
        }]
    }
    return optionObj;
};



export const getPriceLineRefreshOption = (chartInstance: any, price: number | string, currentActive: number = 0) => {
    // 获取当前图表的配置
    let seriesData: any[] = [];
    if (chartInstance !== undefined) {
        const option = chartInstance.getOption();
        // 确保数据存在
        if (option !== undefined && option.series && option.series[0] && option.series[0].data) {
            // 获取第一个系列的数据数组
            seriesData = option.series[0].data;
            // console.log(seriesData)
            const lastIndex = seriesData.length - 1;
            if (seriesData.length > 0 && seriesData[lastIndex]) {
                const now = new Date(); // 获取当前时间
                const seconds = now.getSeconds(); // 获取当前秒数
                const timestampInSeconds = Math.floor(Date.now() / 1000);
                if (currentActive == 0) {
                    // console.log(Number(checkKtime) > Number(seriesData[lastIndex][5]) && Number(seriesData[lastIndex]
                    // 		[5]) + 60 > Number(checkKtime))
                    if (seconds === 0 && isPush === false) {
                        isPush = true;
                        seriesData.push(price)
                    } else {
                        seriesData[lastIndex] = price
                    }
                    if (seconds > 0) {
                        isPush = false;
                    }
                }
            }
        }
    }
    let optionObj = {
        series: [{
            data: seriesData,
            markLine: {
                data: [
                    {
                        yAxis: price
                    }
                ]
            },
        }]
    }
    return optionObj;
};



export const getBarRefreshOption = (chartInstance: any, price: number | string, currentActive: number = 0) => {
    // 获取当前图表的配置
    let seriesData: any[] = [];
    if (chartInstance !== undefined) {
        const option = chartInstance.getOption();
        // 确保数据存在
        if (option !== undefined && option.series && option.series[0] && option.series[0].data) {
            // 获取第一个系列的数据数组
            seriesData = option.series[0].data;
            // console.log(seriesData)
            const lastIndex = seriesData.length - 1;
            if (seriesData.length > 0 && seriesData[lastIndex]) {
                const now = new Date(); // 获取当前时间
                const seconds = now.getSeconds(); // 获取当前秒数
                const timestampInSeconds = Math.floor(Date.now() / 1000);
                if (currentActive == 0) {
                    // console.log(Number(checkKtime) > Number(seriesData[lastIndex][5]) && Number(seriesData[lastIndex]
                    // 		[5]) + 60 > Number(checkKtime))
                    if (seconds === 0 && isPush === false) {
                        isPush = true;
                        seriesData.push(price)
                    } else {
                        seriesData[lastIndex] = price
                    }
                    if (seconds > 0) {
                        isPush = false;
                    }
                }
            }
        }
    }
    let optionObj = {
        series: [{
            data: seriesData,
            markLine: {
                data: [
                    {
                        yAxis: price
                    }
                ]
            },
        }]
    }
    return optionObj;
}