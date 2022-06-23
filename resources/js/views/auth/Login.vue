<template>
    <v-app>
        <v-main> 
            <v-container style="height: 100%;">
                <v-card max-width="400" flat class="mx-auto mt-16">
                    <v-card-title>Log In</v-card-title>
                    <v-card-subtitle>Silakan masuk dengan nomor WhatsApp-mu yang terdaftar</v-card-subtitle>
                    <v-card-text>
                        <v-text-field label="Nomor WhatsApp" v-model="data.username" type="number" placeholder="Cth. 08123456789" :error-messages="error.errors ? error.errors.username : ''"></v-text-field>
                        <v-text-field label="Password" v-model="data.password" :type="showPassword ? 'text' : 'password'" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword = !showPassword" :error-messages="error.errors ? error.errors.password : ''"></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" class="rounded-pill" block @click="login">Log In</v-btn>
                    </v-card-actions>
                    <v-card-actions>
                        <v-btn color="primary" class="rounded-pill" text block router :to="{name: 'Register'}">Belum punya akun? Register</v-btn>
                    </v-card-actions>
                </v-card>
                <v-snackbar v-model="snackbar" timeout="2000">
                    {{ error.message }}
                    <template v-slot:action="{ attrs }">
                        <v-btn color="accent" text v-bind="attrs" click="snackbar = false">Close</v-btn>
                    </template>
                </v-snackbar>
            </v-container>
        </v-main>
    </v-app>
</template>
<script>
import Cookies from 'js-cookie'
export default {
    data() {
        return {
            isLoading: false,
            snackbar: false,
            showPassword: false,
            data: {
                username: '',
                password: '',
            },
            error: {}
        }
    },
    
    methods: {
        login () {
            this.isLoading = true
            axios.post('/api/login', this.data)
            .then((response) => {
                this.isLoading = false
                Cookies.set('token', response.data.token, { expires: 30 })
                if(response.data.role == 'USER') {
                    this.$router.push({ name: 'Home' })
                }else if(response.data.role == 'DRIVER') {
                    this.$router.push({ name: 'DriverHome' })
                }
            })
            .catch((error) => {
                this.isLoading = false
                this.error = error.response.data
                this.snackbar = true
            });
        },
    }
};
</script>