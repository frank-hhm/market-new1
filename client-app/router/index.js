import {RouterMount,createRouter} from 'uni-simple-router';
import store from "@/store/index.js"
const router = createRouter({
	platform: process.env.VUE_APP_PLATFORM,  
	routes: [...ROUTES]
});
//全局路由前置守卫
router.beforeEach((to, from, next) => {
	const token=store.getters["member/getToken"]
	const isMoni=store.state.member.isMoni
	if(to.meta.auth&&!token){
		next();
		next({path:"/pages/login/index",query:{redirect:to.path,_query:{...to.query}}, NAVTYPE: 'push'})
	}
	else if(to.meta.forbidenMock&&isMoni){
		uni.showToast({
			icon:"none",
			title:"模拟禁止使用"
		})
		next(false)
	}
	else{
		next();
	}
	
});
// 全局路由后置守卫
router.afterEach((to, from) => {
})

export {
	router,
	RouterMount
}