<template>
    <v-container fluid>
        <v-card color="grey lighten-3" style="position: relative">
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
            <div id="map" style="height: calc(100vh - 120px); z-index: 1;"></div>
        </v-card>
    </v-container>
</template>

<script>
export default {
    data() {
        return {
            isLoading: false,
            drivers: [],
            users: [],
            restaurants: [],
        }
    },

    computed: {
    },

    watch: {
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
            
            axios.get('/api/users')
            .then((response) => {
                this.isLoading = false
                this.users = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
            
            axios.get('/api/restaurants')
            .then((response) => {
                this.isLoading = false
                this.restaurants = response.data.data
            })
            .catch((error) => {
                this.isLoading = false
            });
            
            this.loadMap()
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
                var map             = L.map('map', {center: [-3.315039,114.5880393], zoom: 14});

                // add driver marker
                for (let i = 0; i < this.drivers.length; i++) {
                    L.marker(this.drivers[i].latlng, {icon: driverIcon}).addTo(map)
                        .bindPopup('<b>' + this.drivers[i].name + '</b><br/>' + this.drivers[i].vehicle_number );
                }

                // add resto marker
                for (let i = 0; i < this.restaurants.length; i++) {
                    L.marker(this.restaurants[i].latlng, {icon: storeIcon}).addTo(map)
                        .bindPopup('<b>' + this.restaurants[i].name + '</b>' );
                }

                // add user marker
                for (let i = 0; i < this.users.length; i++) {
                    L.marker(this.users[i].latlng, {icon: pinIcon}).addTo(map)
                        .bindPopup('<b>' + this.users[i].name + '</b>' );
                }
                                
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
        }
    },
};
</script>