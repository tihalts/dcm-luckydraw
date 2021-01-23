<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Customer Reports</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card-widget card-widget-d">
                        <div class="card-widget-d__user">
                            <a href="javascript:void(0)" class="card-widget-d__avatar">
                                <img src="img/user.png" alt="" class="card-widget-d__image rounded-circle">
                            </a>
                            <a href="javascript:void(0)" class="card-widget-d__name">{{customer.fullname}}</a>
                            <span class="card-widget-d__desc">{{customer.cpr}}</span>
                            <span class="card-widget-d__location">{{customer.email}}</span>
                            <span class="card-widget-d__location">{{customer.mobile}}</span>
                            <span class="card-widget-d__location" v-if="customer.country"><b>{{customer.country.name}}</b></span>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.purchases}}</span>
                                <span class="card-widget-j__stats-title">Purchases</span>
                            </div>
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.purchase_amounts}}</span>
                                <span class="card-widget-j__stats-title">Purchase Amount</span>
                            </div>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.cards}}</span>
                                <span class="card-widget-j__stats-title">Scratch Cards</span>
                            </div>
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.card_wins}}</span>
                                <span class="card-widget-j__stats-title">Scratch Card Wins</span>
                            </div>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.vouchers}}</span>
                                <span class="card-widget-j__stats-title">Vouchers</span>
                            </div>
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.raffle_draws}}</span>
                                <span class="card-widget-j__stats-title">RaffleDraw Wins</span>
                            </div>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.spinners}}</span>
                                <span class="card-widget-j__stats-title">Spinners</span>
                            </div>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{customer.free_gifts}}</span>
                                <span class="card-widget-j__stats-title">Shop For Free</span>
                            </div>
                        </div>
                        <div class="card-widget-d__links">
                            <router-link href="javascript:void(0)" :to="{ name:'CustomerReportPurchases', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Purchases</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'CustomerReportCards', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Scratch Cards</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'CustomerReportVouchers', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Vouchers</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'CustomerReportRaffleDraws', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Raffle Draw</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'CustomerReportSpinners', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Spin & Win</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'ShopForFreeReport', params: { 'customer_id': customer.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Shop For Free</span>
                            </router-link>
                        </div>
                    </div>
                </div>
                <router-view></router-view>
            </div>
        </div>

    </div>

</template>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                customer: [],
                user: {}
            }
        },
        mounted: function () {
            this.getUsers();
        },
        methods: {
            getUsers: function () {
                axios.get('/customer/' + this.$route.params.customer_id + '/details')
                    .then(r => r.data)
                    .then((response) => {
                        this.customer = response.data;
                        this.user = response.user;
                    });
            },
        }
    }

</script>
