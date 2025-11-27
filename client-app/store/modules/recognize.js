// 实名认证模块
export default {
	namespaced: true,
	state: {
		recognizeDialogShowed: 0,
	},
	getters: {
		getRecognizeDialogShowed(state) {
			return state.recognizeDialogShowed || parseInt(uni.getStorageSync("recognizeDialogShowed"))
		}
	},
	mutations: {
		setRecognizeDialogShowed(state, payload) {
			state.recognizeDialogShowed = payload
			uni.setStorageSync("recognizeDialogShowed", payload)
		}
	}
}
