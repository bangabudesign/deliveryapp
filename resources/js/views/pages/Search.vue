<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll elevation-1>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-app-bar-title class="pl-2">
                <div @click="open" style="cursor: pointer">
                    <div class="caption">Pilih Lokasi <v-icon color="primary" small>mdi-chevron-down</v-icon></div>
                    <div class="subtitle-2" v-text="user.address"></div>
                </div>
            </v-app-bar-title>
            <template v-slot:extension>
                <v-text-field v-model="searchTerm" autofocus placeholder="mau makan apa?" background-color="grey lighten-4" dense solo flat hide-no-data hide-details prepend-inner-icon="mdi-magnify" @input="inputSearch"></v-text-field>
            </template>
        </v-app-bar>
        <v-container class="bg-white">
            <!-- content -->
            <CardResto :items="restaurants"/>
            <!-- content -->
        </v-container>
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
    </section>
</template>

<style>
    #map { height: 300px; z-index: 1; }
</style>

<script>
import CardResto from '../../components/CardResto.vue';
export default {
    components: {
        CardResto,
    },
    data() {
        return {
            isLoading: false,
            dialogMaps: false,
            locationDisable: false,
            category: '',
            searchTerm: '',
            user: {},
            defaultUser: {},
            restaurants: [],
            error: {}
        }
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
        async getUser() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/user`);
                this.user = response.data.data
                this.defaultUser = Object.assign({}, response.data.data)
                this.getRestaurants()
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        async getRestaurants() {
            this.isLoading = true
            try {
                const response = await axios.get(`/api/restaurants?near_by=${this.user.lat},${this.user.lng}&category=${this.category}&q=${this.searchTerm}`);
                this.restaurants = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        inputSearch() {
            this.$router.replace({ query: { category: this.category, q: this.searchTerm } })
            this.getRestaurants()
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

            this.defaultUser = Object.assign({}, this.user)
        },
    }
};
</script>