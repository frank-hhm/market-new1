export default [
    {
        path: `/member/list`,
        name: `memberList`,
        component: () => import("@/views/member/member/list.vue"),
        meta: {
            auth: 'member-list',
            menu_name: "会员列表",
        },
    },
    {
        path: `/member/card`,
        name: `memberCard`,
        component: () => import("@/views/member/card/list.vue"),
        meta: {
            auth: 'member-card',
            menu_name: "实名审核",
        },
    }
]