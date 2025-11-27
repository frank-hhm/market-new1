let _config = uni.getStorageSync("systemConfig")
export default {
	namespaced: true,
	state:{
		systemConfig:_config
	},
	getters:{
		getConfig(state){
			return state.systemConfig
		}
	},
	mutations:{
		setConfig(state,data){
			state.systemConfig = data
			uni.setStorageSync("systemConfig",data)
		}
	}
}