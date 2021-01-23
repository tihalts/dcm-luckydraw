<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Shop for free Report</h2>
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
                                    <a class="dropdown-item" @click="exportdata" href="javascript:void(0)">Excel</a>
                                    <a class="dropdown-item" @click="exportPdf" href="javascript:void(0)">PDF</a>
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
                                            <div class="form-group col-md-3">
                                                <div class="form-group">
                                                    <label for="country_id">Country</label>
                                                    <v-select label="name" v-model="country" @change="filter.country_iso = country ? country.iso : null"
                                                        :options="countries" style="width:100%;"></v-select>
                                                </div>
                                            </div>   
                                            <div class="form-group col-md-3">
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
                                            <button type="button" @click="searchFreeGiftReport(1)" class="btn btn-success mb-2 mr-3">Search</button>
                                        </div>
                                        <!-- /.box-footer -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ total_users }}</div>
                                <div class="widget-alpha__description">Total Users</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-users"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">{{ new_users }}</div>
                                <div class="widget-alpha__description">New Users</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-users"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-orange widget-alpha--donut">
                            <div>
                                <div class="widget-alpha__amount">{{ old_users }}</div>
                                <div class="widget-alpha__description">Old Users</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-users"></span>
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
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Customer Name</th>
                                <th>Gift Code</th>
                                <th>Issued By</th>
                                <th>Created By</th>
                                <th v-if="admin.role == 'admin'">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( report , index ) in reports" :key="report.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>
                                    <div class="table__cell-widget" v-if="report.customer">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ report.customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ report.customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b> {{ report.customer.cpr }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-description" v-if="report.gift">
                                            <b>{{ report.gift.name }}  </b>
                                            <span>({{report.code}})</span>
                                        </span>
                                        <span class="table__cell-widget-description" v-else>No gift found</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget" v-if="report.provider">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ report.provider.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ report.provider.email }}</span>
                                        <span class="table__cell-widget-description"><b>Mobile :</b> {{ report.provider.mobile }}</span>
                                    </div>
                                </td>
                                <td>{{ report.created_at }} </td>
                                <td v-if="admin.role == 'admin'">
                                    <button role="button"
                                        @click="fetch(report)" class="btn btn-info">Edit</button>
                                </td>
                            </tr>
                            <tr v-show="!reports.length">
                                <td colspan="7" class="no-data-found-info">No Free Gift sfound</td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="col mb-5">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchFreeGiftReport(1)" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div>
            </div>
        </div>
 <div id="modal-gift-edit" class="modal fade custom-modal-tabs">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Edit Voucher</h5>
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                            <div class="content-alert__info">
                                <span class="content-alert__info-icon ua-icon-warning"></span>
                            </div>
                            <div class="content-alert__content">
                                <div class="content-alert__heading"></div>
                                <div class="content-alert__message">
                                    <ul class="custom-alert__list" v-for="(values , key) in errors" :key="key">
                                        <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="gift-code">Gift Code</label>
                                    <input type="text" class="form-control" id="gift-code" name="gift-code"
                                        v-model="gift.code" placeholder="gift code">
                                    <span>Old Code : <b>{{gift.old_code}}</b></span>
                                </div>
                                <div class="form-group">
                                    <label for="email">Select Promoter</label>
                                    <v-select label="fullname" :filterable="false" :options="users"
                                        @search="onUserSearch" v-model="gift.provider" @change="gift.promoter_id = gift.provider ? gift.provider.id : null" style="width:100%;">
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
                                <div class="form-group">
                                    <label for="end_at">Created At</label>
                                    <flat-pickr class="form-control" :config="configs" name="created_At"
                                        v-model="gift.created_at" placeholder="Created At"></flat-pickr>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="button" @click="edit()">Save</button>
                    </div>
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
                gift: {},
                admin: {},
                errors: null,
                configs: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i:ss",
                    //maxDate: "today",
                    //minDate: "today"
                },
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
            this.getFreeGiftReports();
        },
        methods: {
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
            },
            getFreeGiftReports: function () {
                axios.get('/customer-free-gift/reports')
                    .then(r => r.data)
                    .then((response) => {
                        this.reports = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.new_users = response.new_users;
                        this.old_users = response.old_users;
                        this.total_users = response.total_users;
                        this.admin = response.user;
                    });
            },
            searchFreeGiftReport: function (page) {
                this.filter.page = page;
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                if (this.group) this.filter.group_id = this.group.id;
                var config = { 'params' : this.filter};
                axios.get('/customer-free-gift/reports', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.reports = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        //this.getCharts(config);
                        //window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchFreeGiftReport(1);
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchFreeGiftReport(1);
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
                this.filter = {
                    'category_id': null,
                    'user_id': null,
                    'customer_id': null,
                    'campaign_id': null,
                    'group_id': null,
                    'orderby': 'desc'
                };
            },
            exportdata: function () {
                this.filter.export = 'excel';
                let routeData = this.$router.resolve({
                    path: '/customer-free-gift/export',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            exportPdf: function(){
                this.filter.export = 'pdf';
                let routeData = this.$router.resolve({
                    path: '/customer-free-gift/export',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            edit: function () {
                axios.post('/customer-free-gift/edit/' + this.gift.id, this.gift)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        $('#modal-gift-edit').modal('hide');
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
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
            fetch: function (gift) {
                this.errors = null;
                this.gift = gift;
                this.gift.promoter_id = gift.provider.id;
                this.gift.old_code = gift.code;
                $('#modal-gift-edit').modal('show');
            },
        }
    }

</script>
