<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Activities</h2>
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
            <div class="container-fh__content dataset">
                <div class="row">
                    <div class="col-12">
                        <div class="order-collapse green">
                            <a class="order-collapse__header collapsed" data-toggle="collapse" href="#collapse1"
                                aria-expanded="false" aria-controls="collapse1">
                                <h4> <i class="fa fa-filter"></i> Filter</h4>
                                <span>
                                    <span class="collapse-icon ua-icon-arrow-down-alt"></span>
                                </span>
                            </a>
                            <div class="order-collapse-inner collapse" id="collapse1" style="">
                                <div style="padding:20px;">
                                    <div class="from-block">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label for="email">Select User</label>
                                                <v-select label="fullname" :filterable="false" :options="users"
                                                    @search="onUserSearch" v-model="user" style="width:100%;">
                                                    <template slot="no-options">
                                                        type to search users..
                                                    </template>
                                                    <template slot="option" slot-scope="option">
                                                        <div class="d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                        <span>{{ option.mobile }}</span>
                                                    </template>
                                                    <template slot="selected-option" slot-scope="option">
                                                        <div class="selected d-center">
                                                            {{ option.fullname }}
                                                        </div>
                                                    </template>
                                                </v-select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="radio-group mt-10" style="margin-top: 24px;">
                                                    <label class="radio-group__item">
                                                        <input type="radio" name="orderby-group"
                                                            v-model="filter.orderby" class="radio-group__input"
                                                            value="asc" checked="">
                                                        <span class="radio-group__text">Asc</span>
                                                    </label>
                                                    <label class="radio-group__item">
                                                        <input type="radio" name="orderby-group"
                                                            v-model="filter.orderby" class="radio-group__input"
                                                            value="desc">
                                                        <span class="radio-group__text">Desc</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr />
                                        <div class="form-group text-right m-b-0">
                                            <button type="button" @click="resetFilter"
                                                class="btn btn-default mb-2 mr-3">Reset</button>
                                            <button type="button" @click="searchActivities(1)"
                                                class="btn btn-success mb-2 mr-3">Search</button>
                                        </div>
                                        <!-- /.box-footer -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Old value</th>
                                <th>New value</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( activity , index ) in activities" :key="activity.id">
                                <td><b>{{ (index + 1) + (15 * (currentPage - 1)) }}</b></td>
                                <td> {{ activity.log_name }} </td>
                                <td>{{ activity.description }} </td>
                                <td>
                                    <div v-if="activity.properties.old">
                                        <div v-for="(value , key) in activity.properties.old" :key="key">
                                            <span>{{ key }} : </span>
                                            <span style="color:#808080;">{{ value }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="activity.properties.attributes">
                                        <div v-for="(value , key) in activity.properties.attributes" :key="key">
                                            <span>{{ key }} : </span>
                                            <span style="color:#808080;">{{ value }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget" v-if="activity.causer">
                                        <a href="javascript:void(0)"
                                            class="table__cell-widget-name">{{ activity.causer.fullname }}</a>
                                        <span class="table__cell-widget-description">
                                            {{ activity.causer.email }}</span>
                                            <span class="table__cell-widget-description">{{ activity.causer.mobile }} </span>
                                    </div>
                                    
                                </td>
                                <td>{{ activity.created_at }} </td>
                            </tr>
                            <tr v-show="!activities.length">
                                <td colspan="8" class="no-data-found-info">No Activities found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchActivities" :container-class="'pagination float-right'"
                        :page-class="'page-item'">
                    </paginate>
                </div>
            </div>
        </div>

    </div>

</template>
<style scoped>
    .input.date-rage-picker {
        background: white !important;
    }

    .activity-row {
        margin-top: 1rem;
        margin-bottom: 1rem;
        border: 0;
        border-bottom: 1px solid #0303031a;
    }

    .remove-btn {
        padding: 2px 7px !important;
        font-size: 14px !important;
        margin-top: 30px;
    }

    img.avatar_image {
        height: auto;
        max-width: 2.5rem;
        margin-right: 1rem;
    }

    .d-center {
        display: flex;
        align-items: center;
    }

    .selected img {
        width: auto;
        max-height: 23px;
        margin-right: 0.5rem;
    }

    .v-select .dropdown li {
        border-bottom: 1px solid rgba(112, 128, 144, 0.1);
    }

    .v-select .dropdown li:last-child {
        border-bottom: none;
    }

    .v-select .dropdown li a {
        padding: 10px 20px;
        width: 100%;
        font-size: 1.25em;
        color: #3c3c3c;
    }

    .v-select .dropdown-menu .active>a {
        color: #fff;
    }

    .vue-tel-input.is-invalid {
        border: 1px solid #ec4949 !important;
    }

</style>
<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                activities: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    'orderby': 'desc',
                    'itemPerPage': 15
                },
                form: {},
                totalPages: 0,
                users: [],
                user: {},
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
            this.searchActivities(1);
        },
        created() {
            
            //this.updateDatas ();
        },
        methods: {
            searchActivities: function (page) {
                this.resetControl();
                this.filter.page = page;
                if (this.user) this.filter.user_id = this.user.id;
                var config = {
                    'params': this.filter
                };
                axios.get('/activity/logs', config)
                    .then(r => r.data)
                    .then((response) => {
                        this.activities = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.getActivitieCharts(config);
                    });
            },
            doSomethingOnClose: function (selectedDates, dateStr, instance) {
                var res = dateStr.split("to");
                this.filter.start_date = res[0];
                this.filter.end_date = res[1];
                this.searchActivities(1);
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchActivities(1);
            },
            onUserSearch(search, loading) {
                loading(true);
                this.searchUsers(loading, search, this);
            },
            searchUsers: _.debounce((loading, search, vm) => {
                axios.get('/fetch/users', {
                        params: {
                            'searchText': search
                        }
                    })
                    .then(r => r.data).then((response) => {
                        vm.users = response.data;
                        loading(false);
                    });
            }, 350),
            resetControl: function () {
                this.filter.user_id = null;
            },
            resetFilter: function () {
                this.user = null;
                this.filter = {
                    'user_id': null,
                    'orderby': 'desc'
                };
            }
        }
    }

</script>
