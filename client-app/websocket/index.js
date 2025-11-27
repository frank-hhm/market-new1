import {
	WS_URL
} from "@/common/config.js"
import store from "@/store/index.js"
import {
	router
} from "@/router/index.js"
let isWebSocketOpen = false
let heartbeatIntervalId = null;
export let clientId = ""
export const createWebsocket = () => {
	if (isWebSocketOpen) {
		return
	}
	uni.connectSocket({
		url: WS_URL,
		header: {
			'content-type': 'application/json'
		},
		success: () => {
			console.log("websocket connectSocket success---------")
		},
		fail: (e) => {
			// store.commit('market/setMarketStatus', false)
			console.warn("websocket connectSocket error---------", e)
		}
	});

	uni.onSocketOpen(function(res) {
		// store.commit('market/setMarketStatus', false)
		store.commit('market/setMarketStatus', true)
		// setTimeout(()=>{
		// },1000)
		console.log('WebSocket连接已打开！');
		isWebSocketOpen = true;
		sendPingMessage()
		try {
			heartbeatIntervalId = setInterval(() => {
				if (isWebSocketOpen === true) {
					sendPingMessage()
				} else {
					clearInterval(heartbeatIntervalId);
				}
			}, 20000);
		} catch (err) {
			// console.log("=====", err);
		}
	});

	function sendPingMessage() {
		let obj = {}
		obj.type = 'ping'
		// if (store.getters["market/getMarketId"]) {
		// 	obj.market_subscribe = store.getters["market/getMarketId"]
		// }
		if (store.getters["member/getToken"]) {
			obj.token = store.getters["member/getToken"]
		}
		uni.sendSocketMessage({
			data: JSON.stringify(obj),
			complete(res) {}
		});
	}
	uni.onSocketError(function(res) {
		// store.commit('market/setMarketStatus', false)
		console.log('WebSocket连接打开失败，请检查！', res);
		isWebSocketOpen = false
		if (heartbeatIntervalId) {
			clearInterval(heartbeatIntervalId);
		}
		//重连
		setTimeout(() => {
			createWebsocket()
		}, 100)
	});

	uni.onSocketClose(function(res) {
		console.log("error trigger-------------", res)
		// store.commit('market/setMarketStatus', false)
		isWebSocketOpen = false
		if (heartbeatIntervalId) {
			clearInterval(heartbeatIntervalId);
		}
		//重连
		setTimeout(() => {
			createWebsocket()
		}, 100)
	});

	uni.onSocketMessage(function(res) {
		// console.log("????有发",res)
		if (JSON.parse(res.data)?.code == 1) {
			uni.$emit("websocketMessage", {
				data: JSON.parse(res.data)
			})
		} else if (JSON.parse(res.data)?.code == 410000) {
			store.commit("member/logout")
			router.push("/pages/login/index")
		}
		if (JSON.parse(res.data).type === "init") {
			clientId = JSON.parse(res.data).client_id
			uni.$emit("websocketClient", {
				data: JSON.parse(res.data)
			})
		} else {
			uni.$emit("websocketMessage", {
				data: JSON.parse(res.data)
			})
		}
	});

}
export const sendSocketMessage = (msg) => {
	if (!isWebSocketOpen) {
		return
	}
	uni.sendSocketMessage({
		data: msg
	});
}