export default [
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