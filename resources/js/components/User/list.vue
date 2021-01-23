<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Users</h2>                   
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                   <div class="dataset__header-side"> 
                        <div class="dataset__header-heading">Users</div>
                        <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter By
                            </div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" @click="OrderBy('asc')" href="javascript:void(0)">ASC</a>
                                <a class="dropdown-item"  @click="OrderBy('desc')" href="javascript:void(0)">DESC</a>
                            </div>
                        </div>
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" v-model="filter.searchText" v-on:keyup="searchUsers(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createUser" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="hidden-sm">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="( user , index ) in users" :key="user.id">
                                <td><b>{{ ((index + 1) + (15 * (currentPage - 1)))  }}</b></td>
                                <td>
                                    <router-link href="javascript:void(0)" :to="{ name:'PromoterReportPurchases', params: { 'promoter_id': user.id }}" tag="a" class="table__cell-widget-name">
                                        {{ user.fullname }}
                                    </router-link>
                                </td>
                                <td>{{ user.mobile }} </td>
                                <td>{{ user.email }} </td>
                                <td>
                                    <span v-if="user.status" class="badge badge-success badge-rounded mb-3 mr-3">Active</span>
                                    <span v-if="!user.status" class="badge badge-danger badge-rounded mb-3 mr-3">Deactivated</span>
                                </td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start"
                                                style="position: absolute; transform: translate3d(0px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                                <router-link  class="dropdown-item" :to="{ name:'editUser', params: { 'user_id': user.id }}" tag="a">Edit</router-link>
                                                <a v-if="user.status" class="dropdown-item" @click="removeUser(user.id , index)" href="javascript:void(0)">Deactivated</a>
                                                <a v-if="!user.status" class="dropdown-item" @click="activeUser(user.id , index)" href="javascript:void(0)">Activated</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr v-show="!users.length">
                                    <td colspan="7" class="no-data-found-info">No Users found</td>
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
                        :click-handler="searchUsers"
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
                users: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0
            }
        },
        mounted: function () {
            this.getUsers();
        },
        methods: {
            getUsers: function () {
                axios.get('/user/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.users = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchUsers: function (page) {
                axios.post('/user/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.users = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            removeUser: function (id , index) {
                axios.delete('/user/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.users[index] = response.data;
                        this.users.push();
                    });
            },
            activeUser: function (id , index) {
                axios.get('/user/activated/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.users[index] = response.data;
                        this.users.push();
                    });
            },
            OrderBy: function(orderby){
                this.filter.orderby = orderby;
                this.searchUsers(1);
            },
            createUser: function () {
                this.$router.push({
                    path: '/users/create'
                });
            }
        }
    }

</script>
