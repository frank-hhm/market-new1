export default [
    {
        path: `/system/config`,
        name: `systemConfig`,
        component: () => import("@/views/system/config/index.vue"),
        meta: {
            auth: 'system-config',
            menu_name: "系统设置",
        },
    },
    {
        path: `/system/menus/list`,
        name: `systemConfigMenus`,
        component: () => import("@/views/system/menus/list.vue"),
        meta: {
            auth: 'system-menus-list',
            menu_name: "菜单管理",
        },
    },
    {
        path: `/system/role/list`,
        name: `systeMrole`,
        component: () => import("@/views/system/role/list.vue"),
        meta: {
            auth: 'system-role-list',
            menu_name: "权限管理",
        },
    },
    {
        path: `/system/admin/list`,
        name: `systemAdmin`,
        component: () => import("@/views/system/admin/list.vue"),
        meta: {
            auth: 'system-admin-list',
            menu_name: "管理员",
        },
    },
    {
        path: `/system/config/email`,
        name: `systemConfigEmail`,
        component: () => import("@/views/system/config/email.vue"),
        meta: {
            auth: 'system-config-email',
            menu_name: "邮箱IMAP配置",
        },
    },
    {
        path: `/system/sms`,
        name: `systemConfigSms`,
        component: () => import("@/views/system/config/sms.vue"),
        meta: {
            auth: 'system-config-sms',
            menu_name: "短信配置",
        },
    },
    {
        path: `/system/payment/list`,
        name: `systemPaymentList`,
        component: () => import("@/views/system/other/payment/list.vue"),
        meta: {
            auth: 'system-payment-list',
            menu_name: "收款渠道",
        },
    },
    {
        path: `/system/trade/config`,
        name: `systemConfigTrade`,
        component: () => import("@/views/system/config/trade.vue"),
        meta: {
            auth: 'system-trade-config',
            menu_name: "交易配置",
        },
    },
    {
        path: `/system/config/other`,
        name: `systemConfigOther`,
        component: () => import("@/views/system/config/other.vue"),
        meta: {
            auth: 'system-other-config',
            menu_name: "其他配置",
        },
    },
    {
        path: `/system/market/config`,
        name: `systemConfigMarket`,
        component: () => import("@/views/system/config/market.vue"),
        meta: {
            auth: 'system-market-config',
            menu_name: "行情配置",
        },
    },
    {
        path: `/system/withdraw/config`,
        name: `systemConfigWithdraw`,
        component: () => import("@/views/system/config/withdraw.vue"),
        meta: {
            auth: 'system-withdraw-config',
            menu_name: "提现配置",
        },
    },
    {
        path: `/system/data/cache`,
        name: `systemDataCache`,
        component: () => import("@/views/system/data/cache.vue"),
        meta: {
            auth: 'system-data-cache',
            menu_name: "系统数据缓存",
        },
    },
    {
        path: `/operate-log/list`,
        name: `operateLogsList`,
        component: () => import("@/views/operate-logs/list.vue"),
        meta: {
            auth: 'operate-log-list',
            menu_name: "日志",
        },
    },
    {
        path: `/version/index`,
        name: `versionIndex`,
        component: () => import("@/views/version/index.vue"),
        meta: {
            auth: 'version-index',
            menu_name: "版本管理",
        },
    },
    
]