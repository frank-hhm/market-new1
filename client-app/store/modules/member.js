const RedColor = "#FF4242"
const GreenColor = "#02B373"
const memberDefaultColor = uni.getStorageSync("member_default_color")
//用户模块
export default {
	namespaced: true,
	state: {
		isMoni: false,
		isLogin: false,
		token: uni.getStorageSync("token") || '',
		member: {},
		moniMemberInfo: "",
		memberCoin: {},
		memberDefaultColor: memberDefaultColor || 'red'
	},
	getters: {
		getUpColor(state, getters) {
			return getters.getMemberDefaultColor == "red" ? RedColor : GreenColor
		},
		getDownColor(state, getters) {
			return getters.getMemberDefaultColor == "red" ? GreenColor : RedColor
		},
		getMemberDefaultColor(state) {
			return state.memberDefaultColor
		},
		getMemberCoin(state) {
			return state.memberCoin
		},
		getMemberInfo(state) {
			const member = uni.getStorageSync("member")
			let _member
			if (member) {
				_member = JSON.parse(member)
			}
			return state.member || _member
		},
		getToken(state) {
			return state.token
		},
		
		getBalance(state) {
			let balance = state.memberCoin.balance || 0
			if(isNaN(balance)){
				return 0.00;
			}
			return Number(balance).toFixed(2)
		},
	},
	mutations: {
		setToken(state, payload) {
			state.token = payload
			uni.setStorageSync("token", payload)
		},
		setMemberDefaultColor(state, payload) {
			state.memberDefaultColor = payload
			uni.setStorageSync("member_default_color", payload)
		},
		setMemberCoin(state, payload) {
			state.memberCoin = payload;
		},
		setIsMoni(state, payload) {
			state.isMoni = payload
		},
		setMember(state, payload) {
			if (payload) {
				state.isLogin = true;
				state.member = payload
			} else {
				state.isLogin = false;
			}
			uni.removeStorageSync("member")
			uni.setStorageSync("member", JSON.stringify(payload))
		},
		removeToken(state, payload) {
			state.token = '';
			state.isLogin = false;
			uni.removeStorageSync("token")
			uni.removeStorageSync("member")
		},
		logout(state, payload) {
			state.isLogin = false;
			this.commit("member/removeToken", {})
			this.commit("member/setMember", {})
			this.commit("member/setMemberCoin", {})
			this.commit("member/setIsMoni", false)
		}
	},
}