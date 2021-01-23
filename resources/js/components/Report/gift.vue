<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Gift Report</h2>
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
                                                        <span v-if="option.campaign_type == 'scratch_card'">Scratch Card</span>
                                                        <span v-if="option.campaign_type == 'reward_point'">Voucher</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.name }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="email">Select Gifts</label>
                                                <v-select label="name" :filterable="false" :options="gifts"
                                                    @search="searchGifts" v-model="gift" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search gifts..
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
                                            <button type="button" @click="resetFilter" class="btn btn-default mb-2 mr-3">Reset</button>
                                            <button type="button" @click="searchGiftItems(1)" class="btn btn-success mb-2 mr-3">Search</button>
                                        </div>
                                        <!-- /.box-footer -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ totalGiftItems }}</div>
                                <div class="widget-alpha__description">Total Gift Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">{{ totalUnGiftItems }}</div>
                                <div class="widget-alpha__description">UnGift Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-orange widget-alpha--donut">
                            <div>
                                <div class="widget-alpha__amount">{{ totalGiftedItems }}</div>
                                <div class="widget-alpha__description">Gifted items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ todayGiftedItems }}</div>
                                <div class="widget-alpha__description">Today's remaining</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Gift At</th>
                                <th>Customer</th>
                                <th>Provider</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item , index) in gift_items" :key="item.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>
                                    <div class="table__cell-widget" v-if="item.gift">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ item.gift.name }}</a>
                                        <span class="table__cell-widget-description" v-if="item.gift.campaign">
                                            {{ item.gift.campaign.name }}
                                        </span>
                                    </div>
                                </td>
                                <td>{{ item.code }}</td>
                                <td>{{ item.gift_at }} </td>
                                <td>
                                    <div class="table__cell-widget" v-if="item.card">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ item.card.customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b>
                                            {{ item.card.customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b>
                                            {{ item.card.customer.cpr }}</span>
                                            <span class="table__cell-widget-description"><b>Gifted At :</b>
                                            {{ item.gifted_at }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span v-if="item.card">{{ item.card.provider.fullname }}</span>
                                </td>
                            </tr>
                            <tr v-show="!gift_items.length">
                                <td colspan="11" class="no-data-found-info">No gift items found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchGiftItems" :container-class="'pagination float-right'"
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
                gift_items: [],
                totalItems: null,
                currentPage: 0,
                filter: {'orderby' : 'desc'},
                form: {},
                totalPages: 0,
                campaigns: [],
                campaign: {},
                customers: [],
                customer: {},
                gifts: [],
                gift: {},
                users: [],
                user: {},
                totalGiftItems: 0,
                totalUnGiftItems: 0,
                totalGiftedItems: 0,
                todayGiftedItems: 0,
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
            this.getGifts();
            this.reports();
        },
        methods: {
            reports: function (config = {}) {
                axios.get('fetch/gift/linechart' , config)
                    .then(r => r.data)
                    .then((response) => {
                        this.totalGiftItems = response.data.totalGiftItems;
                        this.totalUnGiftItems = response.data.totalUnGiftItems;
                        this.totalGiftedItems = response.data.totalGiftedItems;
                        this.todayGiftedItems = response.data.todayGiftedItems;
                    });
            },
            getGifts: function () {
                axios.get('/gift/reports')
                    .then(r => r.data)
                    .then((response) => {
                        this.gift_items = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        window.scrollTo(0,0);
                    });
            },
            searchGiftItems: function (page) {
                 this.filter.page = page;
                 this.resetControl();
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                if (this.gift) this.filter.gift_id = this.gift.id;
                var config = { 'params' : this.filter};
                axios.get('/gift/reports', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.gift_items = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.reports(config);
                    });
            },
            exportdata: function () {
                this.resetControl();
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                if (this.gift) this.filter.gift_id = this.gift.id;
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/gift/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            exportPdf: function(type){
                this.resetControl();
                if (this.user) this.filter.user_id = this.user.id;
                if (this.campaign) this.filter.campaign_id = this.campaign.id;
                if (this.customer) this.filter.customer_id = this.customer.id;
                if (this.gift) this.filter.gift_id = this.gift.id;
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/gift/' + type + '/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchGiftItems(1);
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchGiftItems(1);
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
            onCampaignSearch(search, loading) {
                loading(true);
                this.searchCampaigns(loading, search, this);
            },
            searchCampaigns: _.debounce((loading, search, vm) => {
                axios.get('/fetch/campaigns', {
                    params: {
                        'searchText': search,
                        'campaign_type': 'scratch_card'
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
            searchGifts(search, loading) {
                loading(true);
                this.onSearchGifts(loading, search, this);
            },
            onSearchGifts: _.debounce((loading, search, vm) => {
                axios.get('/fetch/gifts', {
                    'searchText': search,
                    'campaign_id' : vm.campaign ? vm.campaign.id : null
                }).then(r => r.data).then((response) => {
                    vm.gifts = response.data;
                    loading(false);
                });
            }, 350),
            resetControl: function(){
                this.filter.user_id = null;
                this.filter.gift_id = null;
                this.filter.customer_id = null;
                this.filter.campaign_id = null;
            },
            resetFilter: function(){
                this.gift = null;
                this.user = null;
                this.customer = null;
                this.campaign = null;
                this.filter = {
                    'category_id': null,
                    'user_id': null,
                    'gift_id': null,
                    'customer_id': null,
                    'campaign_id': null,
                    'orderby': 'desc'
                };
            }
        }
    }

</script>
