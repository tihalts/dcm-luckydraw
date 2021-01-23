<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Permissions</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Permissions</div>
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
                                v-on:keyup="searchPermissions(1)" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="CreatePermission" role="button" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table id="demo-foo-addrow"
                        class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Permission Name </th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Type</th>
                                <th>Group</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(permission , index) in permissions" :key="permission.id">
                                <td>{{ (index + 1) + (15 * (currentPage - 1)) }}</td>
                                <td>{{ permission.name }}</td>
                                <td>{{ permission.slug }}</td>
                                <td>{{ permission.description }}</td>
                                <td>{{ permission.type }}</td>
                                <td>{{ permission.group }}</td>
                                <td>
                                    <router-link tag="button" type="button" class="btn btn-sm btn-info"
                                        :to="{ name:'editPermission', params: { 'permission_id': permission.id }}">
                                        <span class="glyphicon glyphicon-pencil"></span>
                                    </router-link>
                                    <button type="button" class="btn btn-sm btn-danger">
                                        <span class="glyphicon glyphicon-trash"></span>
                                    </button>
                                </td>
                            </tr>
                            <tr v-show="!permissions.length">
                                <td colspan="7" class="no-data-found-info">No Permission found</td>
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
                permissions: [],
                totalItems: null,
                currentPage: null,
                filter: {
                    orderby: 'desc'
                },
                form: {},
                totalPages: 0,
            }
        },
        mounted: function () {
            this.getPermissions();
        },
        methods: {
            getPermissions: function () {
                axios.get('/permission/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.permissions = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                        this.filter = response.filter;
                    });
            },
            searchPermissions: function (page) {
                axios.post('/permission/search/' + page, this.filter)
                    .then(r => r.data)
                    .then((response) => {
                        this.permissions = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.totalPages = Math.ceil(this.totalItems / 15);
                         window.scrollTo(0,0);
                    });
            },
            OrderBy: function (orderby) {
                this.filter.orderby = orderby;
                this.searchPermissions(1);
            },
            remove: function (id, index) {
                axios.delete('/permission/remove/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.permissions.splice(index, 1);
                    });
            },
            CreatePermission: function () {
                this.$router.push({
                    path: '/permissions/create'
                });
            }
        }
    }

</script>
