export default [
    // 登录
    {
        path: '/login',
        name: 'login',
        meta: {
            login:false,
            title: '登录'
        },
        component: () => import('@/views/login/index.vue')
    },
]