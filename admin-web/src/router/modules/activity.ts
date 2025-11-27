export default [
    {
        path: `/article/list`,
        name: `articleList`,
        component: () => import("@/views/activity/article/list.vue"),
        meta: {
            auth: 'article-list',
            menu_name: "文章",
        }
    },
    {
        path: `/banner/list`,
        name: `bannerList`,
        component: () => import("@/views/activity/banner/list.vue"),
        meta: {
            auth: 'banner-list',
            menu_name: "轮播图",
        },
    },
    {
        path: `/notice/list`,
        name: `noticeList`,
        component: () => import("@/views/activity/notice/list.vue"),
        meta: {
            auth: 'notice-list',
            menu_name: "公告",
        },
    },
    
]