<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Shop for free</h2>
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
                <div class="col-md-6">
                    <form role="form" ref="addUserFrom" name="addUserFrom">
                        <div class="main-container">
                            <h3>Create Customer Form</h3>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="from-block">
                                        <div class="row">
                                            <div class="col-md-12">
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
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="first_name">First Name</label>
                                                    <input type="text" class="form-control" name="first_name"
                                                        v-model="form.first_name" placeholder="First Name" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="last_name">Last Name</label>
                                                    <input type="text" class="form-control" name="last_name"
                                                        v-model="form.last_name" placeholder="Last Name" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-12">
                                                <div class="form-group">
                                                    <label for="email">Email</label>
                                                    <input type="email" class="form-control" name="email"
                                                        v-model="form.email" placeholder="Email address" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cpr">CPR</label>
                                                    <input type="text" class="form-control" name="cpr"
                                                        v-model="form.cpr" placeholder="CPR Number" required>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
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
                                            <div class="form-group col-md-6">
                                              <div class="form-group">
                                                    <label for="dob">Date Of Birth</label>
                                                    <flat-pickr class="form-control" :config="dateconfig" name="dob"
                                                        v-model="form.dob" placeholder="Date of Birth"></flat-pickr>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
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
                            <div class="col-sm-12">
                                <div class="form-group text-right m-b-0">      
                                    <button type="button" @click="clearAll()"
                                        class="btn btn-danger mb-2 mr-3">Clear</button>                              
                                    <button type="button" :disabled="isLoading" @click="searchGift()"
                                        class="btn btn-success mb-2 mr-3">Submit</button>
                                </div>
                            </div>
                        </div>    
                       
                    </form>
                </div>
                 <div class="col-md-6">
                    <div class="main-container">
                        <h3>Free Gifts</h3>
                        <hr/>
                        <div class="row">
                            <div v-if="!gift.id" class="col-sm-12">
                                <h4>No Gift Found</h4>
                            </div>                           
                            <div v-if="gift.id" class="col-sm-12">
                                <h4 v-if="gift.campaign">{{ gift.campaign.name }}</h4>
                                <div class="form-group">
                                    <label for="email">{{ gift.name }} ({{ gift.code }})</label>
                                    <v-select label="code" :filterable="false" :options="gift_items"
                                        @search="searchGiftItems" @change="selectedGiftItem"
                                        v-model="gift_item" style="width:100%;">
                                        <template slot="no-options">
                                            type to search gift items..
                                        </template>
                                        <template slot="option" slot-scope="option">
                                            <div class="d-center">
                                                {{ option.code }}
                                            </div>
                                        </template>
                                        <template slot="selected-option" slot-scope="option">
                                            <div class="selected d-center">
                                                {{ option.code }}
                                            </div>
                                        </template>
                                    </v-select>
                                </div>
                                <hr/>
                                <div class="col-sm-12">
                                    <div class="form-group text-right m-b-0">                                    
                                        <button type="button" :disabled="isLoading" @click="assignGift()"
                                            class="btn btn-success mb-2 mr-3">Assign Gift</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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

    export default {
        data() {
            return {
                form: {},
                errors: null,
                user_errors: null,
                customers: [],
                customer: {},
                formData: {},
                otherwise: {},
                searchText: "",
                isLoading: false,
                countries: [],
                searchText: "",
                gift:{},
                gift_item: {},
                gift_items: [],
                dateconfig: {
                    wrap: true,
                    altInput: true,
                    enableTime: false,
                    dateFormat: "Y-m-d",
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
            this.getCountries();
        },
        methods: {
            getCountries: function () {
                axios.get('/getcountries')
                    .then(r => r.data)
                    .then((response) => {
                        this.countries = response.data;
                    });
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
                this.form = this.customer ? this.customer : {};
                console.log(this.customer);                
            },
            searchGift: function () {
                axios.post('/search-free-gift-campaign' , this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.gift = response.data ? response.data : {};
                        this.customer = response.customer;
                        //if(response.data == null) notification('Search Shop for free' , "Shop for free Gifts not found!" , 'danger');
                        //this.listCampaign();
                        if(response.message)
                        notification('Search Shop for free' , response.message , 'danger');
                        else if(response.data == null) notification('Search Shop for free' , "Shop for free Gifts not found!" , 'danger');
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                        notification('Search Shop for free' , "Please provide valid data!" , 'danger');
                        window.scrollTo(0,0);
                    });
            },
            assignGift: function(){
                 axios.post('/assign-free-gift-item' , {'gift_id' : this.gift.id , 'gift_item_id' : this.gift_item.id , 'user_id' : this.form.id})
                    .then(r => r.data)
                    .then((response) => {
                        // this.gift = {};
                        // this.gift_item = {};
                        // this.gift_items = [];
                        //this.form = {};
                        notification('Create Shop for free Item' , "Create Shop for free Gift successfully!" , 'success');
                        this.removeAction();
                        this.clearAll();
                        //this.listCampaign();
                    }).catch((error) => {
                        // this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                        //     .errors : null;
                       notification('Create Shop for free Item' , error.response.data.message , 'danger');
                    });
            },
            removeAction(){
                axios.delete('/remove/purchase/action').then(r => r.data).then((response) => {
                });
            },
            updateAction: function(data){                
                axios.post('/update/purchase/action', data).then(r => r.data).then((response) => {
                });
            },
            // searchGiftItems: function (id) {
            //     axios.post('/free-gift-item/search/list' , {'gift_id' : id})
            //         .then(r => r.data)
            //         .then((response) => {
            //             this.gift_items = response.data;
            //         });
            // },
            searchGiftItems(search, loading) {
                loading(true);
                this.onSearchGiftItems(loading, search, this);
            },
            onSearchGiftItems: _.debounce((loading, search, vm) => {
                axios.post('/free-gift-item/search/list', {
                    'gift_id' : vm.gift.id , 'searchText': search
                }).then(r => r.data).then((response) => {
                    vm.gift_items = response.data;
                    console.log(vm.gift_items);
                    
                    loading(false);
                });
            }, 350),
            selectedGiftItem: function () {
                this.gift_item = this.gift_item;
            },
            onInput({
                number,
                isValid,
                country
            }) {
                this.mobile_isValid = isValid;
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
            clearAll(){
                this.form = {};
                this.customer = null;
                this.gift = {};
                this.gift_item = {};
                this.gift_items = [];
            }
        }
    }

</script>
