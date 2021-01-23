<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Dashboard</h2>
                    <div class="page-content__header-description"></div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.new_customers }}</div>
                                <div class="widget-alpha__description">New Customers</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-users"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.purchases }}</div>
                                <div class="widget-alpha__description">This Month Purchases</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-cart"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-orange widget-alpha--donut">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.purchase_amount }}</div>
                                <div class="widget-alpha__description">This Month Purchase Amount</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-money-bag"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.last_month_purchase_amount }}</div>
                                <div class="widget-alpha__description">Last Month Purchase Amount</div>
                            </div>
                            <span class="widget-alpha__icon ua-icon-money-bag"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.total_scratch_card_campaigns }}</div>
                                <div class="widget-alpha__description">Total Scratch Card Campaigns</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.active_scratch_card_campaigns }}</div>
                                <div class="widget-alpha__description">Active Scratch Card Campaigns</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.scratch_cards }}</div>
                                <div class="widget-alpha__description">Total Purchase Scratch Cards</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.totalGifts }}</div>
                                <div class="widget-alpha__description">Total Gifts</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.totalGiftItems }}</div>
                                <div class="widget-alpha__description">Total Gift Items</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.totalUnGiftItems }}</div>
                                <div class="widget-alpha__description">Total Ungift Items</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.totalGiftedItems }}</div>
                                <div class="widget-alpha__description">Total Gifted Items</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.todayGiftedItems }}</div>
                                <div class="widget-alpha__description">Today Gifted Items</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.total_voucher_campaigns }}</div>
                                <div class="widget-alpha__description">Total Voucher Campaigns</div>
                            </div>
                        </div>
                    </div>
                     <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.active_voucher_campaigns }}</div>
                                <div class="widget-alpha__description">Active Voucher Campaigns</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ dashboard_data.vouchers }}</div>
                                <div class="widget-alpha__description">Vouchers</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dataset__header">
                    <div class="dataset__header-side"> 
                        <div class="dataset__header-heading">List Purchases</div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Purchase No</th>
                                <th>Customer Name</th>
                                <th>Purchase Amount</th>
                                <th>Points</th>
                                <th>Created By</th>
                                <th>Purchase At</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(purchase,index) in purchases" :key="purchase.id">
                                <td><b>{{ ((index + 1) + (10 * (currentPage - 1)))  }}</b></td>
                                <td>{{ purchase.purchase_no }}</td>
                                <td>{{ purchase.customer.fullname }}</td>
                                <td>{{ purchase.amount }} </td>
                                <td>{{ purchase.points }} </td>
                                <td>{{ purchase.user.fullname }} </td>
                                <td>{{ purchase.created_at }} </td>
                            </tr>
                            <tr v-show="!purchases.length">
                                <td colspan="7" class="no-data-found-info">No Purchases found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                     <paginate
                        v-if="totalPages > 1"
                        :page-count="totalPages"
                        :page-range="3"
                        :margin-pages="2"
                        :click-handler="searchPurchases"
                        :container-class="'pagination float-right'"
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
                dashboard_data:{
                    new_customers: 0,
                    purchases: 0,
                    purchase_amount: 0,
                    last_month_purchase_amount: 0
                },
                purchases: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'asc',
                    searchText: null
                },
                form: {},
                totalPages: 0
            }
        },
        mounted: function () {
            this.getPurchases();
            this.getDashboardData();
        },
        methods: {
            getPurchases: function () {
                axios.get('/purchase/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 10);
                        this.filter = response.filter;
                    });
            },
            getDashboardData: function () {
                axios.get('/get/dashboard/data')
                    .then(r => r.data)
                    .then((response) => {
                        this.dashboard_data = response.data;
                    });
            },
            searchPurchases: function (page) {
                console.log(this.filter);
                
                axios.post('/purchase/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchases = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 10);
                        this.filter = response.filter;
                    });
            },
            OrderBy: function(orderby){
                this.filter.orderby = orderby;
                this.searchPurchases(1);
            },
            createPurchase: function () {
                this.$router.push({
                    path: '/purchases/create'
                });
            }
        }
    }

</script>
