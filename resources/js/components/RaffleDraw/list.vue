<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Raffle Draws</h2>                   
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side"> 
                        <div class="dataset__header-heading">Raffle Draws</div>
                        <!-- <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter By
                            </div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" @click="OrderBy('asc')" href="javascript:void(0)">ASC</a>
                                <a class="dropdown-item"  @click="OrderBy('desc')" href="javascript:void(0)">DESC</a>
                            </div>
                        </div> -->
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" v-model="filter.searchText" v-on:keyup="searchRaffleDraws(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createRaffleDraw" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                        <a @click="show_filter = !show_filter" role="button" href="javascript:void(0)"
                            class="mdi mdi-filter dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="main-container" style="padding:10px 20px;" v-if="show_filter">
                    <h1>Fliter</h1>
                    <div style="container">
                        <div class="from-block">
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label for="filter_by">Sort By</label>
                                    <select name="filter_by" id="filter_by" class="form-control"
                                        v-model="filter.filter_by">
                                        <option :value="'id'">Id</option>
                                        <option :value="'name'">Name</option>
                                        <option :value="'created_at'">Created At</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="radio-group mt-10" style="margin-top: 24px;">
                                        <label class="radio-group__item">
                                            <input type="radio" name="orderby-group" v-model="filter.orderby"
                                                class="radio-group__input" value="asc" checked="">
                                            <span class="radio-group__text">Asc</span>
                                        </label>
                                        <label class="radio-group__item">
                                            <input type="radio" name="orderby-group" v-model="filter.orderby"
                                                class="radio-group__input" value="desc">
                                            <span class="radio-group__text">Desc</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="filter_by">Date Between</label>
                                    <div class="input-group">
                                        <flat-pickr v-model="filter.date" :config="config"
                                            class="date-rage-picker form-control" placeholder="Select date range"
                                            @on-close="doSomethingOnClose" name="date">
                                        </flat-pickr>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="resetFilter"
                                    class="btn btn-default mb-2 mr-3">Reset</button>
                                <button type="button" @click="searchRaffleDraws(1)"
                                    class="btn btn-success mb-2 mr-3">Search</button>
                            </div>
                            <!-- /.box-footer -->
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Start At</th>
                                <th>End At</th>
                                <th>No Of Winners</th>
                                <th>Status</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(lucky_draw , index) in lucky_draws" :key="lucky_draw.id">
                                <td><b>{{ lucky_draw.id }}</b></td>
                                <td>{{ lucky_draw.name }}</td>
                                <td>{{ lucky_draw.description }} </td>
                                <td>{{ lucky_draw.start_at }} </td>
                                <td>{{ lucky_draw.end_at }} </td>
                                <td>{{ lucky_draw.no_of_winners }} </td>
                                <td>
                                    <span v-if="lucky_draw.status" class="badge badge-success badge-rounded mb-3 mr-3">Active</span>
                                    <span v-if="!lucky_draw.status" class="badge badge-danger badge-rounded mb-3 mr-3">Deactivated</span>
                                </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">                                        
                                        <div class="dropdown table__cell-actions-item">
                                            <a :href="'/raffledraw/'+ lucky_draw.id +'/select/winners'" v-if="!lucky_draw.is_winner_selected" class="btn btn-warning" style="margin-right:10px;">Raffle Draw</a>
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <router-link  class="dropdown-item" :to="{ name:'editRaffleDraw', params: { 'lucky_draw_id': lucky_draw.id }}" tag="a">Edit</router-link>
                                                <router-link  class="dropdown-item" :to="{ name:'RaffleDrawSettings', params: { 'lucky_draw_id': lucky_draw.id }}" tag="a">Settings</router-link>
                                                <!-- <a class="dropdown-item" v-if="!lucky_draw.is_winner_selected" @click="SelectWinners(lucky_draw.id)" href="javascript:void(0)">Select Winners</a> -->
                                                <a class="dropdown-item" v-if="lucky_draw.is_winner_selected" @click="ShowWinners(lucky_draw.id)" href="javascript:void(0)">Show Winners</a>
                                                <a class="dropdown-item" v-if="lucky_draw.is_winner_selected" @click="reset(lucky_draw.id , index)" href="javascript:void(0)">Reset Winner</a>
                                                <a v-if="lucky_draw.status" class="dropdown-item" @click="remove(lucky_draw.id , index)" href="javascript:void(0)">Deactivated</a>
                                                <a v-if="!lucky_draw.status" class="dropdown-item" @click="active(lucky_draw.id , index)" href="javascript:void(0)">Activated</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!lucky_draws.length">
                                    <td colspan="7" class="no-data-found-info">No Raffle Draws found</td>
                                </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                     <paginate
                        v-if="totalPages > 1"
                        :page-count="totalPages"
                        :page-range="3"
                        :margin-pages="2"
                        :click-handler="searchRaffleDraws"
                        :container-class="'pagination float-right'"
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
                lucky_draws: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                show_filter: false,
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
            this.getRaffleDraws();
        },
        methods: {
            getRaffleDraws: function () {
                axios.get('/lucky-draw/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.lucky_draws = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchRaffleDraws: function (page) {
                axios.post('/lucky-draw/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.lucky_draws = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            }, 
            SelectWinners: function(id){
               this.$router.push({
                    path: 'lucky-draws/' + id + '/select/winners'
                });
            } , 
            ShowWinners: function(id){
                this.$router.push({
                    path: '/lucky-draws/'+ id +'/winners'
                });
            },        
            OrderBy: function(orderby){
                this.filter.orderby = orderby;
                this.searchRaffleDraws(1);
            },
            remove: function(id , index){
                this.$swal({
                    title: "Are you want to delete this item?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.delete('/lucky-draw/remove/' + id)
                            .then(r => r.data)
                            .then((response) => {
                                this.lucky_draws[index] = response.data;
                                this.lucky_draws.push();
                            });
                    } 
                });
               
            },
            active: function (id , index) {
                axios.get('/lucky-draw/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.lucky_draws[index] = response.data;
                        this.lucky_draws.push();
                    });
            },
             doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
            },
            resetFilter: function () {
                this.filter = {
                    orderby: 'desc'
                };

                this.searchRaffleDraws(1);
            },
            reset: function(id , index){
                this.$swal({
                    title: "Are you want to reset Raffle draw winners?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.get('/lucky-draw/' + id + '/reset')
                            .then(r => r.data)
                            .then((response) => {
                                this.lucky_draws[index] = response.data;
                                this.lucky_draws.push();
                            }).catch((error) => {
                                notification('Reset Winners' , error.response.data.message , 'danger');
                            });
                    } 
                });
                
            },
            RaffleDraw:function(id){
                this.$router.push({
                    path: '/raffledraw/'+ id +'/select/winners'
                });
            },
            createRaffleDraw: function () {
                this.$router.push({
                    path: '/lucky-draws/create'
                });
            }
        }
    }

</script>
