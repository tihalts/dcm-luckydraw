<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Voucher</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-7">
                    <div class="dataset__body dataset__body--panel">
                        <table id="demo-foo-addrow"
                            class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Code</th>
                                    <th>Amount</th>
                                    <th>Created At</th>
                                    <th class="hidden-sm">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="(voucher , index) in vouchers" :key="voucher.id">
                                    <td><b>{{ index + 1  }}</b></td>
                                    <td>{{ voucher.code }} </td>
                                    <td>{{ voucher.voucher_amount }}</td>
                                    <td>{{ voucher.created_at }} </td>
                                    <td class="table__cell-actions">
                                        <div class="table__cell-actions-wrap">
                                            <div class="dropdown table__cell-actions-item">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    data-toggle="dropdown" aria-expanded="false">
                                                    Actions
                                                </button>
                                                <div class="dropdown-menu" x-placement="bottom-start">
                                                    <a class="dropdown-item" @click="remove(voucher.id , index)"
                                                        href="javascript:void(0)">Remove</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-show="!vouchers.length">
                                    <td colspan="5" class="no-data-found-info">No vouchers found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-5">
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
                    <div class="main-container">
                        <h3>Campaign Create Form</h3>
                        <form role="form" ref="addVoucherFrom" name="addVoucherFrom"
                            v-on:submit.prevent="createVoucher">
                            <div class="from-block">
                                <div class="form-group">
                                    <label for="voucher_code">Voucher Campaign</label>
                                    <v-select label="name" :filterable="false" :options="campaigns"
                                        @search="onCampaignSearch" v-model="campaign" @change="getVoucherCounts"
                                        style="width:100%;">
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
                                <div class="form-group">
                                    <label for="voucher_code">Voucher Code</label>
                                    <input type="text" class="form-control" name="voucher_code"
                                        v-model="form.voucher_code" placeholder="voucher code" required>
                                    <span>{{voucher_count}} voucher is available </span>
                                </div>

                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <!-- <button type="button" @click="listPurchase()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button> -->
                                    <button type="button" @click="show_card = true"
                                        class="btn btn-default mb-2 mr-3 pull-left">Show Card</button>
                                    <button :disabled="!campaign.id || voucher_count == 0 || creating" type="submit"
                                        class="btn btn-success mb-2 mr-3">Create</button>
                                    <button type="button" v-if="voucher_count == 0 && campaign.id"
                                        @click="listPurchase()" class="btn btn-info mb-2 mr-3">Finished</button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
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
                form: {},
                errors: null,
                campaigns: [],
                campaign: {},
                vouchers: [],
                voucher_count: 0,
            }
        },
        mounted: function () {
            this.getCampaigns();
        },
        methods: {
            getCampaigns: function () {
                axios.get('/fetch/reward-point/campaigns', {
                    params: {
                        'searchText': ''
                    }
                }).then(r => r.data).then((response) => {
                    this.campaigns = response.data;
                    if (this.campaigns.length == 0) {
                        this.listPurchase();
                    }
                });
            },
            getVouchers: function () {
                axios.get('/voucher/list', {
                        params: {
                            'customer_id': this.$route.params.customer_id,
                            'campaign_id': this.campaign.id
                        }
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                    });
            },
            onCampaignSearch(search, loading) {
                loading(true);
                this.searchCampaigns(loading, search, this);
            },
            searchCampaigns: _.debounce((loading, search, vm) => {
                axios.get('/fetch/reward-point/campaigns', {
                    params: {
                        'searchText': search
                    }
                }).then(r => r.data).then((response) => {
                    vm.campaigns = response.data;
                    loading(false);
                });
            }, 350),
            getVoucherCounts: function () {
                if (!this.campaign.id) return;
                axios.get('/campaign/' + this.campaign.id + '/customer/' + this.$route.params.customer_id +
                        '/voucher/limit')
                    .then(r => r.data)
                    .then((response) => {
                        this.voucher_count = response;
                        this.getVouchers();
                    });
            },
            createVoucher: function () {
                if (!this.campaign.id || isNaN(this.form.voucher_code)) return;
                this.form.voucher_code = parseInt(this.form.voucher_code).toString();
                if (this.form.voucher_code.length != this.campaign.digits) return;
                this.form.campaign_id = this.campaign.id;
                this.form.customer_id = this.$route.params.customer_id;
                this.creating = true;
                axios.post('/reward-point/voucher/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.getVoucherCounts();
                        this.form = {};
                        this.creating = false;
                        this.errors = null;
                    }).catch((error) => {
                        this.creating = false;
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                hidebtnLoader();
            },
            remove: function (id) {
                axios.delete('/voucher/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.getVoucherCounts();
                    });
            },
            spinnerWins: function(){
                this.$router.push({
                    name: 'customerSpinners' , params : {
                        customer_id : this.$route.params.customer_id
                    }
                });
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            }
        }
    }

</script>
