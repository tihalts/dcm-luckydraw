<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Dataset Table</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="#">Form</a>
                        </li>
                        <li class="breadcrumb-item active">Dataset Table</li>
                    </ol>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Users</div>
                        <div class="dropdown dataset__header-filter">
                            <div class="dropdown-toggle dataset__header-filter-toggle" data-toggle="dropdown">Filter and
                                Order</div>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">ASC</a>
                                <a class="dropdown-item" href="#">DESC</a>
                            </div>
                        </div>
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <div class="dropdown dataset__header-view dataset__header-control">
                            <div class="mdi mdi-grid dropdown-toggle dropdown-toggle__icon dataset__header-controls-icon"
                                data-toggle="dropdown"></div>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="#">
                                    <span class="mdi mdi-grid dropdown-item__icon"></span>Table View
                                </a>
                                <a class="dropdown-item" href="#">
                                    <span class="mdi mdi-format-list-bulleted dropdown-item__icon"></span>List View
                                </a>
                                <a class="dropdown-item" href="#">
                                    <span class="mdi mdi-grid-large dropdown-item__icon"></span>Grid View
                                </a>
                            </div>
                        </div>
                        <a href="#"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table class="table dataset__table table__actions">
                        <thead>
                            <tr>
                                <th class="table__checkbox">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="ch5">
                                        <label class="custom-control-label" for="ch5"></label>
                                    </div>
                                </th>
                                <th>Project</th>
                                <th>Deadline</th>
                                <th>Leader+Team</th>
                                <th>Budjet</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="is-selected">
                                <td class="table__checkbox">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" checked="" id="ch6">
                                        <label class="custom-control-label" for="ch6"></label>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <a href="#" class="table__cell-widget-name">New Dashboard</a>
                                        <span class="table__cell-widget-description">Google</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-name">13th Nov, 17</span>
                                        <span class="table__cell-widget-description color-danger">Overdue</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-user">
                                        <img src="img/users/user-10.png" alt=""
                                            class="table__cell-user-avatar rounded-circle">
                                        <div class="table__cell-user-wrap">
                                            <a href="#" class="table__cell-widget-name">Annie Cooper</a>
                                            <span class="table__cell-widget-description">UK, Design Team</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="table__cell-widget">
                                        <span class="table__cell-widget-name">$4670</span>
                                        <span class="table__cell-widget-description">Paid</span>
                                    </div>
                                </td>
                                <td class="table__label"><span class="badge badge-success">Label</span></td>
                                <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#">Edit</a>
                                                <a class="dropdown-item" href="#">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>                           
                        </tbody>
                    </table>
                </div>               
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {}
            }
        },
        mounted() {

            console.log('Component mounted.')
        },

        methods: {

        }
    }

</script>
