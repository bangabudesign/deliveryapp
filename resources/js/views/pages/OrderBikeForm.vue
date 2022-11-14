<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll elevation-1>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-app-bar-title class="pl-0" v-text="'Kendaraan Roda Dua'"></v-app-bar-title>
        </v-app-bar>
        <v-container class="bg-white">
            <v-list subheader two-line>
                <v-list-item-group>
                    <v-list-item color="primary" class="px-3" dense @click="open('origin')">
                        <v-list-item-avatar>
                            <v-icon color="white" class="tertiary lighten-1" dark>mdi-map-marker</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title v-text="'Jemput di'"></v-list-item-title>
                            <v-list-item-subtitle v-text="data.origin_address"></v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-icon class="my-auto">
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-list-item-icon>
                    </v-list-item>
                    <v-list-item color="primary" class="px-3" dense @click="open('destination')">
                        <v-list-item-avatar>
                            <v-icon color="white" class="tertiary lighten-1" dark>mdi-map-marker</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title v-text="'Antar ke'"></v-list-item-title>
                            <v-list-item-subtitle v-text="data.destination_address"></v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-icon class="my-auto">
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-list-item-icon>
                    </v-list-item>
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
                </v-list-item-group>
            </v-list>
            <v-divider class="mb-6"></v-divider>
            <div class="d-flex justify-space-between px-4">
                <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Ongkos</div>
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
                <v-btn color="primary" large block :disabled="!(selectedDriver.id && data.origin_lat && data.destination_lat) || isLoading" :loading="isLoading" @click="processOrder">Proses Pesanan <v-icon>mdi-arrow-right</v-icon></v-btn>
            </v-bottom-navigation>
            <!-- dalog map -->
            <v-dialog v-model="dialogMaps" persistent max-width="500">
                <v-card>
                    <v-card-title class="text-h5">{{ modalTitle }}</v-card-title>
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
                        <div id="mapContainer" class="rounded-lg">
                            <div id="map" class="rounded-lg"></div>
                        </div>
                    </v-card-text>
                    <v-card-text>
                        <v-textarea label="Alamat" rows="4" v-model="map.address" outlined></v-textarea>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" text @click="close">Batal</v-btn>
                        <v-btn color="primary darken-1" text @click="updateLocation(map.type)">Simpan</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- end dialog map -->
            <!-- dalog driver -->
            <v-dialog v-model="dialogDriver" persistent max-width="400" content-class="rounded-lg">
                <v-card class="rounded-lg">
                    <v-card-title class="text-h5">Pilih Driver</v-card-title>
                    <v-card-text>
                        <v-list v-if="drivers.length" style="margin-left: -24px; margin-right: -24px;">
                            <v-list-item-group>
                                <v-list-item class="px-5" color="primary" dense v-for="(driver, i) in drivers" :key="i" @click="selectedDriver = driver, data.driver_id = driver.id, dialogDriver = false">
                                    <v-list-item-avatar>
                                        <img :src="driver.avatar_url" :alt="driver.name">
                                    </v-list-item-avatar>
                                    <v-list-item-content>
                                        <v-list-item-title v-text="driver.name"></v-list-item-title>
                                        <v-list-item-subtitle v-text="'Jarak '+Number(driver.distance).toFixed(2)+' km dari titik jemput'"></v-list-item-subtitle>
                                    </v-list-item-content>
                                </v-list-item>
                            </v-list-item-group>
                        </v-list>
                        <v-alert v-else icon="mdi-information" type="warning">Tidak ditemukan driver terdekat. Periksa titik jemput Anda!</v-alert>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="primary darken-1" text @click="dialogDriver = false">Tutup</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!-- end dialog driver -->
            <!-- snack bar -->
            <v-snackbar v-model="snackbar" timeout="5000">
                {{ error.message }}
                <template v-slot:action="{ attrs }">
                    <v-btn color="accent" text v-bind="attrs" @click="snackbar = false">Close</v-btn>
                </template>
            </v-snackbar>
            <!-- end snack bar -->
        </v-container>
    </section>
</template>

<style>
    #map { height: 300px; z-index: 1; }
</style>

<script>
export default {
    data() {
        return {
            isLoading: false,
            snackbar: false,
            dialogMaps: false,
            locationDisable: false,
            dialogDriver: false,
            category: '',
            searchTerm: '',
            user: {},
            defaultUser: {},
            map: {
                type: '',
                latlng: '',
                lat: '',
                lng: '',
                address: ''
            },
            data: {
                type: 'BIKE',
                driver_id: '',
                origin_lat: '',
                origin_lng: '',
                origin_address: 'Klik untuk menentukan lokasi',
                destination_lat: '',
                destination_lng: '',
                destination_address: 'Klik untuk menentukan lokasi',
                delivery_fee: '',
                service_fee: ''
            },
            selectedDriver: {
                id: '',
                avatar: '',
                avatar_url: '',
                name: 'Driver Belum Dipilih',
                motor_type: 'Klik untuk memilih driver',
                vehicle_number: '',
            },
            drivers: [],
            summary: {
                delivery_fee: 0,
                service_fee: 5000,
                total: 5000,
            },
            error: {}
        }
    },

    computed: {
        modalTitle () {
            return this.map.type == 'origin' ? 'Jemput Dimana?' : 'Mau Kemana?'
        },
    },

    created() {
        this.getUser()
        this.category = this.$route.query.category || ''
        this.searchTerm = this.$route.query.q || ''
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
        getLocation(type) {
            navigator.geolocation.getCurrentPosition(
                // Success callback
                (position) => {
                    if(type == 'origin' && this.data.origin_lat && this.data.origin_lng) {
                        this.map.latlng = [this.data.origin_lat, this.data.origin_lng]
                        this.map.lat = this.data.origin_lat
                        this.map.lng = this.data.origin_lng
                        this.map.address = this.data.origin_address
                    } else if(type == 'destination' &&this.data.destination_lat && this.data.destination_lng) {
                        this.map.latlng = [this.data.destination_lat, this.data.destination_lng]
                        this.map.lat = this.data.destination_lat
                        this.map.lng = this.data.destination_lng
                        this.map.address = this.data.destination_address
                    } else {
                        this.map.latlng = [position.coords.latitude, position.coords.longitude]
                        this.map.lat = position.coords.latitude
                        this.map.lng = position.coords.longitude
                    }                    
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
                var map             = L.map('map', {center: this.map.latlng, zoom: 20}),
                    marker          = L.marker(map.getCenter(), {icon: pinIcon, draggable: true}).addTo(map),
                    geocodeService  = L.esri.Geocoding.geocodeService();
                
                // tentukan lokasi awal
                geocodeService.reverse().latlng(this.map.latlng).run( (error, result) => {
                    this.map.address = result.address.Match_addr;
                });
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
                        this.map.lat = lat
                        this.map.lng = lng
                        this.map.latlng = [lat, lng]
                        this.map.address = data.results[i].text
                    }
                });

                // marker
                marker.on('dragend', (e) => {
                    const { lat, lng } = e.target.getLatLng()
                    this.map.lat = lat
                    this.map.lng = lng
                    this.map.latlng = [lat, lng]
                    geocodeService.reverse().latlng(this.map.latlng).run( (error, result) => {
                        marker.setLatLng(this.map.latlng).bindPopup(result.address.Match_addr).openPopup();
                        this.map.address = result.address.Match_addr;
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
        async open(type) {
            if(type == 'origin') {
                Object.assign(this.map, {
                    type: 'origin',
                    latlng: [this.data.origin_lat, this.data.origin_lng],
                    lat: this.data.origin_lat,
                    lng: this.data.origin_lng,
                    address: this.data.origin_address
                })
            } else if(type == 'destination') {
                Object.assign(this.map, {
                    type: 'destination',
                    latlng: [this.data.destination_lat, this.data.destination_lng],
                    lat: this.data.destination_lat,
                    lng: this.data.destination_lng,
                    address: this.data.destination_address
                })
            }
            this.getLocation(type)
            this.dialogMaps = true
        },
        close() {
            this.dialogMaps = false
        },
        updateLocation(type) {
            this.isLoading = true
            if(type == 'origin') {
                console.log(this.map)
                Object.assign(this.data, {
                    origin_lat: this.map.lat,
                    origin_lng: this.map.lng,
                    origin_address: this.map.address,
                })
                this.getDriver()
                this.close()
            } else {
                Object.assign(this.data, {
                    destination_lat: this.map.lat,
                    destination_lng: this.map.lng,
                    destination_address: this.map.address,
                })
                this.close()
            }
            if(this.data.origin_lat && this.data.origin_lng && this.data.destination_lat && this.data.destination_lng) {
                this.getDeliveryFee()
            }
        },
        async getDriver() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/drivers?near_by=${this.data.origin_lat},${this.data.origin_lng}&limit=5&is_working=1`);
                this.drivers = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        async getDeliveryFee() {
            this.isLoading = true
            if(this.data.origin_lat && this.data.origin_lng) {
                try {
                    const response = await axios.post(`/api/delivery-fee`, this.data);
                    this.summary = response.data.data
                    Object.assign(this.data, this.summary)
                    console.log(response.data.data)
                    this.isLoading = false
                } catch (error) {
                    this.isLoading = false
                    this.error = error.response.data
                }
            } else {
                this.error.message = 'Kamu belum menentukan titik jemput'
                this.snackbar = true
            }
        },
        async processOrder() {
            this.isLoading = true
            try {
                const response = await axios.post(`/api/orders`, this.data);
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