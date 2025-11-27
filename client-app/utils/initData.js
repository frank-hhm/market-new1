import store from "@/store"
import Vue from 'vue'
export const initDetail = () => {
	
	if (uni.getStorageSync("token")) {
		Vue.prototype.$u.api.getMemberDetailApi().then((res) => {
			store.commit('member/setIsMoni', res.data.member.moni == 0 ? false : true)
			store.commit('member/setMember', res.data.member)
			store.commit('member/setMemberCoin', res.data.member_coin)
			store.commit('charge/setOrderHold', res.data.order_hold)
			store.commit('charge/setOrderRobot', res.data.order_robot)
		})
	}
}

export const loginqie = () => {
	Vue.prototype.$u.api.loginqieApi().then((res) => {
		store.commit('member/setToken', res.data.token)
		store.commit('member/setIsMoni', res.data.member.moni == 0 ? false : true)
		store.commit('member/setMember', res.data.member)
		store.commit('member/setMemberCoin', res.data.member_coin)
		store.commit('charge/setOrderHold', res.data.order_hold)
		store.commit('charge/setOrderRobot', res.data.order_robot)
	})
}