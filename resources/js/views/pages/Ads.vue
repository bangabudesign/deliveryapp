<template>
    <v-app>
        <v-main>
            <v-container>
                <v-card max-width="400" flat class="mx-auto mt-16">
                <Adsense
                    data-ad-client="ca-pub-1391749186489077"
                    data-ad-slot="6388453684"
                    data-ad-format="auto"
                    data-full-width-responsive="true" class="secondary lighten-4">
                    <span>Loading Ads...</span>
                </Adsense>
                <Adsense
                    data-ad-client="ca-pub-1391749186489077"
                    data-ad-slot="2196544343"
                    data-ad-format="auto"
                    data-full-width-responsive="true" class="secondary lighten-4">
                    <span>Loading Ads...</span>
                </Adsense>
                </v-card>
            </v-container>
        </v-main>
        <BottomNav/>
    </v-app>
</template>
<script>

import BottomNav from '../../components/BottomNav.vue';
export default {
    components: {
        BottomNav
    },
    data() {
        return {
        }
    },
    created() {
    },
    methods: {
    }
};
</script>