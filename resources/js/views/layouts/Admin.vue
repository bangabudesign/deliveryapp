<template>
    <v-app>
        <Navbar></Navbar>
        <v-main style="background-color: #EAF1FA"> 
            <v-container fluid>
                <router-view></router-view>
            </v-container>
        </v-main>
    </v-app>
</template>
<script>
import Navbar from '../../components/Navbar.vue';
export default {
    components: {
        Navbar,
    },
    data() {
        return {
            message: 'Hello World'
        }
    }
};
</script>