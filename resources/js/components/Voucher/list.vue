<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Vouchers</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">List Vouchers</div>
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
                                v-on:keyup="searchVouchers(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
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
                                    <select name="filter_by" id="filter_by" class="form-control"
                                        v-model="filter.filter_by">
                                        <option :value="'id'">Id</option>
                                        <option :value="'code'">Voucher Code</option>
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
                                        <flat-pickr v-model="filter.date" :config="config"
                                            class="date-rage-picker form-control" placeholder="Select date range"
                                            @on-close="doSomethingOnClose" name="date">
                                        </flat-pickr>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="resetFilter"
                                    class="btn btn-default mb-2 mr-3">Reset</button>
                                <button type="button" @click="searchVouchers(1)"
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
                                <th>#</th>
                                <th>Voucher Code</th>
                                <th>Redeemed At</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( voucher , index ) in vouchers" :key="voucher.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ voucher.code }}</td>
                                <td>
                                    <span v-if="voucher.redeemed_at" >{{ voucher.redeemed_at }}</span>
                                    <span v-if="!voucher.redeemed_at">Not yet redeemed</span>
                                </td>
                                <td>{{ voucher.created_at }} </td>
                                <td>
                                    <button role="button" :disabled="voucher.redeemed_at" @click="redeemedVoucher(voucher.id, index)"
                                        class="btn btn-success">Redeemed</button>
                                </td>
                            </tr>
                            <tr v-show="!vouchers.length">
                                <td colspan="5" class="no-data-found-info">No Vouchers found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchVouchers" :container-class="'pagination float-right'"
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
                vouchers: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {},
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
            this.getVouchers();
        },
        methods: {
            getVouchers: function () {
                axios.get('/voucher/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchVouchers: function (page = 1) {
                console.log(this.filter);

                axios.post('/voucher/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchVouchers(1);
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
                this.searchVouchers(1);
            },
            redeemedVoucher: function (id, index) {
                axios.get('/voucher/redeemed/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.searchVouchers(this.currentPage);
                    });
            }
        }
    }

</script>
