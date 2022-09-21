require('./bootstrap');
import Vuetify from 'vuetify';
import 'vuetify/dist/vuetify.min.css';
import Cookies from 'js-cookie';
Vue.use(Vuetify)

const opts = {
    theme: {
        themes: {
            light: {
                primary: '#7309a9',
                accent: '#FF9800',
                secondary: '#757575',
                tertiary: '#09bbbd',
                error: '#FF5252',
                info: '#2196F3',
                warning: '#FFC107',
                success: '#4CAF50',
                danger: '#F44336',
                dark: '#1f1d31',
                muted: '#f4f5fa'
            },
        },
    },
}

export default new Vuetify(opts)

window.Vue = require('vue').default;

import Vue from 'vue';
import router from './router.js';

import Ads from 'vue-google-adsense'
Vue.use(require('vue-script2'))
Vue.use(Ads.Adsense)
Vue.use(Ads.InArticleAdsense)
Vue.use(Ads.InFeedAdsense)

function loggedIn(){
    axios.defaults.headers.common['Authorization'] = 'Bearer ' +Cookies.get('token');
    return Cookies.get('token')
}

router.beforeEach((to, from, next) => {

    let role = Cookies.get('role')
    let path = to.path.split("/")[1]

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!loggedIn()) {
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            })
        } else {
            if (role == 'USER' && path == 'app') {
                next()
            } else if (role == 'DRIVER' && path == 'driver') {
                next()
            }  else if (role == 'MERCHANT' && path == 'merchant') {
                next()
            }  else if (role == 'ADMIN' && path == 'admin') {
                next()
            } else {
                Cookies.remove('token')
                Cookies.remove('role')
                router.push({ name: 'Splash' })
            }
        }
    } else if (to.matched.some(record => record.meta.requiresGuest)) {
        if (loggedIn()) {
            next({
                path: '/app/home',
            })
        } else {
            next()
        }
    } else {
        next();
    }    
})

const app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
    router
});
