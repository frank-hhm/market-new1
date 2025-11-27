import {
	kColor,
	dColor,
	jColor,
	// downColor
} from "./constant.js"
import store from "@/store/index.js"
const upColor = store.getters["member/getUpColor"]
const downColor = store.getters["member/getDownColor"]

function renderItem(params, api) {

	var xValue = api.value(0);
	var openPoint = api.coord([xValue, api.value(1)]);
	var closePoint = api.coord([xValue, api.value(2)]);
	var lowPoint = api.coord([xValue, api.value(3)]);
	var highPoint = api.coord([xValue, api.value(4)]);
	var halfWidth = api.size([1, 0])[0] * 0.35;
	var style = api.style({
		stroke: api.visual('color')
	});
	return {
		type: 'group',
		children: [{
				type: 'line',
				shape: {
					x1: lowPoint[0],
					y1: lowPoint[1],
					x2: highPoint[0],
					y2: highPoint[1]
				},
				style: style
			},
			{
				type: 'line',
				shape: {
					x1: openPoint[0],
					y1: openPoint[1],
					x2: openPoint[0] - halfWidth,
					y2: openPoint[1]
				},
				style: style
			},
			{
				type: 'line',
				shape: {
					x1: closePoint[0],
					y1: closePoint[1],
					x2: closePoint[0] + halfWidth,
					y2: closePoint[1]
				},
				style: style
			}
		]
	};
}
export default {
	backgroundColor: "#fff",
	// 横坐标
	xAxis: [{
		type: 'category',
		data: [],
		// 坐标轴轴线相关设置。
		axisLine: {
			lineStyle: {
				color: "#eee",
			}
		},
		//坐标轴刻度相关设置。
		axisTick: {
			show: false,
		},
		splitLine: {
			show: true,
			lineStyle: {
				color: "#eee",
				type: "dashed"
			}
		},
		axisLabel: {
			color: "#999"
		}
	}],
	// 纵坐标
	yAxis: [{
		scale: true,
		type: 'value',
		position: "right",
		//坐标轴线线的颜色。
		axisLine: {
			lineStyle: {
				color: "#eee",
			}
		},
		//坐标轴刻度相关设置。
		axisTick: {
			show: false,
		},
		//坐标轴的分割段数
		splitNumber: 3,
		//是否显示分隔线。默认数值轴显示，类目轴不显示。
		splitLine: {
			show: true,
			lineStyle: {
				color: "#eee",
				type: "dashed"
			}
		},
		axisLabel: {
			color: "#999"
		}
	}],
	// 距离底部距离
	grid: {
		top: "4%",
		left: "0%",
		right: "4%",
		bottom: "2%",
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
		top: 65,
		height: 20,
		handleIcon: 'path://M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
		handleSize: '120%'
	}, {
		show: true,
		type: 'inside',
		xAxisIndex: 0,
		top: 30,
		height: 20
	}],
	series: [
		{
			//Candlestick 即我们常说的 K线图。
			type: "candlestick",
			scale: true,
			data: [],
			markLine: {
				symbol: "none",
				lineStyle: {
					color: "#1677FF"
				},
				data: [{
					yAxis: 0,
				}],
				label: {
					color: "#fff",
					backgroundColor: "#1677FF",
					padding: [1, 4, 1, 4],
					position: "end"
				}
			},
			//K 线图的图形样式。
			itemStyle: {
				normal: {
					//阳线 图形的颜色。
					color: upColor,
					//阴线 图形的颜色。
					color0: downColor,
					//阳线 图形的描边颜色。
					borderColor: upColor,
					//阴线 图形的描边颜色。
					borderColor0: downColor
				}
			},
		},
		// {
		// 	name: 'boll',
		// 	type: 'custom',
		// 	renderItem: renderItem,
		// 	dimensions: ['-', 'open', 'close', 'lowest', 'highest'],
		// 	encode: {
		// 		x: 0,
		// 		y: [1, 2, 3, 4],
		// 		tooltip: [1, 2, 3, 4]
		// 	},
		// 	data: []
		// },
		{
			name: 'MID',
			type: 'line',
			data: [],
			//是否平滑曲线显示。
			smooth: true,
			//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
			showSymbol: false,
			lineStyle: {
				width: 1,
				color: dColor
			}
		},
		{
			name: 'UP',
			type: 'line',
			data: [],
			//是否平滑曲线显示。
			smooth: true,
			//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
			showSymbol: false,
			lineStyle: {
				width: 1,
				color: jColor
			}
		},
		{
			name: 'LOW',
			type: 'line',
			data: [],
			//是否平滑曲线显示。
			smooth: true,
			//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
			showSymbol: false,
			lineStyle: {
				width: 1,
				color: downColor
			}
		},
	]
}
