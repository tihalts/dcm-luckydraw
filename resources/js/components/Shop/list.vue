<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Shops</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Shops</div>
                        <!-- <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter By
                            </div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" @click="OrderBy('asc')" href="javascript:void(0)">ASC</a>
                                <a class="dropdown-item" @click="OrderBy('desc')" href="javascript:void(0)">DESC</a>
                            </div>
                        </div> -->
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" v-model="filter.searchText"
                                v-on:keyup="searchShops(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createShop" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                        <a @click="show_filter = !show_filter" role="button" href="javascript:void(0)"
                            class="mdi mdi-filter dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="main-container" style="padding:10px 20px;" v-if="show_filter">
                    <h1>Fliter</h1>
                    <div style="container">
                        <div class="from-block">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="filter_by">Sort By</label>
                                    <select name="business_type_id" id="business_type_id" class="form-control"
                                        v-model="filter.business_type_id">
                                        <option v-for="type in categories" :key="type.id" :value="type.id">
                                            {{ type.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="filter_by">Sort By</label>
                                    <select name="filter_by" id="filter_by" class="form-control" v-model="filter.filter_by">
                                        <option :value="'id'">Id</option>
                                        <option :value="'name'">Name</option>
                                        <option :value="'created_at'">Created At</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="radio-group mt-10" style="margin-top: 24px;">
                                        <label class="radio-group__item">
                                            <input type="radio" name="orderby-group" v-model="filter.orderby"
                                                class="radio-group__input" value="asc" checked="">
                                            <span class="radio-group__text">Asc</span>
                                        </label>
                                        <label class="radio-group__item">
                                            <input type="radio" name="orderby-group" v-model="filter.orderby"
                                                class="radio-group__input" value="desc">
                                            <span class="radio-group__text">Desc</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="filter_by">Date Between</label>
                                    <div class="input-group">
                                        <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                            placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                                        </flat-pickr>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="resetFilter"
                                    class="btn btn-default mb-2 mr-3">Reset</button>
                                <button type="button" @click="searchShops(1)"
                                    class="btn btn-success mb-2 mr-3">Search</button>
                            </div>
                            <!-- /.box-footer -->
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Shop Number</th>
                                <th>Shop Name</th>
                                <th>Description</th>
                                <th>Business Type</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(shop , index) in shops" :key="shop.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>
                                    <div class="table__cell-widget">
                                        <router-link  :to="{ name:'shopPurchases', params: { 'shop_id': shop.id }}" tag="a" class="table__cell-widget-name">{{ shop.shop_no }}</router-link>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <router-link href="javascript:void(0)" :to="{ name:'shopPurchases', params: { 'shop_id': shop.id }}" tag="a" class="table__cell-widget-name">{{ shop.name }}</router-link>
                                    </div>
                                </td>
                                <td>{{ shop.description }} </td>
                                <td>{{ shop.business_type.name }} </td>
                                <td>{{ shop.created_at }} </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editShop', params: { 'shop_id': shop.id }}"
                                                    tag="a">Edit</router-link>
                                                <a  class="dropdown-item"
                                                    @click="remove(shop.id , index)"
                                                    href="javascript:void(0)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!shops.length">
                                <td colspan="7" class="no-data-found-info">No Shops found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchShops" :container-class="'pagination float-right'"
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
                shops: [],
                totalItems: null,
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                categories: [],
                totalPages: 0,
                show_filter: false,
                config: {
                    mode: "range",
                    maxDate: "today",
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'j M Y',
                    altInput: true,
                    dateFormat: 'Y-m-d H:i'
                }
            }
        },
        mounted: function () {
            this.getShops();
            this.getBusinesTypes();
        },
        methods: {
            getShops: function () {
                axios.get('/shop/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.shops = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                        this.user = response.user;
                    });
            },
            searchShops: function (page) {
                axios.post('/shop/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.shops = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            getBusinesTypes: function () {
                axios.get('/fetch/shop/business-types')
                    .then(r => r.data)
                    .then((response) => {
                        this.categories = response.data;
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchShops(1);
            },
            remove: function (id, index) {
                this.$swal({
                    title: "Are you want to delete this item?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.delete('/shop/remove/' + id)
                            .then(r => r.data)
                            .then((response) => {
                                this.shops.splice(index, 1);
                            });
                    } 
                });                
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
            },
            resetFilter: function () {
                this.filter = {
                    orderby: 'desc'
                };

                this.searchShops(1);
            },
            createShop: function () {
                this.$router.push({
                    path: '/shops/create'
                });
            }
        }
    }

</script>
