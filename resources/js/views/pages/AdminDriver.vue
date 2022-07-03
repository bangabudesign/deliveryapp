<template>
    <v-container fluid>
        <v-card>

            <v-card-title>
                All Drivers
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
                                    <v-text-field label="Nama" v-model="editedItem.name" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="WhasApp" type="number" v-model="editedItem.phone" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.phone : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Motor Type" v-model="editedItem.motor_type" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.motor_type : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="No Polisi" v-model="editedItem.vehicle_number" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.vehicle_number : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select label="Is Working?" :items="[1,0]" v-model="editedItem.is_working" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.is_working : ''"></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Latitude" type="number" v-model="editedItem.lat" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.lat : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Longtitude" type="number" v-model="editedItem.lng" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.lng : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Password" type="password" v-model="editedItem.password" outlined dense hide-details="auto" :error-messages="error.errors ? error.errors.password : ''"></v-text-field>
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

            <v-data-table :headers="headers" :items="drivers" :search="search" :loading="isLoading">

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
                { text: 'Nama', align: 'start', value: 'name' },
                { text: 'WhatsApp', value: 'phone' },
                { text: 'Motor Type', value: 'motor_type' },
                { text: 'No. Polisi', value: 'vehicle_number' },
                { text: 'Status', value: 'is_working' },
                { text: 'Aksi', value: 'actions', sortable: false },
            ],
            drivers: [],
            editedIndex: -1,
            editedItem: {
                name: '',
                phone: '',
                motor_type: '',
                vehicle_number: '',
                lat: '',
                lng: '',
                password: '',
            },
            defaultItem: {
                name: '',
                phone: '',
                motor_type: '',
                vehicle_number: '',
                lat: '',
                lng: '',
                password: '',
            },
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
            if(this.editedIndex === -1 && val === true) {
                this.getLocation()
            }
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
            axios.get('/api/drivers')
            .then((response) => {
                this.isLoading = false
                this.drivers = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
        },

        editItem (item) {
            this.editedIndex = this.drivers.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.drivers.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/drivers/' + this.editedItem.id);
                this.drivers.splice(this.editedIndex, 1)
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
                    const response = await axios.put('/api/drivers/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.drivers[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/drivers', this.editedItem);
                    this.editedItem = response.data.data
                    this.drivers.push(this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            }
        },

        getLocation() {
            navigator.geolocation.getCurrentPosition(
                // Success callback
                (position) => {
                    this.editedItem.lat = position.coords.latitude
                    this.editedItem.lng = position.coords.longitude
                    console.log(position.coords)
                },
                // Optional error callback
                (error) => {
                    console.log("please enable location")
                    this.locationDisable = true
                }
            );
        },
    },
};
</script>