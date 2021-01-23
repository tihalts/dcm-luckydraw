<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Purchase Promoter Wise Report</h2>
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
                                    <a class="dropdown-item" @click="exportData('excel')" href="javascript:void(0)">Excel</a>
                                    <a class="dropdown-item" @click="exportData('pdf')" href="javascript:void(0)">PDF</a>
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
                                                <label for="email">Shop Category</label>
                                                <select name="business_type_id" id="business_type_id"
                                                    class="form-control" v-model="filter.category_id">
                                                    <option v-for="type in categories" :key="type.id" :value="type.id">
                                                        {{ type.name }}</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Shop</label>
                                                <v-select label="name" :filterable="false" :options="shops"
                                                    @search="onShopSearch" v-model="shop" :class="form-control"
                                                    style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search shops..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.name }}
                                                        </div>
                                                        <span> {{ option.shop_no }}</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.name }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>                         
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Campaign Groups</label>
                                                <v-select label="name" :filterable="false" :options="groups"
                                                    @search="onCampaignGroupSearch" v-model="group" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search campaign groups..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.name }}
                                                        </div>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.name }}
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
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.name }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <!-- <div class="form-group col-md-4">
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
                                            </div> -->
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
                                                <div class="form-group">
                                                    <label for="country_id">Country</label>
                                                    <v-select label="name" v-model="country" @change="filter.country_iso = country ? country.iso : null"
                                                        :options="countries" style="width:100%;"></v-select>
                                                </div>
                                            </div>   
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="customer_type">Customer Type</label>
                                                    <select name="customer_type" class="form-control" v-model="filter.customer_type" id="customer_type">
                                                        <option :value="''" selected>All Customers</option>
                                                        <option :value="'new'">New Customers</option>
                                                        <option :value="'old'">OLD Customers</option>
                                                    </select>
                                                </div>
                                            </div>   
                                        </div>
                                        <hr />
                                        <div class="form-group text-right m-b-0">
                                            <button type="button" @click="resetFilter" class="btn btn-default mb-2 mr-3">Reset</button>
                                            <button type="button" @click="searchReports(1)" class="btn btn-success mb-2 mr-3">Search</button>
                                        </div>
                                        <!-- /.box-footer -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ total_amount }}</div>
                                <div class="widget-alpha__description">Total Amount</div>
                            </div> 
                            <!-- <span class="widget-alpha__icon icon-fa fa fa-users"></span> -->
                        </div>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-widget frappe-chart">
                            <h3>Scratch Card Report</h3>
                            <GChart
                                type="AreaChart"
                                :data="chartData"
                                :options="chartOptions"
                               />
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-widget frappe-chart">
                            <h3>Scratch Card Report</h3>
                            <GChart
                                type="BarChart"
                                :data="chartData"
                                :options="chartOptions"
                               />
                        </div>
                    </div>
                </div> -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group p-1 pull-right">                            
                            <select name="customer_type" class="form-control" style="min-width:80px;max-width:80px;background: white;" v-model="currentPage" @change="searchReports(currentPage)">
                                <option v-for="item in totalPages" :key="item">{{item}}</option>
                            </select> 
                            <label>of {{ totalPages}} page(s)</label>                                
                        </div>                           
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-t-3 m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Promoter</th>
                                <th>Amount</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( report , index ) in reports" :key="report.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>
                                   <div class="table__cell-widget">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ report.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b>
                                            {{ report.email }}</span>
                                        <span class="table__cell-widget-description"><b>Mobile :</b>
                                            {{ report.mobile }}</span>
                                    </div>
                                </td>
                                <td>
                                    {{ report.total }}
                                </td>
                                <td>
                                    {{ report.created_at }}
                                </td>
                            </tr>
                            <tr v-show="!reports.length">
                                <td colspan="7" class="no-data-found-info">No data found</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <!-- <div class="col mb-5">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchReports(1)" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div> -->
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

    export default {
        data() {
            return {
                reports: [],
                totalItems: null,
                currentPage: null,
                filter: {'orderby' : 'desc'},
                form: {},
                totalPages: 0,
                campaigns: [],
                campaign: {},
                customers: [],
                customer: {},
                users: [],
                user: {},
                groups: [],
                group: {},
                new_users: 0,
                old_users: 0,
                total_users: 0,
                countries: [],
                country: {},
                total_amount: 0,
                shop: {},
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
            this.getCountries();
            this.getBusinesTypes();
            this.getReports();
        },
        methods: {
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
            },
            getReports: function () {
                axios.get('/purchase-promoter-report')
                    .then(r => r.data)
                    .then((response) => {
                        this.reports = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.total_amount = response.total_amount;
                        // this.new_users = response.new_users;
                        // this.old_users = response.old_users;
                        // this.total_users = response.total_users;
                    });
            },
            searchReports: function (page) {
                this.filter.page = page;
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                if (this.group) this.filter.group_id = this.group.id;
                if (this.shop) this.filter.shop_id = this.shop.id;
                var config = { 'params' : this.filter};
                axios.get('/purchase-promoter-report', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.reports = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.total_amount = response.total_amount;
                        //this.getCharts(config);
                        //window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchReports(1);
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchReports(1);
            },
            onUserSearch(search, loading) {
                loading(true);
                this.searchUsers(loading, search, this);
            },
            searchUsers: _.debounce((loading, search, vm) => {
                axios.get('/fetch/users', {
                        params: {
                            'searchText': search,
                            'campaign_type' : 'reward_point',
                        }
                    })
                    .then(r => r.data).then((response) => {
                        vm.users = response.data;
                        loading(false);
                    });
            }, 350),
            onCampaignGroupSearch(search, loading) {
                loading(true);
                this.searchCampaignGroups(loading, search, this);
            },
            searchCampaignGroups: _.debounce((loading, search, vm) => {
                axios.get('/free-campaign-group/list', {
                    params: {
                        'searchText': search
                    }
                }).then(r => r.data).then((response) => {
                    vm.groups = response.data;
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
                        'group_id': vm.group ? vm.group.id : null,
                        'campaign_type': 'free_shop'
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
            getBusinesTypes: function () {
                axios.get('/fetch/shop/business-types')
                    .then(r => r.data)
                    .then((response) => {
                        this.categories = response.data;
                    });
            },
            resetControl: function(){
                this.filter.user_id = null;
                this.filter.customer_id = null;
                this.filter.campaign_id = null;
                this.filter.group_id = null;
            },
            resetFilter: function(){
                this.user = null;
                this.customer = null;
                this.campaign = null;
                this.group = null;
                this.shop = null;
                this.filter = {
                    'category_id': null,
                    'user_id': null,
                    'customer_id': null,
                    'campaign_id': null,
                    'group_id': null,
                    'shop_id': null,
                    'orderby': 'desc'
                };
            },
            exportData: function(type){
                let routeData = this.$router.resolve({
                    path: '/purchase-promoter-report/' + type,
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
        }
    }

</script>
