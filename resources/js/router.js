import Vue from 'vue';

import VueRouter from 'vue-router';
Vue.use(VueRouter);

// layouts
import LayoutUser from './views/layouts/User'
import LayoutDriver from './views/layouts/Driver'
import LayoutMerchant from './views/layouts/Merchant'
import LayoutAdmin from './views/layouts/Admin'
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
import DriverDeposit from './views/pages/DriverDeposit'
import DriverDepositDetail from './views/pages/DriverDepositDetail'
// merchant component
import MerchantHome from './views/pages/MerchantHome'
import MerchantOrder from './views/pages/MerchantOrder'
import MerchantProfile from './views/pages/MerchantProfile'
import MerchantRestoForm from './views/pages/MerchantRestoForm'
// admin component
import AdminDashboard from './views/pages/AdminDashboard'
import AdminBanner from './views/pages/AdminBanner'
import AdminDriver from './views/pages/AdminDriver'
import AdminMerchant from './views/pages/AdminMerchant'
import AdminUser from './views/pages/AdminUser'
import AdminDeposit from './views/pages/AdminDeposit'
import AdminTransaction from './views/pages/AdminTransaction'
import AdminBonus from './views/pages/AdminBonus'
import AdminBank from './views/pages/AdminBank'

const routes = [
    {
        path: '/',
        name: 'Splash',
        component: Splash,
        meta: { requiresGuest: true },
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
                path: '/app',
                redirect: '/app/home'
            },
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
                path: '/driver',
                redirect: '/driver/home'
            },
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
            {
                path: 'deposit',
                name: 'DriverDeposit',
                component: DriverDeposit
            },
            {
                path: 'deposit/:id',
                name: 'DriverDepositDetail',
                props: true,
                component: DriverDepositDetail
            }
        ]
    },
    {
        path: '/merchant',
        component: LayoutMerchant,
        meta: { requiresAuth: true },
        children: [
            {
                path: '/merchant',
                redirect: '/merchant/home'
            },
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
    },
    {
        path: '/admin',
        component: LayoutAdmin,
        meta: { requiresAuth: true },
        children: [
            {
                path: '/admin',
                redirect: '/admin/dashboard'
            },
            {
                path: 'dashboard',
                name: 'AdminDashboard',
                component: AdminDashboard
            },
            {
                path: 'banners',
                name: 'AdminBanner',
                component: AdminBanner
            },
            {
                path: 'drivers',
                name: 'AdminDriver',
                component: AdminDriver
            },
            {
                path: 'merchants',
                name: 'AdminMerchant',
                component: AdminMerchant
            },
            {
                path: 'users',
                name: 'AdminUser',
                component: AdminUser
            },
            {
                path: 'deposits',
                name: 'AdminDeposit',
                component: AdminDeposit
            },
            {
                path: 'transactions',
                name: 'AdminTransaction',
                component: AdminTransaction
            },
            {
                path: 'bonuses',
                name: 'AdminBonus',
                component: AdminBonus
            },
            {
                path: 'banks',
                name: 'AdminBank',
                component: AdminBank
            },
        ]
    }
]

const router = new VueRouter({
    linkExactActiveClass: 'active',
    mode: 'history',
    routes: routes
})

export default router