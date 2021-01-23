<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Gift Reports</h2>
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
                                    Campaign Wise Gifts
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportCampaignGifts'}"
                                                    tag="button">See More</router-link>
                                   <a type="button" href="javascript:printChart('CampaignWiseGifts')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                               
                            </div>
                            <bar-chart v-if="campaign_loaded" :options="campaign_options" :chart-data="campaigns"
                                id="CampaignWiseGifts" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Promoter Wise Gifts
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportPromoterGifts'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('PromoterWiseGifts')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="promoter_loaded" :options="promoter_options" :chart-data="promoters"
                                id="PromoterWiseGifts" class="chart-widget__chart"></bar-chart>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="widget widget-tabs widget-index-chart">
                        <div class="widget widget-tabs widget-index-chart">
                            <div class="widget-tabs__header">
                                <div>
                                    Day Wise Gifts
                                </div>
                                <div class="widget-controls__header-controls">
                                    <router-link class="btn btn-default btn-small"
                                                    :to="{ name:'ReportDayGifts'}"
                                                    tag="button">See More</router-link>
                                    <a type="button" href="javascript:printChart('dayWiseGifts')" class="btn btn-default btn-small">
                                        <i class="fa fa-download"></i>
                                    </a>
                                </div>
                            </div>
                            <bar-chart v-if="gift_loaded" :options="gift_options" :chart-data="gifts"
                                id="dayWiseGifts" class="chart-widget__chart"></bar-chart>
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
                campaigns: null,
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
                promoters: null,
                gifts: null,
                promoter_loaded: false,
                campaign_loaded: false,
                gift_loaded: false,
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
                                labelString: 'Total Gifts'
                            }
                        }]
                    }
                },
                campaign_options: {
                    responsive: true,
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
                                labelString: 'Gifts'
                            }
                        }]
                    }
                },
                gift_options: {
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
                                labelString: 'Day wise Gifts'
                            },
                            ticks: {
                                display: true //this will remove only the label
                            }
                        }],
                        yAxes: [{
                            display: true,
                            scaleLabel: {
                                display: true,
                                labelString: 'Gifts'
                            }
                        }]
                    }
                }
            }
        },
        mounted() {
            this.Promoters();
            this.Campaigns();
            this.Gifts();
        },
        methods: {
             Gifts() {
                axios.get('/gift/by-days' , { params:  this.filter } )
                    .then(r => r.data)
                    .then((response) => {
                        this.gifts = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Gifts',
                                backgroundColor: '#236cd9',
                                data: response.data
                            }]
                        };
                        this.gift_loaded = true;
                    });
            },
            Campaigns() {
                axios.get('/gift/by-campaigns' , { params:  this.filter } )
                    .then(r => r.data)
                    .then((response) => {
                        const datas = response.data;
                        const data1 = [];
                        const data2 = [];

                        for(var i = 0; i < datas.length; i++)
                        {
                            data1.push(datas[i]['y']);
                            data2.push(datas[i]['z']);
                        }

                        this.campaigns = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Total Gifts',
                                backgroundColor: '#79ed55',
                                data: data1
                            },{
                                label: 'Ungifts',
                                backgroundColor: '#f87979',
                                data: data2
                            }]
                        };
                        this.campaign_loaded = true;
                    });
            },
            Promoters() {
                axios.get('/gift/by-promoters' , { params:  this.filter } )
                    .then(r => r.data)
                    .then((response) => {
                        this.promoters = {
                            labels: response.labels,
                            datasets: [{
                                label: 'Gifts',
                                backgroundColor: '#236cd9',
                                data: response.data
                            }]
                        };
                        this.promoter_loaded = true;
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.Promoters();
                this.Campaigns();
                this.Gifts();
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
