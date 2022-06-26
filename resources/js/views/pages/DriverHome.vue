<template>
    <section>
        <v-app-bar app color="primary" dark inverted-scroll elevation-1>
            <v-app-bar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-app-bar-title>
            <v-spacer></v-spacer>
        </v-app-bar>
        <v-toolbar color="primary" dark extended flat dense class="mx-auto pt-3">
            <v-toolbar-title>
                <v-img src="/images/logo-white.svg" alt="Logo Radjago" max-height="40" contain position="left center"></v-img>
            </v-toolbar-title>
            <v-spacer></v-spacer>
        </v-toolbar>
        <v-container class="bg-white" style="padding-bottom: 64px;">
            <v-card class="mx-auto mb-4 rounded-lg" style="margin-top: -32px;">
                <v-card-title class="pb-0">
                    <v-avatar>
                        <img :src="user.avatar_url" :alt="user.name">
                    </v-avatar>
                    <v-spacer></v-spacer>
                    <div class="mr-4 font-weight-normal grey--text text--darken-2">{{ status ? 'Aktif' : 'Istirahat' }}</div>
                    <v-switch v-model="status" inset></v-switch>
                </v-card-title>
                <v-card-title class="pt-0" v-text="user.name"></v-card-title>
                <v-card-subtitle v-text="user.motor_type+(user.vehicle_number ? ' • ' : '')+user.vehicle_number"></v-card-subtitle>
            </v-card>
            <!-- status -->
            <v-card class="mb-4 tertiary rounded-lg">
                <v-card-title>
                    <div>
                        <small class="white--text font-weight-normal">Saldo</small>
                        <div class="white--text" style="margin-top: -8px;">Rp 200,000</div>
                    </div>
                    <v-spacer></v-spacer>
                    <v-btn color="white" class="rounded-pill"><v-icon color="primary" left>mdi-plus-circle</v-icon> Top Up</v-btn>
                </v-card-title>
            </v-card>
            <!-- status -->
            <v-card color="grey lighten-3 rounded-lg" class="mb-3" style="position: relative">
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
                <div id="map"></div>
            </v-card>
        </v-container>
        <DriverBottomNav/>
    </section>
</template>
<style>
    #map { height: 300px; z-index: 1; }
</style>
<script>

import CardResto from '../../components/CardResto.vue';
import DriverBottomNav from '../../components/DriverBottomNav.vue';
export default {
    components: {
        CardResto,
        DriverBottomNav
    },
    data() {
        return {
            isLoading: false,
            status: true,
            locationDisable: false,
            user: {},
            defaultUser: {},
            error: {}
        }
    },

    created () {
        this.initialize()
        this.getLocation()
        setInterval(() => {
            this.getLocation()
            console.log("location tracking")
        }, 10000)
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
        getLocation() {
            navigator.geolocation.getCurrentPosition(
                // Success callback
                (position) => {
                    this.user.latlng = [position.coords.latitude, position.coords.longitude]
                    this.user.lat = position.coords.latitude
                    this.user.lng = position.coords.longitude
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
                    marker          = L.marker(this.user.latlng, {icon: driverIcon}).addTo(map),
                    geocodeService  = L.esri.Geocoding.geocodeService();
                                
                // add title layer
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                this.updateLocation()
                this.isLoading = false
            } else {
                setTimeout(() => {
                    this.loadMap()
                }, 1000);
            }
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
    }
};
</script>