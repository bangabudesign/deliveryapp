<template>
    <section>
        <v-app-bar app color="white" light inverted-scroll elevation-1>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-app-bar-title class="pl-0" v-text="restaurant.name + ' - ' +restaurant.address"></v-app-bar-title>
        </v-app-bar>
        <v-toolbar color="primary" dark dense flat class="mx-auto" height="100px" :src="restaurant.image_url">
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>mdi-heart</v-icon>
            </v-btn>
        </v-toolbar>
        <v-container class="bg-white" style="min-height: calc(100vh - 148px);">
            <v-card class="mx-auto mb-6 rounded-lg" style="margin-top: -32px;">
                <v-card-text class="pb-0">
                    <v-file-input :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Upload Gambar" prepend-icon="mdi-camera" label="Upload Gambar" @change="uploadRestoImage"></v-file-input>
                    <v-text-field label="Nama Resto" v-model="restaurant.name" outlined dense :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                    <v-text-field label="Jam Buka" placeholder="00:00" v-model="restaurant.opening_hours" outlined dense :error-messages="error.errors ? error.errors.opening_hours : ''"></v-text-field>
                    <v-text-field label="Jam Tutup" placeholder="00:00" v-model="restaurant.closing_hours" outlined dense :error-messages="error.errors ? error.errors.closing_hours : ''"></v-text-field>
                </v-card-text>
                <v-card-text style="position: relative" class="pt-0">
                    <div v-if="isLoading" style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; display: flex;justify-content: center;align-items: center;">
                        <v-card class="text-center" width="200" max-width="200">
                            <v-card-text>
                                Loading Map
                                <v-progress-linear
                                indeterminate
                                color="primary"
                                class="mt-2 mb-0"
                                ></v-progress-linear>
                            </v-card-text>
                        </v-card>
                    </div>
                    <div v-if="locationDisable" style="position: absolute; top: 0; bottom: 0; left: 0; right: 0; display: flex;justify-content: center;align-items: center;">
                        <v-card class="text-center" width="200" max-width="200">
                            <v-card-text>
                                Please Enable Location
                            </v-card-text>
                        </v-card>
                    </div>
                    <div id="map" class="rounded-lg mb-4"></div>
                    <div>{{ 'lat: '+restaurant.lat }}</div>
                    <div>{{ 'lng: '+restaurant.lng }}</div>
                </v-card-text>
                <v-card-text>
                    <v-textarea label="Alamat" v-model="restaurant.address" outlined dense :error-messages="error.errors ? error.errors.address : ''"></v-textarea>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-btn color="primary" class="rounded-pill" block :loading="isLoading" @click="save">{{ id ? 'Simpan Perubahan' : 'Tambah Resto' }}</v-btn>
                </v-card-actions>
            </v-card>
            <template v-if="id">
                <div class="d-flex">
                    <h2 class="text-h6 mb-4">Daftar Menu</h2>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" class="rounded-pill" text small @click="dialog = true">Tambah Menu</v-btn>
                </div>
                <template v-if="isLoading">
                    <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                        <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                        <div style="max-width: calc(100% - 124px);" class="ma-3">
                            <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                            <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                        </div>
                    </v-card>
                    <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                        <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                        <div style="max-width: calc(100% - 124px);" class="ma-3">
                            <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                            <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                        </div>
                    </v-card>
                    <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                        <v-skeleton-loader color="grey lighten-4" class="ma-3" height="100" width="100" type="image"></v-skeleton-loader>
                        <div style="max-width: calc(100% - 124px);" class="ma-3">
                            <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                            <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                        </div>
                    </v-card>
                    <v-card class="d-flex flex-no-wrap mb-3 rounded-lg" outlined>
                        <v-skeleton-loader color="grey lighten-4" class="ma-3 mr-0" height="100" width="100" type="image"></v-skeleton-loader>
                        <div style="max-width: calc(100% - 124px);" class="ma-3">
                            <v-skeleton-loader color="grey lighten-4" height="15" width="150" type="image" class="rounded-pill mb-4"></v-skeleton-loader>
                            <v-skeleton-loader color="grey lighten-4" height="15" width="80" type="image" class="rounded-pill"></v-skeleton-loader>
                        </div>
                    </v-card>
                </template>
                <template v-if="restaurant.products.length == 0">
                    <v-card flat class="text-center py-16">
                        <v-card-text>
                            <v-icon x-large>mdi-food-fork-drink</v-icon>
                            <div class="subtitle-1 dark--text">Daftar Menu Masih Kosong</div>
                            <div class="caption">Input daftar menu terlebih dahulu!</div>
                        </v-card-text>
                    </v-card>
                </template>
                <v-card v-else class="d-flex flex-no-wrap mb-3 rounded-lg" v-for="(item, index) in restaurant.products" :key="item.id" outlined @click="editMenu(item)">
                    <v-avatar class="ma-3 mr-0 rounded-lg" size="100" rounded>
                        <v-img :src="item.image_url"></v-img>
                    </v-avatar>
                    <div style="max-width: calc(100% - 124px);">
                        <v-card-title v-text="item.name" class="subtitle-2 font-weight-regular"></v-card-title>
                        <v-card-text>
                            <div class="subtitle-2" v-text="numberFormat(item.price)"></div>
                        </v-card-text>
                    </div>
                </v-card>
            </template>
            <!-- dialog detail -->
            <v-dialog v-model="dialog" scrollable max-width="400px" content-class="rounded-lg">
                <v-card flat class="rounded-lg">
                <v-toolbar flat dark color="primary" prominent :src="editedItem.image_url" height="200px">
                    <v-btn icon dark @click="dialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-text class="pt-6">
                    <v-file-input :rules="imageRules" accept="image/png, image/jpeg, image/bmp, image/webp" placeholder="Upload Gambar" prepend-icon="mdi-camera" label="Upload Gambar" @change="uploadProductImage"></v-file-input>
                    <v-text-field label="Nama Menu" v-model="editedItem.name" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.name : ''"></v-text-field>
                    <v-text-field label="Harga" type="number" v-model="editedItem.price" outlined dense :rules="rules" :error-messages="error.errors ? error.errors.price : ''"></v-text-field>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text :loading="isLoading" @click="close">Close</v-btn>
                    <v-btn color="primary" text :loading="isLoading" @click="saveMenu">Save</v-btn>
                </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- end dialog detail -->
        </v-container>
    </section>
</template>

<script>
export default {
    props: ['id'],
    data() {
        return {
            isLoading: false,
            locationDisable: false,
            dialog: false,
            rules: [
                value => !!value || 'Wajib Disi.',
            ],
            imageRules: [
                value => !value || value.size < 2000000 || 'Gambar tidak boleh lebih dari 2 MB!',
            ],
            restaurant: {
                id: '',
                name: '',
                iamge: '',
                image_url: '',
                lat: '',
                lng: '',
                latlng: [],
                address: '',
                opening_hours: '',
                closing_hours: '',
                products: []
            },
            editedIndex: -1,
            editedItem: {
                restaurant_id: this.id,
                name: '',
                image: '',
                price: '',
            },
            defaultItem: {
                restaurant_id: this.id,
                name: '',
                image: '',
                price: '',
            },
            error: {}
        }
    },

    watch: {
        dialog (val) {
            val || this.close()
        },
    },

    created () {
        this.initialize()
        this.getLocation()
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
            if(this.id) {
                this.isLoading = true
                try {
                    const response = await axios.get(`/api/restaurants/${this.id}`);
                    this.restaurant = response.data.data
                    this.isLoading = false
                    this.loadMap()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.getLocation()
            }
        },

        async save() {
            if(this.id) {
                this.isLoading = true
                try {
                    const response = await axios.put(`/api/restaurants/${this.id}`, this.restaurant);
                    this.isLoading = false
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post(`/api/restaurants`, this.restaurant);
                    this.$router.push({ name: 'MerchantHome' })
                    this.isLoading = false
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            }
        },

        editMenu (item) {
            this.editedIndex = this.restaurant.products.indexOf(item)
            this.editedItem = Object.assign({}, item)
            this.dialog = true
        },
        
        close () {
            this.dialog = false
            this.$nextTick(() => {
                this.editedItem = Object.assign({}, this.defaultItem)
                this.editedIndex = -1
            })
            this.error = {}
        },

        async saveMenu () {
            if (this.editedIndex > -1) {
                this.isLoading = true
                try {
                    const response = await axios.put('/api/products/' + this.editedItem.id, this.editedItem);
                    this.editedItem = response.data.data
                    Object.assign(this.restaurant.products[this.editedIndex], this.editedItem)
                    this.isLoading = false
                    this.close()
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.isLoading = true
                try {
                    const response = await axios.post('/api/products', this.editedItem);
                    this.editedItem = response.data.data
                    this.restaurant.products.push(this.editedItem)
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
                    this.restaurant.lat = position.coords.latitude
                    this.restaurant.lng = position.coords.longitude
                    this.restaurant.latlng = [position.coords.latitude, position.coords.longitude]
                    this.loadMap()
                },
                // Optional error callback
                (error) => {
                    console.log("please enable location")
                    this.locationDisable = true
                }
            );
        },

        loadMap() {
            this.isLoading = true
            const container = document.getElementById('map')
            if (container) {
                container._leaflet_id = null;
                // custom icon
                var LeafIcon = L.Icon.extend({
                    options: {
                        iconSize:     [50, 50],
                        iconAnchor:   [25, 50],
                        shadowAnchor: [25, 50],
                        popupAnchor:  [0, -50]
                    }
                });
                var pinIcon = new LeafIcon({iconUrl: '/images/marker-pin.png'}),
                    driverIcon = new LeafIcon({iconUrl: '/images/marker-driver.png'}),
                    storeIcon = new LeafIcon({iconUrl: '/images/marker-store.png'});

                L.icon = function (options) {
                    return new L.Icon(options);
                };
                // variabel
                var map             = L.map('map', {center: this.restaurant.latlng, zoom: 20}),
                    marker          = L.marker(this.restaurant.latlng, {icon: pinIcon, draggable: true}).addTo(map),
                    geocodeService  = L.esri.Geocoding.geocodeService();
                // cari lokasi
                var searchControl = L.esri.Geocoding.geosearch({
                    position: 'topright',
                    placeholder: 'Ketik Alamat Sekarang',
                    useMapBounds: false,
                    providers: [L.esri.Geocoding.arcgisOnlineProvider()]
                }).addTo(map);

                var results = L.layerGroup().addTo(map);
    
                searchControl.on('results', (data) => {
                    results.clearLayers();
                    for (var i = data.results.length - 1; i >= 0; i--) {
                        marker.setLatLng(data.results[i].latlng).bindPopup(data.results[i].text).openPopup();
                        const { lat, lng } = data.results[i].latlng;
                        this.restaurant.lat = lat
                        this.restaurant.lng = lng
                        this.restaurant.latlng = [lat, lng]
                    }
                });

                // marker
                marker.on('dragend', (e) => {
                    const { lat, lng } = e.target.getLatLng()
                    this.restaurant.lat = lat
                    this.restaurant.lng = lng
                    this.restaurant.latlng = [lat, lng]
                    geocodeService.reverse().latlng(this.restaurant.latlng).run( (error, result) => {
                        marker.setLatLng(this.restaurant.latlng).bindPopup(result.address.Match_addr).openPopup();
                    });
                });
                
                // add title layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                this.isLoading = false
            } else {
                setTimeout(() => {
                    this.loadMap()
                }, 1000);
            }
        },
        
        uploadRestoImage(e) {
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
                        this.restaurant.image = response.data.data.image
                        this.restaurant.image_url = response.data.data.image_url
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

        
        uploadProductImage(e) {
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
                        this.editedItem.image = response.data.data.image
                        this.editedItem.image_url = response.data.data.image_url
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
    }
};
</script>