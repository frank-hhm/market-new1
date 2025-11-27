export default [
    {
        path: `/agent/list`,
        name: `agentList`,
        component: () => import("@/views/agent/list.vue"),
        meta: {
            auth: 'agent-list',
            menu_name: "代理商",
        },
    }
]