<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-toolbar-title>Profile</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <v-card flat>
                <v-card-text>
                    <div class="d-flex align-items-center">
                        <v-avatar size="50" @click="openDialog">
                            <img v-if="user.avatar" :src="user.avatar_url" :alt="user.name">
                            <v-icon v-else color="white" class="tertiary lighten-1" dark>mdi-camera</v-icon>
                        </v-avatar>
                        <div class="ml-4">
                            <h2 class="subtitle-1 font-weight-bold" v-text="user.name"></h2>
                            <div class="caption-2" v-text="'0'+user.phone + ' - ' +user.role"></div>
                        </div>
                    </div>
                    <v-divider class="my-6"></v-divider>
                        <v-text-field label="Nama" v-model="user.name" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                        <v-text-field label="WhasApp" type="number" v-model="user.phone" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.phone : ''"></v-text-field>
                        <v-text-field label="Password" type="password" v-model="user.password" outlined dense :error-messages="error.errors ? error.errors.password : ''" hint="Kosongkan jika tidak ingin mengubah password." persistent-hint></v-text-field>
                        <v-btn color="primary" class="rounded-pill mt-4" outlined block @click="updateProfile">Update Profile</v-btn>
                        <v-btn color="danger" class="rounded-pill mt-4" text block @click="logOut">Log Out</v-btn>
                </v-card-text>
            </v-card>
            <!-- dialog images -->
            <v-dialog v-model="dialog" max-width="700">
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Images</span>
                    </v-card-title>
                    <v-card-text>
                        <v-file-input :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Upload Gambar" prepend-icon="mdi-camera" label="Upload Gambar" @change="uploadImage"></v-file-input>
                        <v-row>
                            <v-col v-for="image in images" :key="image.id"  class="d-flex child-flex" cols="4">
                              <v-img :src="image.image_url" :lazy-src="`https://picsum.photos/10/6?`" aspect-ratio="1" class="grey lighten-2" @click="selectAvatar(image.image)">
                                <template v-slot:placeholder>
                                  <v-row class="fill-height ma-0" align="center" justify="center">
                                    <v-progress-circular indeterminate color="grey lighten-5"></v-progress-circular>
                                  </v-row>
                                </template>
                              </v-img>
                            </v-col>
                          </v-row>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="secondary" text @click="dialog = false">Close</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- end dialog images -->
        </v-container>
        <BottomNav/>
    </section>
</template>
<script>
import Cookies from 'js-cookie';
import BottomNav from '../../components/BottomNav.vue';
export default {
    components: {
        BottomNav
    },
    data() {
        return {
            isLoading: false,
            dialog: false,
            rules: [
                value => !!value || 'Wajib Disi.',
            ],
            imageRules: [
                value => !value || value.size < 2000000 || 'Gambar tidak boleh lebih dari 2 MB!',
            ],
            user: {},
            images: [],
            error: {}
        }
    },

    created () {
        this.initialize()
    },

    methods: {
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

        async updateProfile() {
            this.isLoading = true
            try {
                const response = await axios.put('/api/users/' + this.user.id, this.user);
                this.user = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        
        async logOut() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/logout`);
                this.isLoading = false
                Cookies.remove('token')
                Cookies.remove('role')
                this.$router.push({ name: 'Splash' })
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        
        openDialog() {
            this.dialog = true
            if (this.images.length == 0) {
                this.getImages()
            }
        },

        async getImages() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/images`);
                this.images = response.data.data
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
                    axios.post('/api/images', formData).then((response) => {
                        this.images.push(response.data.data)
                        console.log(response.data.message)
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
        
        deleteMedia(id) {
            if(confirm("Hapus gambar?")) { 
                this.loader = true
                axios.delete('/api/images/' + id).then((response) => {
                    if (response.data.status) {
                        // Success notification
                        this.$noty.success(response.data.message)
                    }
                    this.getMedia()
                    this.loader = false
                    this.errors = {}
                }).catch((error) => {
                    if (error.response.status = 422) {
                        this.errors = error.response.data.errors
                        // Error notification
                        this.$noty.error(error.response.data.message,)
                    }
                    this.loader = false
                })
            }
        },

        selectAvatar(filename) {
            this.user.avatar = filename
            this.user.avatar_url = '/images/media/'+filename
            this.dialog = false
            this.updateProfile()
        }
    }
};
</script>