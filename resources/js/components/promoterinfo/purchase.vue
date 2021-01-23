<template>
    <div class="col-md-9">
        <div class="container-fluid container-fh">
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">List Purchases</div>
                        <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter
                                By
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
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Shop</th>
                                <th>Purchase No</th>
                                <th>Purchase Amount</th>
                                <th>Points</th>
                                <th>Created By</th>
                                <th>Purchase At</th>
                                <th v-if="$can('admin')" class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( purchase , index ) in purchases" :key="purchase.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>
                                    <div class="table__cell-widget" v-if="purchase.shop">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ purchase.shop.name }}</a>
                                        <span class="table__cell-widget-description"><b>Shop NO :</b>
                                            {{ purchase.shop.shop_no }}</span>
                                    </div>
                                </td>
                                <td>{{ purchase.purchase_no | UpperCase}} </td>
                                <td>{{ purchase.amount }} </td>
                                <td>{{ purchase.points }} </td>
                                <td>{{ purchase.user.fullname }} </td>
                                <td>{{ purchase.created_at }} </td>
                                <td>
                                    <a v-if="$can('admin')" role="button"
                                        @click="removePurchase(purchase.id, index)" href="javascript:void(0)"
                                        class="btn btn-danger">Remove</a>
                                </td>
                            </tr>
                            <tr v-show="!purchases.length">
                                <td v-if="$can('admin')" colspan="8" class="no-data-found-info">No Purchases
                                    found</td>
                                <td v-if="!$can('admin')" colspan="7" class="no-data-found-info">No Purchases
                                    found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchPurchases" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div>
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
                    "searchText": "",
                    "itemPerPage" : 15,
                    "orderby" : "desc",
                },
                form: {},
                totalPages: 0,
            }
        },
        mounted: function () {
            this.searchPurchases(1);
        },
        methods: {
            searchPurchases: function (page) {
                this.filter.promoter_id = this.$route.params.promoter_id;
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
                axios.delete('/purchase/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases.splice(index, 1);
                    });
            },
        }
    }

</script>
