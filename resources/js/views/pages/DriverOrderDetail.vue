<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll elevation-1>
            <v-btn icon @click="goBack">
                <v-icon>mdi-close</v-icon>
            </v-btn>
            <v-app-bar-title class="pl-0" v-text="'Detail Pesanan'"></v-app-bar-title>
        </v-app-bar>
        <v-container class="px-0" v-if="order && order.id">
            <div id="map" :class="{'d-none': order.status == 'PAID' || order.status == 'CANCELED'}"></div>
            <v-stepper alt-labels flat value="step(order.status)" v-if="order.status != 'CANCELED'">
                <v-stepper-header>
                    <v-stepper-step :complete="step(order.status) > 1" step="1">
                        Pesanan Diterima
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step :complete="step(order.status) > 2" step="2">
                        Pesanan Diambil
                    </v-stepper-step>

                    <v-divider></v-divider>

                    <v-stepper-step :complete="step(order.status) > 3" step="3">
                        Pesanan Selesai
                    </v-stepper-step>
                </v-stepper-header>
            </v-stepper>
            <v-card v-else flat class="pa-10">
                <h2 class="text-center font-weight-light">Pesanan Dibatalkan</h2>
            </v-card>
            <v-divider class="mb-6"></v-divider>
            <v-list subheader two-line>
                <v-list-item-group>
                    <v-subheader class="px-3">Invoice {{ order.invoice }}</v-subheader>
                    <v-list-item color="primary" class="px-3" v-if="order.type == 'FOOD'" dense>
                        <v-list-item-avatar>
                            <v-icon color="white" class="tertiary lighten-1" dark>mdi-store</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title v-text="order.restaurant.name"></v-list-item-title>
                            <v-list-item-subtitle v-text="order.restaurant.address"></v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <template v-else>
                    <v-list-item color="primary" class="px-3" dense>
                        <v-list-item-avatar>
                            <v-icon color="white" class="accent lighten-1" dark>mdi-map-marker</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>Dari</v-list-item-title>
                            <v-list-item-subtitle v-text="order.origin_address"></v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    <v-list-item color="primary" class="px-3" dense>
                        <v-list-item-avatar>
                            <v-icon color="white" class="success lighten-1" dark>mdi-map-marker</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>Ke</v-list-item-title>
                            <v-list-item-subtitle v-text="order.destination_address"></v-list-item-subtitle>
                        </v-list-item-content>
                    </v-list-item>
                    </template>
                    <v-subheader class="px-3">User</v-subheader>
                    <v-list-item color="primary" class="px-3" dense :href="'tel:+62'+order.user.phone">
                        <v-list-item-avatar>
                            <img v-if="order.user.avatar" :src="order.user.avatar_url" :alt="order.user.name">
                            <v-icon v-else color="white" class="tertiary lighten-1" dark>mdi-motorbike</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>{{ order.user.name }}</v-list-item-title>
                            <v-list-item-subtitle v-text="'+62'+order.user.phone"></v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-icon>
                            <v-btn icon color="success" :href="'tel:+62'+order.user.phone">
                                <v-icon>mdi-phone</v-icon>
                            </v-btn>
                        </v-list-item-icon>
                    </v-list-item>
                    <v-subheader class="px-3">Ringkasan Pesanan</v-subheader>
                    <template v-for="(item, index) in order.items">
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
            <div class="d-flex justify-space-between px-4" v-if="order.type == 'FOOD'">
                <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Sub Total</div>
                <div class="subtitle-2">Rp{{ numberFormat(order.sub_total) }}</div>
            </div>
            <div class="d-flex justify-space-between px-4">
                <div class="subtitle-2 grey--text text--darken-1 font-weight-light">{{ order.type == 'FOOD' ? 'Ongkos Kirim' : 'Biaya Perjalanan' }}</div>
                <div class="subtitle-2">Rp{{ numberFormat(order.delivery_fee) }}</div>
            </div>
            <div class="d-flex justify-space-between px-4">
                <div class="subtitle-2 grey--text text--darken-1 font-weight-light">Biaya Layanan</div>
                <div class="subtitle-2">Rp{{ numberFormat(order.service_fee) }}</div>
            </div>
            <div class="d-flex justify-space-between px-4 mt-4 pb-4">
                <div class="subtitle-2 font-weight-bold">Total</div>
                <div class="subtitle-2 font-weight-bold">Rp{{ numberFormat(order.total) }}</div>
            </div>

            <v-bottom-navigation fixed dark horizontal v-if="order.status == 'RECEIVED' || order.status == 'TAKEN'">
                <v-btn color="secondary" v-if="order.status == 'RECEIVED'" large width="100%" @click="updateStatus('CANCELED')">Batalkan</v-btn>
                <v-btn color="primary" v-if="order.status == 'RECEIVED'" large width="100%" @click="updateStatus('TAKEN')">Pesanan Diambil</v-btn>
                <v-btn color="primary" v-if="order.status == 'TAKEN'" large block @click="updateStatus('PAID')">Tandakan Selesai</v-btn>
            </v-bottom-navigation>

            <!-- snack bar -->
            <v-snackbar v-model="snackbar" timeout="2000">
                {{ error.message }}
                <template v-slot:action="{ attrs }">
                    <v-btn color="accent" text v-bind="attrs" click="snackbar = false">Close</v-btn>
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
    props: ['id'],
    data() {
        return {
            isLoading: false,
            order: {},
            snackbar: false,
            error: {}
        }
    },

    created() {
        this.initialize()
        this.loadMap()
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
                const response = await axios.get(`/api/orders/${this.id}`);
                this.order = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },
        step(val) {
            switch(val) {
                case 'RECEIVED':
                    return 2
                    break;
                case 'TAKEN':
                    return 3
                    break;
                case 'PAID':
                    return 4
                    break;
                default:
                    return 0
            }
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
                var map             = L.map('map'),
                    marker          = L.marker(this.order.destination, {icon: pinIcon}).addTo(map),
                    geocodeService  = L.esri.Geocoding.geocodeService();
                
                if(this.order.type == 'FOOD')
                {
                    L.marker(this.order.restaurant.latlng, {icon: storeIcon}).addTo(map)
                } else {
                    L.marker(this.order.origin, {icon: pinIcon}).addTo(map)
                }
                L.marker(this.order.driver.latlng, {icon: driverIcon}).addTo(map)
                map.setZoom(20)

                if(this.order.status == 'RECEIVED') {
                    var routing1 = L.Routing.control({
                        show: false,
                        addWaypoints: false,
                        waypoints: [
                            L.latLng(this.order.driver.latlng),
                            L.latLng(this.order.origin),
                        ],
                        createMarker: function() { return null; },
                        lineOptions: {
                            styles: [{color: '#27b83a', opacity: 1, weight: 5}]
                        }
                    }).addTo(map);
                } else {
                    if(routing1) {
                        routing1.remove()
                    }
                    L.Routing.control({
                        show: false,
                        addWaypoints: false,
                        waypoints: [
                            L.latLng(this.order.driver.latlng),
                            L.latLng(this.order.destination),
                        ],
                        createMarker: function() { return null; },
                        lineOptions: {
                            styles: [{color: '#27b83a', opacity: 1, weight: 5}]
                        }
                    }).addTo(map);
                }

                // realtime get location driver
                setInterval(() => {
                    map.panTo(this.order.driver.latlng)
                }, 2000);
                
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
        async updateStatus(val) {
            this.isLoading = true
            try {
                const response = await axios.put(`/api/orders/${this.order.id}`, { status: val});
                this.isLoading = false
                this.error = {
                    message: response.data.message
                }
                this.order.status = val
                this.snackbar = true
                this.loadMap()
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
                this.snackbar = true
            }
        },
    }
};
</script>