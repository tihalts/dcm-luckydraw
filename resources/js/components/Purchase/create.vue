<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Create Purchase</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div v-if="errors !== null" class="alert content-alert content-alert--danger mt-4" role="alert">
                        <div class="content-alert__info">
                            <span class="content-alert__info-icon ua-icon-warning"></span>
                        </div>
                        <div class="content-alert__content">
                            <div class="content-alert__heading"></div>
                            <div class="content-alert__message">
                                <ul class="custom-alert__list" v-for="(values , key) in errors" :key="key">
                                    <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="main-container m-task__content">
                        <div class="row">
                            <div class="col">
                                <h2 class="m-task__name float-left">Create Purchase Form</h2>
                                <button type="button" class="btn btn-info mb-2 mr-3 float-right"
                                    @click="createCustomerModal">Create Customer</button>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col">
                                <h4 class="m-task__labels-heading">Search Customer</h4>
                                <form role="form" ref="customerSearchFrom" name="customerSearchFrom"
                                    v-on:submit.prevent="customerSearchFrom">
                                    <div class="widget widget-controls widget-table widget-notifications"
                                        style="height:auto;">
                                        <div class="widget-controls__header widget-controls__header--bordered"
                                            style="border: none;">
                                            <div style="width: 100%;">
                                                <div style="margin-top: 20px;">
                                                    <div class="input-group input-group-icon iconfont icon-right">
                                                        <input
                                                            class="form-control navbar-search__input navbar__menu-search-input"
                                                            v-model="searchText" v-on:keyup="searchCustomers(searchText)"
                                                            type="text" placeholder="Search Customers"><span
                                                            class="input-icon ua-icon-search"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="widget-controls__content js-scrollable search-content"
                                            v-if="customers.length" data-simplebar="init">
                                            <div class="simplebar-track vertical">
                                                <div class="simplebar-scrollbar"></div>
                                            </div>
                                            <div class="simplebar-track horizontal" style="visibility: hidden;">
                                                <div class="simplebar-scrollbar"></div>
                                            </div>
                                            <div class="simplebar-scroll-content">
                                                <div class="simplebar-content" style="overflow-x: hidden;">
                                                    <div class="widget-notifications__items">
                                                        <div class="widget-notifications__item"
                                                            v-for="customer in customers" :key="customer.id"
                                                            @click="selectedCustomer(customer)">
                                                            <span
                                                                class="ua-icon-widget-document widget-notifications__item-icon"></span>
                                                            <div class="widget-notifications__info">
                                                                <div class="widget-notifications__name">
                                                                    <a href="javascript:void(0)"
                                                                        class="widget-notifications__user">{{ customer.fullname }}</a>
                                                                </div>
                                                                <span
                                                                    class="widget-notifications__date">{{ customer.mobile }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="m-task__desc" v-if="customer">
                            <div class="m-task__desc-header">
                                <h3 class="m-task__desc-heading">Customer Information</h3>
                                <!-- <a href="#" class="m-task__control">Edit</a> -->
                            </div>
                            <div class="m-task__desc-text">
                                <div class="row">
                                    <div class="col">
                                        <table class="table table-no-border">
                                            <tr>
                                                <td class="text-left" style="width:30%;">Customer Name </td>
                                                <td class="text-left">: {{ customer.fullname }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="width:30%;">Customer CPR </td>
                                                <td class="text-left">: {{ customer.cpr }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="width:30%;">Mobile Number </td>
                                                <td class="text-left">: {{ customer.mobile }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="width:30%;">Email Id </td>
                                                <td class="text-left">: {{ customer.email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-left" style="width:30%;">Points </td>
                                                <td class="text-left">: {{ customer.points }}</td>
                                            </tr>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="m-task__desc-header" style="margin-top:50px;">
                            <h3 class="m-task__desc-heading">Purchase Details</h3>
                        </div>
                        <form role="form" ref="addPurchaseFrom" name="addPurchaseFrom"
                            v-on:submit.prevent="createPurchase">
                            <div class="from-block">
                                <div class="row">
                                    <div class="form-group col-xs-6">
                                        <label for="purchase_amount">Purchase Amount</label>
                                        <input type="number" class="form-control" name="purchase_amount"
                                            @change="getPurchasePoints(purchase_amount)" v-model="purchase_amount"
                                            placeholder="Purchase Amount" required>
                                    </div>
                                    <div class="form-group col-xs-4">
                                        <label for="purchase_points">Purchase Points</label>
                                        <input type="number" class="form-control" name="purchase_points"
                                            v-bind:value="purchase_points" placeholder="Purchase Points" required
                                            readonly>
                                    </div>
                                </div>

                                <!-- /.box-body -->
                                <hr />
                                <div class="form-group text-right m-b-0">
                                    <button type="button" @click="listPurchase()"
                                        class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                                    <button type="submit" :disabled="create_purchase" class="btn btn-success create-purchase mb-2 mr-3">Create </button>
                                </div>
                                <!-- /.box-footer -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="create-customer-modal">
            <div class="modal-dialog" purchase_point="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Create Customer</h5>
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <form purchase_point="form" v-on:submit.prevent="createCustomer()">
                        <div class="modal-body">
                            <div v-if="user_errors !== null" class="alert content-alert content-alert--danger mt-4"
                                role="alert">
                                <div class="content-alert__info">
                                    <span class="content-alert__info-icon ua-icon-warning"></span>
                                </div>
                                <div class="content-alert__content">
                                    <div class="content-alert__heading"></div>
                                    <div class="content-alert__message">
                                        <ul class="custom-alert__list" v-for="(values , key) in user_errors" :key="key">
                                            <li v-for="(value , index) in values" :key="index">{{ value }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" name="first_name" v-model="form.first_name"
                                        placeholder="First Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" v-model="form.last_name"
                                        placeholder="Last Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" v-model="form.email"
                                        placeholder="Email address" required>
                                </div>
                                <div class="form-group">
                                    <label for="cpr">CPR</label>
                                    <input type="text" class="form-control" name="cpr" v-model="form.cpr"
                                        placeholder="CPR Number" required>
                                </div>
                                 <div class="form-group">
                                    <label for="email">Mobile</label>
                                    <vue-tel-input 
                                        :class="!mobile_isValid ? 'is-invalid' : ''" 
                                        :required="true" 
                                        :placeholder="'Enter mobile number'"
                                        :defaultCountry="'bh'"
                                        :enabledCountryCode="true"
                                        :disabledFormatting="true"
                                        :disabledFetchingCountry="true"
                                        @onInput="onInput" 
                                        @onValidate="CheckValidation"
                                        v-model="form.mobile"
                                        :preferredCountries="['bh','sa','kw','ae','om','in','pk','ph','bd']">
                                        </vue-tel-input>
                                </div>
                                <div class="form-group">
                                    <label for="email">Nationality</label>
                                    <v-select label="name" v-model="form.nationality" :options="countries" style="width:100%;"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer--center">
                            <button type="button" class="btn btn-outline-info close" data-dismiss="modal"
                                aria-label="Close">Cancel</button>
                            <button class="btn btn-info" type="submit">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="edit-customer-modal">
            <div class="modal-dialog" purchase_point="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Edit Customer</h5>
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <form purchase_point="form" v-on:submit.prevent="editCustomer()">
                        <div class="modal-body">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" name="first_name" v-model="form.first_name"
                                        placeholder="First Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" name="last_name" v-model="form.last_name"
                                        placeholder="Last Name" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" name="email" v-model="form.email"
                                        placeholder="Email address" required>
                                </div>
                                <div class="form-group">
                                    <label for="cpr">CPR</label>
                                    <input type="text" class="form-control" name="cpr" v-model="form.cpr"
                                        placeholder="CPR Number" required>
                                </div>
                                 <div class="form-group">
                                    <label for="email">Mobile</label>
                                    <vue-tel-input 
                                        :class="!mobile_isValid ? 'is-invalid' : ''" 
                                        :required="true" 
                                        :placeholder="'Enter mobile number'"
                                        :defaultCountry="'bh'"
                                        :enabledCountryCode="true"
                                        :disabledFormatting="true"
                                        :disabledFetchingCountry="true"
                                        @onInput="onInput" 
                                        @onValidate="CheckValidation"
                                        v-model="form.mobile"
                                        :preferredCountries="['bh','sa','kw','ae','om','in','pk','ph','bd']">
                                        </vue-tel-input>
                                </div>
                                <div class="form-group">
                                    <label for="email">Nationality</label>
                                    <v-select label="name" v-model="form.nationality" :options="countries" style="width:100%;"></v-select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer--center">
                            <button type="button" class="btn btn-outline-info close" data-dismiss="modal"
                                aria-label="Close">Cancel</button>
                            <button class="btn btn-info" type="submit">Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</template>
<style scoped>
    .search-content {
        margin-left: 20px;
        margin-right: 20px;
        margin-top: -10px;
        border-top: none;
        border: solid 1px #dad5d5;
    }
     .vue-tel-input.is-invalid{
        border: 1px solid #ec4949 !important;
    }

</style>

<script>
    import axios from 'axios';

    export default {
        data() {
            return {
                form: {},
                errors: null,
                user_errors: null,
                customers: [],
                customer: null,
                formData: {},
                purchase_points: 0,
                points: [],
                otherwise: {},
                searchText: "",
                isLoading: false,
                countries: [],
                searchText: "",
                purchase_amount: 0,
                create_purchase: false,
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                },
                mobile_isValid: false
            }
        },
        mounted: function () {
            this.getCountries();
            this.getPurchasePointSettings();
        },
        methods: {
            getLabel(item) {
                return item.fullname;
            },
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
            },
            getPurchasePointSettings: function () {
                axios.get('/get-purchase-points')
                    .then(r => r.data)
                    .then((response) => {
                        this.points = response.data.points;
                        this.otherwise = response.data.otherwise;
                    });
            },
            createPurchase: function () {
                if (!this.customer) return;
                this.formData.customer_id = this.customer.id;
                this.formData.amount = this.purchase_amount;
                this.create_purchase = true;
                axios.post('/purchase/create', this.formData)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.create_purchase = false;
                        this.listPurchase();
                    }).catch((error) => {
                        this.create_purchase = false;
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });
                hidebtnLoader();
            },
            createCustomer: function () {
                this.user_errors = null;
                if(!this.mobile_isValid) return;
                axios.post('/customer/create', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.customer = response.data;
                        $('#create-customer-modal').modal('hide');
                    }).catch((error) => {
                        this.user_errors = typeof error.response.data.errors !== 'undefined' ? error.response
                            .data.errors : null;
                    });
                //hidebtnLoader();
            },
            searchCustomers: function (text) {
                this.isLoading = true;
                axios.post('/purchase-customer/search', {
                        'searchText': text
                    })
                    .then(r => r.data)
                    .then((response) => {
                        this.customers = response.data;
                        this.isLoading = false;
                    });
                //hidebtnLoader();
            },
            selectedCustomer: function (customer) {
                this.searchText = null;
                this.customers = [];
                this.customer = customer;
            },
            editCustomer: function () {
                this.user_errors = null;
                disableBtnLoader('create-purchase' , true );
                axios.post('/customer/edit/' + this.form.id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.customer = response.data;
                        $('#edit-customer-modal').modal('hide');
                        disableBtnLoader('create-purchase' , false );
                    }).catch((error) => {
                        this.user_errors = typeof error.response.data.errors !== 'undefined' ? error.response
                            .data.errors : null;
                            disableBtnLoader('create-purchase' , false );
                    });
            },
            createCustomerModal: function () {
                $('#create-customer-modal').modal('show');
            },
            editCustomerModal: function (id) {
                axios.get('/customer/fetch/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.customer = response.data;
                        $('#edit-customer-modal').modal('show');
                    });
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            },
            getPurchasePoints: function(amount){
                this.purchase_points = parseInt(amount);
            },
            onInput({ number, isValid, country }) {
                this.form.mobile = number;
                this.mobile_isValid = isValid;
                this.form.country_iso = country.iso2;
            },
            CheckValidation: function({ number, isValid, country }){
                this.mobile_isValid = isValid;
            },
            getPurchasePointss: function (amount) {
                if (amount == 0) {
                    this.purchase_points = 0;
                    return;
                }
                if (parseInt(this.otherwise.amount) <= parseInt(amount)) {
                    this.purchase_points = parseInt(this.otherwise.points);
                    return;
                }
                for (let index = 0; index < this.points.length; index++) {
                    if (parseInt(this.points[index]['amount_from']) <= parseInt(amount) && parseInt(this.points[
                            index]['amount_from']) >= parseInt(amount)) {
                        this.purchase_points = parseInt(this.points[index]['points']);
                        return;
                        break;
                    }
                }
                this.purchase_points = 0;
            }
        }
    }

</script>
