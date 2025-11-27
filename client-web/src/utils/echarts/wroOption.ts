import { getColor } from "./common"
import { kColor, dColor, jColor } from "./constant"
export const wroOption = () => {
    return {
        backgroundColor: "#fff",
        // 横坐标
        xAxis: [{
            type: 'category',
            data: [],
            gridIndex: 0,
            // 坐标轴轴线相关设置。
            axisLine: {
                lineStyle: {
                    type: "dashed",
                    color: getColor("splitLineColor"),
                }
            },
            //坐标轴刻度相关设置。
            axisTick: {
                show: false,
            },
            splitLine: {
                show: true,
                lineStyle: {
                    color: getColor("splitLineColor"),
                    type: "dashed"
                }
            },
            axisLabel: {
                show: false,
                color: getColor("axisLabelColor"),
            },
            axisPointer: {
                show: true, // 显示坐标轴指示器
                type: 'line', // 设置指示器类型为阴影
                label: {
                    show: false, // 显示浮层标签
                    color: getColor("labelColor"), // 标签文字颜色
                    backgroundColor: getColor("formatterBg"), // 标签背景颜色
                    padding: [4, 2, 2, 2], // 标签内边距
                    borderRadius: 0,
                    fontSize: 10
                },
            },
        }],
        // 纵坐标
        yAxis: [{
            scale: true,
            type: 'value',
            position: "left",
            gridIndex: 0,
            //坐标轴线线的颜色。
            axisLine: {
                lineStyle: {
                    color: getColor("splitLineColor"),
                    type: "dashed"
                }
            },
            //坐标轴刻度相关设置。
            axisTick: {
                show: false,
            },
            //坐标轴的分割段数
            splitNumber: 2,
            //是否显示分隔线。默认数值轴显示，类目轴不显示。
            splitLine: {
                show: true,
                lineStyle: {
                    color: getColor("splitLineColor"),
                    type: "dashed"
                }
            },
            axisLabel: {
                margin: 0,
                color: getColor("axisLabelColor"),
                fontSize: 10,
                inside: true,
            }
        }],
        // 距离底部距离
        grid: {
            top: "0%",
            left: "0%",
            right: "0%",
            bottom: "0%",
            containLabel: true,
        },
        //slider时不开启动画
        animation: false,
        //所谓『内置』，即内置在坐标系中。
        // 平移：在坐标系中滑动拖拽进行数据区域平移。
        // 移动端：在移动端触屏上，支持两指滑动缩放。
        dataZoom: [{
            show: false,
            type: 'slider',
            xAxisIndex: 0,
            realtime: false,
            // maxValueSpan:30,
            top: 65,
            height: 20,
            handleIcon: 'path://M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
            handleSize: '120%',
        }, {
            type: 'inside',
            xAxisIndex: 0,
            startValue: 70,
            endValue: 100,
            // maxValueSpan:30,
            top: 30,
            height: 20
        }],
        series: [{
            name: 'ATR',
            type: 'line',
            data: [],
            //是否平滑曲线显示。
            smooth: true,
            //是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
            showSymbol: false,
            lineStyle: {
                width: 1,
                color: kColor
            }
        }]
    }

}

export default wroOption