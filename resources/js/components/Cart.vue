<template>
    <section>
        <v-btn color="primary" dark fixed right bottom fab @click="dialog = true" :style="{ 'display': carts.length ? '' : 'none' }">
            <v-badge color="accent" :content="carts.length || '0'" offset-x="-20" offset-y="-8"></v-badge>
            <v-icon>mdi-cart</v-icon>
        </v-btn>
        <v-dialog v-model="dialog"  fullscreen transition="dialog-bottom-transition">
            <v-card tile class="d-block">
                <v-app-bar color="white" light elevate-on-scroll>
                    <v-btn icon @click="dialog = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                    <v-app-bar-title class="pl-0" v-text="restaurantName || 'Cart'"></v-app-bar-title>
                </v-app-bar>
                <v-container class="px-0 pb-16">
                    <v-list subheader two-line>
                        <v-list-item-group>
                            <v-subheader class="px-3">Dikirim Ke</v-subheader>
                            <v-list-item color="primary" class="px-3" dense @click="open">
                                <v-list-item-avatar>
                                    <v-icon color="white" class="tertiary lighten-1" dark>mdi-map-marker</v-icon>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title v-text="user.name"></v-list-item-title>
                                    <v-list-item-subtitle v-text="user.address"></v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-icon class="my-auto">
                                    <v-icon>mdi-chevron-right</v-icon>
                                </v-list-item-icon>
                            </v-list-item>
                            <v-subheader class="px-3">Driver</v-subheader>
                            <v-list-item color="primary" class="px-3" dense @click="dialogDriver = true">
                                <v-list-item-avatar>
                                    <img v-if="selectedDriver.avatar" :src="selectedDriver.avatar_url" :alt="selectedDriver.name">
                                    <v-icon v-else color="white" class="tertiary lighten-1" dark>mdi-motorbike</v-icon>
                                </v-list-item-avatar>
                                <v-list-item-content>
                                    <v-list-item-title v-text="selectedDriver.name"></v-list-item-title>
                                    <v-list-item-subtitle v-text="selectedDriver.motor_type+(selectedDriver.vehicle_number ? ' • ' : '')+selectedDriver.vehicle_number"></v-list-item-subtitle>
                                </v-list-item-content>
                                <v-list-item-icon class="my-auto">
                                    <v-icon>mdi-chevron-right</v-icon>
                                </v-list-item-icon>
                            </v-list-item>
                            <v-subheader class="px-3">Ringkasan Pesanan</v-subheader>
                            <template v-for="(item, index) in carts">
                                <v-list-item color="primary" :key="item.id" dense class="px-3">
                                    <v-list-item-action class="mr-2">
                                        <v-list-item-action-text class="subtitle-2 font-weight-regular" color="primary" v-text="item.qty + 'x'"></v-list-item-action-text>
                                    </v-list-item-action>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="item.name"></v-list-item-title>
                                        <v-list-item-subtitle v-text="numberFormat(item.price)+(item.note ? ' • ' + item.note : '')"></v-list-item-subtitle>
                                    </v-list-item-content>
                                    <v-list-item-action>
                                        <v-list-item-action-text class="subtitle-2 font-weight-regular" v-text="numberFormat(item.qty * item.price)"></v-list-item-action-text>
                                    </v-list-item-action>
                                </v-list-item>
                            </template>
                        </v-list-item-group>
                    </v-list>
                    <v-divider class="mb-6"></v-divider>
                    <div class="d-flex justify-space-between px-4">
                        <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Sub Total</div>
                        <div class="subtitle-2">Rp{{ numberFormat(summary.sub_total) }}</div>
                    </div>
                    <div class="d-flex justify-space-between px-4">
                        <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Ongkos Kirim</div>
                        <div class="subtitle-2">Rp{{ numberFormat(summary.delivery_fee) }}</div>
                    </div>
                    <div class="d-flex justify-space-between px-4">
                        <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Biaya Layanan</div>
                        <div class="subtitle-2">Rp{{ numberFormat(summary.service_fee) }}</div>
                    </div>
                    <div class="d-flex justify-space-between px-4 mt-4">
                        <div class="subtitle-2 font-weight-bold">Total</div>
                        <div class="subtitle-2 font-weight-bold">Rp{{ numberFormat(summary.total) }}</div>
                    </div>
                    <v-bottom-navigation fixed dark horizontal>
                        <v-btn color="primary" large block :disabled="!selectedDriver.id" @click="processOrder">Proses Pesanan <v-icon>mdi-arrow-right</v-icon></v-btn>
                    </v-bottom-navigation>
                    <!-- dalog map -->
                    <v-dialog v-model="dialogMaps" persistent max-width="500">
                        <v-card>
                            <v-card-title class="text-h5">Edit Lokasi</v-card-title>
                            <v-card-text style="position: relative">
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
                                <div id="map" class="rounded-lg"></div>
                            </v-card-text>
                            <v-card-text>
                                <v-textarea label="Alamat Lengkap" rows="4" v-model="user.address" outlined :error-messages="error.errors ? error.errors.address : ''"></v-textarea>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="primary darken-1" text @click="close">Batal</v-btn>
                                <v-btn color="primary darken-1" text @click="updateLocation">Simpan</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <!-- end dialog map -->
                    <!-- dalog driver -->
                    <v-dialog v-model="dialogDriver" persistent max-width="400" content-class="rounded-xl">
                        <v-card class="rounded-xl">
                            <v-card-title class="text-h5">Pilih Driver</v-card-title>
                            <v-card-text>
                                <v-list style="margin-left: -24px; margin-right: -24px;">
                                    <v-list-item-group>
                                        <v-list-item class="px-5" color="primary" dense v-for="(driver, i) in drivers" :key="i" @click="selectedDriver = driver, dialogDriver = false">
                                            <v-list-item-avatar>
                                                <img :src="driver.avatar_url" :alt="driver.name">
                                            </v-list-item-avatar>
                                            <v-list-item-content>
                                                <v-list-item-title v-text="driver.name"></v-list-item-title>
                                                <v-list-item-subtitle v-text="'Bintang '+driver.rating"></v-list-item-subtitle>
                                            </v-list-item-content>
                                            <v-list-item-action>
                                                <v-btn icon>
                                                    <v-icon color="grey lighten-1">mdi-information</v-icon>
                                                </v-btn>
                                            </v-list-item-action>
                                        </v-list-item>
                                    </v-list-item-group>
                                </v-list>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="primary darken-1" text @click="dialogDriver = false">Batal</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>
                    <!-- end dialog driver -->
                    <!-- snack bar -->
                    <v-snackbar v-model="snackbar" timeout="2000">
                        {{ error.message }}
                        <template v-slot:action="{ attrs }">
                            <v-btn color="accent" text v-bind="attrs" click="snackbar = false">Close</v-btn>
                        </template>
                    </v-snackbar>
                    <!-- end snack bar -->
                </v-container> 
                <!-- content -->
            </v-card>
        </v-dialog>
    </section>
</template>
<script>

export default {
    props: {
        updateCart: Boolean,
        restaurantId: Number,
        restaurantName: String
    },

    data() {
        return {
            isLoading: false,
            snackbar: false,
            dialog: false,
            dialogMaps: false,
            dialogDriver: false,
            locationDisable: false,
            user: {},
            defaultUser: {},
            selectedDriver: {
                id: '',
                avatar: '',
                avatar_url: '',
                name: 'Driver Belum Dipilih',
                motor_type: 'Klik untuk memilih driver',
                vehicle_number: '',
            },
            drivers: [],
            carts: [],
            summary: {
                sub_total: 0,
                delivery_fee: 0,
                service_fee: 0,
                total: 0,
            },
            error: {}
        }
    },

    created () {
        this.getUser()
        this.getDriver()
    },

    watch: {
        updateCart(val){
            this.initialize()
        },
        restaurantId(val){
            this.initialize()
        }
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
                const response = await axios.get(`/api/carts?restaurant_id=${this.restaurantId}`);
                this.carts = response.data.data
                this.summary = response.data.summary
                this.$emit('cartData', this.carts);
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async getUser() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/user`);
                this.user = response.data.data
                this.defaultUser = Object.assign({}, response.data.data)
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async getDriver() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/drivers?limit=5&is_working=1`);
                this.drivers = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        getLocation() {
            navigator.geolocation.getCurrentPosition(
                // Success callback
                (position) => {
                    this.user.latlng = [position.coords.latitude, position.coords.longitude]
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
                var map             = L.map('map', {center: this.user.latlng, zoom: 20}),
                    marker          = L.marker(this.user.latlng, {icon: pinIcon, draggable: true}).addTo(map),
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
                        this.user.lat = lat
                        this.user.lng = lng
                        this.user.latlng = [lat, lng]
                    }
                });

                // marker
                marker.on('dragend', (e) => {
                    const { lat, lng } = e.target.getLatLng()
                    this.user.lat = lat
                    this.user.lng = lng
                    this.user.latlng = [lat, lng]
                    geocodeService.reverse().latlng(this.user.latlng).run( (error, result) => {
                        marker.setLatLng(this.user.latlng).bindPopup(result.address.Match_addr).openPopup();
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

        open() {
            this.dialogMaps = true
            if (this.user.lat && this.user.lng) {
                this.loadMap()
            } else {
                this.getLocation()
            }
        },

        close() {
            this.dialogMaps = false
            this.user = Object.assign({}, this.defaultUser)
            if (this.user.lat && this.user.lng) {
                this.loadMap()
            } else {
                this.getLocation()
            }
            this.error = {}
        },

        updateLocation() {
            this.isLoading = true
            axios.post('/api/update-location', this.user)
            .then((response) => {
                this.isLoading = false
                this.dialogMaps = false
            })
            .catch((error) => {
                this.isLoading = false
                this.error = error.response.data
            });
        },

        async processOrder() {
            this.isLoading = true
            try {
                const response = await axios.post(`/api/orders`, {
                    driver_id : this.selectedDriver.id,
                    restaurant_id : this.restaurantId,
                    lat : this.user.lat,
                    lng : this.user.lng,
                    delivery_address : this.user.address,
                    delivery_fee : this.summary.delivery_fee,
                    service_fee : this.summary.service_fee,
                });
                this.isLoading = false
                this.$router.push({ name: 'Order' })
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
                this.snackbar = true
            }
        },
    }
};
</script>