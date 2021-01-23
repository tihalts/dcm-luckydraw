<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Campaign Gift Items</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a  @click="$router.go(-2)">Shop for free campaign</a>
                            <!-- <router-link :to="{ name:'ScratchCardCampaigns'}" tag="a">Scratch Cards</router-link> -->
                        </li>
                        <li class="breadcrumb-item">
                            <a @click="$router.go(-1)">Gifts</a>
                        </li>
                        <li class="breadcrumb-item active">Gift Items</li>
                    </ol>
                </div>
                <div class="page-content__header-meta">
                    <router-link :to="{ name:'editGiftItems' , params: { 'gift_id': $route.params.gift_id }}" tag="a"
                        class="btn btn-warning icon-left">
                        Edit Items <span class="btn-icon fa fa-pencil"></span>
                    </router-link>
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

                <div class="row">
                    <div class="col"></div>
                    <div class="col">
                        <div class="input-group float-right" style="max-width:300px;">
                            <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                            </flat-pickr>
                        </div>
                    </div>
                </div>
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Campaign Gift Items</div>
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
                        <div class="form-group  dataset__header-search">
                            <select class="form-control" name="item_status" id="item_status" @change="searchGifts(1)"
                                v-model="filter.item_status">
                                <option value="">All</option>
                                <option value="ungifted">Ungifted</option>
                                <option value="gifted">Gifted</option>
                            </select>
                        </div>
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
                                <th>Name</th>
                                <th>Code</th>
                                <th>Gift At</th>
                                <th>Gifted At</th>
                                <th>Created At</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item , index) in gifts" :key="item.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ item.gift.name }}</td>
                                <td>{{ item.code }}</td>
                                <td>{{ item.gift_at }} </td>
                                <td>{{ item.gifted_at }} </td>
                                <td>{{ item.created_at }} </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <router-link class="dropdown-item"
                                                    :to="{ name:'editGiftItem', params: { 'item_id': item.id , 'gift_id': $route.params.gift_id }}"
                                                    tag="a">Edit</router-link>
                                                <a class="dropdown-item" @click="remove(item.id , index)"
                                                    href="javascript:void(0)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!gifts.length">
                                <td colspan="11" class="no-data-found-info">No gift items found</td>
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

<style scoped>
    .widget-alpha__icon.icon-fa {
        font-size: 44px;
        line-height: 44px;
        height: 44px;
    }

</style>
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
                config: {
                    mode: "range",
                    maxDate: "today",
                    wrap: true, // set wrap to true only when using 'input-group'
                    altFormat: 'j M Y',
                    altInput: true,
                    dateFormat: 'Y-m-d H:i'
                }
            }
        },
        mounted: function () {
            this.reports();
            this.getGifts();
        },
        methods: {
            reports: function () {
                axios.get('gift/item/' + this.$route.params.gift_id + '/report')
                    .then(r => r.data)
                    .then((response) => {
                        this.totalGiftItems = response.data.totalGiftItems;
                        this.totalUnGiftItems = response.data.totalUnGiftItems;
                        this.todayGiftItems = response.data.todayGiftItems;
                        this.todayGiftedItems = response.data.todayGiftedItems;
                    });
            },
            getGifts: function () {
                axios.get('/gift/item/list', {
                        params: {
                            'gift_id': this.$route.params.gift_id
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
                this.filter.gift_id = this.$route.params.gift_id;
                axios.post('/gift/item/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        window.scrollTo(0, 0);
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchGifts(1);
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
                        axios.delete('/gift/item/remove/' + id)
                            .then(r => r.data)
                            .then((response) => {
                                this.gifts.splice(index, 1);
                            });
                    } 
                });
            },
            createGift: function () {
                this.$router.push({
                    path: '/gifts/' + this.$route.params.gift_id + '/items/create'
                });
            }
        }
    }

</script>
