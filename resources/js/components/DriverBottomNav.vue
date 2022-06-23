<template>
    <v-bottom-navigation color="primary" fixed grow>
        <v-btn route :to="{ name: 'DriverHome' }">
            <span>Home</span>
            <v-icon>mdi-home</v-icon>
        </v-btn>
        <v-btn route :to="{ name: 'DriverOrder' }">
            <span>Orders</span>
            <v-icon>mdi-file-document</v-icon>
        </v-btn>
        <v-btn route :to="{ name: 'DriverProfile' }">
            <span>Account</span>
            <v-icon>mdi-account</v-icon>
        </v-btn>
    </v-bottom-navigation>
</template>

<script>
    export default {
    }
</script>
