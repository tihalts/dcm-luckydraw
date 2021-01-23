<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Purchase Reports</h2>
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
                                    Purchase Reports
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportDaySales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('PurchaseReports')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="sale_loaded" :options="sale_options" :chart-data="sales"
                                id="PurchaseReports" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Customer Wise Sales
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCustomerSales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('CustomerWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="customer_loaded" :options="customer_options" :chart-data="customers"
                                id="CustomerWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Promoter Wise Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportPromoterSales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('PromoterWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="promoter_loaded" :options="promoter_options" :chart-data="promoter"
                               id="PromoterWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Shop Wise Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportShopSales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('ShopWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="shop_loaded" :options="shop_options" :chart-data="shops"
                                id="ShopWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Category Wise Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCategorySales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('CategoryWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="category_loaded" :options="category_options" :chart-data="categories"
                                id="CategoryWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <!-- <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Top Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCountryCustomers'}"
                                                    tag="button">See More</router-link>
                                </div>
                            </div>
                            <bar-chart v-if="top_sale_loaded" :options="top_sale_options" :chart-data="top_sales"
                                class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div> -->

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Campaign Wise Sales
                                </div>
                               <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCampaignSales'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('CampaignWiseSales')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="campaign_loaded" :options="campaign_options" :chart-data="campaigns"
                                id="CampaignWiseSales" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>
                
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
                sales: null,
                customers: null,
                promoter: null,
                shops: null,
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
                categories: null,
                top_sales: null,
                campaigns: null,
                sale_loaded: false,
                customer_loaded: false,
                promoter_loaded: false,
                shop_loaded: false,
                category_loaded: false,
                top_sale_loaded: false,
                campaign_loaded: false,
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
                                labelString: 'Sales in BD'
                            }
                        }]
                    }
                },
                promoter_options: {
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
                                labelString: 'Promoters'
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
                category_options: {
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
                                labelString: 'Categories'
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
                shop_options: {
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
                                labelString: 'Shops'
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
                customer_options: {
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
                                labelString: 'Sales in BD'
                            }
                        }]
                    }
                },
                top_sale_options: {
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
                                labelString: 'Top Sales'
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
                campaign_options: {
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
                                labelString: 'Campaigns'
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
            }
        },
        mounted() {
            this.SaleReports();
            this.CustomerWiseSales();
            this.PromoterWiseSales();
            this.ShopWiseSales();
            this.CategoryWiseSales();
            this.CampaignWiseSales();
            this.TopSales();
        },
        methods: {
            SaleReports() {
                axios.get('/sale/reports' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.sales = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#f87979',
                                data: response.data
                            }]
                        };
                        this.sale_loaded = true;
                    });
            },
            CustomerWiseSales() {
                axios.get('/sale/report-by-customer' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.customers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#236cd9',
                                data: response.data
                            }]
                        };
                        this.customer_loaded = true;
                    });
            },
            PromoterWiseSales() {
                axios.get('/sale/report-by-promoter' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.promoter = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#8c37a3',
                                data: response.data
                            }]
                        };
                        this.promoter_loaded = true;
                    });
            },
            ShopWiseSales() {
                axios.get('/sale/report-by-shop' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.shops = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#c78b2a',
                                data: response.data
                            }]
                        };
                        this.shop_loaded = true;
                    });
            },
            CategoryWiseSales() {
                axios.get('/sale/report-by-category' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.categories = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#37a361',
                                data: response.data
                            }]
                        };
                        this.category_loaded = true;
                    });
            },
            TopSales() {
                axios.get('/sale/top-reports' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.top_sales = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#4b2edb',
                                data: response.data
                            }]
                        };
                        this.top_sale_loaded = true;
                    });
            },
            CampaignWiseSales() {
                axios.get('/sale/report-by-campaign' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.campaigns = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Sales in BD',
                                backgroundColor: '#ac2be3',
                                data: response.data
                            }]
                        };
                        this.campaign_loaded = true;
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.SaleReports();
                this.CustomerWiseSales();
                this.PromoterWiseSales();
                this.ShopWiseSales();
                this.CategoryWiseSales();
                this.CampaignWiseSales();
                this.TopSales();
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
