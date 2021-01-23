<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <!-- <h2 class="page-content__header-heading">Spin & Win Gifts</h2> -->
                </div>
                 <div class="page-content__header-meta">
                   <a class="btn btn-success icon-left" style="color:white;">
                        Remaining Spins : {{ unspins }}
                    </a>
                    <a @click="finishAction()" class="btn btn-info icon-left" style="color:white;">
                        Goto Next  
                        <span class="btn-icon mdi mdi-wallet-giftcard"></span>
                    </a>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Spin & Win Gifts</div>
                        <!-- <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter By
                            </div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" @click="OrderBy('asc')" href="javascript:void(0)">ASC</a>
                                <a class="dropdown-item" @click="OrderBy('desc')" href="javascript:void(0)">DESC</a>
                            </div>
                        </div> -->
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
                                <button type="button" @click="searchUsers(1)"
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
                                    <th>Gift Name</th>
                                    <th>Code</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="gift in gifts" :key="gift.id">
                                    <td>{{gift.name}}</td>
                                    <td>{{gift.code}}</td>
                                </tr>
                                <tr v-show="!gifts.length">
                                    <td colspan="11" class="no-data-found-info">No gifts found</td>
                                </tr>
                            </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchUsers" :container-class="'pagination float-right'"
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
                user: {},
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
        created() {
            this.timer = setInterval(() => {
                this.getUserGifts();
            }, 10000);
        },
        beforeDestroy() {
            clearInterval(this.timer);
        },
        mounted: function () {
            this.getUserGifts();
        },
        methods: {
            getUserGifts: function () {
               axios.get('/customer/' + this.$route.params.customer_id + '/spun/gifts')
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = response.data;
                        this.unspins = response.unspin;
                        // if (this.unspin == 0 ) {
                        //     this.rewardPoints();
                        // }
                    });
            },
            finishAction: function(){
                  this.$swal({
                            title: "Please check before leave all gifts provided!",
                            text: "Are you sure? You won't be able to revert this!",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "Yes"
                        }).then((result) => { // <--
                            if (result.value) { // <-- if confirmed
                                this.rewardPoints();
                            } 
                        });                               
            },
            rewardPoints: function(){
                this.$router.push({
                    name: 'createCustomerVoucher' , params : {
                        customer_id : this.$route.params.customer_id
                    }
                });
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            }
        }
    }

</script>
