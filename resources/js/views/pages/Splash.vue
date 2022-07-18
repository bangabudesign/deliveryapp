<template>
    <v-app>
        <v-main>
            <v-container>
                <v-card max-width="400" flat class="mx-auto mt-16">
                    <v-card-title>
                        <v-img src="/images/logo-colored.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
                    </v-card-title>
                    <v-card-text class="pt-5">
                        <v-carousel cycle hide-delimiter-background :show-arrows="false" height="auto" light>
                            <v-carousel-item v-for="slide in slides" :key="slide.id">
                                <v-img :aspect-ratio="16/9" :src="slide.image" class="rounded-xl"></v-img>
                                <div class="mt-4 mb-16">
                                    <h2 class="subtitle-1 font-weight-bold" v-text="slide.title"></h2>
                                    <p class="caption" v-text="slide.subtitle"></p>
                                </div>
                            </v-carousel-item>
                        </v-carousel>
                    </v-card-text>
                    <v-card-text>
                        <v-btn color="primary" class="rounded-pill mb-4" large block router :to="{name: 'Login'}">Login</v-btn>
                        <v-btn v-if="deferredPrompt" color="success" class="rounded-pill mb-4" outlined large block @click="install">Install Aplikasi <v-icon right>mdi-cloud-download</v-icon></v-btn>
                    </v-card-text>
                </v-card>
            </v-container>
        </v-main>
    </v-app>
</template>
<script>

export default {
    data() {
        return {
            deferredPrompt: null,
            slides: [
                {
                    id: 1,
                    image: '/images/splash/1.jpeg',
                    title: "Selamat Datang!",
                    subtitle: "Aplikasi yang buat hidupmu lebih nyaman. Siap bantu kamu kapan pun, di mana pun."
                },
                {
                    id: 2,
                    image: '/images/splash/2.jpeg',
                    title: "Pesan Makan & Belanja",
                    subtitle: "Lagi pengen makan sesuatu? RG Delivery siap bantu beliin gak pake lama."
                },
                {
                    id: 3,
                    image: '/images/splash/3.jpeg',
                    title: "Transportasi & Logistik",
                    subtitle: "Anterin kamu jalan atau ambilin barang lebih gampang tinggal ngeklik doang."
                }
            ]
        }
    },
    created() {
        window.addEventListener("beforeinstallprompt", (e) => {
        e.preventDefault();
            // Stash the event so it can be triggered later.
            this.deferredPrompt = e;
        });
        window.addEventListener("appinstalled", () => {
            this.deferredPrompt = null;
        });
    },
    methods: {
        async install() {
            this.deferredPrompt.prompt();
        }
    }
};
</script>