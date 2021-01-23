<template>
    <div class="col-md-9">
        <div class="container-fluid container-fh">
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">List Vouchers</div>
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
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Voucher Code</th>
                                <th>Voucher Amount</th>
                                <th>Redeemed At</th>
                                <th>Issued By</th>
                                <th>Created By</th>
                                <th v-if="$can('admin')" class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( rewardpoint , index ) in rewardpoints" :key="rewardpoint.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ rewardpoint.code}} </td>
                                <td>{{ rewardpoint.voucher_amount }} </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-description"
                                            v-if="rewardpoint.redeemed_at">{{ rewardpoint.redeemed_at }}</span>
                                        <span class="table__cell-widget-description red" v-else>Not yet redeemed</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget" v-if="rewardpoint.provider">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ rewardpoint.provider.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b>
                                            {{ rewardpoint.provider.email }}</span>
                                        <span class="table__cell-widget-description"><b>Mobile :</b>
                                            {{ rewardpoint.provider.mobile }}</span>
                                    </div>
                                </td>
                                <td>{{ rewardpoint.created_at }} </td>
                                <td v-if="$can('admin')">
                                    <button role="button"
                                        @click="fetch(rewardpoint)" class="btn btn-info">Edit</button>
                                </td>
                            </tr>
                            <tr v-show="!rewardpoints.length">
                                <td v-if="$can('admin')" colspan="8" class="no-data-found-info">No Vouchers
                                    found</td>
                                <td v-if="!$can('admin')" colspan="7" class="no-data-found-info">No Vouchers
                                    found</td>
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
        <div id="modal-voucher-edit" class="modal fade custom-modal-tabs">
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
                                    <label for="voucher-code">Voucher Code</label>
                                    <input type="text" class="form-control" id="voucher-code" name="voucher-code"
                                        v-model="voucher.code" placeholder="voucher code">
                                    <span>Old Code : <b>{{voucher.old_code}}</b></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="button" @click="edit">Save</button>
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
                rewardpoints: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    "searchText": "",
                    "itemPerPage" : 15,
                    "orderby" : "desc",
                },
                form: {},
                totalPages: 0,
                user: {},
                voucher: {},
                errors: null,
            }
        },
        mounted: function () {
            this.searchVouchers(1);
        },
        methods: {
            searchVouchers: function (page) {
               this.filter.customer_id = this.$route.params.customer_id;
                axios.post('/reward-point/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.rewardpoints = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            fetch: function (voucher) {
                this.errors = null;
                this.voucher = voucher;
                this.voucher.old_code = voucher.code;
                $('#modal-voucher-edit').modal('show');
            },
            edit: function () {
                axios.post('/reward-point/edit/' + this.voucher.id, {
                        'voucher_code': this.voucher.code
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        $('#modal-voucher-edit').modal('hide');
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchVouchers(1);
            },
            redeemedVoucher: function (id, index) {
                axios.get('/voucher/redeemed/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.searchVouchers(this.currentPage);
                    });
            }
        }
    }

</script>
