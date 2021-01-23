<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Spin & Win Sales Report</h2>
                </div>
                <div class="page-content__header-meta">
                    <div class="row">
                        <div class="col-8">
                            <div class="input-group" style="float:left;">
                                <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                    placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                                </flat-pickr>
                            </div>
                        </div>
                        <div class="col-2">
                            <div class="dropdown mr-3">
                                <button class="btn btn-info dropdown-toggle" type="button" data-toggle="dropdown">Export</button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" @click="exportPdf('excel')" href="javascript:void(0)">Excel</a>
                                    <a class="dropdown-item" @click="exportPdf('pdf')" href="javascript:void(0)">PDF</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="row">
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-amaranth">
                            <div>
                                <div class="widget-alpha__amount">{{ data.totalSales }}</div>
                                <div class="widget-alpha__description">Total Sales</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-green-jungle">
                            <div>
                                <div class="widget-alpha__amount">{{ data.totalPurchases }}</div>
                                <div class="widget-alpha__description">Total Purchases</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <div class="widget widget-alpha widget-alpha--color-orange widget-alpha--donut">
                            <div>
                                <div class="widget-alpha__amount">{{ data.totalGifts }}</div>
                                <div class="widget-alpha__description">Total Gifts</div>
                            </div> <span class="widget-alpha__icon icon-fa fa fa-gift"></span>
                        </div>
                    </div>
                </div>

                <!-- <div class="row">
                    <div class="col"></div>
                    <div class="col">
                        <div class="input-group float-right" style="max-width:300px;">
                            <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                            </flat-pickr>
                        </div>
                    </div>
                </div> -->
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Spin & Win Report</div>
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
                        <!-- <div class="form-group  dataset__header-search">
                            <select class="form-control" name="item_status" id="item_status" @change="searchGifts(1)"
                                v-model="filter.item_status">
                                <option value="">All</option>
                                <option value="ungifted">Ungifted</option>
                                <option value="gifted">Gifted</option>
                            </select>
                        </div> -->
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <v-select label="name" :filterable="false" :options="spinners"
                                @search="searchSpinners" @change="selectedSpinner"
                                v-model="spinner" style="width:100%;">
                                <template slot="no-options">
                                    type to search spinner..
                                </template>
                                <template slot="option" slot-scope="option">
                                    <div class="d-center">
                                        {{ option.name }}
                                    </div>
                                </template>
                                <template slot="selected-option" slot-scope="option">
                                    <div class="selected d-center">
                                        {{ option.name }}
                                    </div>
                                </template>
                            </v-select>
                            <!-- <input class="form-control dataset__header-search-input" v-model="filter.searchText"
                                v-on:keyup="searchGifts(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span> -->
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date</th>
                                <th>Total Purchase</th>
                                <th>Total Amount</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(item , index) in reports" :key="item.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>{{ item.date}}</td>
                                <td>{{ item.total }}</td>
                                <td>{{ item.amount }} </td>
                            </tr>
                            <tr v-show="!reports.length">
                                <td colspan="11" class="no-data-found-info">No reports found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="getReportLists" :container-class="'pagination float-right'"
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
                data: {},
                reports: [],
                totalItems: null,
                totalPages: 0,
                spinners:[],
                spinner: {},
                currentPage: 0,
                filter: {
                    orderby: 'desc'
                },
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
            this.getSpinner(); 
            this.getReports();
            this.getReportLists();
        },
        methods: {
            searchSpinners: _.debounce((loading, search) => {
                loading(true);
                this.getSpinner(search);
                loading(false);
            }, 350),
            getSpinner: function (search = "") {
                axios.get('/spinandwin/list' , {'searchText' : search})
                    .then(r => r.data)
                    .then((response) => {
                        this.spinners = response.data;
                    });
            },
            selectedSpinner(){
                if(this.spinner){
                this.filter.spinner_id = this.spinner.id;
                    this.getReports();
                    this.getReportLists();
                }               
            },
            getReports: function () {
                axios.post('/spinandwin/total-sale-report' , this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.data = response.data;
                    });
            },
            getReportLists: function (page = 1) {
                axios.post('/spinandwin/sale-report/list/' + page,  this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.reports = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        window.scrollTo(0, 0);
                    });
            },
             exportdata: function () {
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/spinandwin/sale/report/exports',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            exportPdf: function(type){
                var config = {
                    'params': this.filter
                };
                let routeData = this.$router.resolve({
                    path: '/spinandwin/sale/' + type + '/report',
                    query: this.filter
                });
                window.open(routeData.href, '_blank');
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0].trim();
                this.filter.end_date = res[1].trim();
                this.getReports();
                this.getReportLists(1);
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.getReports();
                this.getReportLists(1);
            }
        }
    }

</script>
