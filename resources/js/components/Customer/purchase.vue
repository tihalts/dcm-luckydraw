<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Customer Purchase</h2>
                </div>
                <div class="page-content__header-meta">
                    <a @click="finishAction()" class="btn btn-info icon-left" style="color:white;">
                        Reset Mirror <span class="btn-icon mdi mdi-backup-restore"></span>
                    </a>
                </div>
            </div>

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
            <!-- flex-column-reverse flex-md-row -->
            <div class="row">
                <div class="col-sm-12">
                    <form role="form" ref="addUserFrom" name="addUserFrom" v-on:submit.prevent="createPurchase">
                        <div class="main-container">
                            <h3>Create Customer Purchase Form</h3>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="from-block">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Search Customers</label>
                                                    <v-select label="fullname" :filterable="false" :options="customers"
                                                        @search="searchCustomers" @change="selectedCustomer"
                                                        v-model="customer" style="width:100%;">
                                                        <template slot="no-options">
                                                            type to search customers..
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
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="first_name">First Name</label>
                                                    <input type="text" class="form-control" name="first_name"
                                                        v-model="form.first_name" placeholder="First Name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="last_name">Last Name</label>
                                                    <input type="text" class="form-control" name="last_name"
                                                        v-model="form.last_name" placeholder="Last Name" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        v-model="form.email" placeholder="Email address" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label for="cpr">CPR</label>
                                                    <input type="text" class="form-control" name="cpr"
                                                        v-model="form.cpr" placeholder="CPR Number" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Mobile</label>
                                                    <vue-tel-input :class="!mobile_isValid ? 'is-invalid' : ''"
                                                        :required="true" :placeholder="'Enter mobile number'"
                                                        :defaultCountry="'bh'" :enabledCountryCode="true"
                                                        :disabledFormatting="true" :disabledFetchingCountry="true"
                                                        @onInput="onInput" @onValidate="CheckValidation"
                                                        v-model="form.mobile"
                                                        :preferredCountries="['bh','sa','kw','ae','om','in','pk','ph','bd']">
                                                    </vue-tel-input>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <div class="form-group">
                                                    <label for="email">Nationality</label>
                                                    <v-select label="name" v-model="form.nationality"
                                                        :options="countries" style="width:100%;"></v-select>
                                                </div>
                                            </div>
                                        </div>                                        
                                    </div>
                                </div>
                            </div>
                        </div>                       
                        <div class="main-container">
                            <h3>Purchases</h3>
                            <hr>
                            <div class="row purchase-row"   v-for="(purchase , index) in purchases" :key="index">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <label for="email">Shop</label>
                                        <v-select label="name" :filterable="false" :options="shops" @search="onShopSearch"
                                            v-model="purchase.shop" :class="form-control" style="width:100%;">
                                            <template slot="no-options">
                                                type to search shops..
                                            </template>
                                            <template slot="option" slot-scope="option">
                                                <div class="d-center">
                                                    {{ option.name }}
                                                </div>
                                                <span> {{ option.shop_no }}</span>
                                            </template>
                                            <template slot="selected-option" slot-scope="option">
                                                <div class="selected d-center">
                                                    {{ option.name }}
                                                </div>
                                            </template>
                                        </v-select>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-4">
                                    <div class="form-group">
                                        <label for="email">Purchase Amount</label>
                                        <input type="number" class="form-control" name="purchase_amount"
                                            v-model="purchase.amount" placeholder="Purchase Amount" required>
                                        <!-- <span>Lucky draw points {{purchase.amount}}</span> -->
                                    </div>
                                </div>
                                <div class="col-xs-1 col-md-2">
                                    <button v-if="purchases.length != 1" class="btn remove-btn mb-2 mr-3 btn-danger"
                                        @click="removePurchase(index)">X</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <button type="button" @click="addPurchase" class="btn btn-warning mb-2 mr-3">Add</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group text-right m-b-0">
                                <button type="button" @click="listUser()"
                                    class="btn btn-default mb-2 mr-3 float-left">Cancel</button>
                                <!-- <base-button type="submit" :loading="create_purchase" :disabled="this.purchases.length == 0" icon="save" color="success"
                                    class="btn btn-success float-right" >
                                    Add
                                </base-button> -->
                                <button type="submit" :disabled="create_purchase"
                                    class="btn btn-success mb-2 mr-3">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</template>

<style scoped>
    /* .purchase-row:nth-child(even) {background: #ecebeb}
    .purchase-row:nth-child(odd) {background: #f5f5b8} */
    .purchase-row{
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
    import _ from 'lodash';
    import BaseButton from '../base-button';

    export default {
        components: {
            'base-button': BaseButton,
        },
        data() {
            return {
                form: {},
                errors: null,
                user_errors: null,
                customers: [],
                customer: {},
                formData: {},
                purchase_points: 0,
                points: [],
                otherwise: {},
                searchText: "",
                isLoading: false,
                countries: [],
                searchText: "",
                purchase_amount: 0,
                shops: [],
                shop: {},
                campaigns: [],
                campaign: {},
                create_purchase: false,
                purchases: [],
                add_voucher: 0,
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                },
                mobile_isValid: false,
            }
        },
        watch: {
            form: {
                handler: function(newValue) {
                    this.updateAction(newValue);
                },
                deep: true               
            }
        },
        mounted: function () {
            //this.finishAction();
            this.getCountries();
            this.addPurchase();
        },
        methods: {
            onShopSearch(search, loading) {
                loading(true);
                this.searchShops(loading, search, this);
            },
            searchShops: _.debounce((loading, search, vm) => {
                axios.get('/fetch/shops', {
                    params: {
                        'searchText': search
                    }
                }).then(r => r.data).then((response) => {
                    vm.shops = response.data;
                    loading(false);
                });
            }, 350),
            onCampaignSearch(search, loading) {
                loading(true);
                this.searchCampaigns(loading, search, this);
            },
            searchCampaigns: _.debounce((loading, search, vm) => {
                axios.get('/fetch/scratch-card/campaigns', {
                    params: {
                        'searchText': search
                    }
                }).then(r => r.data).then((response) => {
                    vm.campaigns = response.data;
                    loading(false);
                });
            }, 350),
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
            },
            createPurchase: function () {
                if (!this.mobile_isValid || this.purchases.length == 0) return;
                this.form.purchases = this.purchases;
                this.create_purchase = true;
                axios.post('/customer/create/purchase', this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.form = {};
                        notification('update or create customer', 'update customer purchase successfull.!');
                        //this.create_purchase = false;
                        this.customer = response.data;
                        this.$router.push({
                            name: 'CustomerScratchCards',
                            params: {
                                'customer_id': this.customer.id
                            }
                        });
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    }).finally(() => {
                        this.create_purchase = false;
                    });
                hidebtnLoader();
            },
            searchCustomers(search, loading) {
                loading(true);
                this.onSearchCustomers(loading, search, this);
            },
            onSearchCustomers: _.debounce((loading, search, vm) => {
                axios.post('/purchase-customer/search', {
                    'searchText': search
                }).then(r => r.data).then((response) => {
                    vm.customers = response.data;
                    loading(false);
                });
            }, 350),
            selectedCustomer: function () {
                this.form = this.customer;
            },
            onInput({
                number,
                isValid,
                country
            }) {
                this.mobile_isValid = isValid;
            },
            listPurchase: function () {
                this.$router.push({
                    path: '/purchases'
                });
            },
            CheckValidation: function ({
                number,
                isValid,
                country
            }) {
                this.form.mobile = number;
                this.mobile_isValid = isValid;
                this.form.dialCode = '+' + country.dialCode;
                this.form.country_iso = country.iso2;
            },
            listUser: function () {
                this.$router.push({
                    path: '/customers'
                });
            },
            addPurchase: function () {
                this.purchases.push({
                    shop_id: 0.00,
                    amount: 0.00
                });
            },
            removePurchase: function (index) {
                this.purchases.splice(index, 1);
            },
            updateAction: function(data){                
                axios.post('/update/purchase/action', data).then(r => r.data).then((response) => {
                });
            },
            finishAction: function(){
                this.$swal({
                    title: "Are you want to reset mirror?",
                    text: "Are you sure? You won't be able to revert this!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Yes, Reset Mirror!"
                }).then((result) => { // <--
                    if (result.value) { // <-- if confirmed
                        axios.delete('/remove/purchase/action').then(r => r.data).then((response) => {
                            notification('Reset Mirror' , "Reset mirror successfully!" , 'success');
                        });
                    } else {
                         notification('Reset Mirror' , "Reset mirror cancelled!" , 'warning');  
                    }
                });
                
            }
        }
    }

</script>
