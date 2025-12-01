import store from "@/store/index.js"
import {
	BASE_URL
} from "./config.js"
import qs from "qs"
import { router } from "../router/index.js"
const config = {
	baseUrl: BASE_URL(), // 请求的本域名
	method: 'POST',
	// 设置为json，返回后会对数据进行一次JSON.parse()
	dataType: 'json',
	showLoading: false, // 是否显示请求中的loading
	loadingText: '请求中...', // 请求loading中的文字提示
	loadingTime: 800, // 在此时间内，请求还没回来的话，就显示加载中动画，单位ms
	originalData: true, // 是否在拦截器中返回服务端的原始数据
	loadingMask: true, // 展示loading的时候，是否给一个透明的蒙层，防止触摸穿透
	// 配置请求头信息
	header: {
		'content-type': 'application/x-www-form-urlencoded;charset=UTF-8'
	},
}
const install = (Vue, vm) => {
	// 此为自定义配置参数，具体参数见上方说明
	Vue.prototype.$u.http.setConfig(config);
	Vue.prototype.$u.http.interceptor.request = (config) => {
		const token = store.getters["member/getToken"];
		 if (token) {
			config.header['Authori-zation'] = 'Bearer ' + token
		}
		// if (config.method == "POST" && config.data && token) {
		// 	config.headers['Authori-zation'] = 'Bearer ' + token
		// 	config.data =qs.stringify(Object.assign({}, {
		// 		token
		// 	}, config.data)) 
		// }
		// else{
		// 	config.data =Object.assign({}, {
		// 		token
		// 	}, config.data)
		// }
		return config;
	}
	Vue.prototype.$u.http.interceptor.response = (res) => {
		if (res.statusCode == 200 && (res.data.code == 1 || res.data.code == 200)) {
			return res.data;
		} else {
			if(res.data.code === 410000){
				uni.showToast({
					icon: "none",
					title: res.data.message,
					duration: 2000
				});
				router.push('/pages/login/index')
			}
			// 如果返回false，则会调用Promise的reject回调，
			// 并将进入this.$u.post(url).then().catch(res=>{})的catch回调中，res为服务端的返回值
			return false;
		}
	}
}

export default {
	install
}
