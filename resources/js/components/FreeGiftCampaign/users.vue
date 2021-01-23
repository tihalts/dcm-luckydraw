<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Customer Free Gifts</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Customer Free Gifts</div>
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
                                v-on:keyup="searchCampaigns(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer</th>
                                <th>Gift</th>
                                <th>Campagin</th>
                                <th>Promoter</th>
                                <th>Created At</th>
                                <!-- <th>Status</th> -->
                                <!-- <th class="hidden-sm">Action</th> -->
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(gift , index) in gifts" :key="gift.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td v-if="gift.customer">
                                     <div class="table__cell-widget">
                                        <a href="javascript:void(0)" class="table__cell-widget-name">{{ gift.customer.fullname }}</a>
                                        <span class="table__cell-widget-description"><b>Email :</b> {{ gift.customer.email }}</span>
                                        <span class="table__cell-widget-description"><b>CPR :</b> {{ gift.customer.cpr }}</span>
                                    </div>
                                </td>
                                <td v-else></td>
                                <td v-if="gift.gift">{{ gift.gift.name }}</td>
                                <td v-else></td>
                                <td v-if="gift.campaign">{{ gift.campaign.name }}</td>
                                <td v-else></td>
                                <td v-if="gift.provider">{{ gift.provider.fullname }}</td>
                                <td v-else></td>
                                <td>{{ gift.created_at }} </td>
                            </tr>
                            <tr v-show="!gifts.length">
                                <td colspan="9" class="no-data-found-info">No Shop for free Campaigns found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchCampaigns" :container-class="'pagination float-right'"
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
                gifts: [],
                totalItems: null,
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
            }
        },
        mounted: function () {
            this.getCampaigns();
        },
        methods: {
            getCampaigns: function () {
                axios.get('/customer-free-gift/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchCampaigns: function (page) {
                this.filter.group_id = this.$route.params.group_id;
                axios.post('/customer-free-gift/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchCampaigns(1);
            },
            remove: function (id, index) {
                axios.delete('/customer-free-gift/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts[index] = response.data;
                        this.gifts.push();
                    });
            },
            active: function (id, index) {
                axios.get('/customer-free-gift/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts[index] = response.data;
                        this.gifts.push();
                    });
            },
            createCampaign: function () {
                this.$router.push({
                    name: 'createFreeGiftCampaign', params : {'group_id' : this.$route.params.group_id}
                });
            },
            campaignGifts: function (id) {
                this.$router.push({
                    path: '/free-gift-gifts/' + id + '/gifts'
                });
            }
        }
    }

</script>