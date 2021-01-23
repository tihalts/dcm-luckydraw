<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Shop for free Campaigns</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Shop for free Campaigns</div>
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
                                <!-- <th>Customer Limit</th>
                                <th>Day Limit</th> -->
                                <th>Group</th>
                                <th>Start At</th>
                                <th>End At</th>
                                <th>Vouchers</th>
                                <th>Created At</th>
                                <!-- <th>Status</th> -->
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(campaign , index) in campaigns" :key="campaign.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ campaign.name }}</td>
                                <!-- <td>{{ campaign.data.customer_limit }} </td>
                                <td>{{ campaign.data.day_limit }} </td> -->
                                <td v-if="campaign.group">{{ campaign.group.name }}</td>
                                <td v-else></td>
                                <td>{{ campaign.start_at }} </td>
                                <td>{{ campaign.end_at }} </td>
                                <td>{{ campaign.gift_vouchers_count }} </td>
                                <td>{{ campaign.created_at }} </td>
                                <!-- <td>
                                    <span v-if="campaign.status" class="badge badge-success badge-rounded mb-3 mr-3">Active</span>
                                    <span v-if="!campaign.status" class="badge badge-danger badge-rounded mb-3 mr-3">Deactivated</span>
                                </td> -->
                                <td>
                                    <button v-if="campaign.status" @click="remove(campaign.id , index)" class="btn btn-sm btn-icon btn-success" type="button"><span class="fa fa-lightbulb-o"></span></button> 
                                    <button v-if="!campaign.status" @click="active(campaign.id , index)" class="btn btn-sm btn-icon btn-danger" type="button"><span class="fa fa-lightbulb-o"></span></button> 
                                    <button @click="campaignGifts(campaign.id )" class="btn btn-sm btn-icon btn-info" type="button"><span class="fa fa-gift"></span></button>   
                                    <router-link tag="button"
                                    :to="{ name:'editFreeGiftCampaign', params: { 'group_id' : $route.params.group_id, 'campaign_id': campaign.id }}"
                                    class="btn btn-sm btn-icon btn-info" type="button"><span class="fa fa-edit"></span></router-link>                                  
                                </td>    
                            </tr>
                            <tr v-show="!campaigns.length">
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
                axios.get('/free-gift-campaign/list' , { params : { 'group_id' : this.$route.params.group_id }})
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
                this.filter.group_id = this.$route.params.group_id;
                axios.post('/free-gift-campaign/search/' + page, this.filter)
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
                axios.delete('/free-gift-campaign/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns[index] = response.data;
                        this.campaigns.push();
                    });
            },
            active: function (id, index) {
                axios.get('/free-gift-campaign/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns[index] = response.data;
                        this.campaigns.push();
                    });
            },
            createCampaign: function () {
                this.$router.push({
                    name: 'createFreeGiftCampaign', params : {'group_id' : this.$route.params.group_id}
                });
            },
            campaignGifts: function (id) {
                this.$router.push({
                    path: '/free-gift-campaigns/' + id + '/gifts'
                });
            }
        }
    }

</script>
