<template>
    <section>
        <v-app-bar app color="primary" dark inverted-scroll elevation-1>
            <v-app-bar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-app-bar-title>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>mdi-bell</v-icon>
            </v-btn>
        </v-app-bar>
        <v-toolbar color="primary" dark extended flat dense class="mx-auto pt-3">
            <v-toolbar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>mdi-bell</v-icon>
            </v-btn>
        </v-toolbar>
        <v-container class="bg-white" style="padding-bottom: 64px;">
            <v-card class="mx-auto rounded-pill" flat style="margin-top: -32px;">
                <v-text-field class="rounded-pill" v-model="searchTerm" placeholder="mau makan apa?" solo prepend-inner-icon="mdi-magnify" @input="inputSearch"></v-text-field>
            </v-card>
            <!-- menu -->
            <v-row class="mb-3">
                <v-col cols="3" v-for="menu in menus" :key="menu.id">
                    <v-card class="d-flex flex-column align-center justify-center" flat route :to="{ name: 'Search', query: {category: menu.label.toLowerCase()} }">
                        <v-avatar>
                            <v-icon :style="{backgroundColor: menu.color}" dark>{{menu.icon}}</v-icon>
                            <!-- <img :src="menu.icon_url" :alt="menu.label"> -->
                        </v-avatar>
                        <span class="text-center mt-2 caption" v-text="menu.label"></span>
                    </v-card>
                </v-col>
            </v-row>
            <!-- end menu -->
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
            <CardResto v-else :items="restaurants"/>
            <!-- content -->
        </v-container>
        <BottomNav/>
    </section>
</template>
<script>

import CardResto from '../../components/CardResto.vue';
import BottomNav from '../../components/BottomNav.vue';
export default {
    components: {
        CardResto,
        BottomNav
    },
    data() {
        return {
            isLoading: false,
            searchTerm: '',
            menus: [
                {
                    id: 1,
                    icon: 'mdi-map-marker-radius',
                    color: '#2b7bec',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Disekitar'
                },
                {
                    id: 2,
                    icon: 'mdi-rice',
                    color: '#ff6a30',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Makanan'
                },
                {
                    id: 3,
                    icon: 'mdi-glass-mug',
                    color: '#a46dfe',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Minuman'
                },
                {
                    id: 4,
                    icon: 'mdi-food',
                    color: '#93d31b',
                    icon_url: 'https://cdn.vuetifyjs.com/images/cards/cooking.png',
                    label: 'Snack'
                }
            ],
            restaurants: [],
        }
    },

    created () {
        this.initialize()
    },

    methods: {
        async initialize() {
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
        inputSearch() {
            if(this.searchTerm.length > 2) {
                this.$router.push({ name: 'Search', query: { q: this.searchTerm } })
            }
        }
    }
};
</script>