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
                secondary: '#FAFAFA',
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

function loggedIn(){
    axios.defaults.headers.common['Authorization'] = 'Bearer ' +Cookies.get('token');
    return Cookies.get('token')
}

router.beforeEach((to, from, next) => {

    if (to.matched.some(record => record.meta.requiresAuth)) {
        if (!loggedIn()) {
            next({
                path: '/login',
                query: { redirect: to.fullPath }
            })
        } else {
            next()
        }
    } else if (to.matched.some(record => record.meta.requiresRegister)) {
        if (!loggedIn()) {
            next({
                path: '/register',
                query: { redirect: to.fullPath }
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
