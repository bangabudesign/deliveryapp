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
            <!-- slideshow -->
            <v-card flat class="mb-6 rounded-lg">
                <v-carousel cycle hide-delimiter-background :show-arrows="false" height="auto" light>
                    <v-carousel-item v-for="slide in slides" :key="slide.id">
                        <v-img :aspect-ratio="16/9" :src="slide.image" class="rounded-lg"></v-img>
                    </v-carousel-item>
                </v-carousel>
            </v-card>
            <!-- end slideshow -->
            <!-- content -->
            <h2 class="text-h6 mb-4">Mau makan apa hari ini?</h2>
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
<style>
    .v-carousel__controls .v-btn--icon.v-size--small {
        height: 10px;
        width: 10px;
    }
</style>
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
            slides: [],
            restaurants: [],
            user: {}
        }
    },

    created () {
        this.initialize()
        this.getSlides()
    },

    methods: {
        async initialize() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/user`);
                this.user = response.data.data
                this.getRestaurants()
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        async getSlides() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/slides`);
                this.slides = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        async getRestaurants() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/restaurants?near_by=${this.user.lat},${this.user.lng}`);
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