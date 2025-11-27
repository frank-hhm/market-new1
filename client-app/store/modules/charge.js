// 交易


export default {
	namespaced: true,
	state: {
		order_hold: [],
		order_robot:[]
	},
	getters: {
		getOrderHold(state) {
			return state.order_hold || []
		},
		getOrderRobot(state) {
			return state.order_robot || []
		}
	},
	mutations: {
		setOrderHold(state, payload) {
			state.order_hold = payload
		},
		setOrderRobot(state, payload) {
			state.order_robot = payload
		}
	}
}