<template>
    <v-container fluid>
        <v-row class="mb-5" v-if="stats">
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-title>
                        {{ numberFormat(stats.trx_count) }} Trx
                    </v-card-title>
                    <v-card-text>
                        Total Transaksi Bulan Ini
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-title>
                        Rp {{ numberFormat(stats.profit_sharing) }}
                    </v-card-title>
                    <v-card-text>
                        Total Sharing Bulan Ini
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-title>
                        {{ numberFormat(stats.user_premium) }} Orang
                    </v-card-title>
                    <v-card-text>
                        Total User Premium
                    </v-card-text>
                </v-card>
            </v-col>
            <v-col cols="12" sm="6" md="3">
                <v-card>
                    <v-card-title>
                        {{ numberFormat(stats.user_achieve) }} Orang
                    </v-card-title>
                    <v-card-text>
                        User Premium Capai Target
                    </v-card-text>
                </v-card>
            </v-col>
        </v-row>
        <v-card>

            <v-card-title>
                Profit Sharing History
                <v-spacer></v-spacer>
                <v-text-field class="mr-3" v-model="search" append-icon="mdi-magnify" label="Search"  dense hide-details></v-text-field>
                <v-btn color="primary" @click="sendProfit">Bagikan Profit</v-btn>
                <!-- <v-dialog v-model="dialog" max-width="500">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn color="primary" v-bind="attrs" v-on="on">Bagikan Profit</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="text-h5">{{ formTitle }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-select label="User Premium" v-model="editedItem.user_id" :item-text="'name'" :item-value="'id'" :items="users" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.user_id : ''">
                                        <template v-slot:prepend-item>
                                            <v-list-item>
                                                <v-list-item-content>
                                                    <v-text-field v-model="searchUser" placeholder="Search" @input="getUsers" outlined dense hide-details="auto" append-icon="mdi-magnify"></v-text-field>
                                                </v-list-item-content>
                                            </v-list-item>
                                            <v-divider></v-divider>
                                        </template>
                                    </v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Nominal" type="number" v-model="editedItem.amount" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.amount : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Admin Fee" type="number" v-model="editedItem.admin_fee" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.admin_fee : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select label="Status" :items="['PENDING', 'SUCCESS', 'CANCEL', 'REJECTED']" v-model="editedItem.status" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.status : ''"></v-select>
                                </v-col>
                            </v-row>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="secondary" text @click="close">Cancel</v-btn>
                            <v-btn  color="primary" text @click="save" :loading="isLoading">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog> -->

                <v-dialog v-model="dialogDelete" max-width="290">
                    <v-card>
                        <v-card-title class="text-h5">Hapus data ini?</v-card-title>
                        <v-card-text>Data yang telah dihapus tidak dapat di kembalikan.</v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="secondary" text @click="deleteItemConfirm" :loading="isLoading">OK</v-btn>
                            <v-btn color="primary" text @click="closeDelete">Cancel</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>

            </v-card-title>

            <v-data-table :headers="headers" :items="sharings" :search="search" :loading="isLoading">

                <template v-slot:item.nominal="{ item }">
                    {{ numberFormat(item.amount) }}
                </template>

            </v-data-table>

        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            isLoading: false,
            dialog: false,
            dialogDelete: false,
            search: '',
            rules: [
                value => !!value || 'Wajib Disi.',
            ],
            headers: [
                { text: 'Tanggal', align: 'start', value: 'created_at' },
                { text: 'User', value: 'user.name' },
                { text: 'Nominal', value: 'nominal' },
                { text: 'Catatan', value: 'notes' },
            ],
            sharings: [],
            editedIndex: -1,
            editedItem: {
                order_id: '',
                merchant_id: '',
                amount: 0,
                notes: '',
            },
            defaultItem: {
                order_id: '',
                merchant_id: '',
                amount: 0,
                notes: '',
            },
            searchUser: '',
            users: [],
            stats: {},
            error: {}
        }
    },

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Tambah Data' : 'Edit Data'
        },
    },

    watch: {
        dialog (val) {
            val || this.close()
            this.getUsers()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },

    created () {
        this.initialize()
        this.getStats()
    },

    methods: {
        numberFormat(n = 0, c = 0, d, t) {
        var c = isNaN(c = Math.abs(c)) ? 2 : c, 
            d = d == undefined ? "." : d, 
            t = t == undefined ? "," : t, 
            s = n < 0 ? "-" : "", 
            i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
            j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        },
        initialize () {
            this.isLoading = true
            axios.get('/api/sharings')
            .then((response) => {
                this.isLoading = false
                this.sharings = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
        },

        editItem (item) {
            this.editedIndex = this.sharings.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.sharings.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/sharings/' + this.editedItem.id);
                this.sharings.splice(this.editedIndex, 1)
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
            this.closeDelete()
        },

        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
            this.error = {}
        },

        closeDelete () {
            this.dialogDelete = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
            this.error = {}
        },

        async save () {
            if (this.editedIndex > -1) {
                this.isLoading = true
                try {
                    const response = await axios.put('/api/sharings/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.sharings[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/sharings', this.editedItem);
                    this.editedItem = response.data.data
                    this.sharings.push(this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            }
        },

        async getUsers () {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/users?q=${this.searchUser}&premium=1&limit=10`);
                this.users = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async getStats () {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/sharings/stats`);
                this.stats = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async sendProfit () {
            this.isLoading = true
            try {
                const response = await axios.post(`/api/sharings/send`);
                if(response) {
                    this.initialize()
                }
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    },
};
</script>