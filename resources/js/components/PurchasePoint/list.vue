<template>
    <div class="page-content">
        <div class="container-fluid container-fh">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Purchase Points</h2>
                </div>
            </div>
            <div class="container-fh__content dataset">
                <div class="dataset__header">
                    <div class="dataset__header-side">
                        <div class="dataset__header-heading">Purchase Points</div>
                    </div>
                    <div class="dataset__header-controls">
                        <div class="input-group input-group-icon icon-right dataset__header-search">
                            <input class="form-control dataset__header-search-input" type="text" placeholder="Search">
                            <span class="input-icon mdi mdi-magnify"></span>
                        </div>
                        <a @click="createPurchasePointModal()" href="javascript:void(0)"
                            class="mdi mdi-plus-circle dataset__header-control dataset__header-controls-icon"></a>
                    </div>
                </div>
                <div class="dataset__body dataset__body--panel">
                    <table class="table dataset__table table__actions">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Amount From</th>
                                <th>Amount To</th>
                                <th>Points</th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="(purchase_point , index) in purchase_points" :key="purchase_point.id">
                                <td>{{ (index + 1) + (15 * (currentPage - 1)) }}</td>
                                <td>{{purchase_point.purchase_from}}</td>
                                <td>{{purchase_point.purchase_to}}</td>
                                <td>{{purchase_point.purchase_points}}</td>
                                 <td class="table__cell-actions">
                                    <div class="table__cell-actions-wrap">
                                        <div class="dropdown table__cell-actions-item">
                                            <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                data-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <div class="dropdown-menu" x-placement="bottom-start">
                                                <a  class="dropdown-item" href="javascript:void(0)" @click="editPurchasePointModal(purchase_point.id)">Edit</a>
                                                <a  class="dropdown-item" href="javascript:void(0)" @click="removePurchasePoint(purchase_point.id)">Remove</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>                               
                            </tr>
                            <tr v-show="!purchase_points.length">
                                <td colspan="5" class="no-data-found-info">No Booking Enquires found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="create-purchase-point-modal">
            <div class="modal-dialog" purchase_point="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Create Purchase Point</h5>                       
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <form purchase_point="form" v-on:submit.prevent="createPurchasePoint()">
                    <div class="modal-body">
                         
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="purchase_from">Purchase Amount From</label>
                                    <input id="purchase_from" type="number" min="0" class="form-control" name="purchase_from"
                                        v-model="form.purchase_from" required placeholder="Purchase Amount From">
                                    <span class="error-message purchase_from"></span>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_to">Purchase Amount To</label>
                                    <input id="purchase_to" type="number" min="0" class="form-control" name="purchase_to"
                                        v-model="form.purchase_to" placeholder="Purchase Amount To">
                                    <span class="error-message purchase_to"></span>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_to">Points</label>
                                    <input id="purchase_points" type="number" min="0" class="form-control" name="purchase_points"
                                        v-model="form.purchase_points" placeholder="Purchase Points">
                                    <span class="error-message purchase_points"></span>
                                </div>
                            </div>
                       
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info close" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="submit">Create</button>
                    </div>
                     </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit-purchase-point-modal">
            <div class="modal-dialog" purchase_point="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Edit Purchase Point</h5>                       
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <form purchase_point="form" v-on:submit.prevent="editPurchasePoint()">
                    <div class="modal-body">
                         
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="purchase_from">Purchase Amount From</label>
                                    <input id="purchase_from" type="number" min="0" class="form-control" name="purchase_from"
                                        v-model="form.purchase_from" required placeholder="Purchase Amount From">
                                    <span class="error-message purchase_from"></span>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_to">Purchase Amount To</label>
                                    <input id="purchase_to" type="number" min="0" class="form-control" name="purchase_to"
                                        v-model="form.purchase_to" placeholder="Purchase Amount To">
                                    <span class="error-message purchase_to"></span>
                                </div>
                                <div class="form-group">
                                    <label for="purchase_to">Points</label>
                                    <input id="purchase_points" type="number" min="0" class="form-control" name="purchase_points"
                                        v-model="form.purchase_points" placeholder="Purchase Points">
                                    <span class="error-message purchase_points"></span>
                                </div>
                            </div>
                       
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info close" data-dismiss="modal" aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="submit">Edit</button>
                    </div>
                     </form>
                </div>
            </div>
        </div>     
     
    </div>
</template>

<script>
    import axios from 'axios';
    import vSelect from 'vue-select';

    export default {
        data() {
            return {
                purchase_points: [],
                totalItems: null,
                currentPage: null,
                filter: [],
                form: {},
            }
        },
        mounted: function () {
            this.getPurchasePoints(); //method1 will execute at pageload
        },
        methods: {
            getPurchasePoints: function () {
                axios.get('/purchase-point/list')
                    .then(r => r.data)
                    .then((response) => {
                        this.purchase_points = response.data;
                        this.totalItems = response.totalItems;
                        this.currentPage = response.currentPage;
                        this.filter = response.filter;
                    });
            },
            createPurchasePoint: function () {
                axios.post('/purchase-point/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchase_points.push(response.data);
                        this.form = {};
                        $('#create-purchase-point-modal').modal('hide');
                    });
                //hidebtnLoader();
            },
            editPurchasePoint: function () {
                axios.post('/purchase-point/edit/' + this.form.id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.purchase_points = apiModifyTable(this.purchase_points, this.form.id, response.data);
                        this.form = {};
                        $('#edit-purchase-point-modal').modal('hide');
                    });
                //hidebtnLoader();
            },
            createPurchasePointModal: function () {
                $('#create-purchase-point-modal').modal('show');
            },
            editPurchasePointModal: function (id) {
                axios.get('/purchase-point/fetch/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                        $('#edit-purchase-point-modal').modal('show');
                    });
            },
        }
    }

</script>