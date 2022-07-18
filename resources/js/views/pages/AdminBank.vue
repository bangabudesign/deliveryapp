<template>
    <v-container fluid>
        <v-card>

            <v-card-title>
                All Banks
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
                                    <v-text-field label="Nama Bank" v-model="editedItem.bank_name" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.bank_name : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Nomor Rekening" v-model="editedItem.account_number" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.account_number : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Atas Nama" v-model="editedItem.account_name" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.account_name : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select label="Status" :items="[1, 0,]" v-model="editedItem.is_active" outlined dense hide-details="auto" :error-messages="error.errors ? error.errors.is_active : ''"></v-select>
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

            <v-data-table :headers="headers" :items="banks" :search="search" :loading="isLoading">

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
                { text: 'Nama Bank', align: 'start', value: 'bank_name' },
                { text: 'Nomor Rekening', value: 'account_number' },
                { text: 'Atas Nama', value: 'account_name' },
                { text: 'Status', value: 'is_active' },
                { text: 'Aksi', value: 'actions', sortable: false },
            ],
            banks: [],
            editedIndex: -1,
            editedItem: {
                bank_name: '',
                account_number: 0,
                account_name: '',
                is_active: 1,
            },
            defaultItem: {
                bank_name: '',
                account_number: '',
                account_name: '',
                is_active: '',
            },
            searchBank: '',
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
            axios.get('/api/banks')
            .then((response) => {
                this.isLoading = false
                this.banks = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
        },

        editItem (item) {
            this.editedIndex = this.banks.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.banks.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/banks/' + this.editedItem.id);
                this.banks.splice(this.editedIndex, 1)
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
                    const response = await axios.put('/api/banks/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.banks[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/banks', this.editedItem);
                    this.editedItem = response.data.data
                    this.banks.push(this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            }
        },
    },
};
</script>