<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-toolbar-title>Confirm Deposit</v-toolbar-title>
        </v-app-bar>
        <v-container class="px-0" style="padding-bottom: 64px;">
            <v-list subheader>
                <v-list-item-group>
                    <v-subheader>Detail :</v-subheader>
                    <v-divider></v-divider>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Tgl Deposit'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.created_at"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Status'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.status"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Tgl Konfirmasi'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.confirmed_at || 'Belum Dikonfirmasi'"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Nominal'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.amount"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Admin Fee'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.admin_fee"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-list-item>
                        <v-list-item-content>
                            <v-list-item-title class="subtitle-2 black--text font-weight-bold" v-text="'Total'"></v-list-item-title>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-list-item-action-text class="subtitle-2 black--text font-weight-bold" v-text="deposit.total"></v-list-item-action-text>
                        </v-list-item-action>
                    </v-list-item>
                    <v-subheader class="mt-4">Tujuan Transfer :</v-subheader>
                    <v-divider></v-divider>
                    <template v-if="deposit.bank">
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Nama Bank'"></v-list-item-title>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.bank.bank_name"></v-list-item-action-text>
                            </v-list-item-action>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Nomor Rekening'"></v-list-item-title>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.bank.account_number"></v-list-item-action-text>
                            </v-list-item-action>
                        </v-list-item>
                        <v-list-item>
                            <v-list-item-content>
                                <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="'Atas Nama'"></v-list-item-title>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="deposit.bank.account_name"></v-list-item-action-text>
                            </v-list-item-action>
                        </v-list-item>
                    </template>
                    <v-subheader class="mt-4">Bukti Transfer :</v-subheader>
                    <v-divider></v-divider>
                </v-list-item-group>
            </v-list>
            <v-card flat>
                <v-card-text>
                    <v-file-input v-if="!deposit.receipt" outlined dense :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Upload Gambar" label="Upload Bukti Transfer" @change="uploadImage"></v-file-input>
                    <v-img v-else :src="deposit.receipt_url" class="rounded-lg"></v-img>
                    <v-btn v-if="deposit.receipt" block large color="primary" class="mt-4 rounded-pill" @click="goBack">Selesai</v-btn>
                </v-card-text>
            </v-card>
        </v-container>
    </section>
</template>
<script>
import Cookies from 'js-cookie';
export default {
    props: ['id'],
    data() {
        return {
            isLoading: false,
            imageRules: [
                value => !value || value.size < 2000000 || 'Gambar tidak boleh lebih dari 2 MB!',
            ],
            deposit: {},
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

        async initialize() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/deposits/${this.id}`);
                this.deposit = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        uploadImage(e) {
            let files = e
            if (files.size >= 2000000) {
                console.log('Gambar tidak boleh lebih dari 2 MB!')
            } else {
                let formData = new FormData()
                let image = files
                formData.append('image', image)

                if (files) {
                    this.isLoading = true
                    axios.post(`/api/deposits/receipt/${this.id}`, formData).then((response) => {
                        this.deposit = response.data.data
                        this.isLoading = false
                        this.errors = {}
                    }).catch((error) => {
                        if (error.response.status = 422) {
                            this.errors = error.response.data.errors
                            // Error notification
                            console.log(error.response.data.message)
                        }
                        this.isLoading = false
                    })
                }
            }
        },
    }
};
</script>