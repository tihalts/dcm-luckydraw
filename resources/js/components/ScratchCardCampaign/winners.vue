<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Winners</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Customers</div>
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
                                v-on:keyup="searchUsers(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Mobile</th>
                                <th>Total Points</th>
                                <th>Total Amount</th>
                                <th>Created At</th>
                                <!-- <th class="hidden-sm" v-if="user.role == 'admin'">Action</th> -->
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(customer , index) in customers" :key="customer.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>
                                    <div class="table__cell-widget">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b> {{ customer.cpr }}</span>
                                    </div>
                                </td>
                                <td>{{ customer.mobile }} </td>
                                <td>{{ customer.points }} </td>
                                <td>{{ customer.purchase_amount }} </td>
                                <td>{{ customer.created_at }} </td>
                                <!-- <td class="table__cell-actions" v-if="user.role == 'admin'">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editCustomer', params: { 'customer_id': customer.id }}"
                                                    tag="a">Edit</router-link>
                                                <a  class="dropdown-item"
                                                    @click="remove(customer.id , index)"
                                                    href="javascript:void(0)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td> -->
                            </tr>
                            <tr v-show="!customers.length">
                                <td colspan="7" class="no-data-found-info">No Winners found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchUsers" :container-class="'pagination float-right'"
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
                customers: [],
                totalItems: null,
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.getUsers();
        },
        methods: {
            getUsers: function () {
                axios.get('/scratch/card/winners/list' , { params : {'campaign_id' : this.$route.params.campaign_id}})
                    .then(r => r.data)
                    .then((response) => {
                        this.customers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                        this.user = response.user;
                    });
            },
            searchUsers: function (page) {
                this.filter.campaign_id = this.$route.params.campaign_id;
                axios.post('/scratch/card/winners/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.customers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchUsers(1);
            },
        }
    }

</script>
