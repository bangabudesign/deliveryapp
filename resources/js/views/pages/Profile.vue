<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-toolbar-title>Profile</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <v-card flat>
                <v-card-text class="text-center">
                    <v-avatar size="60">
                        <img v-if="user.avatar" :src="user.avatar_url" :alt="user.name">
                        <v-icon v-else color="white" class="tertiary lighten-1" dark>mdi-account</v-icon>
                    </v-avatar>
                    <div class="my-4">
                        <h2 v-text="user.name"></h2>
                        <div class="subtitle-2 mt-2" v-text="'0'+user.phone"></div>
                        <div class="subtitle-2 mt-2" v-text="user.role"></div>
                    </div>
                    <v-btn color="danger" class="rounded-pill" text block @click="logOut">Log Out</v-btn>
                </v-card-text>
            </v-card>
        </v-container>
        <BottomNav/>
    </section>
</template>
<script>
import Cookies from 'js-cookie';
import BottomNav from '../../components/BottomNav.vue';
export default {
    components: {
        BottomNav
    },
    data() {
        return {
            isLoading: false,
            user: {},
        }
    },

    created () {
        this.initialize()
    },

    methods: {
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
        
        async logOut() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/logout`);
                this.isLoading = false
                Cookies.remove('token')
                Cookies.remove('role')
                this.$router.push({ name: 'Splash' })
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    }
};
</script>