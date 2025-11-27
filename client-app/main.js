import Vue from 'vue'
import App from './App'
import 'js_sdk/ican-H5Api/ican-H5Api.js'
Vue.config.productionTip = false
//路由
import {router,RouterMount} from './router/index.js'  //路径换成自己的
Vue.use(router)
import Routerlink from './node_modules/uni-simple-router/dist/link.vue'     
Vue.component('router-link',Routerlink)

App.mpType = 'app'
//uview-ui
import uView from "uview-ui";
Vue.use(uView);
//vuex
import store from "@/store/index.js"
Vue.filter('time', function (time) {
  const date=new Date(time*1000)
  const year=date.getFullYear()
  const month=date.getMonth()+1
  const day=date.getDate()
  const hour=date.getHours()
  const minus=date.getMinutes()
  const seconds=date.getSeconds()
  return `${year}-${month}-${day} ${hour}:${minus}:${seconds}`
})
const app = new Vue({
	store,
    ...App
})
// http拦截器，此为需要加入的内容，如果不是写在common目录，请自行修改引入路径
import httpInterceptor from '@/common/http.interceptor.js'
// 这里需要写在最后，是为了等Vue创建对象完成，引入"app"对象(也即页面的"this"实例)
Vue.use(httpInterceptor, app)

//api
import httpApi from "@/api/index.js"
Vue.use(httpApi, app)

//v1.3.5起 H5端 你应该去除原有的app.$mount();使用路由自带的渲染方式
// #ifdef H5
	RouterMount(app,router,'#app')
// #endif

// #ifndef H5
	app.$mount(); //为了兼容小程序及app端必须这样写才有效果
// #endif

// 修复echarts 插件左右滑动问题
// #ifdef H5
window.wx = {}
// #endif


Vue.filter('money', function (value) {
	if (!value) return '0.00';
	var value = value.toFixed(2);//提前保留两位小数
	var intPart = Number(value).toFixed(0); // 获取整数部分
	var intPartFormat = intPart.toString().replace(/(\d)(?=(?:\d{3})+$)/g, '$1,'); // 将整数部分逢三一断 ？？？
	var floatPart = '.00'; // 预定义小数部分
	value = value.toString();//将number类型转为字符串类型，方便操作
	var value2Array = value.split('.');
	if (value2Array.length === 2) { // =2表示数据有小数位
	  floatPart = value2Array[1].toString(); // 拿到小数部分
	  if (floatPart.length === 1) { // 补0,实际上用不着
		  return intPartFormat + '.' + floatPart + '0';
	  } else {
		  return intPartFormat + '.' + floatPart;
	  }
	} else {
	  return intPartFormat + floatPart;
	}
})