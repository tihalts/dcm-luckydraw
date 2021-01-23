<template>
    <div class="col-md-9">
        <div class="container-fluid container-fh">
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">List Spin & Wins</div>
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
                                v-on:keyup="searchSpinner(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Gift Code</th>
                                <th>Spin At</th>
                                <th>Issued By</th>
                                <th>Created By</th>
                                <th v-if="$can('admin')" class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( spinner , index ) in spinners" :key="spinner.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-description" v-if="spinner.gift">
                                            <b>{{ spinner.gift.name }}  </b>
                                            <span>({{spinner.code}})</span>
                                        </span>
                                        <span class="table__cell-widget-description" v-else>No gift found</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-description" v-if="spinner.spin_at">{{ spinner.spin_at }}</span>
                                        <span class="table__cell-widget-description red" v-else>Not yet spin</span>
                                    </div>
                                </td>

                                <td>
                                    <div class="table__cell-widget" v-if="spinner.provider">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ spinner.provider.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ spinner.provider.email }}</span>
                                        <span class="table__cell-widget-description"><b>Mobile :</b> {{ spinner.provider.mobile }}</span>
                                    </div>
                                </td>
                                <td>{{ spinner.created_at }} </td>
                                <td>
                                   <button v-if="spinner.gift" :id="'sc' + spinner.id" role="button" @click="sendNotifications(spinner.id)"
                                        class="btn btn-info">SEND MSG</button>
                                </td>
                            </tr>
                            <tr v-show="!spinners.length">
                                <td v-if="$can('admin')" colspan="7" class="no-data-found-info">No spinner found</td>
                                <td v-if="!$can('admin')" colspan="6" class="no-data-found-info">No spinner found</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchSpinner" :container-class="'pagination float-right'"
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
                spinners: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    "searchText": "",
                    "itemPerPage" : 15,
                    "orderby" : "desc",
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.searchSpinner(1);
        },
        methods: {
            searchSpinner: function (page) {
                this.filter.promoter_id = this.$route.params.promoter_id;
                axios.post('/customer/spinandwins/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.spinners = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            sendNotifications: function(id){
                axios.get('/spinner/winner/notification/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        $('sc' + id).prop('disabled', true);
                        notification('Send Notification', 'Send Notification successfull.!');
                    }).catch((error) => {
                        $('sc' + id).prop('disabled', false)
                        notification('Send Notification', 'Send Notification Faild.!');
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchSpinner(1);
            }
        }
    }

</script>
