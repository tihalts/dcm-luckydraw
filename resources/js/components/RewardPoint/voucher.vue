<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Vouchers</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">               
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Amount</th>
                                <th>Redeemed At</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( voucher , index ) in vouchers" :key="voucher.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td>{{ voucher.code }}</td>
                                <td>{{ voucher.amout }}</td>
                                <td>
                                    <span v-if="voucher.redeemed_at" >{{ voucher.redeemed_at }}</span>
                                    <span v-if="!voucher.redeemed_at">Not yet redeemed</span>
                                </td>
                                <td>{{ voucher.created_at }} </td>
                                <td>
                                    <button role="button" :disabled="voucher.redeemed_at" @click="redeemedVoucher(voucher.id, index)"
                                        class="btn btn-success">Redeemed</button>
                                </td>
                            </tr>
                            <tr v-show="!vouchers.length">
                                <td colspan="5" class="no-data-found-info">No Vouchers found</td>
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

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                vouchers: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.getVouchers();
        },
        methods: {
            getVouchers: function () {
                axios.get('/voucher/list' , { params : {'purchase_id' : this.$route.params.purchase_id}})
                    .then(r => r.data)
                    .then((response) => {
                        this.vouchers = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            redeemedVoucher: function (id, index) {
                axios.get('/voucher/redeemed/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.getVouchers();
                    });
            }
        }
    }

</script>
