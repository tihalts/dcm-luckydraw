<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Campaign Gift Items</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                                <router-link  :to="{ name:'ScratchCardCampaigns'}"
                                            tag="a">Scratch Cards</router-link>
                        </li>
                        <li class="breadcrumb-item">
                                <a @click="$router.go(-2)">Gifts</a>
                        </li>
                        <li class="breadcrumb-item active">Gift Items</li>
                    </ol>
                </div>

                <div class="page-content__header-meta">
                    <a href="javascript:void(0)" class="btn btn-danger icon-left" @click="$router.go(-1)">
                        Cancel Edit <span class="btn-icon fa fa-chevron-left"></span>
                    </a>
                </div>
            </div>
            <div class="container-fh__content dataset">
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
                            <select class="form-control" name="item_status" id="item_status" @change="searchGifts(1)" v-model="filter.item_status">
                                <option value="">All</option>
                                <option value="ungifted">Ungifted</option>
                                <option value="gifted">Gifted</option>
                            </select>
                        </div>
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" v-model="filter.searchText"
                                v-on:keyup="getGifts()" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>
                                    <button role="button"
                                        @click="deleteItems()" class="btn btn-danger">Delete ({{items.length}} Items) </button>
                                </th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Code</th>
                                <th>Gift At</th>
                                <th>Gifted At</th>
                                <!-- <th>Created At</th> -->
                                <th>
                                    <button role="button"
                                        @click="updateItems()" class="btn btn-success">Update</button>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item , index) in gifts" :key="item.id">
                                <td class="table__checkbox">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" v-model="items" :value="item.id" @click="AddItem()" :id="'ch'+ item.id">
                                        <label class="custom-control-label" :for="'ch'+ item.id"></label>
                                    </div>
                                </td>
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ item.gift.name }}</td>
                                <td>
                                    <input type="text" min="0" class="form-control" name="code"
                                        v-model="item.code" placeholder="Gift code" required>
                                </td>
                                <td>
                                    <flat-pickr class="form-control" :config="config" name="end_at"
                                        v-model="item.gift_at" placeholder="End At"></flat-pickr>
                                </td>
                                <td>{{ item.gifted_at }} </td>
                                <td class="table__cell-actions">
                                     <button role="button"
                                        @click="deleteItem(item.id)" class="btn btn-danger">Delete</button>
                                </td>
                            </tr>
                            <tr v-show="!gifts.length">
                                <td colspan="11" class="no-data-found-danger">No gift items found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchGifts" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div> -->
            </div>
        </div>

    </div>

</template>

<style scoped>
.widget-alpha__icon.icon-fa{
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
                items: [],
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                },
            }
        },
        mounted: function () {
            this.getGifts();
        },
        methods: {
            getGifts: function () {
                this.filter.gift_id = this.$route.params.gift_id;
                axios.get('/gift/items/list', {
                        params:  this.filter
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        //this.filter = response.filter;
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
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.getGifts();
            },
            remove: function (id, index) {
                axios.delete('/gift/item/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts.splice(index, 1);
                    });
            },
            createGift: function () {
                this.$router.push({
                    path: '/gifts/' + this.$route.params.gift_id + '/items/create'
                });
            },
            deleteItems(){
                this.$swal({
                    title: "Are you want to delete these items?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.delete('/gift/items' , { params: {'items' : this.items} } )
                            .then(r => r.data)
                            .then((response) => {
                                this.getGifts();
                                this.items = [];
                            });
                    } 
                });
            },
            deleteItem(id){
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
                                this.getGifts();
                            });
                    } 
                });
            },
            updateItems(){
                this.$swal({
                    title: "Are you want to update items?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.post('/gift/items' , {'items' : this.gifts})
                            .then(r => r.data)
                            .then((response) => {
                                this.getGifts();
                            });
                    } 
                });
            }
        },
    }

</script>
