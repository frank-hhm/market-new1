import store from "@/store"
import Vue from 'vue'
import {
	initDetail
} from "@/utils/initData.js"
export const websocketCallbackFunction = (res) => {
	let _res = res.data;
	switch (_res.type) {
		case "market":
			//实时行情
			if (_res.data) {
				store.commit('market/setMarketItem', _res.data)
			}
			break;
		case "productCache":
			Vue.prototype.$u.api.getProductConfigApi().then((res) => {
				store.commit('market/setMarkets', res.data.list || [])
				store.commit('market/setSector', res.data.sector || [])
			})
			break;
		case "memberDetail":
			initDetail()
			break;
		case "createOrder":
			initDetail()
			break;
		case "pingCang":
			initDetail()
			break;
		case "closeOrder":
			initDetail()
			break;
			


	}
}