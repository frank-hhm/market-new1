export default [
    {
        path: `/product/list`,
        name: `productList`,
        component: () => import("@/views/product/list.vue"),
        meta: {
            auth: 'product-list',
            menu_name: "产品管理",
        },
    },
    {
        path: `/product/sector`,
        name: `productSector`,
        component: () => import("@/views/product-sector/list.vue"),
        meta: {
            auth: 'product-sector',
            menu_name: "板块管理",
        },
    },
]