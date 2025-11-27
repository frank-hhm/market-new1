import store from "@/store/index.js"
const upColor = store.getters["member/getUpColor"]
const downColor = store.getters["member/getDownColor"]

function candlestickOption(xiaoshu = 2) {

	return {
		backgroundColor: '#ffffff',
		tooltip: {
			show: true,
			trigger: "axis",
			backgroundColor: 'rgba(0,0,0,0.7)',
			textStyle: {
				color: "#fff",
				fontSize: 12,
			},
			formatter: function(seriesData) {
				let label = "<div>"
				seriesData.forEach(item => {
					// console.log(item)
					if (item.seriesType === "candlestick") {
						//[开盘值, 收盘值, 最低值, 最高值]
						label +=
							`<span>开盘</span>: ${item.value[1]}</br><span>收盘</span>: ${item.value[2]}</br><span>最低</span>: ${item.value[4]}</br><span>最高</span>: ${item.value[3]}</br><span>成交量</span>: ${item.value[5]}</br>`
					} else {
						label += `${item.seriesName}: ${item.value} </br>`
					}
				})

				label += '</div>'
				// console.log(label)
				return label
			},
			axisPointer: {
				show: true,
				type: "cross",
			},
		},

		// 横坐标
		xAxis: [{
			/*坐标轴类型。
					可选：
					'value' 数值轴，适用于连续数据。
					'category' 类目轴，适用于离散的类目数据，为该类型时必须通过 data 设置类目数据。
					'time' 时间轴，适用于连续的时序数据，与数值轴相比时间轴带有时间的格式化，在刻度计算上也有所不同，例如会根据跨度的范围来决定使用月，星期，日还是小时范围的刻度。
					'log' 对数轴。适用于对数数据。*/
			type: "category",
			gridIndex: 0,
			data: [],
			// 坐标轴轴线相关设置。
			axisLine: {
				lineStyle: {
					color: "#E6E6E6",
				}
			},
			// 坐标轴刻度标签的相关设置。
			axisLabel: {
				show: true,
				color: "#696677",
				fontSize: 10,
			},
			//坐标轴刻度相关设置。
			axisTick: {
				show: false,
			},
			splitLine: {
				show: true,
				lineStyle: {
					color: "#F2F2F2",
					// type: "dashed"
				},
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
					// borderRadius: 8,
					// shadowColor: "#cdcdcd",
					// shadowBlur: 10,
					fontSize: 10
				},
			},
		}],
		// 纵坐标
		yAxis: [{
			/*只在数值轴中（type: 'value'）有效。
			是否是脱离 0 值比例。设置成 true 后坐标刻度不会强制包含零刻度。在双数值轴的散点图中比较有用。*/
			scale: true,
			type: 'value',
			position: "left",
			gridIndex: 0,
			//坐标轴的分割段数
			splitNumber: 2,
			//坐标轴线线的颜色。
			axisLine: {
				lineStyle: {
					color: "#A6A6A6",
				}
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
					// borderRadius: 8,
					// shadowColor: "#cdcdcd",
					// shadowBlur: 10,
					fontSize: 10
				},
			},
			//刻度标签与轴线之间的距离。
			axisLabel: {
				color: "#A6A6A6",
				fontSize: 12,
				inside: true,
				formatter: function(value, index) {
					return value.toFixed(xiaoshu);
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
					color: "#F2F2F2",
					// type: "dashed"
				}
			},
		}],
		//slider时不开启动画
		animation: false,
		// 距离底部距离
		grid: {
			top: "0%",
			left: "0%",
			right: "0%",
			bottom: "0%",
			containLabel: true,
			backgroundColor: "#ffffff"
		},
		//所谓『内置』，即内置在坐标系中。
		// 平移：在坐标系中滑动拖拽进行数据区域平移。
		// 缩放：
		// PC端：鼠标在坐标系范围内滚轮滚动（MAC触控板类同）
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
			handleSize: '100%',
		}, {
			type: 'inside',
			realtime: false,
			xAxisIndex: [0, 1],
			startValue: 70,
			endValue: 90,
			// maxValueSpan:30,
			top: 30,
			height: 20
		}],
		series: [{
			//Candlestick 即我们常说的 K线图。
			type: "candlestick",
			scale: true,
			data: [],
			markLine: {
				symbol: "none",
				lineStyle: {
					color: '#FFC300',
					type: "solid",
					width: 1,
				},
				precision: xiaoshu,
				data: [{
					yAxis: 0,
				}],
				label: {
					position: 'insideStartBottom',
					show: true, 
					color: '#fff',
					backgroundColor: '#FFC300',
					borderWidth:'1px',
					borderType:'solid',
					padding: [4,4,4,4], // 标签内边距
					borderRadius:4,
					fontSize: 10,
				}
			},
			markPoint: {
				label: {
					show: true,
					position: 'middle'
				},
				data: [{
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
							fontSize:10,
							formatter: (param) => {
								return param != null ? param.value.toFixed(xiaoshu) + '' : '';
							}
						},
						itemStyle: {
							borderColor:"#000000",
							color:"#000000",
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
							fontSize:10,
							formatter: (param) => {
								return param != null ? param.value.toFixed(xiaoshu) + '' : '';
							}
						},
						itemStyle: {
							borderColor:"#000000",
							color:"#000000",
						}
					}
				]
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
					borderColor0: downColor,
				}
			},
			barWidth: "70%"
		}],
	}
}
export default candlestickOption