<template>
    <div>
        <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined v-for="(item, index) in items" :key="item.id" route :to="{ name: 'MerchantRestoForm', params: {id: item.id} }">
            <v-avatar class="ma-3 rounded-lg" size="100" rounded>
                <v-img :src="item.image_url"></v-img>
            </v-avatar>
            <div style="max-width: calc(100% - 124px);">
                <v-card-title v-text="item.name" class="pl-0 d-inline-block text-truncate subtitle-2" style="max-width: 100%;"></v-card-title>
                <v-card-subtitle class="pl-0 caption">
                    <div v-text="item.menu" class="text-muted"></div>
                    <div>Jarak 0.8 km</div>
                </v-card-subtitle>
            </div>
        </v-card>
    </div>
</template>

<script>
    export default {
        props: {
            items: {
                type: Array,
                required: true,
            }
        },
        mounted() {
        }
    }
</script>
