<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Vouchers</h2>
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
                <div class="row">
                    <div class="col-12">
                        <div class="order-collapse green">
                            <a class="order-collapse__header collapsed" data-toggle="collapse" href="#collapse1"
                                aria-expanded="false" aria-controls="collapse1">
                                <h4> <i class="fa fa-filter"></i> Filter</h4>
                                <span>
                                    <span class="collapse-icon ua-icon-arrow-down-alt"></span>
                                </span>
                            </a>
                            <div class="order-collapse-inner collapse" id="collapse1" style="">
                                <div style="padding:20px;">
                                    <div class="from-block">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Promoter</label>
                                                <v-select label="fullname" :filterable="false" :options="users"
                                                    @search="onUserSearch" v-model="user" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search promoters..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                        <span>{{ option.mobile }}</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Customers</label>
                                                <v-select label="fullname" :filterable="false" :options="customers"
                                                    @search="searchCustomers" v-model="customer" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search customers..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                        <span>{{ option.mobile }}</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Campaigns</label>
                                                <v-select label="name" :filterable="false" :options="campaigns"
                                                    @search="onCampaignSearch" v-model="campaign" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search campaigns..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.name }}
                                                        </div>
                                                        <span v-if="option.campaign_type == 'scratch_card'">Scratch
                                                            Card</span>
                                                        <span v-if="option.campaign_type == 'reward_point'">Reward
                                                            Point</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.name }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="radio-group mt-10" style="margin-top: 24px;">
                                                    <label class="radio-group__item">
                                                        <input type="radio" name="orderby-group"
                                                            v-model="filter.orderby" class="radio-group__input"
                                                            value="asc" checked="">
                                                        <span class="radio-group__text">Asc</span>
                                                    </label>
                                                    <label class="radio-group__item">
                                                        <input type="radio" name="orderby-group"
                                                            v-model="filter.orderby" class="radio-group__input"
                                                            value="desc">
                                                        <span class="radio-group__text">Desc</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr />
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
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-widget frappe-chart">
                            <h3>Voucher Report</h3>
                            <GChart
                                type="AreaChart"
                                :data="chartData"
                                :options="chartOptions"
                               />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-widget frappe-chart">
                            <h3>Voucher Report</h3>
                            <GChart
                                type="BarChart"
                                :data="chartData"
                                :options="chartOptions"
                               />
                        </div>
                    </div>
                </div> -->
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voucher Code</th>
                                <th>Customer Name</th>
                                <th>Voucher Amount</th>
                                <th>Redeemed At</th>
                                <th>Issued By</th>
                                <th>Created By</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( voucher , index ) in vouchers" :key="voucher.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ voucher.code}} </td>
                                <td>
                                    <div class="table__cell-widget" v-if="voucher.customer">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ voucher.customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b>
                                            {{ voucher.customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b>
                                            {{ voucher.customer.cpr }}</span>
                                    </div>
                                </td>
                                <td>{{ voucher.voucher_amount }} </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-description"
                                            v-if="voucher.redeemed_at">{{ voucher.redeemed_at }}</span>
                                        <span class="table__cell-widget-description red" v-else>Not yet redeemed</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget" v-if="voucher.provider">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ voucher.provider.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b>
                                            {{ voucher.provider.email }}</span>
                                        <span class="table__cell-widget-description"><b>Mobile :</b>
                                            {{ voucher.provider.mobile }}</span>
                                    </div>
                                </td>
                                <td>{{ voucher.created_at }} </td>
                            </tr>
                            <tr v-show="!vouchers.length">
                                <td colspan="7" class="no-data-found-info">No Vouchers found</td>
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
        padding: 10px 20px;
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
    import {
        GChart
    } from 'vue-google-charts';

    export default {
        data() {
            return {
                vouchers: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    'orderby': 'desc'
                },
                form: {},
                totalPages: 0,
                voucher: {},
                errors: null,
                campaigns: [],
                campaign: {},
                customers: [],
                customer: {},
                users: [],
                user: {},
                config: {
                    mode: "range",
                    maxDate: "today",
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'j M Y',
                    altInput: true,
                    dateFormat: 'Y-m-d H:i'
                },
                chartDataHeader: ['Date', 'Vouchers'],
                updatedChartData: [],
                chartOptions: {
                    chart: {
                        title: 'Voucher Report',
                        subtitle: 'Voucher'
                    },
                    bars: 'vertical',
                },
            }
        },
        components: {
            GChart
        },
        computed: {
            chartData () {
                return [ this.chartDataHeader, ...this.updatedChartData ];
            }
        },
        mounted: function () {
            this.getVouchers();
            this.getCharts();
        },
        methods: {
            getCharts: function (config = {}) {
                axios.get('/fetch/voucher/linechart' , config)
                    .then(r => r.data)
                    .then((response) => {
                        const charts = response.data;
                        this.updatedChartData = Object.keys(charts).map(function(key) {
                                                       return [key, parseFloat(charts[key])];
                                                    });
                    });
            },
            getVouchers: function () {
                axios.get('/voucher/reports')
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
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                var config = {
                    'params': this.filter
                };

                axios.get('/voucher/reports', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.getCharts(config);
                        window.scrollTo(0,0);
                    });
            },
            exportdata: function () {
                this.resetControl();
                if (this.user.id) this.filter.user_id = this.user.id;
                if (this.campaign.id) this.filter.campaign_id = this.campaign.id;
                if (this.customer.id) this.filter.customer_id = this.customer.id;
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
                this.resetControl();
                if (this.user.id) this.filter.user_id = this.user.id;
                if (this.campaign.id) this.filter.campaign_id = this.campaign.id;
                if (this.customer.id) this.filter.customer_id = this.customer.id;
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/voucher/' + type + '/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchVouchers(1);
            },
            getBusinesTypes: function () {
                axios.get('/fetch/shop/business-types')
                    .then(r => r.data)
                    .then((response) => {
                        this.categories = response.data;
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchVouchers(1);
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchVouchers(1);
            },
            removePurchase: function (id, index) {
                axios.delete('/purchase/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases.splice(index, 1);
                    });
            },
            onUserSearch(search, loading) {
                loading(true);
                this.searchUsers(loading, search, this);
            },
            searchUsers: _.debounce((loading, search, vm) => {
                axios.get('/fetch/users', {
                        params: {
                            'searchText': search
                        }
                    })
                    .then(r => r.data).then((response) => {
                        vm.users = response.data;
                        loading(false);
                    });
            }, 350),
            onCampaignSearch(search, loading) {
                loading(true);
                this.searchCampaigns(loading, search, this);
            },
            searchCampaigns: _.debounce((loading, search, vm) => {
                axios.get('/fetch/campaigns', {
                    params: {
                        'searchText': search,
                        'campaign_type': 'scratch_card',
                    }
                }).then(r => r.data).then((response) => {
                    vm.campaigns = response.data;
                    loading(false);
                });
            }, 350),
            searchCustomers(search, loading) {
                loading(true);
                this.onSearchCustomers(loading, search, this);
            },
            onSearchCustomers: _.debounce((loading, search, vm) => {
                axios.post('/purchase-customer/search', {
                    'searchText': search
                }).then(r => r.data).then((response) => {
                    vm.customers = response.data;
                    loading(false);
                });
            }, 350),
            resetControl: function () {
                this.filter.user_id = null;
                this.filter.customer_id = null;
                this.filter.campaign_id = null;
            },
            resetFilter: function () {
                this.user = null;
                this.customer = null;
                this.campaign = null;
                this.filter = {
                    'category_id': null,
                    'user_id': null,
                    'customer_id': null,
                    'campaign_id': null,
                    'orderby': 'desc'
                };
            }
        }

    }

</script>
