<template>
    <div class="page-content">

        <div class="container-fluid">
            <div class="page-content__header">
                <div>
                    <h2 class="page-content__header-heading">Edit Spin And Win</h2>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 offset-md-1">
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
                    <div class="main-container">
                        <h3>Spin And Win Edit Form</h3>
                        <hr />
                        <form role="form" ref="editSpinwinFrom" name="editSpinwinFrom"
                            v-on:submit.prevent="editSpinwin">
                            <div class="from-block">
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="spin_and_win_name">Spin & Win Name</label>
                                            <input type="text" class="form-control" name="spin_and_win_name"
                                                v-model="form.spin_and_win_name" placeholder="Spin and win Name"
                                                required>
                                        </div>
                                        <div class="form-group">
                                            <label for="min_amount">Min Amount</label>
                                            <input type="number" min="0" class="form-control" name="min_amount"
                                                v-model="form.min_amount" placeholder="Min Amount">
                                        </div>
                                        <div class="form-group">
                                            <label for="customer_limit">Per Customer Limit</label>
                                            <input type="number" min="0" class="form-control" name="customer_limit"
                                                v-model="form.customer_limit" placeholder="Per Customer Limit">
                                        </div>
                                        <!-- <div class="form-group">
                                            <label for="max_winners">Maximum Winners</label>
                                            <input type="number" min="0" class="form-control" name="max_winners"
                                                v-model="form.max_winners" placeholder="Spinwin maximum winners">
                                        </div> -->
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="form-group">
                                            <label for="start_at">Start At</label>
                                            <flat-pickr class="form-control" :config="config" name="start_at"
                                                v-model="form.start_at" placeholder="Start At"></flat-pickr>
                                        </div>
                                        <div class="form-group">
                                            <label for="end_at">End At</label>
                                            <flat-pickr class="form-control" :config="config" name="end_at"
                                                v-model="form.end_at" placeholder="End At"></flat-pickr>
                                        </div>
                                         <div class="form-group">
                                            <label for="customer_gift_limit">Max Gifts Per Customer</label>
                                            <input type="number" min="0" class="form-control" name="customer_gift_limit"
                                                v-model="form.customer_gift_limit" placeholder="Max Gifts Per Customer">
                                        </div>
                                        <div class="form-group">
                                            <label for="probability">Winner Probabilities</label>
                                            <input type="number" min="0" class="form-control" name="probability"
                                                v-model="form.probability" placeholder="Winner Probabilities">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="main-container">
                            <h3>Templates</h3>
                            <hr/>
                        <div class="row">
                            <div class="col-md-12">

                                <div class="form-group">
                                    <label for="sms">SMS Template</label>
                                    <textarea class="form-control" name="sms" v-model="form.sms" rows="5"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="description">Send SMS</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="send_sms" v-bind:true-value="1"
                                                v-bind:false-value="0" v-model="form.send_sms">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Email Template</label>
                                    <textarea class="form-control" name="sms" v-model="form.email" rows="10"></textarea>
                                    <a :href="'spin-win-email-preview/' + $route.params.spinner_id" target="_blank"
                                        type="button" class="btn btn-info">Preview</a>
                                    <!-- <ckeditor :editor="editor" v-model="form.email" :config="editorConfig"></ckeditor> -->
                                </div>
                                <div class="form-group">
                                    <label for="description">Send Email</label>
                                    <div class="">
                                        <label class="switch mr-3">
                                            <input type="checkbox" name="send_email" v-bind:true-value="1"
                                                v-bind:false-value="0" v-model="form.send_email">
                                            <span class="switch-slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="main-container">
                        <h3>Gift Details</h3>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-striped table-bordered m-b-0 tickets-list table-actions-bar dt-responsive nowrap">
                                    <thead>
                                        <tr>
                                            <th>Gift Name</th>
                                            <th>Display Name</th>
                                            <th>Color</th>
                                            <th class="hidden-sm">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(item , index) in items" :key="item.id">
                                            <td>
                                                <span v-if="item.gift">{{ item.gift.name }}</span>
                                                <span v-else>No Gift</span>
                                            </td>
                                            <td>{{ item.name}}</td>
                                            <td>
                                                <div :style="'background-color:' + item.bg_color + ';color:' + item.text_color" class="size-25">B</div>
                                                <div :style="'background-color:' + item.text_color + ';color:' + item.bg_color" class="size-25">T</div>
                                            </td>
                                            <td>
                                                <div class="dropdown table__cell-actions-item">
                                                    <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" x-placement="bottom-start">                                                        
                                                        <a class="dropdown-item" @click="fetchGift(item.id )"
                                                            href="javascript:void(0)">Edit</a>
                                                        <a class="dropdown-item"
                                                            @click="removeGift(item.id , index)"
                                                            href="javascript:void(0)">Delete</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td v-if="!items.length"  colspan="4" class="no-data-found-info">No Gifts found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr/>
                        <div class="form-group text-right m-b-0">
                            <button type="button" @click="showGift()" class="btn btn-success mb-2 mr-3">Add</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-11 col-sm-12 offset-md-1">
                    <hr />
                    <div class="form-group text-right m-b-0">
                        <button type="button" @click="listSpinwin()"
                            class="btn btn-default mb-2 mr-3 pull-left">Cancel</button>
                        <button @click="editSpinwin()" type="button" class="btn btn-success mb-2 mr-3">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal-create-gift" class="modal fade custom-modal-tabs">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Add Gift</h5>
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="display_name">Display Name</label>
                                    <input type="text" class="form-control" id="display_name" name="display_name"
                                        v-model="gift.name" placeholder="Display name">
                                </div>
                                <div class="form-group">
                                    <label for="gift_name">Gift</label>
                                    <select v-model="gift.gift_id" class="form-control" id="gift_id">
                                        <option :value="''">No Gift</option>
                                        <option v-for="g in gifts" :key="g.id" :value="g.id">{{ g.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bg_color">Background Color</label>
                                    <chrome-picker v-model="background_color"></chrome-picker>
                                </div>
                                <div class="form-group">
                                    <label for="text_color">Text Color</label>
                                    <chrome-picker v-model="text_color"></chrome-picker>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="button" @click="createGift()">Create</button>
                    </div>
                </div>
            </div>
        </div>
        <div id="modal-edit-gift" class="modal fade custom-modal-tabs">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header has-border">
                        <h5 class="modal-title">Edit Gift</h5>
                        <button type="button" class="close custom-modal__close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="ua-icon-modal-close"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="display_name">Display Name</label>
                                    <input type="text" class="form-control" id="display_name" name="display_name"
                                        v-model="gift.name" placeholder="Display name">
                                </div>
                                <div class="form-group">
                                    <label for="gift_name">Gift</label>
                                    <select v-model="gift.gift_id" class="form-control" id="gift_id">
                                        <option :value="''">No Gift</option>
                                        <option v-for="g in gifts" :key="g.id" :value="g.id">{{ g.name }}</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="bg_color">Background Color</label>
                                    <chrome-picker v-model="background_color"></chrome-picker>
                                </div>
                                <div class="form-group">
                                    <label for="text_color">Text Color</label>
                                    <chrome-picker v-model="text_color"></chrome-picker>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-footer--center">
                        <button type="button" class="btn btn-outline-info" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                        <button class="btn btn-info" type="button" @click="editGift()">Update</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axios from 'axios';    
    import Chrome from 'vue-color/src/components/Chrome';

    export default {
        components: {
            'chrome-picker': Chrome,
        },
        data() {
            return {
                form: {},
                gifts: [],
                items: [],
                color: '#000000',
                gift: {
                    name: null,
                    gift_id: null,
                    bg_color: '#000000',
                    text_color: '#000000',
                    spinner_id: null
                },
                errors: null,
                background_color: {},
                text_color: {},
                config: {
                    wrap: true,
                    altInput: true,
                    enableTime: true,
                    dateFormat: "Y-m-d H:i",
                },
                colors: [ 
                    '#ee1c24', '#3cb878', '#00aef0', '#f26522', '#000000', 
                    '#e70697', '#fff200', '#f6989d', '#a186be'
                ],
            }
        },
        mounted: function () {
            this.getSpinwin();
        },
        methods: {
            getSpinwin: function () {
                axios.get('/spinner/fetch/' + this.$route.params.spinner_id)
                    .then(r => r.data)
                    .then((response) => {
                        this.form = response.data;
                        this.gifts = this.form.gifts;
                        this.items = this.form.items;
                    });
            },
            editSpinwin: function () {
                axios.post('/spinner/edit/' + this.$route.params.spinner_id, this.form)
                    .then(r => r.data)
                    .then((response) => {
                        this.errors = null;
                        this.listSpinwin();
                    }).catch((error) => {
                        this.errors = typeof error.response.data.errors !== 'undefined' ? error.response.data
                            .errors : null;
                    });;
                hidebtnLoader();
            },
            showGift: function(){
                this.gift = {
                    name: null,
                    gift_id: null,
                    bg_color: '#000000',
                    text_color: '#000000',
                    spinner_id: null
                };
                this.background_color = {};
                this.text_color = {};
                $('#modal-create-gift').modal('show');
            },
            removeGift: function (id, index) {
                axios.delete('/spinner/gift-item/' + id)
                    .then(r => r.data)
                    .then((response) => {
                        this.items = response.data.items;
                    });
            },
            createGift: function () {
                //if(!this.gift.gift_id) return;
                this.gift.spinner_id = this.$route.params.spinner_id;
                console.log(this.background_color);
                console.log(this.text_color);
                this.gift.bg_color =  this.background_color ?  this.background_color.hex : null;
                this.gift.text_color =  this.text_color ?  this.text_color.hex : null;
                axios.post('/spinner/gift-item/create', this.gift)
                    .then(r => r.data)
                    .then((response) => {
                        this.items = response.data.items;
                        $('#modal-create-gift').modal('hide');
                    });
                hidebtnLoader();
            },
            editGift: function () {
                //if(!this.gift.gift_id) return;
                this.gift.spinner_id = this.$route.params.spinner_id;
                this.gift.bg_color =  this.background_color ?  this.background_color.hex : null;
                this.gift.text_color =  this.text_color ?  this.text_color.hex : null;
                axios.post('/spinner/gift-item/edit/' + this.gift.id , this.gift)
                    .then(r => r.data)
                    .then((response) => {
                         this.items = response.data.items;
                        $('#modal-edit-gift').modal('hide');
                    }).catch((error) => {
                        
                    });
                hidebtnLoader();
            },
            fetchGift: function (id) {
                axios.get('/spinner/gift-item/fetch/' + id )
                    .then(r => r.data)
                    .then((response) => {
                        this.gift = response.data;
                        this.background_color = {hex: this.gift.bg_color ? this.gift.bg_color : '#000000'};
                        this.text_color = {hex: this.gift.text_color ? this.gift.text_color : '#000000'};
                        $('#modal-edit-gift').modal('show');
                    });
                hidebtnLoader();
            },
            listSpinwin: function () {
                this.$router.push({
                    name: 'SpinAndWins'
                });
            }
        }
    }

</script>
<style scope>
.size-25{
    font-size: 16px;
    width: 25px;
    text-align: center;
    height: 25px;
    border-radius: 50%;
}

.dropdown.v-select.single.searchable .dropdown-toggle{
    padding: 5px !important;
}
</style>