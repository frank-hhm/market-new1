let mainIndicatorsIndex=1
let mainStorage=uni.getStorageSync("mainIndicatorsIndex")
let assistantIndicatorsIndex=1
let assistantStorage=uni.getStorageSync("assistantIndicatorsIndex")
if(mainStorage){
	mainIndicatorsIndex=parseInt(mainStorage)
}
if(assistantStorage){
	assistantIndicatorsIndex=parseInt(assistantStorage)
}
export default {
	namespaced: true,
	state:{
		//主指标列表
		mainIndicators:["不显示","MA","EMA","BBI","MIKE","BOLL"],
		mainIndicatorsIndex:mainIndicatorsIndex,
		//副指标列表
		assistantIndicators:['成交量',"KDJ","MACD","ARBR","ATR","BIAS","KD","W&R"],
		assistantIndicatorsIndex:assistantIndicatorsIndex
	},
	mutations:{
		modifyMainIndicatorsIndex(state,payload){
			state.mainIndicatorsIndex=payload
			uni.setStorageSync("mainIndicatorsIndex",JSON.stringify(payload))
		},
		modifyAssistantIndicatorsIndex(state,payload){
			state.assistantIndicatorsIndex=payload
			uni.setStorageSync("assistantIndicatorsIndex",JSON.stringify(payload))
		}
	}
}