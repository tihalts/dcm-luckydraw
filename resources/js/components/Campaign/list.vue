<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Campaigns</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Campaigns</div>
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
                                <th>Campaign Name</th>
                                <th>Min Amount</th>
                                <th>Max Amount</th>
                                <th>Per Day Limit</th>
                                <th>Per Customer Limit</th>
                                <th>Campaign Type</th>
                                <th>Expires At</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(campaign , index) in campaigns" :key="campaign.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ campaign.name }}</td>
                                <td>{{ campaign.min_amount }}</td>
                                <td>{{ campaign.max_amount }} </td>
                                <td>{{ campaign.per_day_limit }} </td>
                                <td>{{ campaign.per_customer_limit }} </td>
                                <td>{{ campaign.campaign_type }} </td>
                                <td>{{ campaign.expires_at }} </td>
                                <td>{{ campaign.created_at }} </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editCampaign', params: { 'campaign_id': campaign.id }}"
                                                    tag="a">Edit</router-link>
                                                <a  class="dropdown-item"
                                                    @click="remove(campaign.id , index)"
                                                    href="javascript:void(0)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!campaigns.length">
                                <td colspan="7" class="no-data-found-info">No Campaigns found</td>
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
                axios.get('/campaign/list')
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
                axios.post('/campaign/search/' + page, this.filter)
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
                axios.delete('/campaign/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns.splice(index, 1);
                    });
            },
            createCampaign: function () {
                this.$router.push({
                    path: '/campaigns/create'
                });
            }
        }
    }

</script>
