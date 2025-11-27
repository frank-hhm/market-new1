import {
	upColor,
	downColor
} from "./constant.js"

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
		},
		axisPointer: {
			show: true, // 显示坐标轴指示器
			type: 'line', // 设置指示器类型为阴影
			label: {
				show: true, // 显示浮层标签
				color: '#fff', // 标签文字颜色
				backgroundColor: '#989898', // 标签背景颜色
				padding: [4, 2, 2, 2], // 标签内边距
				borderRadius: 0,
			},
		},
	}],
	// 纵坐标
	yAxis: [{
		scale: true,
		type: 'value',
		position: "left",
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
			show: false,
			lineStyle: {
				color: "#eee",
				type: "dashed"
			}
		},
		axisLabel: {
			show: true,
			inside: true,
			color: "#999"
		},
		axisPointer: {
			show: true, // 显示坐标轴指示器
			type: 'line', // 设置指示器类型为阴影
			label: {
				show: true, // 显示浮层标签
				color: '#fff', // 标签文字颜色
				backgroundColor: '#989898', // 标签背景颜色
				padding: [4, 2, 2, 2], // 标签内边距
				borderRadius: 0,
				// shadowColor: "#cdcdcd",
				// shadowBlur: 10
			},
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
		handleSize: '120%'
	}, {
		type: 'inside',
		xAxisIndex: 0,
		// startValue: 70,
		// endValue: 100,
		// maxValueSpan:30,
		top: 30,
		height: 20
	}],
	series: [{
		name: '分时',
		type: 'line',
		data: [],
		//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
		showSymbol: false,
		areaStyle: {
			color: "#3986F7",
			opacity: 0.1
		},
		lineStyle: {
			color: "#3986F7",
			width: 1
		},
		markLine: {
			symbol: "none",
			lineStyle: {
				color: "#1677FF"
			},
			data: [{
				yAxis: 0,
			}],
			label: {
				position: 'insideStartTop',
				show: true, // 显示浮层标签
				color: '#fff', // 标签文字颜色
				backgroundColor: '#3888F7', // 标签背景颜色
				padding: [4, 2, 2, 2], // 标签内边距
				borderRadius: 0,
				fontSize: 10
			},
		},
	}, ]
}