<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Purchase Report by Customers</h2>
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
                                <button class="btn btn-info dropdown-toggle" type="button"
                                    data-toggle="dropdown">Export</button>
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
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Customers</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( purchase , index ) in purchases" :key="purchase.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ purchase.date }}</td>
                                <td>{{ purchase.customers }} </td>
                            </tr>
                            <tr v-show="!purchases.length">
                                <td colspan="8" class="no-data-found-info">No Purchases found</td>
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
                purchases: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    'orderby': 'desc'
                },
                form: {},
                totalPages: 0,
                campaigns: [],
                campaign: {},
                customers: [],
                customer: {},
                categories: [],
                category: {},
                users: [],
                user: {},
                shops: [],
                shop: {},
                chart_purchases: {},
                config: {
                    mode: "range",
                    maxDate: "today",
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'j M Y',
                    altInput: true,
                    dateFormat: 'Y-m-d H:i'
                },
                chartDataHeader: ['Year', 'Sales'],
                updatedChartData: [],
                chartOptions: {
                    chart: {
                        title: 'Sales Performance',
                        subtitle: 'Sales'
                    },
                    bars: 'vertical',
                }
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
            this.getPurchases();
            //this.getBusinesTypes();
            //this.getPurchaseCharts();
        },
        created() {
            
            //this.updateDatas ();
        },
        methods: {
            getPurchaseCharts: function (config) {
                axios.get('/fetch/purchase/linechart' , config)
                    .then(r => r.data)
                    .then((response) => {
                        const chart_purchases = response.data;
                        this.updatedChartData = Object.keys(chart_purchases).map(function(key) {
                                                       return [key, parseFloat(chart_purchases[key])];
                                                    });
                    });
            },
            getBusinesTypes: function () {
                axios.get('/fetch/shop/business-types')
                    .then(r => r.data)
                    .then((response) => {
                        this.categories = response.data;
                    });
            },
            getPurchases: function () {
                axios.get('/report/purchase-by-customers')
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        //this.filter = {};
                    });
            },
            searchPurchases: function (page) {
                this.resetControl();
                this.filter.page = page;
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.shop) this.filter.shop_id = this.shop.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                var config = {
                    'params': this.filter
                };
                axios.get('/purchase/reports', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        window.scrollTo(0,0);
                        this.getPurchaseCharts(config);
                    });
            },
            exportdata: function () {
                this.resetControl();
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.shop) this.filter.shop_id = this.shop.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/purchase/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            exportPdf: function () {
                this.resetControl();
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.shop) this.filter.shop_id = this.shop.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/purchase/pdf/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchPurchases(1);
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
            onShopSearch(search, loading) {
                loading(true);
                this.searchShops(loading, search, this);
            },
            searchShops: _.debounce((loading, search, vm) => {
                //if (!vm.filter['category_id']) return;
                axios.get('/fetch/shops', {
                        params: {
                            'searchText': search,
                            'category_id': vm.filter['category_id'] ? vm.filter['category_id'] : null
                        }
                    })
                    .then(r => r.data).then((response) => {
                        vm.shops = response.data;
                        loading(false);
                    });
            }, 350),
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
                        'searchText': search
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
                this.filter.shop_id = null;
                this.filter.customer_id = null;
                this.filter.campaign_id = null;
            },
            resetFilter: function () {
                this.shop = null;
                this.user = null;
                this.customer = null;
                this.campaign = null;
                this.filter = {
                    'category_id': null,
                    'user_id': null,
                    'shop_id': null,
                    'customer_id': null,
                    'campaign_id': null,
                    'orderby': 'desc'
                };
            }
        }
    }

</script>
