<template>
    <v-bottom-navigation color="primary" fixed grow>
        <v-btn route :to="{ name: 'Home' }">
            <span>Home</span>
            <v-icon>mdi-home</v-icon>
        </v-btn>
        <v-btn route :to="{ name: 'Ads' }">
            <span>Ads</span>
            <v-icon>mdi-bullhorn</v-icon>
        </v-btn>
        <v-btn route :to="{ name: 'Order' }">
            <span>Orders</span>
            <v-icon>mdi-file-document</v-icon>
        </v-btn>
        <v-btn route :to="{ name: 'Profile' }">
            <span>Account</span>
            <v-icon>mdi-account</v-icon>
        </v-btn>
    </v-bottom-navigation>
</template>

<script>
    export default {
    }
</script>
