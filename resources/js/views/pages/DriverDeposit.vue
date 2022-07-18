<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>To Up Saldo</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <v-card flat>
                <v-card-text>
                    <v-text-field label="Nama Driver" disabled :rules="rules" v-model="user.name" outlined dense :error-messages="error.errors ? error.errors.user_id : ''"></v-text-field>
                    <v-select label="Tujuan Transfer" v-model="editedItem.bank_id" :item-value="'id'" :items="banks" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.bank_id : ''">
                        <template v-slot:item="{item}">
                            {{ item.bank_name}}
                        </template>
                        <template v-slot:selection="{item}">
                            {{ item.bank_name + ' - ' + item.account_number + ' - ' + item.account_name}}
                        </template>
                    </v-select>
                    <v-text-field label="Nominal" :rules="amountRules" type="number" v-model="editedItem.amount" outlined dense :error-messages="error.errors ? error.errors.amount : ''"></v-text-field>
                    <!-- <v-file-input outlined dense :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Upload Gambar" label="Upload Bukti Transfer" @change="uploadImage"></v-file-input> -->
                    <v-btn color="primary" class="rounded-pill mt-4" block @click="confirmDeposit" :loading="isLoading">Top Up</v-btn>
                </v-card-text>
            </v-card>
        </v-container>
    </section>
</template>
<script>
import Cookies from 'js-cookie';
export default {
    data() {
        return {
            isLoading: false,
            rules: [
                value => !!value || 'Wajib Disi.',
            ],
            amountRules: [value => {
                if(value < 50000) return 'Nominal deposit minimal Rp 50,000'
                return true
            }],
            user: {},
            banks: [],
            editedItem: {
                user_id: '',
                bank_id: '',
                amount: 50000,
                admin_fee: 0,
            },
            error: {}
        }
    },

    created () {
        this.initialize()
        this.getBanks()
    },

    methods: {
        goBack() {
            this.$router.go(-1)
        },

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

        async getBanks() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/banks`);
                this.banks = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async confirmDeposit() {
            this.isLoading = true
            this.editedItem.user_id = this.user.id
            try {
                const response = await axios.post('/api/deposits', this.editedItem);
                this.editedItem = response.data.data
                this.$router.push({ name: 'DriverHome' })
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    }
};
</script>