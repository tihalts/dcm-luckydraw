<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Roles</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Roles</div>
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
                                v-on:keyup="searchRoles(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createRole" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Role Name </th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(role , index) in roles" :key="role.id">
                                <td>{{ (index + 1) + (15 * (currentPage - 1)) }}</td>
                                <td>{{ role.name }}</td>
                                <td>{{ role.slug }}</td>
                                <td>{{ role.description }}</td>
                                <td>{{ role.type }}</td>
                                <td>{{ role.group }}</td>
                                <td>
                                    <router-link tag="button" type="button" class="btn btn-sm btn-success icon-left"
                                        :to="{ name:'rolePermissions', params: { 'role_id': role.id }}">
                                        <span class="fa fa-shield"></span>
                                    </router-link>
                                    <router-link tag="button" type="button" class="btn btn-sm btn-info icon-left"
                                        :to="{ name:'editRole', params: { 'role_id': role.id }}">
                                        <span class="fa fa-pencil"></span>
                                    </router-link>
                                    <button type="button" class="btn btn-sm btn-danger icon-left">
                                        <span class="fa fa-trash"></span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-show="!roles.length">
                                <td colspan="7" class="no-data-found-info">No Roles found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col">
                    <paginate v-if="totalPages > 1" :page-count="totalPages" :page-range="3" :margin-pages="2"
                        :click-handler="searchRoles" :container-class="'pagination float-right'"
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
                roles: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
                user: {}
            }
        },
        mounted: function () {
            this.getRoles();
        },
        methods: {
            getRoles: function () {
                axios.get('/role/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.roles = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchRoles: function (page) {
                axios.post('/role/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.roles = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            createRole: function () {
                this.$router.push({
                    path: '/roles/create'
                });
            }
        }
    }

</script>
