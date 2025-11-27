import { getColor, parseData } from "./common";
const setLineOption = (items: any[], decimal: number, klineType: string) => {
    let lineData = [];
    let date = items.map((item) => {
        const date = new Date(item[0] * 1000);
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate();
        const hour = date.getHours();
        const minus = date.getMinutes();
        const seconds = date.getSeconds();
        // return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`;
        return `${hour}:${minus}`;
    });
    let data = items.map((item) => item[1])
    return {
        times: date, data: data
    }
};

export const lineOption:any = (data: any, decimal: number, klineType: string) => {
    let m_data = setLineOption(data, decimal, klineType);
    let dataLeng = m_data.data.length;
    return {
        backgroundColor: "#fff",
        // 横坐标
        xAxis: [{
            type: 'category',
            data: m_data.times,
            // 坐标轴轴线相关设置。
            axisLabel: {
                //label文字设置
                show: true,
                color: getColor("axisLabelColor"),
                fontSize: 10,
            },
            axisLine: {
                show: false,
                lineStyle: {
                    type: 'dashed',
                    color: getColor("splitLineColor"),
                }
            },
            splitLine: { //分割线设置
                show: true,
                lineStyle: {
                    color: getColor("splitLineColor"),
                    type: "dashed"
                }
            },
            axisTick: {
                show: false,
            },
            splitNumber: 20,
            min:  (value:any) => {
                return value.min - 20;
            },
            max:  (value:any) => {
                return value.max + 20;
            },
            axisPointer: {
                show: true, // 显示坐标轴指示器
                type: 'line', // 设置指示器类型为阴影
                label: {
                    show: true, // 显示浮层标签
                    color: getColor("labelColor"), // 标签文字颜色
                    backgroundColor: getColor("formatterBg"), // 标签背景颜色
                    padding: [6, 4, 4, 4], // 标签内边距
                    borderRadius: 0,
                },
            },
        }],
        // 纵坐标
        yAxis: [{
            scale: true,
            type: 'value',
            position: "right",
            axisLine: {
                show: true,
                lineStyle: {
                    type: 'dashed',
                    color: getColor("splitLineColor"),
                }
            },
            axisPointer: {
                show: true, // 显示坐标轴指示器
                type: 'line', // 设置指示器类型为阴影
                label: {
                    show: true, // 显示浮层标签
                    precision: 3,
                    color: getColor("labelColor"), // 标签文字颜色
                    backgroundColor: getColor("formatterBg"), // 标签背景颜色
                    padding: [4, 2, 2, 2], // 标签内边距
                    borderRadius: 0,
                    // borderRadius: 8,
                    // shadowColor: "#cdcdcd",
                    // shadowBlur: 10,
                    fontSize: 12
                },
            },
            //刻度标签与轴线之间的距离。
            axisLabel: {
                margin: 0,
                color: getColor("axisLabelColor"),
                fontSize: 12,
                interval: "auto",
                inside: true,
                formatter: (value:any, index:number) => {
                    return value.toFixed(decimal);
                }
            },
            //坐标轴刻度相关设置。
            axisTick: {
                show: false,
            },
            //是否显示分隔线。默认数值轴显示，类目轴不显示。
            splitLine: {
                show: true,
                lineStyle: {
                    color: getColor("splitLineColor"),
                    type: "dashed"
                }
            },
        }],
        // 距离底部距离
        grid: {
            top: "2%",
            left: "0%",
            right: "0%",
            bottom: "4%",
            containLabel: true,
        },
        //slider时不开启动画
        animation: false,
        //所谓『内置』，即内置在坐标系中。
        // 平移：在坐标系中滑动拖拽进行数据区域平移。
        dataZoom: [{
            show: false,
            type: 'slider',
            xAxisIndex: 0,
            realtime: false,
            // startValue: 70,
            // endValue: 100,
            // maxValueSpan:30,
            top: 65,
            height: 20,
            handleIcon: 'path://M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
            handleSize: '120%',
            startValue: dataLeng - 100,
            endValue: dataLeng + 20,
        }, {
            type: 'inside',
            xAxisIndex: 0,
            // startValue: 70,
            // endValue: 100,
            // maxValueSpan:30,
            top: 30,
            height: 20,
            startValue: dataLeng - 100,
            endValue: dataLeng + 20,
        }],
        series: [{
            name: '分时',
            type: 'line',
            data: m_data.data,
            //是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
            showSymbol: false,
            areaStyle: {
                color: getColor("defaultColor"),
                opacity: 0.1
            },
            lineStyle: {
                color: getColor("defaultColor"),
                width: 1
            },
            markLine: {
                symbol: "none",
                precision: decimal,
                lineStyle: {
                    color: getColor("defaultColor"),
                },
                data: [{
                    yAxis: 0,
                }],
                label: {
                    position: 'insideEndBottom',
                    show: true, // 显示浮层标签
                    color: getColor("labelColor"), // 标签文字颜色
                    backgroundColor: getColor("defaultColor"), // 标签背景颜色
                    padding: [6, 4, 4, 4], // 标签内边距
                    borderRadius: 0,
                    fontSize: 12
                },
            },
        },]
    }
}

export default lineOption