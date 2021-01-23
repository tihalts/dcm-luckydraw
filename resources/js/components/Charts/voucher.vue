<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Voucher Reports</h2>
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
                                    Date Wise Vouchers
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportVoucherDays'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('DateWiseVouchers')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                               
                            </div>
                            <bar-chart v-if="date_voucher_option" :options="date_voucher_options" :chart-data="date_vouchers"
                                id="DateWiseVouchers" class="chart-widget__chart"></bar-chart>
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
                                                    :to="{ name:'ReportVoucherCampaigns'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('CampaignWiseVouchers')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                               
                            </div>
                            <bar-chart v-if="campaign_voucher_option" :options="campaign_voucher_options" :chart-data="campaign_vouchers"
                                id="CampaignWiseVouchers" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Promoter Wise Vouchers
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportVoucherPromoters'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('PromoterWiseVouchers')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                               
                            </div>
                            <bar-chart v-if="promoter_option" :options="promoter_options" :chart-data="promoters"
                                id="PromoterWiseVouchers" class="chart-widget__chart"></bar-chart>
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
                date_vouchers: null,
                promoters: null,
                campaign_vouchers: null,
                promoter_option: false,
                date_voucher_option: false,
                campaign_voucher_option: false,
                promoter_options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
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
                                labelString: 'Promoters'
                            }
                        }]
                    }
                },
                date_voucher_options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
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
                    maintainAspectRatio: false,
                    legend: {
                        display: false,
                    },
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
            this.CampaignWiseVouchers();
            this.DateWiseVouchers();
            this.Promoters();
        },
        methods: {
            Promoters() {
                axios.get('/voucher/by-promoters' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.promoters = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Vouchers',
                                backgroundColor: '#c78b2a',
                                data: response.data
                            }]
                        };
                        this.promoter_option = true;
                    });
            },
            DateWiseVouchers() {
                axios.get('/customer/date-wise-vouchers' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.date_vouchers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Vouchers',
                                backgroundColor: '#c78b2a',
                                data: response.data
                            }]
                        };
                        this.date_voucher_option = true;
                    });
            },
            CampaignWiseVouchers() {
                axios.get('/customer/campaign-wise-vouchers' , { params:  this.filter })
                    .then(r => r.data)
                    .then((response) => {
                        this.campaign_vouchers = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Vouchers',
                                backgroundColor: '#37a361',
                                data: response.data
                            }]
                        };
                        this.campaign_voucher_option = true;
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.Promoters();
                this.CampaignWiseVouchers();
                this.DateWiseVouchers();
            },
        }
    }

</script>

<style >
    .widget-index-chart {
        height: 480px;
        padding: 0px 20px;
    }   

</style>
