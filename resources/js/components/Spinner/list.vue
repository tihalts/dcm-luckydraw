<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Spin & Wins</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Spin & Wins</div>
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
                                v-on:keyup="searchSpinAndWins(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createSpinAndWins" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Total Gifts</th>
                                <th>Total UnGifts</th>
                                <th>Today / Yesterday Gifts</th>
                                <th>Today / Yesterday UnGifts</th>
                                <th>Start At</th>
                                <th>End At</th>
                                <th>Status</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(spinwin , index) in spinwins" :key="spinwin.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ spinwin.name }}</td>
                                <td>{{ spinwin.total_gift_items }} </td>
                                <td>{{ spinwin.total_ungift_items }} </td>
                                <td>{{ spinwin.today_gift_items }} / {{ spinwin.yesterday_gift_items }}</td>
                                <td>{{ spinwin.today_gifted_items }} / {{ spinwin.yesterday_gifted_items }}</td>
                                <td>{{ spinwin.start_at }} </td>
                                <td>{{ spinwin.end_at }} </td>
                                <td>
                                    <span v-if="spinwin.status" class="badge badge-success badge-rounded mb-3 mr-3">Active</span>
                                    <span v-if="!spinwin.status" class="badge badge-danger badge-rounded mb-3 mr-3">Deactivated</span>
                                </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editSpinAndWin', params: { 'spinner_id': spinwin.id }}"
                                                    tag="a">Edit</router-link>
                                                <!-- <a  class="dropdown-item"
                                                    @click="spinwinWinners(spinwin.id )"
                                                    href="javascript:void(0)">Winners</a> -->
                                                <a class="dropdown-item" @click="spinwinGifts(spinwin.id )"
                                                    href="javascript:void(0)">Gifts</a>
                                                <a v-if="spinwin.status" class="dropdown-item"
                                                    @click="remove(spinwin.id , index)"
                                                    href="javascript:void(0)">Deactivated</a>
                                                <a v-if="!spinwin.status" class="dropdown-item"
                                                    @click="active(spinwin.id , index)"
                                                    href="javascript:void(0)">Activated</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!spinwins.length">
                                <td colspan="11" class="no-data-found-info">No SpinAndWins found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchSpinAndWins" :container-class="'pagination float-right'"
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
                spinwins: [],
                totalItems: null,
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
            }
        },
        mounted: function () {
            this.getSpinAndWins();
        },
        methods: {
            getSpinAndWins: function () {
                axios.get('/spinner/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.spinwins = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchSpinAndWins: function (page) {
                axios.post('/spinner/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.spinwins = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchSpinAndWins(1);
            },
            remove: function (id, index) {
                axios.delete('/spinner/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.spinwins[index] = response.data;
                        this.spinwins.push();
                    });
            },
            active: function (id, index) {
                axios.get('/spinner/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.spinwins[index] = response.data;
                        this.spinwins.push();
                    });
            },
            spinwinWinners: function (id) {
                this.$router.push({
                    path: '/spin-and-wins/' + id + '/winners'
                });
            },
            createSpinAndWins: function () {
                this.$router.push({
                    name: 'createSpinAndWin'
                });
            },
            spinwinGifts: function (id) {
                this.$router.push({
                    path: '/spin-and-wins/' + id + '/gifts'
                });
            }
        }
    }

</script>
