<template>
    <v-app>
        <v-main> 
            <v-container style="height: 100%;">
                <v-card max-width="400" flat class="mx-auto mt-16">
                    <v-card-title>Register</v-card-title>
                    <v-card-subtitle>Mohon lengkapi data diri-mu terlebih dahulu</v-card-subtitle>
                    <v-card-text>
                        <v-text-field label="Nama Lengkap" v-model="data.name" placeholder="Cth. Ahmad Sabar"  :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                        <v-text-field label="Nomor WhatsApp" v-model="data.phone" type="number" placeholder="Cth. 08123456789" :error-messages="error.errors ? error.errors.phone : ''"></v-text-field>
                        <v-text-field label="Password" v-model="data.password" :type="showPassword ? 'text' : 'password'" :append-icon="showPassword ? 'mdi-eye' : 'mdi-eye-off'" @click:append="showPassword = !showPassword" :error-messages="error.errors ? error.errors.password : ''"></v-text-field>
                        <v-checkbox v-model="data.term" label="Saya menyetujui syarat & ketentuan yang berlaku" :error-messages="error.errors ? error.errors.term : ''"></v-checkbox>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn color="primary" class="rounded-pill" block @click="register">Register</v-btn>
                    </v-card-actions>
                    <v-card-actions>
                        <v-btn color="primary" class="rounded-pill" text block router :to="{name: 'Login'}">Sudah punya akun? Log In</v-btn>
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

export default {
    data() {
        return {
            isLoading: false,
            snackbar: false,
            showPassword: false,
            data: {
                name: '',
                phone: '',
                password: '',
                term: 1
            },
            error: {}
        }
    },
    

    methods: {
        register () {
            this.isLoading = true
            axios.post('/api/register', this.data)
            .then((response) => {
                this.isLoading = false
                this.$router.push({ name: 'Login' })
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