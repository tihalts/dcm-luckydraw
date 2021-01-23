<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Customer Reports</h2>
                </div>
                <div class="page-content__header-meta">
                    <div class="row">
                        <div class="col">
                            <div class="input-group" style="float:left;">
                                <flat-pickr v-model="filter.date" :config="config" class="date-rage-picker form-control"
                                    placeholder="Select date range" @on-close="doSomethingOnClose" name="date">
                                </flat-pickr>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Country Wise Coustomers
                                </div>
                                <div class="widget-controls__header-controls">
                                  
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCountryCustomers'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('countyWiseCustomer')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="loaded" :options="options" :chart-data="customers"
                              id="countyWiseCustomer"  class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Country Wise Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCountrySales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('countyWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="loaded1" :options="sale_options" :chart-data="sales"
                                id="countyWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Customer Wise Vouchers
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportVoucherCustomers'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('countyWiseVouchers')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                               
                            </div>
                            <bar-chart v-if="loaded2" :options="customer_voucher_options" :chart-data="customer_vouchers"
                                id="countyWiseVouchers" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Date Wise Vouchers
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportVoucherDays'}"
                                                    tag="button">See More</router-link>
                                </div>
                               
                            </div>
                            <bar-chart v-if="loaded3" :options="date_voucher_options" :chart-data="date_vouchers"
                                class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Campaign Wise Vouchers
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCountryCustomers'}"
                                                    tag="button">See More</router-link>
                                </div>
                               
                            </div>
                            <bar-chart v-if="loaded4" :options="campaign_voucher_options" :chart-data="campaign_vouchers"
                                class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div> -->
                
            </div>
        </div>
    </div>

</template>

<script>
    import axios from 'axios';
    import BarChart from './BarChart.js';
    import LineChart from './LineChart.js';

    export default {
        components: {
            BarChart,
            LineChart
        },
        data() {
            return {
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
                },
                customers: null,
                sales: null,
                customer_vouchers: null,
                date_vouchers: null,
                campaign_vouchers: null,
                loaded: false,
                loaded1: false,
                loaded2: false,
                loaded3: false,
                loaded4: false,
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Countries'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'users'
                            }
                        }]
                    }
                },
                sale_options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Countries'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Sales in BD'
                            }
                        }]
                    }
                },
                customer_voucher_options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Customers'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Vocuhers'
                            }
                        }]
                    }
                },
                date_voucher_options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Date'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Vocuhers'
                            }
                        }]
                    }
                },
                campaign_voucher_options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Campaign'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Vocuhers'
                            }
                        }]
                    }
                },
            }
        },
        mounted() {
            this.countryWiseCoustomers();
            this.countryWiseSales();
            this.CoustomerWiseVouchers();
            //this.CampaignWiseVouchers();
            //this.DateWiseVouchers();
        },
        methods: {
            countryWiseCoustomers() {
                axios.get('/customer/top-countries' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.customers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Customers',
                                backgroundColor: '#f87979',
                                data: response.data
                            }]
                        };
                        this.loaded = true;
                    });
            },
            countryWiseSales() {
                axios.get('/customer/sale-by-countries' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.sales = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#236cd9',
                                data: response.data
                            }]
                        };
                        this.loaded1 = true;
                    });
            },
            CoustomerWiseVouchers() {
                axios.get('/customer/customer-wise-vouchers' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.customer_vouchers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Vouchers',
                                backgroundColor: '#8c37a3',
                                data: response.data
                            }]
                        };
                        this.loaded2 = true;
                    });
            },
            DateWiseVouchers() {
                axios.get('/customer/date-wise-vouchers' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.date_vouchers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Dates',
                                backgroundColor: '#c78b2a',
                                data: response.data
                            }]
                        };
                        this.loaded3 = true;
                    });
            },
            CampaignWiseVouchers() {
                axios.get('/customer/campaign-wise-vouchers' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.campaign_vouchers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Capaigns',
                                backgroundColor: '#37a361',
                                data: response.data
                            }]
                        };
                        this.loaded4 = true;
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.countryWiseCoustomers();
                this.countryWiseSales();
                this.CoustomerWiseVouchers();
                this.CampaignWiseVouchers();
                this.DateWiseVouchers();
            },
        }
    }

</script>

<style scoped>
    .widget-index-chart {
        height: 480px;
        padding: 0px 20px;
    }

    .input.date-rage-picker {
        background: white !important;
    }

</style>
