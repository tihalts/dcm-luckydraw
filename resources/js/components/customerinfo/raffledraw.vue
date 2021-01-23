<template>
    <div class="col-md-9">

        <div class="container-fluid container-fh">
            <div class="dataset__header">
                <div class="dataset__header-side">
                    <div class="dataset__header-heading">Raffle Draws</div>
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
                    <div class="input-group input-group-icon icon-right dataset__header-search">
                        <input class="form-control dataset__header-search-input" v-model="filter.searchText"
                            v-on:keyup="getWinners(1)" type="text" placeholder="Search">
                        <span class="input-icon mdi mdi-magnify"></span>
                    </div>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Raffle Draw Name</th>
                                <th>UUID</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(winner , index) in winners" :key="winner.id">
                                <td><b>{{ (index + 1) }}</b></td>
                                <td>
                                    <span v-if="winner.lucky_draw">{{ winner.lucky_draw.name }}</span>
                                </td>
                                <td>{{ winner.uuid }} </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <button :id="'winnder_' + winner.id" class="btn btn-success"
                                            @click="sendWinnerMsg(winner.winner.id)">Send Notification</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!winners.length">
                                <td colspan="7" class="no-data-found-info">No Winners found</td>
                            </tr>
                        </tbody>
                    </table>
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
                winners: [],
                totalItems: 0,
                currentPage: 1,
                filter: {
                    "searchText": "",
                    "itemPerPage": 15,
                    "orderby": "desc",
                },
                totalPages: 0,
            }
        },
        mounted: function () {
            this.getWinners(1);
        },
        methods: {
            getWinners: function (page) {
                axios.post('/customer/' + this.$route.params.customer_id + '/raffle-draw/search/' + page, this
                        .filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.winners = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            sendWinnerMsg: function (id) {
                axios.get('/sendwinner/notification/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        $('winnder_' + id).prop('disabled', true);
                        notification('Send Notification', 'Send Notification successfull.!');
                    }).catch((error) => {
                        $('winnder_' + id).prop('disabled', false)
                        notification('Send Notification', 'Send Notification Faild.!');
                    });
            },
        }
    }

</script>
