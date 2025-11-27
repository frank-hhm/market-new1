import BasicLayout from "@/components/layouts/index.vue";

import system from './system'
import media from './media'
import plugins from './plugins'
import product from './product'
import agent from './agent'
import member from './member'
import finance from './finance'
import activity from './activity'
import order from './order'
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
            path: '/detail',
            name: '/detail',
            meta: {
                auth: false,
                menu_name: "个人中心",
            },
            component: () => import('@/views/index/detail.vue')
        },
        ...system,
        ...media,
        ...plugins,
        ...product,
        ...agent,
        ...member,
        ...finance,
        ...activity,
        ...order,
        ...marketing
    ]
}]
