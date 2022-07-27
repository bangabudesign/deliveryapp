<template>
    <v-container fluid>
        <v-card>

            <v-card-title>
                All Banners
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
                                    <v-file-input v-if="editedIndex == -1" :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Ukuran 500x200px" prepend-icon="mdi-camera" label="Gambar Ukuran 500 x 200 px" hide-details="auto" @change="uploadImage"></v-file-input>
                                    <v-img v-else :aspect-ratio="5/2" :src="editedItem.image_url" class="rounded-lg"></v-img>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Nama" v-model="editedItem.name" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Link" v-model="editedItem.link" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.link : ''"></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field label="Urutan" v-model="editedItem.order" type="number" outlined dense hide-details="auto" :rules="rules" :error-messages="error.errors ? error.errors.order : ''"></v-text-field>
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

            <v-data-table :headers="headers" :items="banners" :search="search" :loading="isLoading">
                <template v-slot:item.image="{item}">
                    <v-img :aspect-ratio="5/2" :src="item.image_url" class="rounded my-2" max-width="120"></v-img>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon small class="mr-2"  @click="editItem(item)">mdi-pencil</v-icon>
                    <v-icon small @click="deleteItem(item)">mdi-delete</v-icon>
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
            imageRules: [
                value => !value || value.size < 2000000 || 'Gambar tidak boleh lebih dari 2 MB!',
            ],
            headers: [
                { text: 'Gambar', align: 'start', value: 'image', sortable: false },
                { text: 'Nama', align: 'start', value: 'name' },
                { text: 'Urutan', value: 'order' },
                { text: 'Status', value: 'is_active' },
                { text: 'Aksi', value: 'actions', sortable: false },
            ],
            banners: [],
            editedIndex: -1,
            editedItem: {
                name: '',
                image: '',
                image_url: '',
                link: '',
                order: 0,
                target_blank: 0,
                is_active: 1,
            },
            defaultItem: {
                name: '',
                image: '',
                image_url: '',
                link: '',
                order: 0,
                target_blank: 0,
                is_active: 1,
            },
            searchBank: '',
            banners: [],
            drivers: [],
            error: {}
        }
    },

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Tambah Banner' : 'Edit Banner'
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
            axios.get('/api/banners')
            .then((response) => {
                this.isLoading = false
                this.banners = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
        },

        editItem (item) {
            this.editedIndex = this.banners.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },

        deleteItem (item) {
            this.editedIndex = this.banners.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialogDelete = true
        },

        async deleteItemConfirm () {
            this.isLoading = true
            try {
                const response = await axios.delete('/api/banners/' + this.editedItem.id);
                this.banners.splice(this.editedIndex, 1)
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
                    axios.post('/api/banners', formData).then((response) => {
                        this.banners.push(response.data.data)
                        this.editItem(response.data.data)
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

        async save () {
            if (this.editedIndex > -1) {
                this.isLoading = true
                try {
                    const response = await axios.put('/api/banners/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.banners[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/banners', this.editedItem);
                    this.editedItem = response.data.data
                    this.banners.push(this.editedItem)
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