<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Purchases</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">List Purchases</div>
                        <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter By
                            </div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" @click="OrderBy('asc')" href="javascript:void(0)">ASC</a>
                                <a class="dropdown-item" @click="OrderBy('desc')" href="javascript:void(0)">DESC</a>
                            </div>
                        </div>
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" v-model="filter.searchText"
                                v-on:keyup="searchPurchases(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <!-- <a @click="createPurchase" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a> -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group p-1 pull-right">                            
                            <select name="customer_type" class="form-control" style="min-width:80px;max-width:80px;background: white;" v-model="currentPage" @change="searchPurchases(currentPage)">
                                <option v-for="item in totalPages" :key="item">{{item}}</option>
                            </select> 
                            <label>of {{ totalPages}} page(s)</label>                                
                        </div>                           
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Shop</th>
                                <th>Purchase No</th>
                                <th>Customer Name</th>
                                <th>Purchase Amount</th>
                                <th>Points</th>
                                <th>Created By</th>
                                <th>Purchase At</th>
                                <th v-if="user.role == 'admin'" class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( purchase , index ) in purchases" :key="purchase.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>
                                    <div class="table__cell-widget" v-if="purchase.shop">
                                        <router-link tag="a" :to="{ name:'shopPurchases', params: { 'shop_id': purchase.shop.id }}" class="table__cell-widget-name">
                                            {{ purchase.shop.name }}
                                        </router-link>
                                        <span class="table__cell-widget-description"><b>Shop NO :</b> {{ purchase.shop.shop_no }}</span>
                                    </div>
                                </td>
                                <td>{{ purchase.purchase_no | UpperCase}} </td>
                                <td>
                                    <div class="table__cell-widget" v-if="purchase.customer">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ purchase.customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ purchase.customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b> {{ purchase.customer.cpr }}</span>
                                    </div>
                                </td>
                                <td>{{ purchase.amount }} </td>
                                <td>{{ purchase.points }} </td>
                                <td>{{ purchase.user.fullname }} </td>
                                <td>{{ purchase.created_at }} </td>
                                <td>
                                    <a v-if="user.role == 'admin'" role="button" @click="removePurchase(purchase.id, index)" href="javascript:void(0)"
                                        class="btn btn-danger">Remove</a>
                                </td>
                            </tr>
                            <tr v-show="!purchases.length">
                                <td v-if="user.role == 'admin'" colspan="8" class="no-data-found-info">No Purchases found</td>
                                <td v-if="user.role != 'admin'" colspan="7" class="no-data-found-info">No Purchases found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchPurchases" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div> -->
            </div>
        </div>

    </div>

</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                purchases: [],
                totalItems: null,
                currentPage: null,
                filter: {
                     orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.getPurchases();
        },
        methods: {
            getPurchases: function () {
                axios.get('/purchase/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                        this.user = response.user;
                    });
            },
            searchPurchases: function (page) {
                console.log(this.filter);

                axios.post('/purchase/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchPurchases(1);
            },
            removePurchase: function (id, index) {
                this.$swal({
                    title: "Are you want to delete this item?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                       axios.delete('/purchase/remove/' + id)
                            .then(r => r.data)
                            .then((response) => {
                                this.purchases.splice(index , 1);
                            });
                    } 
                });
                
            },
            createPurchase: function () {
                this.$router.push({
                    path: '/purchases/create'
                });
            }
        }
    }

</script>
