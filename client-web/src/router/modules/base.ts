import BasicLayout from "@/components/layouts/index.vue";
export default [{
    path: '/',
    name: '/',
    header: '/',
    redirect: {
        path: `/index`
    },
    component: BasicLayout,
    children: [
        {
            path: '/index',
            name: '/index',
            meta: {
                auth: false,
                menu_name: "首页",
            },
            component: () => import('@/views/index/index.vue')
        },
        {
            path: '/download',
            name: '/download',
            meta: {
                auth: false,
                menu_name: "下载",
            },
            component: () => import('@/views/index/download.vue')
        },
        {
            path: '/about',
            name: '/about',
            meta: {
                auth: false,
                menu_name: "关于我们",
            },
            component: () => import('@/views/index/about.vue')
        },
        {
            path: '/article',
            name: '/article',
            meta: {
                auth: false,
                menu_name: "实时资讯",
            },
            component: () => import('@/views/index/article.vue')
        },
        {
            path: '/article/detail',
            name: '/articleDetail',
            meta: {
                auth: false,
                menu_name: "资讯详细",
            },
            component: () => import('@/views/index/article-detail.vue')
        },
        {
            path: '/warning',
            name: '/warning',
            meta: {
                auth: false,
                menu_name: "安全保障",
            },
            component: () => import('@/views/index/warning.vue')
        },
        {
            path: '/private',
            name: '/private',
            meta: {
                auth: false,
                menu_name: "隐私条款",
            },
            component: () => import('@/views/index/private.vue')
        },
        {
            path: '/disclaimer',
            name: '/disclaimer',
            meta: {
                auth: false,
                menu_name: "免责声明",
            },
            component: () => import('@/views/index/disclaimer.vue')
        },
        {
            path: '/trading',
            name: '/trading',
            meta: {
                auth: false,
                menu_name: "交易中",
            },
            component: () => import('@/views/trading/index.vue')
        },
        {
            path: '/test',
            name: '/indexTest',
            meta: {
                auth: false,
                menu_name: "首页",
            },
            component: () => import('@/views/index/test.vue')
        },
        
    ]
}]
