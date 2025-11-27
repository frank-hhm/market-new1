export const upColor = "#FF3B30"
export const downColor = "#22B462"

export const MA5Color = "#1677FF"
export const MA10Color = "#FFD700"
export const MA15Color = "#FF00FF"
export const MA30Color = "#00FF7F"
export const MA45Color = "#00BFFF"
export const MA60Color = "#FFA500"

export const kColor = "#1677FF"
export const dColor = "#FFD700"
export const jColor = "#FF00FF"
export const MAOptions = [{
		name: "MA5",
		//折线/面积图
		// 折线图是用折线将各个数据点标志连接起来的图表，用于展现数据的变化趋势。可用于直角坐标系和极坐标系上。
		type: "line",
		data: [],
		//是否平滑曲线显示。
		smooth: true,
		//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA5Color
		}
	},
	{
		name: "MA10",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA10Color
		}
	},
	{
		name: "MA15",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA15Color
		}
	},
	// {
	// 	name: "MA30",
	// 	type: "line",
	// 	data: [],
	// 	smooth: true,
	// 	showSymbol: false,
	// 	lineStyle: {
	// 		width:1,
	// 		color: MA30Color
	// 	}
	// }
]

export const MIKEOptions = [{
		name: "初级压力线",
		//折线/面积图
		// 折线图是用折线将各个数据点标志连接起来的图表，用于展现数据的变化趋势。可用于直角坐标系和极坐标系上。
		type: "line",
		data: [],
		//是否平滑曲线显示。
		smooth: true,
		//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA5Color
		}
	},
	{
		name: "中级压力线",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA10Color
		}
	},
	{
		name: "强力压力线",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA15Color
		}
	},
	{
		name: "初级支撑线",
		//折线/面积图
		// 折线图是用折线将各个数据点标志连接起来的图表，用于展现数据的变化趋势。可用于直角坐标系和极坐标系上。
		type: "line",
		data: [],
		//是否平滑曲线显示。
		smooth: true,
		//是否显示 symbol, 如果 false 则只有在 tooltip hover 的时候显示
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA30Color
		}
	},
	{
		name: "中级支撑线",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA45Color
		}
	},
	{
		name: "强力支撑线",
		type: "line",
		data: [],
		smooth: true,
		showSymbol: false,
		lineStyle: {
			width: 1,
			color: MA60Color
		}
	},
]
