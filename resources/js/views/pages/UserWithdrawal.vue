<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Tarik Tunai</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <v-card flat>
                <v-card-text>
                    <div class="dark--text">Saldo: <b>Rp  {{ user ? numberFormat(user.total_balance) : 0 }}</b></div>
                    <div class="danger--text">{{  error.errors ? error.errors.saldo : '' }}</div>
                    <v-divider class="my-5"></v-divider>
                    <v-text-field label="Nominal Penarikan (Min. 50,000)" :rules="amountRules" type="number" v-model="editedItem.amount" outlined dense :error-messages="error.errors ? error.errors.amount : ''"></v-text-field>
                    <v-select label="Bank Tujuan" v-model="editedItem.bank_name" :items="['BCA','MANDIRI','BNI','BRI','BSI']" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.bank_name : ''"></v-select>
                    <v-text-field label="Nomor Rekening" :rules="rules" type="number" v-model="editedItem.account_number" outlined dense :error-messages="error.errors ? error.errors.account_number : ''"></v-text-field>
                    <v-text-field label="Atas Nama" disabled :rules="rules" type="text" v-model="editedItem.account_name" outlined dense persistent-hint hint="Nama rekening tujuan harus sama dengan nama akun terdaftar" :error-messages="error.errors ? error.errors.account_name : ''"></v-text-field>
                    <v-btn color="primary" class="rounded-pill mt-4" block @click="confirmWithdawal" :loading="isLoading">Tarik Tunai</v-btn>
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
                id: '',
                user_id: '',
                bank_id: '',
                amount: 50000,
                admin_fee: 0,
                bank_name: '',
                account_number: '',
                account_name: ''
            },
            error: {}
        }
    },

    created () {
        this.initialize()
    },

    methods: {
        goBack() {
            this.$router.go(-1)
        },

        numberFormat(n = 0, c = 0, d, t) {
        var c = isNaN(c = Math.abs(c)) ? 2 : c, 
            d = d == undefined ? "." : d, 
            t = t == undefined ? "," : t, 
            s = n < 0 ? "-" : "", 
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
            j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        },

        async initialize() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/user`);
                this.user = response.data.data
                this.editedItem.account_name = response.data.data.name
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async confirmWithdawal() {
            this.isLoading = true
            this.editedItem.user_id = this.user.id
            try {
                const response = await axios.post('/api/withdrawals', this.editedItem);
                this.editedItem = response.data.data
                this.$router.replace({ name: 'Home' })
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    }
};
</script>