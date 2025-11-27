import { getColor, parseBarData, parseData } from "./common"

export const barOption = (data: any, decimal: number, klineType: string) => {

    let m_data = parseBarData(data);
    let dataLeng = m_data.number.length;

    return {
        animation: false,
        backgroundColor: "#fff",
        //量 柱状图 涨跌颜色映射
        visualMap: {
            show: false,
            seriesIndex: 0,
            dimension: 2,
            pieces: [{
                value: 1,
                color: getColor("downColor")
            }, {
                value: -1,
                color: getColor("upColor")
            }]
        },
        // 横坐标
        xAxis: [{
            type: 'category',
            data: m_data.times,
            show: true,
            gridIndex: 0,
            boundaryGap: true,
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
            min: (value: any) => {
                return value.min - 20;
            },
            max: (value: any) => {
                return value.max + 20;
            },
            axisPointer: {
                show: true, // 显示坐标轴指示器
                type: 'line', // 设置指示器类型为阴影
                label: {
                    show: true, // 显示浮层标签
                    color: getColor("labelColor"), // 标签文字颜色
                    backgroundColor: getColor("formatterBg"), // 标签背景颜色
                    padding: [4, 2, 2, 2], // 标签内边距
                    borderRadius: 0,
                    // borderRadius: 8,
                    // shadowColor: "#cdcdcd",
                    // shadowBlur: 10,
                    fontSize: 10
                },
            },
        }],
        // 纵坐标
        yAxis: {
            scale: true,
            type: 'value',
            position: "right",
            gridIndex: 0,
            //坐标轴的分割段数
            splitNumber: 6,
            //坐标轴线线的颜色。
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
                formatter: (value: any, index: number) => {
                    return value.toFixed(1);
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
        },
        grid: [
            {
                id: 'gd1',
                top: "0%",
                left: "0%",
                right: "0%",
                height: '100%',
                containLabel: true,
            }
        ],
        dataZoom: [{
            show: false,
            type: 'slider',
            xAxisIndex: 0,
            realtime: false,
            // maxValueSpan:30
            handleSize: '100%',
            handleIcon: 'path://M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
            startValue: dataLeng - 100,
            endValue: dataLeng + 20,

        }, {
            type: 'inside',
            realtime: false,
            xAxisIndex: [0, 1],
            startValue: dataLeng - 100,
            endValue: dataLeng + 20,
        }],
        series: [{
            scale: true,
            name: '量',
            type: 'bar',
            data: m_data.number,
            smooth: true,
            //是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
            showSymbol: false,
        },]
    }
}


export default barOption