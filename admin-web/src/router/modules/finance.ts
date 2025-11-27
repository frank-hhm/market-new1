export default [
    {
        path: `/finance/recharge`,
        name: `financeRechargeList`,
        component: () => import("@/views/finance/recharge/list.vue"),
        meta: {
            auth: 'finance-recharge-list',
            menu_name: "充值申请",
        },
    },
    {
        path: `/finance/member-withdrawal`,
        name: `financeMemberWithdrawal`,
        component: () => import("@/views/finance/withdrawal/member.vue"),
        meta: {
            auth: 'finance-member-withdrawal',
            menu_name: "用户提现申请",
        },
    },
    {
        path: `/finance/water`,
        name: `financeWaterList`,
        component: () => import("@/views/finance/water/list.vue"),
        meta: {
            auth: 'finance-water-list',
            menu_name: "流水记录",
        },
    },
    {
        path: `/finance/member_coin`,
        name: `financeMemberCoinList`,
        component: () => import("@/views/finance/member_coin/list.vue"),
        meta: {
            auth: 'finance-member_coin-list',
            menu_name: "用户资产",
        },
    },
    {
        path: `/finance/agent-water`,
        name: `financeAgentWaterList`,
        component: () => import("@/views/finance/water/agent-water.vue"),
        meta: {
            auth: 'finance-agent-water',
            menu_name: "代理商资金明细",
        },
    },
]