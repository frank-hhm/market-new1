import * as echarts from 'echarts';
import { numberFormat } from '../data/string';
import { getColor, getDecimal, parseData } from './common';
import { calculateMA } from './ma';
export const initOption = (data: any, decimal: number, klineType: string) => {
    let m_data = parseData(data);
    let dataLeng = m_data.klines.length;
    return {
        animation: false,
        legend: {
            show: false,
        },
        axisPointer: {
            link: [
                {
                    xAxisIndex: 'all'
                }
            ],
            label: {
                backgroundColor: getColor("labelBg")
            }
        },
        tooltip: {
            show: true,
            trigger: "axis",
            backgroundColor: getColor("formatterBg"),
            textStyle: {
                color: getColor("labelColor"),
                fontSize: 12,
            },
            axisPointer: {
                show: true,
                type: "cross",
            },
            formatter: (param: any) => {
                // console.log(param)
                return (
                    '开盘：' + numberFormat(param[0]?.data[1] || 0, decimal) + '<br/>' +
                    '收盘：' + numberFormat(param[0]?.data[2] || 0, decimal) + '<br/>' +
                    '最高：' + numberFormat(param[0]?.data[3] || 0, decimal) + '<br/>' +
                    '最低：' + numberFormat(param[0]?.data[4] || 0, decimal) + '<br/>' +
                    '手：' + numberFormat(param[0]?.data[5] || 0, decimal) + '<br/>' +
                    'M5：' + numberFormat(param[1]?.value || 0, decimal) + '<br/>' +
                    'M10：' + numberFormat(param[2]?.value || 0, decimal) + '<br/>' +
                    'M20：' + numberFormat(param[3]?.value || 0, decimal) + '<br/>' +
                    'M30：' + numberFormat(param[4]?.value || 0, decimal) + '<br/>'
                    // '时间：' + param[0].axisValue + '<br/>'
                );
            }
        },
        grid: [
            {
                id: 'gd1',
                top: "5%",
                left: "0%",
                right: "0%",
                height: '95%',
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
        xAxis:
        {
            type: 'category',
            show: true,
            gridIndex: 0,
            data: m_data.times,
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
        }
        ,
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
        },
        series: [
            {
                name: klineType,
                type: "candlestick",
                data: m_data.klines,
                markLine: {
                    symbol: "none",
                    precision: decimal,
                    lineStyle: {
                        color: getColor("defaultColor"),
                        type: "dashed",
                        width: 1,
                    },
                    label: {
                        position: 'insideEndBottom',
                        show: true, // 显示浮层标签
                        color: getColor("tagColor"), // 标签文字颜色
                        backgroundColor: getColor("defaultColor"), // 标签背景颜色
                        padding: [6, 4, 4, 4], // 标签内边距
                        borderRadius: 0,
                        fontSize: 12
                    }
                },
                //最高最低

                markPoint: {
                    label: {
                        show: true,
                        position: 'middle'
                    },
                    data: [
                        {
                            name: '最高',
                            type: 'max',
                            valueDim: 'lowest',
                            symbol: 'triangle',
                            symbolSize: 5,
                            symbolRotate: 180,
                            symbolOffset: [0, "-50%"],
                            label: {
                                position: "left",
                                color: "#000000",
                                distance: 6,
                                formatter: (param: any) => {
                                    return param != null ? param.value.toFixed(decimal) + '' : '';
                                }
                            },
                            itemStyle: {
                                borderColor: getColor("axisLabelColor"),
                                color: getColor("axisLabelColor"),
                            }
                        },
                        {
                            name: '最低',
                            type: 'min',
                            valueDim: 'highest',
                            symbol: 'triangle',
                            symbolSize: 5,
                            // symbolOffset:[0,80],
                            symbolOffset: [0, "50%"],
                            label: {
                                position: "left",
                                color: "#000000",
                                distance: 6,
                                formatter: (param: any) => {
                                    return param != null ? param.value.toFixed(decimal) + '' : '';
                                }
                            },
                            itemStyle: {
                                borderColor: getColor("axisLabelColor"),
                                color: getColor("axisLabelColor"),
                            }
                        }
                    ],
                    tooltip: {
                        formatter: (param: any) => {
                            return param.name + '<br>' + (param.data.coord || '');
                        }
                    }
                },
                //K 线图的图形样式。
                itemStyle: {
                    color: getColor("upColor"),
                    color0: getColor("downColor"),
                    borderColor: getColor("upBorderColor"),
                    borderColor0: getColor("downBorderColor")
                },
                barWidth: "70%",
            },
            // {
            //     name: 'MA5',
            //     type: 'line',
            //     symbol: "none",
            //     data: calculateMA(m_data.klines, 5, decimal),
            //     smooth: true,
            //     lineStyle: {
            //         opacity: 0.8,
            //         width: 1
            //     }
            // },
            // {
            //     name: 'MA10',
            //     type: 'line',
            //     symbol: "none",
            //     data: calculateMA(m_data.klines, 10, decimal),
            //     smooth: true,
            //     lineStyle: {
            //         opacity: 0.8,
            //         width: 1
            //     }
            // },
            // {
            //     name: 'MA15',
            //     type: 'line',
            //     symbol: "none",
            //     data: calculateMA(m_data.klines, 15, decimal),
            //     smooth: true,
            //     lineStyle: {
            //         opacity: 0.8,
            //         width: 1
            //     }
            // },
        ]
    }
}

export default initOption