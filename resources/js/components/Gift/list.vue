<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Campaign Gifts</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                             <router-link :to="{ name:'ScratchCardCampaigns'}"
                                         tag="a">Scratch Card Campaigns</router-link>
                        </li>
                        <li class="breadcrumb-item active">Gifts</li>
                    </ol>
                </div>
                <div class="page-content__header-meta">

                    <router-link :to="{ name:'GiftReport' }" tag="a"
                        class="btn btn-info icon-left">
                        Reports <span class="btn-icon fa fa-book"></span>
                    </router-link>
                    <a href="javascript:void(0)" @click="importGifts" class="btn btn-warning icon-left">
                        Import Gift <span class="btn-icon fa fa-upload"></span>
                    </a>
                     <a href="javascript:void(0)" @click="moveGiftsNextDay" class="btn btn-info icon-left">
                        Unused Gifts Move Today <span class="btn-icon fa fa-gift"></span>
                    </a>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="row">
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ totalGiftItems }}</div>
                                <div class="widget-alpha__description">Total Gift Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">{{ totalUnGiftItems }}</div>
                                <div class="widget-alpha__description">Total Ungifted Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-orange widget-alpha--donut">
                            <div>
                                <div class="widget-alpha__amount">{{ todayGiftItems }}</div>
                                <div class="widget-alpha__description">Today's Gift Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-java widget-alpha--help">
                            <div>
                                <div class="widget-alpha__amount">{{ todayGiftedItems }}</div>
                                <div class="widget-alpha__description">Today's Gifted Items</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Campaign Gifts</div>
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
                                v-on:keyup="searchGifts(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createGift" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Total Gifts</th>
                                <th>Gifted Items</th>
                                <!-- <th>Start At</th>
                                <th>End At</th> -->
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(gift , index) in gifts" :key="gift.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>
                                    <img :src="'storage/' + gift.image" class="img-thumbnail" width="100" height="100" alt="No Image" />
                                </td>
                                <td>{{ gift.name }}</td>
                                <td>{{ gift.code }}</td>
                                <td>{{ gift.total_gifts }} </td>
                                <td>{{ gift.gifted_items }}</td>
                                <!-- <td>{{ gift.start_at }} </td>
                                <td>{{ gift.end_at }} </td> -->
                                <td>{{ gift.created_at }} </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editGift', params: { 'gift_id': gift.id , 'campaign_id': $route.params.campaign_id }}"
                                                    tag="a">Edit</router-link>
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'GiftItems', params: { 'gift_id': gift.id }}" tag="a">
                                                    Gift Items</router-link>
                                                <a class="dropdown-item" @click="remove(gift.id , index)"
                                                    href="javascript:void(0)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!gifts.length">
                                <td colspan="11" class="no-data-found-info">No Campaign gifts found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchGifts" :container-class="'pagination float-right'"
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
                totalGiftItems: 0,
                totalUnGiftItems: 0,
                todayGiftItems: 0,
                todayGiftedItems: 0,
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
            }
        },
        mounted: function () {
            this.getGifts();
            this.reports();
        },
        methods: {
            reports: function () {
                axios.get('/campaign/gift/'+ this.$route.params.campaign_id +'/report')
                    .then(r => r.data)
                    .then((response) => {
                        this.totalGiftItems = response.data.totalGiftItems;
                        this.totalUnGiftItems = response.data.totalUnGiftItems;
                        this.todayGiftItems = response.data.todayGiftItems;
                        this.todayGiftedItems = response.data.todayGiftedItems;
                    });
            },
            getGifts: function () {
                axios.get('/gift/list', {
                        params: {
                            'campaign_id': this.$route.params.campaign_id
                        }
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchGifts: function (page) {
                this.filter.campaign_id = this.$route.params.campaign_id;
                axios.post('/gift/search/' + page, this.filter)
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
                this.searchGifts(1);
            },
            remove: function (id, index) {
                this.$swal({
                    title: "Are you want to delete this item?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.delete('/gift/remove/' + id)
                            .then(r => r.data)
                            .then((response) => {
                                this.gifts.splice(index, 1);
                            });
                    } 
                });                
            },
            giftWinners: function (id) {
                this.$router.push({
                    path: '/gifts/' + id + '/winners'
                });
            },
            moveGiftsNextDay: function(){
                axios.get('/campaigns/'+ this.$route.params.campaign_id +'/move/unused/gifts')
                    .then(r => r.data)
                    .then((response) => {
                     notification('Move Gifts', 'Move Gifts successfully!', 'success');
                    }).catch((error) => {
                        notification('Move Gifts', "Move Gifts Faild!", 'danger');
                    });
            },
            importGifts: function () {
                this.$router.push({
                    path: '/campaigns/' + this.$route.params.campaign_id + '/gifts/imports'
                });
            },
            createGift: function () {
                this.$router.push({
                    path: '/campaigns/' + this.$route.params.campaign_id + '/gifts/create'
                });
            }
        }
    }

</script>
