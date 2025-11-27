import router from "@/router"
import NProgress from "nprogress"
import "nprogress/nprogress.css"
import { getToken, setDocumentTitle } from "@/utils"

NProgress.configure({
    showSpinner: false,
})

router.beforeEach(async (to: any, from: any, next: any) => {
    NProgress.start()
    // 判断是否需要登录才可以进入
    const token = getToken()
    if (to.matched.some((_: any) => _.meta.login !== false) || token) {
        try {

            next()
            NProgress.done()
            // 确保添加路由已完成
        } catch (err: any) {
            // 过程中发生任何错误，都直接重置 Token，并重定向到登录页面
            NProgress.done()
        }
    } else {
        next()
    }
})

router.afterEach((to: any, from: any, next: any) => {
    setDocumentTitle(to?.meta?.menu_name);
    NProgress.done()
})
