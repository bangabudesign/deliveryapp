<template>
    <section>
        <v-app-bar app color="white" light elevate-on-scroll>
            <v-toolbar-title>Profile</v-toolbar-title>
        </v-app-bar>
        <v-container class="bg-white px-0" style="padding-bottom: 64px;">
            <!-- content -->
            <v-list>
                <v-list-item-group>
                    <template v-for="(item, index) in orders">
                        <v-list-item :key="item.id" dense>
                            <v-list-item-content>
                                <v-list-item-title v-text="item.resto"></v-list-item-title>
                                <v-list-item-subtitle v-text="item.date"></v-list-item-subtitle>
                            </v-list-item-content>
                            <v-list-item-action>
                                <v-list-item-action-text class="subtitle-1 font-weight-regular" v-text="item.price"></v-list-item-action-text>
                            </v-list-item-action>
                        </v-list-item>
                    </template>
                </v-list-item-group>
            </v-list>            
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

    created () {
        this.initialize()
    },

    methods: {
        async initialize() {
            this.orders = [
                {
                    id: 1,
                    resto: 'Hayo Kebab And Burger - Cempaka Raya',
                    price: 'Rp45,000',
                    date: '30 Mei 2022'
                },
                {
                    id: 2,
                    resto: 'Rocket Chicken - Teluk Dalam',
                    price: 'Rp30,000',
                    date: '30 Mei 2022'
                },
                {
                    id: 3,
                    resto: 'Dapur 3 Badingsanak - Kertak Ilir',
                    price: 'Rp25,000',
                    date: '30 Mei 2022'
                },
                {
                    id: 4,
                    resto: 'Warung Ayuja - Pasar Lama',
                    price: 'Rp40,000',
                    date: '30 Mei 2022'
                }
            ]
        }
    }
};
</script>