<template>
    <v-app>
        <v-main>
            <v-container>
                <v-card max-width="400" flat class="mx-auto mt-16">
                    <v-card-title>
                        <v-img class="mx-auto" src="/images/logo-colored.svg" alt="Logo Radjago" max-height="40" contain position="center center"></v-img>
                    </v-card-title>
                    <v-card-text class="pt-5 text-center">
                        <h1 calass="display-1">Coming Soon</h1>
                        <div class="text-h6 pt-5">Kami sedang mempersiapkan sesuatu yang hebat untuk Anda.</div>
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