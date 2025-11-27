import Vue from "vue"
import Vuex from "vuex"

Vue.use(Vuex)
import recognize from "./modules/recognize.js"
import echarts from "./modules/echarts.js"
import app from "./modules/app.js"
import market from "./modules/market.js"
import member from "./modules/member.js"
import charge from "./modules/charge.js"
import wallet from "./modules/wallet.js"
import dataArray from "./modules/dataArray.js"
const store = new Vuex.Store({
	
	modules:{
		recognize,
		echarts,
		app,
		market,
		member,
		charge,
		wallet,
		dataArray
	}
})

export default store