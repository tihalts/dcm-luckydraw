<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Scratch Card Campaigns</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Scratch Card Campaigns</div>
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
                        <a @click="createCampaign" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Min Amount</th>
                                <th>Total Gifts</th>
                                <th>Total UnGifts</th>
                                <th>Today / Yesterday Gifts</th>
                                <th>Today / Yesterday UnGifts</th>
                                <th>Start At</th>
                                <th>End At</th>
                                <th>Status</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(campaign , index) in campaigns" :key="campaign.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ campaign.name }}</td>
                                <td>{{ campaign.data.amount }}</td>
                                <td>{{ campaign.total_gift_items }} </td>
                                <td>{{ campaign.total_ungift_items }} </td>
                                <td>{{ campaign.today_gift_items }} / {{ campaign.yesterday_gift_items }}</td>
                                <td>{{ campaign.today_gifted_items }} / {{ campaign.yesterday_gifted_items }}</td>
                                <td>{{ campaign.start_at }} </td>
                                <td>{{ campaign.end_at }} </td>
                                <td>
                                    <span v-if="campaign.status" class="badge badge-success badge-rounded mb-3 mr-3">Active</span>
                                    <span v-if="!campaign.status" class="badge badge-danger badge-rounded mb-3 mr-3">Deactivated</span>
                                </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editScratchCardCampaign', params: { 'campaign_id': campaign.id }}"
                                                    tag="a">Edit</router-link>
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'ScratchCardTemplates', params: { 'campaign_id': campaign.id }}"
                                                    tag="a">Templates</router-link>
                                                <!-- <a  class="dropdown-item"
                                                    @click="campaignWinners(campaign.id )"
                                                    href="javascript:void(0)">Winners</a> -->
                                                <a class="dropdown-item" @click="campaignGifts(campaign.id )"
                                                    href="javascript:void(0)">Gifts</a>
                                                <a v-if="campaign.status" class="dropdown-item"
                                                    @click="remove(campaign.id , index)"
                                                    href="javascript:void(0)">Deactivated</a>
                                                <a v-if="!campaign.status" class="dropdown-item"
                                                    @click="active(campaign.id , index)"
                                                    href="javascript:void(0)">Activated</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!campaigns.length">
                                <td colspan="11" class="no-data-found-info">No Campaigns found</td>
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
                campaigns: [],
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
                axios.get('/scratch-card-campaign/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchCampaigns: function (page) {
                axios.post('/scratch-card-campaign/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns = response.data;
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
                axios.delete('/scratch-card-campaign/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns[index] = response.data;
                        this.campaigns.push();
                    });
            },
            active: function (id, index) {
                axios.get('/scratch-card-campaign/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns[index] = response.data;
                        this.campaigns.push();
                    });
            },
            campaignWinners: function (id) {
                this.$router.push({
                    path: '/scratch-card-campaigns/' + id + '/winners'
                });
            },
            createCampaign: function () {
                this.$router.push({
                    path: '/scratch-card-campaigns/create'
                });
            },
            campaignGifts: function (id) {
                this.$router.push({
                    path: '/campaigns/' + id + '/gifts'
                });
            }
        }
    }

</script>
