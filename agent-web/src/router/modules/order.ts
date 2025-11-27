export default [
    {
        path: `/product/order/list`,
        name: `productOrderList`,
        component: () => import("@/views/product-order/list.vue"),
        meta: {
            auth: 'product-order-list',
            menu_name: "平仓记录",
        },
    },
    {
        path: `/product/order/chi`,
        name: `productOrderChi`,
        component: () => import("@/views/product-order/chi.vue"),
        meta: {
            auth: 'product-order-chi',
            menu_name: "持仓记录",
        },
    },
    {
        path: `/product/order/gua-list`,
        name: `productOrderGuaList`,
        component: () => import("@/views/product-order/gua-list.vue"),
        meta: {
            auth: 'product-order-gua-list',
            menu_name: "当前挂单",
        },
    },
    {
        path: `/product/order/moni-list`,
        name: `productOrderMoniList`,
        component: () => import("@/views/product-order/moni-list.vue"),
        meta: {
            auth: 'product-order-moni-list',
            menu_name: "模拟平仓记录",
        },
    },
    {
        path: `/product/order/moni-chi`,
        name: `productOrderMoniChi`,
        component: () => import("@/views/product-order/moni-chi.vue"),
        meta: {
            auth: 'product-order-moni-chi',
            menu_name: "模拟持仓记录",
        },
    },
]