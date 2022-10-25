<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-toolbar-title>Orders</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <!-- content -->
            <v-list v-if="orders.length">
                <v-list-item-group>
                    <template v-for="(item, index) in filteredOrder">
                        <v-list-item :key="index" dense  route :to="{ name: 'OrderDetail', params: {id: item.id} }">
                            <v-list-item-content>
                                <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="item.restaurant ? 'Food - '+item.restaurant.name : 'Motor - '+item.destination_address"></v-list-item-title>
                                <v-list-item-subtitle class="caption font-weight-regular" v-text="indoDate(item.created_at, false, true)"></v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-list-item-action-text class="subtitle-2 black--text font-weight-regular" v-text="numberFormat(item.total)"></v-list-item-action-text>
                                <v-list-item-action-text class="caption font-weight-bold" :class="statusColor(item.status)" v-text="item.status"></v-list-item-action-text>
                            </v-list-item-action>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>
            <v-card v-else flat class="text-center pt-16">
                <v-card-text>
                    <v-icon x-large>mdi-food-fork-drink</v-icon>
                    <div class="subtitle-1 dark--text">Wah, Pesananmu Masih Kosong</div>
                    <div class="caption">Yuk pesan makanan atau minuman, RadjaGo siap anter ke tempat kamu!</div>
                </v-card-text>
            </v-card>
            <!-- content -->
        </v-container>
        <BottomNav/>
    </section>
</template>
<script>
import BottomNav from '../../components/BottomNav.vue';
export default {
    components: {
        BottomNav
    },
    data() {
        return {
            isLoading: false,
            orders: [],
        }
    },

    computed: {
        filteredOrder() {
            return _.sortBy( this.orders, ['created_at', 'status']).reverse();
        }
    },

    created () {
        this.initialize()
    },

    methods: {
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
                const response = await axios.get(`/api/orders`);
                this.orders = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        indoDate(val, day = true, time = true) {
            var hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var bulan = ['Januari', 'Februari', 'Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

            var tanggal = new Date(val).getDate();
            var xhari = new Date(val).getDay();
            var xbulan = new Date(val).getMonth();
            var xtahun = new Date(val).getYear();
            var xwaktu = time ? new Date(val).toLocaleTimeString() : '';

            var hari = day ? hari[xhari] +', ' : '';
            var bulan = bulan[xbulan];
            var tahun = (xtahun < 1000)?xtahun + 1900 : xtahun;

            return hari + tanggal + ' ' + bulan + ' ' + tahun + ' ' + xwaktu;
        },

        statusColor(val) {
            switch(val) {
                case 'RECEIVED':
                    return 'warning--text'
                    break;
                case 'TAKEN':
                    return 'primary--text'
                    break;
                case 'PAID':
                    return 'success--text'
                    break;
                case 'CANCEL':
                    return 'danger--text'
                    break;
                default:
                    return 'black--text'
            }
        },
    }
};
</script>