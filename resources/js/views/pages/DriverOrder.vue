<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-toolbar-title>Orders</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <!-- content -->
            <v-list>
                <v-list-item-group>
                    <template v-for="(item, index) in orders">
                        <v-list-item :key="item.id" dense @click="detail(item)">
                            <v-list-item-content>
                                <v-list-item-title class="subtitle-2 black--text font-weight-regular" v-text="item.user.name +' - '+item.restaurant.name"></v-list-item-title>
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
            <!-- content -->
        </v-container>
        <DriverOrderDetail :order="orderDetail" :showDialog="showDialog" @closeDialog="close"/>
        <DriverBottomNav/>
    </section>
</template>
<script>

import DriverOrderDetail from '../../components/DriverOrderDetail.vue';
import DriverBottomNav from '../../components/DriverBottomNav.vue';
export default {
    components: {
        DriverOrderDetail,
        DriverBottomNav
    },
    data() {
        return {
            isLoading: false,
            showDialog: false,
            orders: [],
            orderDetail: {}
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
                case 'UNPAID':
                    return 'warning--text'
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

        detail(item) {
            this.orderDetail = item
            this.showDialog = true
        },

        close(val) {
            this.showDialog = val
        }
    }
};
</script>