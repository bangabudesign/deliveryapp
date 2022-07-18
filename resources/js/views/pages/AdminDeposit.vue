<template>
    <v-container fluid>
        <v-card>

            <v-card-title>
                All Deposits
                <v-spacer></v-spacer>
                <v-text-field class="mr-3" v-model="search" append-icon="mdi-magnify" label="Search"  dense hide-details></v-text-field>
                <v-dialog v-model="dialog" max-width="500">
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
                                    <v-select label="Tujuan Transfer" v-model="editedItem.bank_id" :item-value="'id'" :items="banks" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.bank_id : ''">
                                        <template v-slot:item="{item}">
                                            {{ item.bank_name}}
                                        </template>
                                        <template v-slot:selection="{item}">
                                            {{ item.bank_name + ' - ' + item.account_number + ' - ' + item.account_name}}
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
                </v-dialog>

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

            <v-data-table :headers="headers" :items="deposits" :search="search" :loading="isLoading">

                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2"  @click="editItem(item)">mdi-pencil</v-icon>
                    <!-- <v-icon small @click="deleteItem(item)">mdi-delete</v-icon> -->
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
                { text: 'Nama', value: 'user.name' },
                { text: 'WhatsApp', value: 'user.phone' },
                { text: 'Nominal', value: 'total' },
                { text: 'Status', value: 'status' },
                { text: 'Confirmed At', value: 'confirmed_at' },
                { text: 'Aksi', value: 'actions', sortable: false },
            ],
            deposits: [],
            editedIndex: -1,
            editedItem: {
                user_id: '',
                bank_id: '',
                amount: 0,
                admin_fee: 0,
                status: 'PENDING',
                confirmed_at: '',
            },
            defaultItem: {
                user_id: '',
                bank_id: '',
                amount: 0,
                admin_fee: 0,
                status: '',
                confirmed_at: '',
            },
            searchUser: '',
            banks: [],
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
            this.getBanks()
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
            axios.get('/api/deposits')
            .then((response) => {
                this.isLoading = false
                this.deposits = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
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

        editItem (item) {
            this.editedIndex = this.deposits.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.deposits.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/deposits/' + this.editedItem.id);
                this.deposits.splice(this.editedIndex, 1)
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
                    const response = await axios.put('/api/deposits/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.deposits[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/deposits', this.editedItem);
                    this.editedItem = response.data.data
                    this.deposits.push(this.editedItem)
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