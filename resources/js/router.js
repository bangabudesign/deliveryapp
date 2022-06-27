import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

// layouts
import LayoutUser from './views/layouts/User'
import LayoutDriver from './views/layouts/Driver'
import LayoutMerchant from './views/layouts/Merchant'
// auth
import Login from './views/auth/Login'
import Register from './views/auth/Register'
// user component
import Splash from './views/pages/Splash'
import Home from './views/pages/Home'
import Search from './views/pages/Search'
import Order from './views/pages/Order'
import Profile from './views/pages/Profile'
import RestoDetail from './views/pages/RestoDetail'
// driver component
import DriverHome from './views/pages/DriverHome'
import DriverOrder from './views/pages/DriverOrder'
import DriverProfile from './views/pages/DriverProfile'
// merchant component
import MerchantHome from './views/pages/MerchantHome'
import MerchantOrder from './views/pages/MerchantOrder'
import MerchantProfile from './views/pages/MerchantProfile'
import MerchantRestoForm from './views/pages/MerchantRestoForm'

const routes = [
    {
        path: '/',
        name: 'Splash',
        component: Splash
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: { requiresGuest: true },
    },
    {
        path: '/register',
        name: 'Register',
        component: Register,
        meta: { requiresGuest: true },
    },
    {
        path: '/app',
        component: LayoutUser,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'home',
                name: 'Home',
                component: Home
            },
            {
                path: 'search',
                name: 'Search',
                component: Search
            },
            {
                path: 'order',
                name: 'Order',
                component: Order
            },
            {
                path: 'profile',
                name: 'Profile',
                component: Profile
            },
            {
                path: 'resto/:id',
                name: 'RestoDetail',
                props: true,
                component: RestoDetail
            }
        ]
    },
    {
        path: '/driver',
        component: LayoutDriver,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'home',
                name: 'DriverHome',
                component: DriverHome
            },
            {
                path: 'order',
                name: 'DriverOrder',
                component: DriverOrder
            },
            {
                path: 'profile',
                name: 'DriverProfile',
                component: DriverProfile
            },
        ]
    },
    {
        path: '/merchant',
        component: LayoutMerchant,
        meta: { requiresAuth: true },
        children: [
            {
                path: 'home',
                name: 'MerchantHome',
                component: MerchantHome
            },
            {
                path: 'order',
                name: 'MerchantOrder',
                component: MerchantOrder
            },
            {
                path: 'profile',
                name: 'MerchantProfile',
                component: MerchantProfile
            },
            {
                path: 'resto/:id?',
                name: 'MerchantRestoForm',
                props: true,
                component: MerchantRestoForm
            }
        ]
    }
]

const router = new VueRouter({
    linkExactActiveClass: 'active',
    mode: 'history',
    routes: routes
})

export default router