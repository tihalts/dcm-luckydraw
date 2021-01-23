<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Voucher Wise Vouchers</h2>
                </div>
                <div class="page-content__header-meta">
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group" style="float:left;">
                                <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                    placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="dropdown mr-3">
                                <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown">Export</button>
                                <div class="dropdown-menu">
                                   <a class="dropdown-item" @click="exportPdf('excel')" href="javascript:void(0)">Excel</a>
                                    <a class="dropdown-item" @click="exportPdf('pdf')" href="javascript:void(0)">PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Voucher Wise Vouchers</div>
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
                                v-on:keyup="searchVouchers(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <!-- <a @click="createUser" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a> -->
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Campaign Name</th>
                                <th>Date</th>
                                <th>Total Vouchers</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( voucher , index ) in vouchers" :key="voucher.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ voucher.campaign.name }} </td>
                                <td>{{ voucher.date }} </td>
                                <td>{{ voucher.total_vouchers }} </td>
                            </tr>
                            <tr v-show="!vouchers.length">
                                <td colspan="4" class="no-data-found-info">No Vouchers found</td>
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
<style scoped>
    .input.date-rage-picker {
        background: white !important;
    }

    .purchase-row {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-bottom: 1px solid #0303031a;
    }

    .remove-btn {
        padding: 2px 7px !important;
        font-size: 14px !important;
        margin-top: 30px;
    }

    img.avatar_image {
        height: auto;
        max-width: 2.5rem;
        margin-right: 1rem;
    }

    .d-center {
        display: flex;
        align-items: center;
    }

    .selected img {
        width: auto;
        max-height: 23px;
        margin-right: 0.5rem;
    }

    .v-select .dropdown li {
        border-bottom: 1px solid rgba(112, 128, 144, 0.1);
    }

    .v-select .dropdown li:last-child {
        border-bottom: none;
    }

    .v-select .dropdown li a {
        padding: 0px 20px;
        width: 100%;
        font-size: 1.25em;
        color: #3c3c3c;
    }

    .v-select .dropdown-menu .active>a {
        color: #fff;
    }

    .vue-tel-input.is-invalid {
        border: 1px solid #ec4949 !important;
    }

</style>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                vouchers: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    'orderby': 'desc'
                },
                totalPages: 0,
                voucher: {},
                errors: null,
                config: {
                    mode: "range",
                    maxDate: "today",
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'j M Y',
                    altInput: true,
                    dateFormat: 'Y-m-d H:i'
                },
            }
        },
        mounted: function () {
            this.getVouchers();
        },
        methods: {
            getVouchers: function () {
                axios.get('/reports/voucher/by-day/list' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                    });
            },
            searchVouchers: function (page) {
                this.filter.page = page;
                var config = {
                    'params': this.filter
                };

                axios.get('/reports/voucher/by-day/list', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        window.scrollTo(0,0);
                    });
            },
            exportdata: function () {
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/voucher/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            exportPdf: function(type){
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/reports/voucher/by-day/' + type,
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchVouchers(1);
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchVouchers(1);
            },
        }

    }

</script>
