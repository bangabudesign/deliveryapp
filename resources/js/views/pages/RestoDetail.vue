<template>
    <section>
        <v-app-bar app color="white" light inverted-scroll elevation-1>
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-app-bar-title class="pl-0" v-text="restaurant.name + ' - ' +restaurant.address"></v-app-bar-title>
        </v-app-bar>
        <v-toolbar color="primary" dark dense flat class="mx-auto" height="100px" src="https://cdn.vuetifyjs.com/images/cards/cooking.png">
            <v-btn icon @click="goBack">
                <v-icon>mdi-arrow-left</v-icon>
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn icon>
                <v-icon>mdi-heart</v-icon>
            </v-btn>
        </v-toolbar>
        <v-container class="bg-white" style="min-height: calc(100vh - 148px);">
            <v-card class="mx-auto mb-4 rounded-lg" style="margin-top: -32px;">
                <v-card-title class="pb-0" v-text="restaurant.name + ' - ' +restaurant.address"></v-card-title>
                <v-list>
                    <v-list-item dense>
                        <v-list-item-avatar>
                            <v-icon class="grey lighten-1" dark>mdi-star</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>4.7 (237)</v-list-item-title>
                            <v-list-item-subtitle>Rating dan Ulasan</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-icon class="my-auto">
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-list-item-icon>
                    </v-list-item>
                    <v-list-item dense>
                        <v-list-item-avatar>
                            <v-icon class="grey lighten-1" dark>mdi-motorbike</v-icon>
                        </v-list-item-avatar>
                        <v-list-item-content>
                            <v-list-item-title>Jarak</v-list-item-title>
                            <v-list-item-subtitle>{{ restaurant.distance.toFixed(2)+' km dari tempat Anda' }}</v-list-item-subtitle>
                        </v-list-item-content>
                        <v-list-item-icon class="my-auto">
                            <v-icon>mdi-chevron-right</v-icon>
                        </v-list-item-icon>
                    </v-list-item>
                </v-list>
            </v-card>
            <h2 class="text-h6 mb-4">Recomended</h2>
            <template v-if="isLoading || restaurant.products.length == 0">
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
            <v-card v-else class="d-flex flex-no-wrap mb-3 rounded-lg" v-for="(item, index) in restaurant.products" :key="item.id" outlined @click="showDetail(item)">
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
            <!-- dialog detail -->
            <v-dialog v-model="dialogDetail" scrollable max-width="400px" content-class="rounded-xl">
                <v-card flat class="rounded-xl">
                <v-toolbar flat dark color="primary" prominent :src="detailItem.image_url" height="200px">
                    <v-btn icon dark @click="dialogDetail = false">
                        <v-icon>mdi-close</v-icon>
                    </v-btn>
                </v-toolbar>
                <v-card-title class="subtitle-1 mb-3" v-text="detailItem.name">
                </v-card-title>
                <v-card-subtitle class="text-h5" v-text="numberFormat(detailItem.price)"></v-card-subtitle>
                <v-card-text>
                    <div class="d-flex justify-center align-center mb-5">
                        <v-btn color="accent" dark fab small depressed @click="decreaseQty()">
                            <v-icon>mdi-minus</v-icon>
                        </v-btn>
                        <div class="px-5 text-h6 text-dark" v-text="detailItem.qty"></div>
                        <v-btn color="accent" dark fab small depressed @click="increaseQty()">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <v-textarea v-model="detailItem.note" label="Catatan Tambahan" rows="2"></v-textarea>
                    <v-btn color="primary" class="rounded-pill" large block @click="addToCart(detailItem)" :loading="isLoading">{{ detailItem.cart_id ? 'Update Cart' : 'Add to Cart - ' + numberFormat(detailItem.price) }}</v-btn>
                    <v-btn v-if="detailItem.cart_id" color="danger" text class="rounded-pill mt-3 red lighten-5" large block @click="deleteFromCart(detailItem)" :loading="isLoading">Remove from Cart</v-btn>
                </v-card-text>
                </v-card>
            </v-dialog>
            <!-- end dialog detail -->
            <cart :restaurant="restaurant" :updateCart="updateCart" @cartData="sycronize" />
        </v-container>
    </section>
</template>

<script>
import Cart from '../../components/Cart.vue';
export default {
    props: ['id'],
    components: {
        Cart
    },
    data() {
        return {
            isLoading: false,
            updateCart: false,
            restaurant: {
                name: "Resto Name"
            },
            dialogDetail: false,
            detailItem: {
                restaurant_id: '',
                product_id: '',
                image_url: '',
                name: '',
                price: '',
                qty: 1,
                note: ''
            }
        }
    },

    created () {
        this.initialize()
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
                const response = await axios.get(`/api/restaurants/${this.id}`);
                this.restaurant = response.data.data
                this.isLoading = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async showDetail(item) {
            this.isLoading = true
            this.detailItem = Object.assign({}, {
                cart_id: item.cart_id,
                restaurant_id: this.restaurant.id,
                product_id: item.id,
                image_url: item.image_url,
                name: item.name,
                price: item.price,
                qty: item.qty || 1,
                note: item.note
            })
            this.dialogDetail = true
            this.isLoading = false
        },

        decreaseQty() {
            if(this.detailItem.qty > 1) {
                this.detailItem.qty = this.detailItem.qty - 1
            }
        },

        increaseQty() {
            this.detailItem.qty = this.detailItem.qty + 1
        },

        async addToCart(item) {
            this.isLoading = true
            try {
                const response = await axios.post(`/api/carts`, item);
                this.isLoading = false
                this.updateCart = !this.updateCart
                this.dialogDetail = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        async deleteFromCart(item) {
            this.isLoading = true
            try {
                const response = await axios.delete(`/api/carts/${item.cart_id}`);
                this.isLoading = false
                this.updateCart = !this.updateCart
                var productIndex = this.restaurant.products.findIndex((product => product.id == item.product_id));
                this.restaurant.products[productIndex].cart_id = undefined
                this.restaurant.products[productIndex].qty = 1
                this.restaurant.products[productIndex].note = ''
                this.dialogDetail = false
            } catch (error) {
                this.isLoading = false
                this.error = error.response.data
            }
        },

        sycronize(carts){
            for (let i = 0; i < carts.length; i++) {
                var productIndex = this.restaurant.products.findIndex((product => product.id == carts[i].product_id));
                this.restaurant.products[productIndex].cart_id = carts[i].id
                this.restaurant.products[productIndex].qty = carts[i].qty
                this.restaurant.products[productIndex].note = carts[i].note
            }
        }
    }
};
</script>