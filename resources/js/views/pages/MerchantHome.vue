<template>
    <section>
        <v-app-bar app color="success" dark inverted-scroll elevation-1>
            <v-app-bar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-app-bar-title>
            <v-spacer></v-spacer>
        </v-app-bar>
        <v-toolbar color="success" dark extended flat dense class="mx-auto pt-3">
            <v-toolbar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-toolbar-title>
            <v-spacer></v-spacer>
        </v-toolbar>
        <v-container class="bg-white" style="padding-bottom: 64px;">
            <v-card class="mx-auto mb-3 rounded-lg" style="margin-top: -32px;">
                <v-card-title>
                    <div>
                        <small class="dark--text font-weight-normal">Saldo</small>
                        <div class="dark--text" style="margin-top: -8px;">Rp  {{ user ? numberFormat(user.total_balance) : 0 }}</div>
                    </div>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-title>
                    <v-row style="margin-left:-4px; margin-right:-4px;">
                        <v-col cols="6" class="px-1" v-for="menu in menus" :key="menu.id">
                            <v-btn :color="menu.color" dark elevation="0" class="rounded-pill py-4" x-small block route :to="{ name: 'Search', query: {category: menu.label.toLowerCase()} }">
                                <v-icon color="white" left>{{menu.icon}}</v-icon> {{menu.label}}</v-btn>
                        </v-col>
                    </v-row>
                </v-card-title>
            </v-card>
            <!-- content -->
            <template v-if="isLoading || restaurants.length == 0">
                <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                    <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                    <div style="max-width: calc(100% - 124px);" class="ma-3">
                        <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                        <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                    </div>
                </v-card>
                <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                    <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                    <div style="max-width: calc(100% - 124px);" class="ma-3">
                        <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                        <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                    </div>
                </v-card>
                <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                    <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                    <div style="max-width: calc(100% - 124px);" class="ma-3">
                        <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                        <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                    </div>
                </v-card>
                <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                    <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                    <div style="max-width: calc(100% - 124px);" class="ma-3">
                        <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                        <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                    </div>
                </v-card>
            </template>
            <MerchantCardResto v-else :items="restaurants"/>
            <!-- content -->
            <v-btn color="primary" dark fixed right bottom fab style="bottom: 80px;" route :to="{ name: 'MerchantRestoForm' }">
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </v-container>
        <MerchantBottomNav/>
    </section>
</template>
<style>
    #map { height: 300px; z-index: 1; }
</style>
<script>

import MerchantCardResto from '../../components/MerchantCardResto.vue';
import MerchantBottomNav from '../../components/MerchantBottomNav.vue';
export default {
    components: {
        MerchantCardResto,
        MerchantBottomNav
    },
    data() {
        return {
            isLoading: false,
            user: {},
            menus: [
                // {
                //     id: 1,
                //     icon: 'mdi-plus-circle',
                //     color: '#4CAF50',
                //     icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                //     label: 'Top Up'
                // },
                {
                    id: 2,
                    icon: 'mdi-arrow-up-bold-circle',
                    color: '#7309a9',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Kirim'
                },
                {
                    id: 3,
                    icon: 'mdi-arrow-down-bold-circle',
                    color: '#09bbbd',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Tarik'
                },
            ],
            restaurants: [],
            error: {}
        }
    },

    created () {
        this.initialize()
        this.getRestaurants()
    },

    methods: {
        numberFormat(n = 0, c = 0, d, t) {
        var c = isNaN(c = Math.abs(c)) ? 2 : c, 
            d = d == undefined ? "." : d, 
            t = t == undefined ? "," : t, 
            s = n < 0 ? "-" : "", 
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
            j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        },
        async initialize() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/user`);
                this.user = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        async getRestaurants() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/restaurants`);
                this.restaurants = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    }
};
</script>