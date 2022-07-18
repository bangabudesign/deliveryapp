<template>
    <v-container fluid>
        <v-card>

            <v-card-title>
                All Bonuses
                <v-spacer></v-spacer>
                <v-text-field class="mr-3" v-model="search" append-icon="mdi-magnify" label="Search"  dense hide-details></v-text-field>
                <!-- <v-dialog v-model="dialog" max-width="500">
                    <template v-slot:activator="{ on, attrs }">
                        <v-btn color="primary" v-bind="attrs" v-on="on">Tambah Data</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="text-h5">{{ formTitle }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-row>
                                <v-col cols="12">
                                    <v-select label="Driver" v-model="editedItem.user_id" :item-text="'name'" :item-value="'id'" :items="drivers" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.user_id : ''">
                                        <template v-slot:prepend-item>
                                            <v-list-item>
                                                <v-list-item-content>
                                                    <v-text-field v-model="searchUser" placeholder="Search" @input="getDrivers" outlined dense hide-details="auto" append-icon="mdi-magnify"></v-text-field>
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

            <v-data-table :headers="headers" :items="bonuses" :search="search" :loading="isLoading">

                <!-- <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2"  @click="editItem(item)">mdi-pencil</v-icon>
                    <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
                </template> -->

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
                { text: 'Order Id', value: 'order_id' },
                { text: 'Merchant', value: 'merchant.name' },
                { text: 'Nominal', value: 'amount' },
                { text: 'Catatan', value: 'notes' },
            ],
            bonuses: [],
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
            drivers: [],
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
            this.getDrivers()
        },
        dialogDelete (val) {
            val || this.closeDelete()
        },
    },

    created () {
        this.initialize()
    },

    methods: {
        initialize () {
            this.isLoading = true
            axios.get('/api/bonuses')
            .then((response) => {
                this.isLoading = false
                this.bonuses = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
        },

        editItem (item) {
            this.editedIndex = this.bonuses.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.bonuses.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/bonuses/' + this.editedItem.id);
                this.bonuses.splice(this.editedIndex, 1)
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
                    const response = await axios.put('/api/bonuses/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.bonuses[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/bonuses', this.editedItem);
                    this.editedItem = response.data.data
                    this.bonuses.push(this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            }
        },

        async getDrivers () {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/drivers?q=${this.searchUser}&limit=10`);
                this.drivers = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
    },
};
</script>