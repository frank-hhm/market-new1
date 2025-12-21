import BasicLayout from "@/components/layouts/index.vue";

import media from './media'
import member from './member'
import finance from './finance'
import order from './order'
import agent from './agent'
import marketing from './marketing'

export default [{
    path: '/',
    name: '/',
    header: '/',
    redirect: {
        path: `/detail`
    },
    component: BasicLayout,
    children: [
        // {
        //     path: '/index',
        //     name: '/index',
        //     meta: {
        //         auth: false,
        //         menu_name: "首页",
        //     },
        //     component: () => import('@/views/index/index.vue')
        // },
        {
            path: '/detail',
            name: '/detail',
            meta: {
                auth: false,
                menu_name: "个人中心",
            },
            component: () => import('@/views/index/detail.vue')
        },
        ...media,
        ...member,
        ...finance,
        ...order,
        ...agent,
        ...marketing
    ]
}]
