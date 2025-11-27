export default [
    {
        path: `/marketing/turntable/record-list`,
        name: `marketingTurntableRecorList`,
        component: () => import("@/views/marketing/turntable/record-list.vue"),
        meta: {
            auth: 'marketing-turntable-record-list',
            menu_name: "转盘抽奖记录",
        }
    },
    {
        path: `/marketing/follow/person/list`,
        name: `marketingFollowPersonList`,
        component: () => import("@/views/marketing/follow/person/list.vue"),
        meta: {
            auth: 'marketing-follow-person-list',
            menu_name: "跟单交易员",
        }
    },
    {
        path: `/marketing/follow/index`,
        name: `marketingFollowIndex`,
        component: () => import("@/views/marketing/follow/index.vue"),
        meta: {
            auth: 'marketing-follow-index',
            menu_name: "跟单",
        }
    },
    {
        path: `/marketing/follow/order`,
        name: `marketingFollowOrder`,
        component: () => import("@/views/marketing/follow/order.vue"),
        meta: {
            auth: 'marketing-follow-order',
            menu_name: "跟单订单",
        }
    },
]