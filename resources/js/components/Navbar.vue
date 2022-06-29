<template>
    <nav>
        <v-app-bar fixed elevate-on-scroll light color="white" app>
            <v-app-bar-nav-icon v-on:click="drawer = !drawer"></v-app-bar-nav-icon>
            <v-toolbar-title>
                <span class="font-weight-bold">RG ADMIN</span>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn @click="logout" :loading="isLoading" depressed class="grey--text">
                <span>Sign Out</span>
                <v-icon right>mdi-logout</v-icon>
            </v-btn>
        </v-app-bar>
        <v-navigation-drawer v-model="drawer" color="primary" dark app>
            <v-toolbar elevate-on-scroll light color="white">
                <v-list-item-title>
                    <v-avatar class="mr-4" color="white" size="50">
                        <img src="/apple-icon.png" alt="Logo">
                    </v-avatar>
                    <span class="font-weight-bold">RG Delivery</span>
                </v-list-item-title>
                <v-divider vertical></v-divider>
            </v-toolbar>
            <v-list dense shaped>
                <div v-for="(link,i) in links" :key="i">
                    <v-subheader class="pl-5" v-if="link.type == 'subheader'">{{ link.title }}</v-subheader>
                    <template v-else>
                        <v-list-group color="white" v-model="link.title == parent" v-if="link.submenu.length >= 1" :prepend-icon="link.icon">
                            <template v-slot:activator>
                                <v-list-item-title>{{ link.title }}</v-list-item-title>
                            </template>
                            <v-list-item color="white" v-for="sub in link.submenu" :key="sub.title" router :to="{ name: sub.route, query: { q: sub.query }}" v-on:click="parent = link.title">
                                <v-list-item-title><v-icon>mdi-chevron-right</v-icon> {{ sub.title }}</v-list-item-title>
                            </v-list-item>
                        </v-list-group>
                        <v-list-item color="white" v-else router :to="{ name: link.route }" v-on:click="parent = link.title">
                            <v-list-item-icon>
                            <v-icon>{{ link.icon }}</v-icon>
                            </v-list-item-icon>
                            <v-list-item-title>{{ link.title}}</v-list-item-title>
                        </v-list-item>
                    </template>
                </div>
            </v-list>
        </v-navigation-drawer>
    </nav>
</template>
<script>
import Cookies from 'js-cookie';
export default {
    data() {
        return {
            drawer: null,
            mini: false,
            parent: null,
            isLoading: false,
            links: [
                {
                    'title' : 'MENU', 'type' : 'subheader'
                },
                {
                    'title' : 'Dashboard', 'icon' : 'mdi-view-dashboard', 'route' : 'AdminDashboard',
                    'submenu' : []
                },
                {
                    'title' : 'Drivers', 'icon' : 'mdi-motorbike', 'route' : 'AdminDriver',
                    'submenu' : [
                        // {'title' : 'All Drivers', 'route' : 'AdminDriver', 'query' : ''},
                        // {'title' : 'Add New', 'route' : 'AdminDriver', 'query' : ''},
                    ]
                },
                {
                    'title' : 'Merchants', 'icon' : 'mdi-account-multiple', 'route' : 'AdminMerchant',
                    'submenu' : []
                },
                {
                    'title' : 'Users', 'icon' : 'mdi-account-multiple', 'route' : 'AdminUser',
                    'submenu' : []
                },
            ],
        }
    },

    created () {
    },

    computed : {
    },
    
    methods: {
        checkRoute () {
            return this.links.filter((link) => {
                return link.title.toLowerCase().includes('Dashboard'.toLowerCase());
            });
        },
        logout () {
            this.isLoading = true
            axios.post('/api/logout', this.data)
            .then((response) => {
                Cookies.remove('token')
                this.isLoading = false
                this.$router.push({
                    path: '/login'
                })
            })
            .catch((error) => {
                this.isLoading = false
            });
        },
    }
};
</script>