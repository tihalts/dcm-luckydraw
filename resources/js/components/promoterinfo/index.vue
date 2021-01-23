<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Promoter Reports</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card-widget card-widget-d">
                        <div class="card-widget-d__user">
                            <a href="javascript:void(0)" class="card-widget-d__avatar">
                                <img src="img/user.png" alt="" class="card-widget-d__image rounded-circle">
                            </a>
                            <a href="javascript:void(0)" class="card-widget-d__name">{{promoter.fullname}}</a>
                            <span class="card-widget-d__desc">{{promoter.cpr}}</span>
                            <span class="card-widget-d__location">{{promoter.email}}</span>
                            <span class="card-widget-d__location">{{promoter.mobile}}</span>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{promoter.purchases}}</span>
                                <span class="card-widget-j__stats-title">Purchases</span>
                            </div>
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{promoter.purchase_amounts}}</span>
                                <span class="card-widget-j__stats-title">Purchase Amount</span>
                            </div>
                        </div>
                        <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{promoter.cards}}</span>
                                <span class="card-widget-j__stats-title">Scratch Cards</span>
                            </div>
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{promoter.vouchers}}</span>
                                <span class="card-widget-j__stats-title">Vouchers</span>
                            </div>
                        </div>
                         <div class="card-widget-j__stats">
                            <div class="card-widget-j__stats-item">
                                <span class="card-widget-j__stats-value">{{promoter.spinners}}</span>
                                <span class="card-widget-j__stats-title">Spin & Winss</span>
                            </div>
                        </div>
                        <div class="card-widget-d__links">
                            <router-link href="javascript:void(0)" :to="{ name:'PromoterReportPurchases', params: { 'promoter_id': promoter.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Purchases</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'PromoterReportCards', params: { 'promoter_id': promoter.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Scratch Cards</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'PromoterReportVouchers', params: { 'promoter_id': promoter.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Vouchers</span>
                            </router-link>
                            <router-link href="javascript:void(0)" :to="{ name:'PromoterReportSpinners', params: { 'promoter_id': promoter.id }}" tag="a" class="card-widget-d__link">
                                <span class="fa fa-history card-widget-d__link-icon"></span>
                                <span class="card-widget-d__link-text">Spin & Wins</span>
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
                promoter: [],
                user: {}
            }
        },
        mounted: function () {
            this.getUsers();
        },
        methods: {
            getUsers: function () {
                axios.get('/promoter/' + this.$route.params.promoter_id + '/details')
                    .then(r => r.data)
                    .then((response) => {
                        this.promoter = response.data;
                        this.user = response.user;
                    });
            },
        }
    }

</script>
